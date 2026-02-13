<?php

namespace Xqueue\Maileon\Plugin\Console;

use Magento\Framework\Console\CommandListInterface;
use Xqueue\Maileon\Console\Command\ImportNewsletterSubscribersCommand;
use Xqueue\Maileon\Console\Command\ImportOrderHistoryCommand;
use Xqueue\Maileon\Console\Command\MarkAbandonedCartsCommand;
use Xqueue\Maileon\Console\Command\SendAbandonedCartsCommand;

class CommandListPlugin
{
    public function __construct(
        protected ImportNewsletterSubscribersCommand $importSubscribersCommand,
        protected ImportOrderHistoryCommand $importOrderHistoryCommand,
        protected MarkAbandonedCartsCommand $markAbandonedCartsCommand,
        protected SendAbandonedCartsCommand $sendAbandonedCartsCommand
    ) {}

    /**
     * Append Maileon CLI commands without overriding core commands
     *
     * @param CommandListInterface $subject
     * @param array $result
     * @return array
     */
    public function afterGetCommands(CommandListInterface $subject, array $result): array
    {
        $result[] = $this->importSubscribersCommand;
        $result[] = $this->importOrderHistoryCommand;
        $result[] = $this->markAbandonedCartsCommand;
        $result[] = $this->sendAbandonedCartsCommand;

        return $result;
    }
}
