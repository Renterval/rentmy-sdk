<?php

namespace RentMy;

class Products extends RentMy
{

    public $accessToken;
    public $locationId;

    /**
     * Products constructor.
     * @param $accessToken
     * @param $location_id
     */
    public function __construct($accessToken, $location_id)
    {
        $this->accessToken = $accessToken;
        $this->locationId = $location_id;
        parent::__construct();
    }

    /**
     * @param $params
     * @return mixed
     */
    function productList($params)
    {
        try {
            $response = self::httpPost(
                '/products/online',
                [
                    'token' => $this->accessToken,
                    'location' => $this->locationId
                ],
                $params
            );
            return $response;
        } catch (Exception $e) {

        }

    }
    /**
     * @param $params
     * @return mixed
     */
    function productsByPrice($params)
    {
        try {
//            $get_fields_string='';
//            foreach ($params as $key => $value) {
//                $get_fields_string .= $key . '=' . $value . '&';
//            }
//
//            $get_fields_string = '?' . $get_fields_string;
            $response = self::httpGet(
                '/products/list',
                [
                    'token' => $this->accessToken,
                ],
                $params
            );
            return $response;
        } catch (Exception $e) {

        }

    }
    /**
     * @param $params
     * @return mixed|string|null
     */
    function productListByCategory($params)
    {
        try {
            $response = self::httpPost(
                '/category/products/' . $params['category_id'],
                [
                    'token' => $this->accessToken,
                    'location' => $this->locationId,
                ],
               $params
            );
            return $response;
        } catch (Exception $e) {

        }

    }
    /**
     * @param $params
     * @return mixed|string|null
     */
    function productSearch($params)
    {
        try {
            $response = self::httpPost(
                '/search/products/',
                [
                    'token' => $this->accessToken,
                    'location' => $this->locationId,
                ],
               $params
            );
            return $response;
        } catch (Exception $e) {

        }

    }

    /**
     * @param $product_id
     * @return mixed|string|null
     */
    function details($product_id, $cart_params = null)
    {
        try {

            if (!empty($_SESSION['RentMy']['rent_start']) && !empty($_SESSION['RentMy']['rent_end'])) {
                $cart_token= !empty($_SESSION['RentMy']['cart_token']) ?$_SESSION['RentMy']['cart_token'] : '';
                $start_date =!empty($_SESSION['RentMy']['rent_start']) ?  urlencode($_SESSION['RentMy']['rent_start']) : '';
                $end_date= !empty($_SESSION['RentMy']['rent_end']) ? urlencode($_SESSION['RentMy']['rent_end']) : '';
                $add_params = '&token=' . $cart_token . '&start_date=' . $start_date . '&end_date=' . $end_date;
            } else {
                $add_params = '';
            }
            $location_id = $this->locationId;
            $response = self::httpGet(
                '/products/' . $product_id . '?location=' . $location_id . $add_params,
                [
                    'token' => $this->accessToken,
                ],
                null
            );
            return $response;
        } catch (Exception $e) {

        }

    }

    /**
     * @param $package_id
     * @return mixed|string|null
     */
    function package_details($product_id, $cart_params = null)
    {
        try {
            if (!empty($_SESSION['RentMy']['rent_start']) && !empty($_SESSION['RentMy']['rent_end'])) {
                $cart_token= !empty($_SESSION['RentMy']['cart_token']) ?$_SESSION['RentMy']['cart_token'] : '';
                $start_date =!empty($_SESSION['RentMy']['rent_start']) ?  urlencode($_SESSION['RentMy']['rent_start']) : '';
                $end_date= !empty($_SESSION['RentMy']['rent_end']) ? urlencode($_SESSION['RentMy']['rent_end']) : '';
                $add_params = '&token=' . $cart_token . '&start_date=' . $start_date . '&end_date=' . $end_date;
                //$add_params = '&token=' . $cart_params['token'] . '&start_date=' . $cart_params['start_date'] . '&end_date=' . $cart_params['end_date'];
            } else {
                $add_params = '';
            }

            $location_id = $this->locationId;
            $response = self::httpGet(
                '/package-details/' . $product_id . '/360?location=' . $location_id . $add_params,
                [
                    'token' => $this->accessToken,
                ],
                null

            );
            // print_r("<pre>");print_r($response);print_r("</pre>");
            return $response['result'];
        } catch (Exception $e) {

        }

    }

    /** Check package availability
     * @param $data
     * @return mixed
     */
    function check_package_availability($data)
    {
        try {

            $location_id = $this->locationId;
            $params = [];
            foreach ($data['products'] as $p) {
                $params['variants[]'] = $p['variants_products_id'];
            }

            $response = self::httpPost(
                '/package/' . $data['product_uid'] . '/availability',
                [
                    'token' => $this->accessToken,
                ],
                $params
            );
            return $response;
        } catch (Exception $e) {

        }

    }
    /** Check package availability
     * for cart - type = cart & token = cart token
     * @param $data
     * @return mixed
     */
    function getAvailability($params)
    {
        try {
            $response = self::httpGet(
                '/products/availability'  ,
                [
                    'token' => $this->accessToken,
                ],
                $params

            );
            return $response;
        } catch (Exception $e) {

        }

    }

    /**
     * @param $data ['product_id']
     * @param $data ['variant_id']
     * @param $data ['chain']
     *
     */
    function get_product_variant_chain($data)
    {
        try {
            $response = self::httpGet(
                '/variant-chain?product_id=' . $data['product_id'] . '&variant_id=' . $data['variant_id'] . '&variant_chain=',
                [
                    'token' => $this->accessToken,
                ],
                null
            );
            return !empty($response['result']['data']) ? $response['result']['data'] : [];
        } catch (Exception $e) {

        }
    }

    function get_product_fromchain($data)
    {
        try {
            $response = self::httpGet(
                '/get-path-of-chain?product_id=' . $data['product_id'] . '&variant_id=' . $data['variant_id'] . '&variant_chain=' . $data['chain_id'],
                [
                    'token' => $this->accessToken,
                    'location' => $this->locationId,
                ],
                null
            );
            return !empty($response['result']['data']) ? $response['result']['data'] : [];
        } catch (Exception $e) {

        }
    }

    /** Check package and item available with price returned
     * @param $data
     * @return mixed
     */
    function get_price_value($data)
    {
        try {

            $location_id = $this->locationId;
            $params = $data;
            $params['location'] = $location_id;
            $response = self::httpPost(
                '/get-price-value',
                [
                    'token' => $this->accessToken,
                    'location' => $location_id,
                ],
                $params
            );
            return $response;
        } catch (Exception $e) {

        }

    }

    /** Check package add ons and return add on products
     * @param $data
     * @return mixed
     */
    function get_addon_products($product_id)
    {
        try {
            $location_id = $this->locationId;
            $response = self::httpGet(
                '/products/' . $product_id . '/addons?required=true&location=' . $location_id,
                [
                    'token' => $this->accessToken,
                    'location' => $location_id
                ]
            );
            return $response['result'];
        } catch (Exception $e) {

        }

    }

    /** get related products by product id ...
     * @param $data
     * @return mixed
     */
    function get_related_products($product_id)
    {
        try {
            $location_id = $this->locationId;
            $response = self::httpPost(
                '/products/' . $product_id . '/user/related-products',
                [
                    'token' => $this->accessToken,
                    'location' => $location_id
                ]
            );
            return $response['result'];
        } catch (Exception $e) {

        }

    }

    /**
     * Get list of featured items
     * @return mixed
     */
    function get_featured_products()
    {
        try {
            $location_id = $this->locationId;
            $response = self::httpGet(
                '/products/featured',
                [
                    'token' => $this->accessToken,
                    'location' => $location_id
                ]
            );
            return $response['result'];
        } catch (Exception $e) {

        }
    }

    /**
     * Get exact duration of a selected date
     * @return mixed
     */
    function getExactDuration($start_date)
    {
        try {
            $location_id = $this->locationId;
            $response = self::httpPost(
                '/product/get_exact_duration',
                [
                    'token' => $this->accessToken,
                    'location' => $location_id
                ],
                [
                    'start_date' => $start_date
                ]
            );
            return $response['result']['data'];
        } catch (Exception $e) {

        }
    }

    /**
     * Get duration from a given date and time
     * @return mixed
     */
    function getDatesFromDuration($data)
    {
        try {
            $location_id = $this->locationId;
            $data['location_id'] = $location_id;
            $response = self::httpPost(
                '/product/get_dates_from_duration',
                [
                    'token' => $this->accessToken,
                    'location' => $location_id
                ],
                $data
            );
            return $response['result']['data'];
        } catch (Exception $e) {

        }
    }

    /**
     * Get duration from a start_date date and price id
     * @return mixed
     */
    function getDatesPriceDuration($data)
    {
        try {
            $location_id = $this->locationId;
            $add_params = '?start_date=' . urlencode($data['start_date']) . '&price_id=' . $data['price_id'] . '&location=' . $location_id;
            $response = self::httpGet(
                '/product/get_dates_price_duration' . $add_params,
                [
                    'token' => $this->accessToken,
                    'location' => $location_id
                ]
            );
            return $response['result']['data'];
        } catch (Exception $e) {

        }
    }

    /**
     * @param $data
     * @return mixed|string
     */
    function get_pacakge_info_by_variant($data){
        $param_add = '';
        $param_add = '?start_date='. $data['start_date'] . '&end_date='.$data['end_date'] . '&location='. $this->locationId;
        foreach($data['variants'] as $variant){
            $param_add .= '&variants[]='. $variant;
        }
        $response = self::httpGet(
            '/package/'.$data['pacakge_uid'].'/term/360'.$param_add,
            [
                'token' => $this->accessToken
            ],
            null
        );
        return $response;
    }
    /** Check package and item available with price returned
     * @param $data
     * @return mixed
     */
    function get_package_value($data)
    {
        try {

            $params = $data;
            $params['location'] = $this->locationId;
            $response = self::httpPost(
                '/get-package-price',
                [
                    'token' => $this->accessToken,
                    'location' => $this->locationId
                ],
                $params
            );
            return $response;
        } catch (Exception $e) {

        }

    }
    /** Check package add ons and return add on products
     * @param $data
     * @return mixed
     */
    function custom_builder_addons($product_id, $custom_builder)
    {
        try {
            $location_id = $this->locationId;
            $response = self::httpGet(
                '/products/' . $product_id . '/addons?required=true&location=' . $location_id .'&custom_builder='.$custom_builder,
                [
                    'token' => $this->accessToken,
                    'location' => $location_id
                ]
            );
            return $response['result'];
        } catch (Exception $e) {
        }
    }
    /** return available time of a date
     * @param $data
     * @return mixed
     */
    function getAvailableTimeOfDay($data)
    {
        try {

            $params = $data;
            $params['location'] = $this->locationId;
            $response = self::httpPost(
                '/products/available-time',
                [
                    'token' => $this->accessToken,
                    'location' => $this->locationId
                ],
                $params
            );
            return $response;
        } catch (Exception $e) {

        }

    }
}

?>
