<?php

namespace RentMy;

Class RentMyToken extends RentMy
{
    public $apiKey;
    public $apiSecret;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get AccessToken
     * @param $rentmy_apiKey
     * @param $rentmy_secretKey
     * @return mixed
     */
    public function getToken($rentmy_apiKey, $rentmy_secretKey)
    {

        $this->apiKey = $rentmy_apiKey;
        $this->apiSecret = $rentmy_secretKey;
        try {
            $response = self::httpPost(
                '/apps/access-token',
                null,
                [
                    'api_key' => $this->apiKey,
                    'api_secret' => $this->apiSecret
                ]
            );
            return $response['result']['data'];

        } catch (Exception $e) {

        }
    }
    /**
     * Get Store token when store name from url
     * @param $type
     * @return mixed
     */
    public function getTokenFromStoreName($name, $params=[])
    {
        try {
            $disable_default_time = $params['disable_default_time'] ?? false;
            $response = self::httpPost(
                '/apps/access-token',
                null,
                [
                    'store_name' => $name,
                    'disable_default_time' => $disable_default_time
                ]
            );
            return $response['result']['data'];
        } catch (Exception $e) {

        }
    }

}


?>
