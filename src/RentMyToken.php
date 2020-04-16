<?php

namespace RentMy;
require_once 'RentMy.php';

Class RentMyToken extends RentMy
{
    public $apiKey;
    public $apiSecret;

    public function __construct()
    {

    }


    /**
     * Get AccessToken
     * @return mixed
     * @todo check domain name
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

            if (!empty($response['result']['data']['token'])) {

                $_SESSION['rentmy_accessToken'] = $response['result']['data']['token'];
                $_SESSION['rentmy_refreshToken'] = $response['result']['data']['refresh_token'];
                $_SESSION['rentmy_storeId'] = $response['result']['data']['store_id'];
                $_SESSION['rentmy_locationId'] = $response['result']['data']['location_id'];
            }

        } catch (Exception $e) {

        }

    }

}


?>
