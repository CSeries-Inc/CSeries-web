<?php
namespace cseries\Utils\T411\Authentication;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Rosello\T411\Config\AuthConfig;
use Rosello\T411\Config\TokenConfig;
use Rosello\T411\Exception\AuthenticationException;

class Authentication
{
    /**
     * @var Client
     */
    private $client;

    public function __construct(ClientInterface $client = null)
    {
        if ($client) {
            $this->client = $client;
        } else {
            $this->client = new Client();
        }
    }

    /**
     * @param AuthConfig $config
     *
     * @return TokenConfig
     * @throws AuthenticationException
     */
    public function auth(AuthConfig $config)
    {
        $response = $this->client->post($config->getBaseUrl() . '/auth', array(
            'body' => array(
                'username' => $config->getUsername(),
                'password' => $config->getPassword()
            )
        ));
        $data = $response->json();

        if (isset($data['error']) && isset($data['code'])) {
            throw new AuthenticationException($data['error'], $data['code']);
        }

        return new TokenConfig($data['token']);
    }
}