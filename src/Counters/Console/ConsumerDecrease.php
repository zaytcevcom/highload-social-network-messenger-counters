<?php

declare(strict_types=1);

namespace App\Counters\Console;

use App\Counters\Command\Counter\Decrease\DecreaseCommand;
use App\Counters\Command\Counter\Decrease\DecreaseHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ZayMedia\Shared\Components\Queue\Queue;

final class ConsumerDecrease extends Command
{
    public function __construct(
        private readonly Queue $queue,
        private readonly DecreaseHandler $handler,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('counters:consumer-decrease')
            ->setDescription('Consumer decrease counter');
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
                new DecreaseCommand(
                    conversationId: $info['conversationId'],
                    userId: $info['userId'],
                    value: $info['value']
                )
            );
        };

        $this->queue->consume(
            'conversation-counter-decrease',
            $callback
        );

        return 0;
    }
}
