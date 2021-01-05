<?php
namespace RentMy;
/**
 * Class RentMy_Pages
 * This is for About Us, Contact Us, and other custom pages API
 */
Class Pages extends RentMy
{

    private $accessToken;
    private $locationId;

    function __construct($accessToken, $locationId)
    {
        $this->accessToken = $accessToken;
        $this->locationId = $locationId;
    }
    /**
     * Get about us page information
     * @return mixed|string|null
     */
    function aboutUs()
    {
        try {
            $response = self::httpGet(
                '/pages/about',
                [
                    'token' => $this->accessToken,
                ]
            );

            return !empty($response['result']['data']) ? $response['result']['data'] : null;
        } catch (Exception $e) {

        }

    }


}
