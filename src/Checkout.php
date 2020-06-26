<?php
namespace RentMy;

/**
*Checkout Class
**/

class Checkout extends RentMy{

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
            $data['shipping_method'] = 1;
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
                '/free-shipping/' . $_SESSION['cart_token'],
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
                'token' => $_SESSION['cart_token']
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
                    } else {
                        $shipping_method = 4;
                    }

                    foreach ($shippings as $shipping) {
                        $html = '<label class="radio-container radiolist-container">';
                        $json = json_encode($shipping);
                        $html .= "<input type='radio' data-type='" . $shipping_method . "'   data-amount='" . $shipping['charge'] . "' data-tax='" . $shipping['tax'] . "' name='shipping_method' value='" . $json . "'><span class='rentmy-radio-text'>" . $shipping['service_name'] . "</span>";
                        $html .= '<span class="rentmy-radio-date">Estimated Delivery Date: ' . date("F j, Y", strtotime($shipping['delivery_date'])) . '</span>';
                        $html .= '<span class="rentmy-radio-day">  Delivery days: ' . $shipping['delivery_days'] . '</span>';
                        $html .= '<span class="rentmy-radio-price">' . self::currency($shipping['charge']) . '</span>';
                        $html .= '<span class="checkmark"></span></label>';

                        $res .= $html;
                        $fulfillment['data'][$i] = ['html' => $html, 'cost' => $shipping['charge']];
                        $i++;
                    }
                }
                $fulfillment['html'] = $html_head . $res;
            }

        } else {
            $fulfillment = [];
        }

        return $fulfillment;
    }

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
            if (!empty($_SESSION['cart_token'])) {
                $response = self::httpPost(
                    '/carts/delivery',
                    [
                        'token' => $this->accessToken,
                    ],
                    [
                        'shipping_cost' => $params['shipping_cost'],
                        'shipping_method' => $params['shipping_method'],
                        'tax' => $params['tax'],
                        'token' => $_SESSION['cart_token'],
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
            $info = $_SESSION['checkout']['info'];
            $fulfillment = $_SESSION['checkout']['fulfillment'];
            $payment = $data;
            $cartToken = $_SESSION['cart_token'];
            if (empty($cartToken)) {
                return ['status' => 'NOK', 'message' => 'Invalid cart.'];
            }
            $checkout_info = [
                'first_name' => $info['first_name'],
                'last_name' => $info['last_name'],
                'mobile' => $info['mobile'],
                'email' => $info['email'],
                'address_line1' => $info['address_line1'],
                'address2' => $info['address_line2'],
                'city' => $info['city'],
                'state' => $info['state'],
                'combinedAddress' => "",
                'country' => 'us',
                'zipcode' => $info['zipcode'],
                'custom_values' => null,
                'special_instructions' => $info['special_instructions'],
                'special_requests' => $info['special_requests'],
                'driving_license' => $info['driving_license'],
                'fieldSelection' => null,
                'fieldText' => null,
                'pickup' => 130,
                'delivery' => $fulfillment['delivery'],
                'shipping_method' => $fulfillment['shipping_method'],
                'currency' => 'USD',
                'token' => $cartToken,
                'custom_values' => null,
                'signature' => null,
                'gateway_id' => $payment['payment_gateway_id'],
                'type' => $payment['payment_gateway_type'],
                'note' => $payment['note'],
                'payment_gateway_name' => trim($payment['payment_gateway_name']),
                'account' => $payment['card_no'],
            ];
            if (!empty($info['signature'])) {
                $checkout_info['signature'] = trim($info['signature']);
            }
            if ($fulfillment['delivery']['type'] == 'shipping') {
                $checkout_info['shipping_address1'] = $fulfillment['shipping_address1'];
                $checkout_info['shipping_address2'] = $fulfillment['shipping_address2'];
                $checkout_info['shipping_city'] = $fulfillment['shipping_city'];
                $checkout_info['shipping_country'] = $fulfillment['shipping_country'];
                $checkout_info['shipping_email'] = $info['email'];
                $checkout_info['shipping_first_name'] = $info['first_name'];
                $checkout_info['shipping_last_name'] = $info['last_name'];
                $checkout_info['shipping_mobile'] = $info['mobile'];
                $checkout_info['shipping_state'] = $fulfillment['shipping_state'];
                $checkout_info['shipping_zipcode'] = $fulfillment['shipping_zipcode'];
            }
            if ($checkout_info['payment_gateway_name'] != 'Stripe' && $checkout_info['type'] == 1) {
                $checkout_info["expiry"] = $payment['exp_month'] . $payment['exp_year'];
                $checkout_info['cvv2'] = $payment['cvv'];
            }

            if (!empty($data['custom_values'])) {
                $checkout_info['custom_values'] = $data['custom_values'];
            }


            // added for partial payments
            if (!empty($payment['payment_amount'])) {
                $checkout_info['payment_amount'] = $payment['payment_amount'];
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

            $_SESSION['order_uid'] = $response['result']['data']['order']['data']['uid'];
            // delete session && cookie
            unset($_SESSION['cart_token']);
            unset($_SESSION['checkout']);
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
}