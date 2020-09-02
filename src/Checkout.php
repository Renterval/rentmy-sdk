<?php

namespace RentMy;

/**
 *Checkout Class
 **/
class Checkout extends RentMy
{

    private $accessToken;
    private $locationId;

    /**
     * Checkout constructor.
     * @param $accessToken
     * @param $location_id
     */

    public $shipping_type = ['fedex' => 4, 'ups' => 5, 'standard' => 6];

    function __construct($accessToken, $location_id)
    {
        $this->accessToken = $accessToken;
        $this->locationId = $location_id;
    }

    // capture data from first step of checkout
    function saveInfo($params)
    {
        self::setCheckoutSession('info', $params);
        return $params;

    }


    /**
     * get shipping methods
     * @param $data
     * @return mixed|string|null
     */

    // capture data from second step of checkout
    function saveFulfilment($params)
    {
        if ($params['type'] == 'instore') {
            $data['delivery'] = $params;
            $data['shipping_method'] = 1;
            self::setCheckoutSession('fulfillment', $data);
        } elseif ($params['type'] == 'delivery') {
            $data['shipping_method'] = 2;
            $data['delivery'] = $params;
            self::setCheckoutSession('fulfillment', $data);
        } elseif ($params['type'] == 'shipping') {
            $data = $params;
            $data['delivery'] = json_decode(stripslashes($params['shipping']), true);
            $data['delivery']['type'] = $params['type'];
            unset($data['shipping']);
            self::setCheckoutSession('fulfillment', $data);
        }

        return $params;

    }


    // get checkout custom fields
    function getCustomFields()
    {
        try {
            $response = self::httpGet(
                '/custom-fields',
                [
                    'token' => $this->accessToken,
                ]
            );
            return $response;
        } catch (Exception $e) {

        }
    }

    // get terms and conditions fields
    function termsAndCondition()
    {
        try {
            $response = self::httpGet(
                '/pages/terms-and-conditions',
                [
                    'token' => $this->accessToken,
                    'location' => $this->locationId
                ]
            );
            return $response;
        } catch (Exception $e) {

        }
    }

    // check free shipping for the cart token
    function checkFreeShipping()
    {
        try {
            $response = self::httpGet(
                '/free-shipping/' . $_SESSION['RentMy']['cart_token'],
                [
                    'token' => $this->accessToken,
                    'location' => $this->locationId,
                ]
            );
            return $response;
        } catch (Exception $e) {

        }
    }

    // upload files upon custom fields files field
    function uploadMedia($media)
    {
        try {
            $response = self::httpPost(
                '/media/upload',
                [
                    'token' => $this->accessToken,
                    'location' => $this->locationId
                ],
                $media
            );
            return $response;
        } catch (Exception $e) {

        }
    }

    // get currency configurations
    function getCurrencyConfig()
    {
        try {
            $response = self::httpGet(
                '/currency-config',
                [
                    'token' => $this->accessToken,
                ]
            );
            return $response;
        } catch (Exception $e) {

        }
    }


    // get checkout location lists
    function getLocationLists()
    {
        try {
            $response = self::httpGet(
                '/locations/list',
                [
                    'token' => $this->accessToken,
                ]
            );
            return $response;
        } catch (Exception $e) {

        }
    }


//    function getShippingList($data)
//    {
//        unset($data['loc']);
//        $response = self::httpPost(
//            '/shipping/rate',
//            [
//                'token' => $this->accessToken,
//            ],
//            [
//                'address' => $data,
//                'pickup' => $this->locationId,
//                'token' => $_SESSION['cart_token']
//            ]
//        );
//        if ($response['status'] == 'NOK') {
//            return $response;
//        }
//        if ($response['status'] == 'OK') {
//            if (!empty($response['result'])) {
//                $fulfillment = [];
//                $i = 0;
//                $res = '';
//                $html_head = '<h4 class="shipping-choose-label">Select Shipping Method</h4>';
//                foreach ($response['result'] as $key => $shippings) {
//
//                    if (strtolower($key) == 'standard') {
//                        $shipping_method = 6;
//                    } else {
//                        $shipping_method = 4;
//                    }
//
//                    foreach ($shippings as $shipping) {
//                        $html = '<label class="radio-container radiolist-container">';
//                        $json = json_encode($shipping);
//                        $html .= "<input type='radio' data-type='" . $shipping_method . "'   data-amount='" . $shipping['charge'] . "' data-tax='" . $shipping['tax'] . "' name='shipping_method' value='" . $json . "'><span class='rentmy-radio-text'>" . $shipping['service_name'] . "</span>";
//                        $html .= '<span class="rentmy-radio-date">Estimated Delivery Date: ' . date("F j, Y", strtotime($shipping['delivery_date'])) . '</span>';
//                        $html .= '<span class="rentmy-radio-day">  Delivery days: ' . $shipping['delivery_days'] . '</span>';
//                        $html .= '<span class="rentmy-radio-price">' . self::currency($shipping['charge']) . '</span>';
//                        $html .= '<span class="checkmark"></span></label>';
//
//                        $res .= $html;
//                        $fulfillment['data'][$i] = ['html' => $html, 'cost' => $shipping['charge']];
//                        $i++;
//                    }
//                }
//                $fulfillment['html'] = $html_head . $res;
//            }
//
//        } else {
//            $fulfillment = [];
//        }
//
//        return $fulfillment;
//    }

    /**
     * @param $data
     * @return mixed|string|null
     */
    function getDeliveryCost($data)
    {
        try {
            $response = self::httpPost(
                '/delivery-charge-list',
                [
                    'token' => $this->accessToken,
                ],
                [
                    'address' => $data,
                ]
            );

            return $response;
        } catch (Exception $e) {

        }
    }

    // get delivery addresses methods
    function addShippingToCarts($params)
    {
        try {
            if (!empty($_SESSION['RentMy']['cart_token'])) {
                $response = self::httpPost(
                    '/carts/delivery',
                    [
                        'token' => $this->accessToken,
                    ],
                    [
                        'shipping_cost' => $params['shipping_cost'],
                        'shipping_method' => $params['shipping_method'],
                        'tax' => $params['tax'],
                        'token' => $_SESSION['RentMy']['cart_token'],
                    ]
                );
                return $response;
            } else {
                return ['status' => 'NOK', 'message' => 'Invalid cart token'];
            }
        } catch (Exception $e) {

        }
    }

    // finally do the checkout process
    function doCheckout($data)
    {

        try {
            $cartToken = $_SESSION['RentMy']['cart_token'];
            if (empty($cartToken)) {
                return ['status' => 'NOK', 'message' => 'Invalid cart.'];
            }
            $checkout_info = [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'mobile' => $data['mobile'],
                'email' => $data['email'],
                'address_line1' => $data['address_line1'],
                'address2' => $data['address_line2'],
                'city' => $data['city'],
                'state' => $data['state'],
                'country' => $data['country'],
                'zipcode' => $data['zipcode'],
                'custom_values' => null,
                'special_instructions' => isset($data['special_instructions']) ? $data['special_instructions'] : '',
                'special_requests' => isset($data['special_requests']) ? $data['special_requests'] : '',
                'driving_license' => isset($data['driving_license']) ? $data['driving_license'] : '',
                'fieldSelection' => null,
                'fieldText' => null,
                'pickup' => '',
                'delivery' => $data['delivery'],
                'shipping_method' => $data['shipping_method'],
                'currency' => 'USD',
                'token' => $cartToken,
                'custom_values' => null,
                'signature' => null,
                'gateway_id' => $data['gateway_id'],
                'type' => $data['type'],
                'note' => $data['note'],
                'payment_gateway_name' => trim($data['payment_gateway_name']),
                'account' => isset($data['account']) ? $data['account'] : '',
            ];
            if (!empty($info['signature'])) {
                $checkout_info['signature'] = trim($data['signature']);
            }
            if ($data['shipping_method'] != 1) {
                $checkout_info['shipping_address1'] = $data['shipping_address1'];
                $checkout_info['shipping_address2'] = $data['shipping_address2'];
                $checkout_info['shipping_city'] = $data['shipping_city'];
                $checkout_info['shipping_country'] = $data['shipping_country'];
                $checkout_info['shipping_email'] = $data['email'];
                $checkout_info['shipping_first_name'] = $data['first_name'];
                $checkout_info['shipping_last_name'] = $data['last_name'];
                $checkout_info['shipping_mobile'] = $data['mobile'];
                $checkout_info['shipping_state'] = $data['shipping_state'];
                $checkout_info['shipping_zipcode'] = $data['shipping_zipcode'];
            }
            if ($checkout_info['payment_gateway_name'] != 'Stripe' && $checkout_info['type'] == 1) {
                $checkout_info["expiry"] = $data['exp_month'] . $data['exp_year'];
                $checkout_info['cvv2'] = $payment['cvv'];
            }

            if (!empty($data['custom_values'])) {
                $checkout_info['custom_values'] = $data['custom_values'];
            }


            // added for partial payments
            if (!empty($payment['payment_amount'])) {
                $checkout_info['payment_amount'] = $data['payment_amount'];
                $checkout_info['amount_tendered'] = 0;
            }
            // partial payment ends

            $response = self::httpPost(
                '/orders/online',
                [
                    'token' => $this->accessToken,
                    'Location' => $this->locationId
                ],
                $checkout_info
            );
            if (!$response['result']['data']['payment']['success']) {
                if (empty($response['result']['data']['payment']['message'])) {
                    $message = "Payment not completed successfully . Order can't be created. Please try again.";
                } else {
                    $message = $response['result']['data']['payment']['message'];
                }
                return ['status' => 'NOK', 'message' => $message];
            } else if (!$response['result']['data']['availability']['success']) {
                return ['status' => 'NOK', 'message' => "Order can't be created . Some products may not available . Please try again . "];
            }

            $_SESSION['RentMy']['order_uid'] = $response['result']['data']['order']['data']['uid'];
            // delete session && cookie
            unset($_SESSION['RentMy']['cart_token']);
            unset($_SESSION['RentMy']['rent_start']);
            unset($_SESSION['RentMy']['rent_end']);
            return ['status' => 'OK', 'uid' => $_SESSION['order_uid']];
        } catch (Exception $e) {

        }
    }

    /**
     * @param $type - info for billing details , fulfillment for shipping details
     * @param $data
     */
    function setCheckoutSession($type, $data)
    {
        $_SESSION['checkout'][$type] = $data;
    }

    /**
     * @param string $type type = '' return full checkout details, info for billing, fulfillment for shipping/delivery
     * @return mixed
     */
    function getCheckoutSession($type = '')
    {
        if (empty($type)) {
            return $_SESSION['checkout'];
        } else {
            return $_SESSION['checkout'][$type];
        }
    }

    /**
     * get shipping methods
     * @param $data
     * @return mixed|string|null
     */
    function getShippingList($data)
    {
        unset($data['loc']);
        $response = self::httpPost(
            '/shipping/rate',
            [
                'token' => $this->accessToken,
            ],
            [
                'address' => $data,
                'pickup' => $this->locationId,
                'token' => $_SESSION['RentMy']['cart_token']
            ]
        );
        if ($response['status'] == 'NOK') {
            return $response;
        }
        if ($response['status'] == 'OK') {
            if (!empty($response['result'])) {
                $fulfillment = [];
                $i = 0;
                $res = '';
                $html_head = '<h4 class="shipping-choose-label">Select Shipping Method</h4>';
                foreach ($response['result'] as $key => $shippings) {
                    if (strtolower($key) == 'standard') {
                        $shipping_method = 6;
                    } else if (strtolower($key) == 'flat') {
                        $shipping_method = 7;
                    } else {
                        $shipping_method = 4;
                    }

                    if (strtolower($key) == 'standard') {
                        $html = '<label class="radio-container radiolist-container">';
                        $json = json_encode($shippings);
                        $html .= "<input type='radio' data-type='" . $shipping_method . "'   data-amount='" . $shippings['charge'] . "'data-tax='0' name='selected_shipping_data' value='" . $json . "'/>&nbsp;<span class='rm-shipping-text'>" . $shippings['carrier_code'] . "</span>";
                        $html .= '<span class="rentmy-radio-price pull-right">' . self::currency($shippings['charge']) . '</span>';
                        $html .= '<span class="checkmark"></span></label>';
                        $res .= $html;
                    } elseif (strtolower($key) == 'flat') {
                        $html = '<label class="radio-container radiolist-container">';
                        $json = json_encode($shippings);
                        $html .= "<input type='radio' data-type='" . $shipping_method . "'   data-amount='" . $shippings['charge'] . "'data-tax='0' name='selected_shipping_data' value='" . $json . "'/>&nbsp;<span class='rentmy-radio-text'>" . $shippings['carrier_code'] . "</span>";
                        $html .= '<span class="rentmy-radio-price pull-right">' . self::currency($shippings['charge']) . '</span>';
                        $html .= '<span class="checkmark"></span></label></div>';
                        $res .= $html;
                    } else {
                        foreach ($shippings as $shipping) {
                            $html = '<label class="radio-container radiolist-container">';
                            $json = json_encode($shipping);
                            $html .= "<input type='radio' data-type='" . $shipping_method . "'   data-amount='" . $shipping['charge'] . "' data-tax='" . $shipping['tax'] . "' name='selected_shipping_data' value='" . $json . "'/>&nbsp;<span class='rentmy-radio-text'>" . $shipping['service_name'] . "</span>";
                            $html .= '<span class="rentmy-radio-date">Estimated Delivery Date: ' . date("F j, Y", strtotime($shipping['delivery_date'])) . '</span>';
                            $html .= '<span class="rentmy-radio-day">  Delivery days: ' . $shipping['delivery_days'] . '</span>';
                            $html .= '<span class="rentmy-radio-price">' . self::currency($shipping['charge']) . '</span>';
                            $html .= '<span class="checkmark"></span></label>';
                            $res .= $html;
                            $fulfillment['data'][$i] = ['html' => $html, 'cost' => $shipping['charge']];
                            $i++;
                        }
                    }
                }
                $fulfillment['html'] = $html_head . $res;
            }
        } else {
            $fulfillment = [];
        }
        return $fulfillment;
    }
}