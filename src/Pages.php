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

    /**
     * Send contact us data
     * @param $data
     * @return array|string[]
     */
    function sendEmailFromContact($data)
    {
        try {
            $response = self::httpPost(
                '/contactus',
                [
                    'token' => $this->accessToken,
                ],
                $data
            );

            if (isset($response['result']['message'])) {
                $message = $response['result']['message'];
                return ['status' => 'NOK', 'message' => $message];
            } else {
                return ['status' => 'OK'];
            }
        } catch (Exception $e) {

        }
    }

}
