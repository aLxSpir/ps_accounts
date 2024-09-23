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

namespace PrestaShop\Module\PsAccounts\Api\Client;

use PrestaShop\Module\PsAccounts\Http\Client\Guzzle\GuzzleClient;
use PrestaShop\Module\PsAccounts\Http\Client\Guzzle\GuzzleClientFactory;

class ExternalAssetsClient
{
    /**
     * @var string
     */
    private $apiUrl;

    /**
     * @var GuzzleClient
     */
    private $client;

    /**
     * @var int
     */
    private $defaultTimeout;

    /**
     * ServicesAccountsClient constructor.
     *
     * @param string $apiUrl
     * @param GuzzleClient|null $client
     * @param int $defaultTimeout
     *
     * @throws \Exception
     */
    public function __construct(
                     $apiUrl,
        GuzzleClient $client = null,
                     $defaultTimeout = 20
    ) {
        $this->apiUrl = $apiUrl;
        $this->client = $client;
        $this->defaultTimeout = $defaultTimeout;
    }

    /**
     * @return GuzzleClient
     */
    private function getClient()
    {
        if (null === $this->client) {
            $this->client = (new GuzzleClientFactory())->create([
                'name' => static::class,
                'base_uri' => $this->apiUrl,
                'headers' => $this->getHeaders(),
                'timeout' => $this->defaultTimeout,
            ]);
        }

        return $this->client;
    }

    /**
     * @param array $additionalHeaders
     *
     * @return array
     */
    private function getHeaders($additionalHeaders = [])
    {
        return array_merge([
            'Accept' => 'application/json',
        ], $additionalHeaders);
    }

    /**
     * @param string $uri
     *
     * @return array
     */
    public function getTestimonials($uri)
    {
        $this->getClient()->setRoute($uri);

        return $this->getClient()->get();
    }
}