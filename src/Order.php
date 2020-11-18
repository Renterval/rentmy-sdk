<?php

namespace RentMy;

class Order extends RentMy
{

    private $accessToken;
    private $locationId;

    /**
     * Order constructor.
     * @param $accessToken
     * @param $location_id
     */

    function __construct($accessToken, $location_id)
    {
        $this->accessToken = $accessToken;
        $this->locationId = $location_id;
    }

    function viewOrderDetails($order_id)
    {
        try {
            $response = self::httpGet(
                '/orders/' . $order_id . '/complete',
                [
                    'token' => $this->accessToken,
                    'location' => $this->locationId
                ]
            );
            return $response;
        } catch (Exception $e) {

        }
    }

    /**
     * List all the active additional services for any store
     * @return mixed|string
     */
    function getAdditionalServices()
    {
        try {
            $response = self::httpGet(
                '/settings/orders/additional-charges',
                [
                    'token' => $this->accessToken,
                ]
            );
            return $response;
        } catch (Exception $e) {

        }
    }

    /**
     * This function will re calculated the order total, tax, payment amount based on
     * selected addional services.
     * Sample data
     * {
     * "token": "1605679756129",
     * "additional_charges": [
     *  {
     *      "id": 1,
     *      "value": null,
     *      "is_selected": false
     *  },
     *  {
     *      "id": 2,
     *      "value": 2.99,
     *      "is_selected": true
     *  }
     * ]
     * }
     */
    function addServicesWithCartTotal($data)
    {
        try {
            $response = self::httpPost(
                '/orders/additional-charges',
                [
                    'token' => $this->accessToken,
                ],
                $data
            );
            return $response;
        } catch (Exception $e) {

        }
    }

    /**
     * This function will return all the added additional servicess with any order
     * @param $order_id
     * @return mixed|string
     */
    function getOrderAdditionalServices($order_id)
    {
        try {
            $response = self::httpGet(
                '/orders/view-charges/' . $order_id . '?type=order',
                [
                    'token' => $this->accessToken,
                ]
            );
            return $response;
        } catch (Exception $e) {

        }
    }

}