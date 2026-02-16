<?php

namespace Xqueue\Maileon\Model\Maileon;

use de\xqueue\maileon\api\client\contacts\Contacts;
use de\xqueue\maileon\api\client\contacts\ContactsService;
use de\xqueue\maileon\api\client\contacts\Permission;
use de\xqueue\maileon\api\client\contacts\SynchronizationMode;
use de\xqueue\maileon\api\client\transactions\TransactionsService;
use Exception;
use Psr\Log\LoggerInterface;
use Xqueue\Maileon\Helper\Config;

class ImportService
{
    private ?ContactsService $contactsService = null;

    private ?TransactionsService $transactionsService = null;

    public function __construct(
        protected LoggerInterface $logger,
        protected Config $config
    ) {}

    /**
     * @throws Exception
     */
    public function syncContacts(Contacts $contacts, bool $withoutPermission = false): bool
    {
        if ($withoutPermission) {
            $permission = Permission::$NONE;
        } else {
            $permission = Permission::getPermission($this->config->getNewsletterSubscriberImportPermission());
        }

        $contactsService = $this->getContactsService();
        $response = $contactsService->synchronizeContacts(
            $contacts,
            $permission,
            SynchronizationMode::$UPDATE,
            false,
            true,
            true,
            false
        );

        return $response->isSuccess();
    }

    /**
     * @throws Exception
     */
    public function sendTransactions(array $transactions): bool
    {
        $transactionsService = $this->getTransactionsService();
        $response = $transactionsService->createTransactions($transactions, true, true);

        return $response->isSuccess();
    }

    private function getContactsService(): ContactsService
    {
        if ($this->contactsService === null) {
            $this->contactsService = new ContactsService($this->getMaileonConfig());
        }

        return $this->contactsService;
    }

    private function getTransactionsService(): TransactionsService
    {
        if ($this->transactionsService === null) {
            $this->transactionsService = new TransactionsService($this->getMaileonConfig());
        }

        return $this->transactionsService;
    }

    private function getMaileonConfig(): array
    {
        return [
            'BASE_URI' => 'https://api.maileon.com/1.0',
            'API_KEY'  => $this->config->getApiKey(),
            'TIMEOUT'  => 30,
        ];
    }
}
