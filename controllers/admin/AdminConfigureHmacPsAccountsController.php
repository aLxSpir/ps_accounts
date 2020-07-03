<?php
/**
* 2007-2020 PrestaShop.
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2020 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

use Symfony\Component\Dotenv\Dotenv;

/**
 * Controller generate hmac and redirect on hmac's file.
 */
class AdminConfigureHmacPsAccountsController extends ModuleAdminController
{
    /**
     * @return void
     */
    public function initContent()
    {
        $dotenv = new Dotenv();
        $dotenv->load(_PS_MODULE_DIR_ . 'ps_accounts/.env');

        try {
            if (null === Tools::getValue('hmac')) {
                throw new Exception("Caught exception: Hmac does not exist \n");
            }
            $hmacPath = _PS_ROOT_DIR_ . '/upload/';
            foreach (['hmac' => '/[a-zA-Z0-9]{8,64}/', 'uid' => '/[a-zA-Z0-9]{8,64}/', 'slug' => '/[-_a-zA-Z0-9]{8,255}/'] as $key => $value) {
                if (!array_key_exists($key, Tools::getAllValues())) {
                    throw new Exception("Missing query params \n");
                }

                if (!preg_match($value, Tools::getValue($key))) {
                    throw new Exception("Invalide query params \n");
                }
            }

            if (!is_dir($hmacPath)) {
                mkdir($hmacPath);
            }

            if (!is_writable($hmacPath)) {
                throw new Exception("Directory isn't writable \n");
            }

            file_put_contents($hmacPath . Tools::getValue('uid') . '.txt', Tools::getValue('hmac'));
        } catch (Exception $e) {
        }
        $url = $_ENV['ACCOUNTS_SVC_UI_URL'];
        if (false === $url) {
            throw new \Exception('Environmenrt variable ACCOUNTS_SVC_UI_URL should not be empty');
        }
        if ('/' === substr($url, -1)) {
            $url = substr($url, 0, -1);
        }

        header(
            'Location: ' . $url . '/shop/account/verify/' . Tools::getValue('uid')
            . '?shopKey='
            . urlencode(Configuration::get('PS_ACCOUNTS_RSA_SIGN_DATA'))
        );
        exit;
    }
}
