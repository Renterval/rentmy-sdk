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
                [
                    'page_no' => $params['page_no'],
                    'limit' => $params['limit'],
                    'tag_id' => $params['tag_id'],
                    'price_max' => !empty($params['price_max'])?$params['price_max']:'',
                    'price_min' => !empty($params['price_min'])?$params['price_min']:'',
                    'purchase_type' => !empty($params['purchase_type'])?$params['purchase_type']:'',
                    'all' => true,
                    'sort' => !empty($params['sort']) ? $params['sort'] : '',
                    'sort_type' => !empty($params['sort_type']) ? $params['sort_type'] : ''
                ]
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
                [
                    'page_no' => $params['page_no'],
                    'limit' => $params['limit'],
                    'tag_id' => $params['tag_id'],
                    'price_max' => $params['price_max'],
                    'price_min' => $params['price_min'],
                    'purchase_type' => $params['purchase_type'],
                    'all' => true,
                    'sort' => !empty($params['sort']) ? $params['sort'] : '',
                    'sort_type' => !empty($params['sort_type']) ? $params['sort_type'] : ''
                ]
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
                [
                    'page_no' => $params['page_no'],
                    'limit' => $params['limit'],
                    'search' => $params['search'],
                    'category_id' => '',
                    'sort' => !empty($params['sort']) ? $params['sort'] : '',
                    'sort_type' => !empty($params['sort_type']) ? $params['sort_type'] : ''
                ]
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

            if (!empty($cart_params['token']) && !empty($cart_params['start_date']) && !empty($cart_params['end_date'])) {
                $add_params = '&token=' . $cart_params['token'] . '&start_date=' . urlencode($cart_params['start_date']) . '&end_date=' . urlencode($cart_params['end_date']);
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

            if (!empty($cart_params['token']) && !empty($cart_params['start_date']) && !empty($cart_params['end_date'])) {
                $add_params = '&token=' . $cart_params['token'] . '&start_date=' . $cart_params['start_date'] . '&end_date=' . $cart_params['end_date'];
            } else {
                $add_params = '';
            }

            $location_id = $this->locationId;
            $response = self::httpGet(
                '/package-details/' . $product_id . '/360?location=' . $location_id . $add_params,
                [
                    'token' => $this->accessToken,
                ],
                null,

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
            return !empty($response['data']) ? $response['data'] : [];
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
            return !empty($response['data']) ? $response['data'] : [];
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
                $params,
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


}

?>