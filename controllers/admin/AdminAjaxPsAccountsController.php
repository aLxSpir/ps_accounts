<?php
/**
 * 2007-2020 PrestaShop and Contributors.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
use PrestaShop\Module\PsAccounts\Service\SshKey;

class AdminAjaxPsAccountsController extends ModuleAdminController
{
    /**
     * AJAX: Generate ssh key.
     */
    public function ajaxProcessGenerateSshKey()
    {
        $sshKey = new SshKey();
        $key    = $sshKey->generate();
        Configuration::updateValue('PS_ACCOUNTS_RSA_PRIVATE_KEY', $key['privatekey']);
        Configuration::updateValue('PS_ACCOUNTS_RSA_PUBLIC_KEY', $key['publickey']);
        $data = 'data';
        Configuration::updateValue(
            'PS_ACCOUNTS_RSA_SIGN_DATA',
            $sshKey->signData(
                Configuration::get('PS_ACCOUNTS_RSA_PRIVATE_KEY'),
                $data
            )
        );

        $this->ajaxDie(
            json_encode(Configuration::get('PS_ACCOUNTS_RSA_PUBLIC_KEY'))
        );
    }

    /**
     * AJAX: Reset onboading.
     */
    public function ajaxProcessResetOnboarding()
    {
        Configuration::updateValue('PS_ACCOUNTS_RSA_PRIVATE_KEY', null);
        Configuration::updateValue('PS_ACCOUNTS_RSA_PUBLIC_KEY', null);
        Configuration::updateValue('PS_ACCOUNTS_RSA_SIGN_DATA', null);
        Configuration::updateValue('PS_PSX_FIREBASE_EMAIL', null);
        Configuration::updateValue('PS_PSX_FIREBASE_ID_TOKEN', null);
        Configuration::updateValue('PS_PSX_FIREBASE_LOCAL_ID', null);
        Configuration::updateValue('PS_PSX_FIREBASE_REFRESH_TOKEN', null);
        Configuration::updateValue('PS_PSX_FIREBASE_REFRESH_DATE', null);
        Configuration::updateValue('PS_PSX_FIREBASE_ADMIN_TOKEN', null);
        Configuration::updateValue('PS_PSX_FIREBASE_LOCK', null);

        $this->ajaxDie(
            json_encode(true)
        );
    }
}
