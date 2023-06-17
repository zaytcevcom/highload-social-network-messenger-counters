<?php

declare(strict_types=1);

namespace App\Counters\Console;

use App\Counters\Command\Counter\Increase\IncreaseCommand;
use App\Counters\Command\Counter\Increase\IncreaseHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ZayMedia\Shared\Components\Queue\Queue;

final class ConsumerIncrease extends Command
{
    public function __construct(
        private readonly Queue $queue,
        private readonly IncreaseHandler $handler,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('counters:consumer-increase')
            ->setDescription('Consumer increase counter');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $callback = function (object $msg) use ($output): void {
            /**
             * @var array{
             *     conversationId:int,
             *     userId:int,
             *     value:int,
             * } $info
             */
            $info = json_decode((string)$msg->body, true);

            $output->writeln('<info>[ConversationId]</info> - ' . $info['conversationId']);

            $this->handler->handle(
                new IncreaseCommand(
                    conversationId: $info['conversationId'],
                    userId: $info['userId'],
                    value: $info['value']
                )
            );
        };

        $this->queue->consume(
            queue: 'conversation-counter-increase',
            callback: $callback
        );

        return 0;
    }
}
