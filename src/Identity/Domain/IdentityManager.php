<?php

namespace PrestaShop\Module\PsAccounts\Identity\Domain;

interface IdentityManager
{
    /**
     * @param string $shopId
     *
     * @return Identity
     */
    public function get($shopId);

    /**
     * @param Identity $identity
     *
     * @return void
     */
	public function save(Identity $identity);
}
