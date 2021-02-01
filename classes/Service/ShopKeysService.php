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

namespace PrestaShop\Module\PsAccounts\Service;

use phpseclib\Crypt\RSA;
use PrestaShop\Module\PsAccounts\Exception\SshKeysNotFoundException;
use PrestaShop\Module\PsAccounts\Repository\ConfigurationRepository;

/**
 * Manage RSA
 */
class ShopKeysService
{
    const SIGNATURE_DATA = 'data';

    /**
     * @var RSA
     */
    private $rsa;

    /**
     * @var ConfigurationRepository
     */
    private $configuration;

    public function __construct(ConfigurationRepository $configuration)
    {
        $this->rsa = new RSA();
        $this->rsa->setHash('sha256');
        $this->rsa->setSignatureMode(RSA::SIGNATURE_PKCS1);

        $this->configuration = $configuration;
    }

    /**
     * @return array
     */
    public function createPair()
    {
        $this->rsa->setPrivateKeyFormat(RSA::PRIVATE_FORMAT_PKCS1);
        $this->rsa->setPublicKeyFormat(RSA::PUBLIC_FORMAT_PKCS1);

        return $this->rsa->createKey();
    }

    /**
     * @param string $privateKey
     * @param string $data
     *
     * @return string
     */
    public function signData($privateKey, $data)
    {
        $this->rsa->loadKey($privateKey, RSA::PRIVATE_FORMAT_PKCS1);

        return base64_encode($this->rsa->sign($data));
    }

    /**
     * @param string $publicKey
     * @param string $signature
     * @param string $data
     *
     * @return bool
     */
    public function verifySignature($publicKey, $signature, $data)
    {
        $this->rsa->loadKey($publicKey, RSA::PUBLIC_FORMAT_PKCS1);

        return $this->rsa->verify($data, base64_decode($signature));
    }

    /**
     * @param bool $refresh
     *
     * @return void
     *
     * @throws SshKeysNotFoundException
     */
    public function generateKeys($refresh = true)
    {
        if ($refresh || false === $this->hasKeys()) {
            $key = $this->createPair();
            $this->configuration->updateAccountsRsaPrivateKey($key['privatekey']);
            $this->configuration->updateAccountsRsaPublicKey($key['publickey']);

            $this->configuration->updateAccountsRsaSignData(
                $this->signData(
                    $this->configuration->getAccountsRsaPrivateKey(),
                    self::SIGNATURE_DATA
                )
            );

            if (false === $this->hasKeys()) {
                throw new SshKeysNotFoundException('No RSA keys found for the shop');
            }
        }
    }

    /**
     * @return void
     *
     * @throws SshKeysNotFoundException
     */
    public function regenerateKeys()
    {
        $this->generateKeys(true);
    }

    /**
     * @return bool
     */
    public function hasKeys()
    {
        return false === (
                empty($this->configuration->getAccountsRsaPublicKey())
                || empty($this->configuration->getAccountsRsaPrivateKey())
                || empty($this->configuration->getAccountsRsaSignData())
            );
    }

    /**
     * @return string
     */
    public function getPublicKey()
    {
        return $this->configuration->getAccountsRsaPublicKey();
    }

    /**
     * @return string
     */
    public function getSignature()
    {
        return $this->configuration->getAccountsRsaSignData();
    }
}