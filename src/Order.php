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
    function getAdditionalServices($cartToken = '')
    {
        try {
            $response = self::httpGet(
                '/settings/orders/additional-charges?type=active&cart_token=' . $cartToken,
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
     *
     *   "cart_token": "1606201026285",
     *   "additional_charges": [
     *      {
     *          "id": 11,
     *          "value": null,
     *          "is_selected": false,
     *          "order_additional_charge_id": null,
     *          "selected_option": null
     *      },
     *      {
     *          "id": 16,
     *          "value": 2,
     *          "is_selected": true,
     *          "order_additional_charge_id": 266,
     *          "selected_option": "Optional value"
     *      },
     *      {
     *           "id": 12,
     *          "value": 35,
     *          "is_selected": true,
     *          "order_additional_charge_id": null,
     *          "selected_option": null
     *      }
     *      ]
     * }
     */
    function addServicesWithCartTotal($data)
    {
        try {
            $response = self::httpPost(
                '/orders/additional-charges/create',
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
     * This function will return all the added additional servicess with any order or cart
     * when type = 'cart' then type_id should be cart id  - this will be used before creating order for listing added services in cart
     * where type = 'order' then type_id should be order id - this will be used for showing added services for any order after creating order
     * @param $order_id
     * @return mixed|string
     */
    function getOrderAdditionalServices($type, $type_id)
    {
        try {
            if ($type == 'cart') {
                $response = self::httpGet(
                    '/cart/view-charges/' . $type_id,
                    [
                        'token' => $this->accessToken,
                    ]
                );
            } else {
                $response = self::httpGet(
                    '/orders/view-charges/' . $type_id . '?type=' . $type,
                    [
                        'token' => $this->accessToken,
                    ]
                );
            }

            return $response;
        } catch (Exception $e) {

        }
    }

}