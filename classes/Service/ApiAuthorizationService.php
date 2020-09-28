<?php

namespace PrestaShop\Module\PsAccounts\Service;

use PrestaShop\Module\PsAccounts\Repository\AccountsSyncRepository;

class ApiAuthorizationService
{
    /**
     * @var AccountsSyncRepository
     */
    private $accountsSyncStateRepository;

    public function __construct(AccountsSyncRepository $accountsSyncStateRepository)
    {
        $this->accountsSyncStateRepository = $accountsSyncStateRepository;
    }

    /**
     * Authorizes if the call to endpoint is legit and creates sync state if needed
     *
     * @param string $jobId
     *
     * @return bool
     *
     * @throws \PrestaShopDatabaseException
     */
    public function authorizeCall($jobId)
    {
        $syncState = $this->accountsSyncStateRepository->findSyncStateByJobId($jobId);

        if ($syncState) {
            return true;
        }

        //TODO: HERE WE CHECK WITH ACCOUNTS API IF JOB IS LEGIT
        return $this->accountsSyncStateRepository->insertSync($jobId, date(DATE_ATOM));
    }
}