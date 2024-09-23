<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

namespace PrestaShop\Module\PsAccounts\Middleware;

use Exception;
use PrestaShop\Module\PsAccounts\Log\Logger;
use PrestaShop\Module\PsAccounts\Provider\OAuth2\PrestaShopLogoutTrait;
use PrestaShop\Module\PsAccounts\Provider\OAuth2\PrestaShopSession;
use PrestaShop\Module\PsAccounts\Provider\OAuth2\ShopProvider;
use PrestaShop\Module\PsAccounts\Service\PsAccountsService;
use Ps_accounts;

class Oauth2Middleware
{
    use PrestaShopLogoutTrait;

    /**
     * @var PsAccountsService
     */
    private $psAccountsService;

    /**
     * @var PrestaShopSession
     */
    private $prestaShopSession;

    /**
     * @var ShopProvider
     */
    private $shopProvider;

    public function __construct(Ps_accounts $module)
    {
        $this->psAccountsService = $module->getService(PsAccountsService::class);
        $this->prestaShopSession = $module->getService(PrestaShopSession::class);
        $this->shopProvider = $module->getService(ShopProvider::class);
    }

    /**
     * @return void
     */
    public function execute()
    {
        try {
            if (isset($_GET['logout'])) {
                $this->executeLogout();
            }
        } catch (Exception $e) {
            Logger::getInstance()->err($e->getMessage());
        }
    }

    /**
     * @return void
     *
     * @throws Exception
     */
    public function executeLogout()
    {
        if ($this->psAccountsService->getLoginActivated() &&
            !isset($_GET[ShopProvider::QUERY_LOGOUT_CALLBACK_PARAM])) {
            $this->oauth2Logout();
        }
        $this->getOauth2Session()->clear();
    }

    /**
     * @return ShopProvider
     *
     * @throws Exception
     */
    protected function getProvider()
    {
        return $this->shopProvider;
    }

    /**
     * @return PrestaShopSession
     *
     * @throws Exception
     */
    protected function getOauth2Session()
    {
        return $this->prestaShopSession;
    }

    /**
     * @return bool
     *
     * @throws Exception
     */
    protected function isOauth2LogoutEnabled()
    {
        // return $this->module->hasParameter('ps_accounts.oauth2_url_session_logout');
        // FIXME
        return true;
    }
}
