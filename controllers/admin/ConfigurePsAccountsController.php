<?php
/**
* 2007-2019 PrestaShop.
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
*  @copyright 2007-2019 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
use PrestaShop\Module\PsAccounts\Service\SshKey;

class ConfigurePsAccountsController extends ModuleAdminController
{
    /**
     * Construct.
     */
    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
    }

    /**
     * Initialize the content by adding Boostrap and loading the TPL.
     *
     * @param none
     *
     * @return none
     */
    public function initContent()
    {
        parent::initContent();
        $this->module->loadAsset();
        $this->context->smarty->assign(array(
            'appLink' => Tools::getShopDomainSsl(true).$this->module->getPath().'views/js/index.js',
            'chunkVendorsLink' => Tools::getShopDomainSsl(true).$this->module->getPath().'views/js/chunk-vendors.js',
        ));
        Media::addJsDef([
            'ajax_controller_url' => $this->context->link->getAdminLink('AdminAjaxPsAccounts'),
        ]);
        $this->manageSshKey();
        $this->setTemplate('configure.tpl');
    }

    private function manageSshKey()
    {
        if (
            !Configuration::get('PS_ACCOUNT_RSA_PUBLIC_KEY')
            && !Configuration::get('PS_ACCOUNT_RSA_PRIVATE_KEY')
        ) {
            $sshKey = new SshKey();
            $sshKey->generate();
            Configuration::updateValue('PS_ACCOUNT_RSA_PUBLIC_KEY', $sshKey->getPublicKey());
            Configuration::updateValue('PS_ACCOUNT_RSA_PRIVATE_KEY', $sshKey->getPrivateKey());
        }
    }
}
