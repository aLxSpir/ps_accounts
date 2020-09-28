<?php

namespace PrestaShop\Module\PsAccounts\Service;

use Exception;
use GuzzleHttp\Exception\ClientException;
use PrestaShop\Module\PsAccounts\Api\Client\SegmentClient;

class SegmentService
{
    /**
     * @var SegmentClient
     */
    private $segmentClient;
    /**
     * @var CompressionService
     */
    private $compressionService;

    public function __construct(
        SegmentClient $segmentClient,
        CompressionService $compressionService
    ) {
        $this->segmentClient = $segmentClient;
        $this->compressionService = $compressionService;
    }

    /**
     * @param string $syncId
     * @param array $data
     *
     * @return array
     */
    public function upload($syncId, $data)
    {
        try {
            $compressedData = $this->compressionService->gzipCompressData($data);
        } catch (Exception $exception) {
            return ['error' => $exception->getMessage()];
        }

        try {
            $response = $this->segmentClient->upload($syncId, $compressedData);
        } catch (ClientException $exception) {
            return ['error' => $exception->getMessage()];
        }

        return $response;
    }
}