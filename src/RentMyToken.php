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

}


?>
