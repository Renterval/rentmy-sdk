<?php
namespace RentMy;

class Config extends RentMy{

    private $accessToken;
    private $locationId;

    /**
     * Checkout constructor.
     * @param $accessToken
     * @param $location_id
     */

    function __construct($accessToken, $location_id)
    {
        $this->accessToken = $accessToken;
        $this->locationId = $location_id;
    }

    /**
     * get store configs according to store params
     * @param $type categories,location,tags,variants,variant_sets,paymentgateways
     * primarily we will store only location id
     * settings?type=categories,location,tags,variants,variant_sets,paymentgateways
     * @return mixed|string|null
     */
    public function config($type)
    {
        try {
            $response = self::httpGet(
                '/settings?type=' . $type,
                $this->accessToken
            );
            return $response['result']['data'];
        } catch (Exception $e) {

        }
    }


    /**
     * Get categories with threaded mode
     * @return mixed|string|null
     */
    public function categories()
    {
        try {
            $response = self::httpGet(
                '/categories',
                $this->accessToken,
                null
            );
            return $response['result']['data'];
        } catch (Exception $e) {

        }

    }

    /**
     * Get Store config
     * @return mixed
     */
    public function store_config()
    {
        try {
            if (empty($_SESSION['config'])) {
                $response = self::httpGet(
                    '/settings?type=store_config',
                    $this->accessToken
                );
                $_SESSION['config'] = $response['result']['data']['config'];
            }
            return  $_SESSION['config'];

        } catch (Exception $e) {

        }
    }

    /**
     * Get Store Contents
     * @return mixed
     */
    public function store_contents()
    {
        try {
            $response = self::httpGet(
                '/contents',
                [
                    'token' => $this->accessToken,
                    'location' => $this->locationId
                ]
            );
            return $response['result']['data'];

        } catch (Exception $e) {

        }
    }

    /**
     * Get country list
     * @return mixed
     */
    public function countries()
    {
        $response = self::httpGet(
            '/countries',
            $this->accessToken
        );
        return $response['result']['data'];

    }

    // get delivery settings
   public function getDeliverySettings()
    {
        try {
            $response = self::httpGet(
                '/stores/delivery-settings',
                [
                    'token' => $this->accessToken,
                ]
            );
            return $response['result'];
        } catch (Exception $e) {

        }
    }

    // get location list
    public function getLocationList()
    {
        try {
            $response = self::httpGet(
                '/locations/list',
                [
                    'token' => $this->accessToken,
                ]
            );
            return $response['result'];
        } catch (Exception $e) {

        }

    }

    // get payment gateways that are enabled
    public function getPaymentGateWays()
    {
        $response = self::httpGet(
            '/payments/gateway?is_online=1',
            [
                'token' => $this->accessToken,
            ]
        );
        return $response['result'];

    }
}