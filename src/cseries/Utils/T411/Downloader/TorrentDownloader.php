<?php

namespace cseries\Utils\T411\Downloader;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use cseries\Utils\T411\Config\TokenConfig;
use cseries\Utils\T411\Model\Torrent;

/**
 * Class TorrentDownloader
 * @package Rosello\T411\Downloader
 */
class TorrentDownloader
{
    /**
     * @param TokenConfig $tokenConfig
     * @param ClientInterface $client
     */
    public function __construct(TokenConfig $tokenConfig, ClientInterface $client = null)
    {
        if ($client) {
            $this->client = $client;
        } else {
            $this->client = new Client();
        }

        $this->config = $tokenConfig;
    }

    /**
     * @param Torrent $torrent
     * @return string
     */
    public function download(Torrent $torrent)
    {
        $response = $this->client->post($this->config->getBaseUrl() . "/torrents/download/{$torrent->getId()}", array(
            'headers' => array(
                "Authorization" => $this->config->getToken()
            )
        ));

        return $response->getBody()->getContents();
    }
}