<?php
namespace RentMy;
/**
 * Class Cart
 */
Class Cart extends RentMy
{
    private $locationId;
    private $cartToken;
    private $accessToken;
    function __construct($accessToken, $locationId){
        $this->locationId = $locationId;
        $this->accessToken = $accessToken;
        $this->cartToken = !empty($_SESSION['RentMy']['cart_token'])? $_SESSION['RentMy']['cart_token']:'';
    }
    /**
     * submit cart using these method
     * @return mixed|string|null
     */
    function addToCart($params)
    {
        try {
            $params['location'] = $this->locationId;
            $params['token'] = $this->cartToken;
            $response = self::httpPost(
                '/carts/add-to-cart',
                $this->accessToken,
                $params
            );
            
            if ($response['status'] == 'OK') {
                if (!empty($response['result']['data']['token'])) {
                    self::setCartToken($response['result']['data']['token']);
                    self::setRentStart($response['result']['data']['rent_start']);
                    self::setRentEnd($response['result']['data']['rent_end']);
                    return $response;
                }else{
                    return ['status'=> 'NOK','result'=> $response['result']];
                }
            }

            return $response;
        } catch (Exception $e) {

        }
    }

    /**
     * Rental add to cart when date is missing
     * @param $params
     * @return array|mixed|string
     */
    function addToCartWithoutDate($params)
    {
        try {
            $params['location'] = $this->locationId;
            $params['token'] = $this->cartToken;
            $response = self::httpPost(
                '/v2/carts/add',
                $this->accessToken,
                $params
            );

            if ($response['status'] == 'OK') {
                if (!empty($response['result']['data']['token'])) {
                    self::setCartToken($response['result']['data']['token']);
                    return $response;
                }else{
                    return ['status'=> 'NOK','result'=> $response['result']];
                }
            }

            return $response;
        } catch (Exception $e) {

        }
    }
    /**
     * Package add to cart .
     * @return mixed|string|null
     */
    function addPackageToCart($params)
    {
        try {
            $params['location'] = $this->locationId;
            $params['token'] = $this->cartToken;
            $response = self::httpPost(
                '/package/add-to-cart',
                [
                    'token'=>$this->accessToken,
                    'location' => $location_id
                ],
                $params
            );
            
            if ($response['status'] == 'OK') {
                if (!empty($response['result']['data']['token'])) {
                    self::setCartToken($response['result']['data']['token']);
                    self::setRentStart($response['result']['data']['rent_start']);
                    self::setRentEnd($response['result']['data']['rent_end']);
                    return $response;
                }else{
                    return ['status'=> 'NOK','result'=> $response['result']];
                }
            }

            return $response;
        } catch (Exception $e) {

        }
    }

    /**
     * view cart using these method
     * @return mixed|string|null
     */
    function viewCart()
    {
        try {
            $response = self::httpGet(
                '/carts/' . $this->cartToken,
                $this->accessToken,
                null
            );
            return $response;
        } catch (Exception $e) {

        }
    }

    /** get related products by cart token ...
     * @param $data
     * @return mixed
     */
    function get_related_products_cart($token)
    {
        try {
            $location_id = $this->locationId;
            $response = self::httpPost(
                '/products/'.$token.'/user/related-products?source=cart',
                [
                    'token' => $this->accessToken,
                    'location' => $location_id
                ]
            );
            return $response;
        } catch (Exception $e) {

        }

    }

    /**
     * update cart using these method
     * @return mixed|string|null
     */
    function updateCart($params)
    {
        try {
            $response = self::httpPost(
                '/carts/update',
                [
                    'token' => $this->accessToken,
                    'location' => $this->locationId
                ],
                [
                    'id' => $params['id'],
                    'increment' => $params['increment'],
                    'token' => $_SESSION['RentMy']['cart_token'],
                    'price' => $params['price'],
                ]
            );
            return $response;
        } catch (Exception $e) {

        }
    }


    /**
     * get cart available products
     * @return mixed|string|null
     */
    function getCartAvailability($params)
    {
        try {
            $response = self::httpPost(
                '/products/availability',
                [
                    'token' => $this->accessToken,
                    'location' => $this->locationId
                ],
                [
                    'start_date' => $params['start_date'],
                    'end_date' => $params['end_date'],
                    'token' => $this->cartToken,
                    'type' => $params['type'],
                    'source' => $params['source'],
                ]
            );
            return $response;
        } catch (Exception $e) {

        }
    }

    /**
     * delete cart using these method
     * @return mixed|string|null
     */
    function deleteCart($params)
    {
        try {
            $response = self::httpPost(
                '/carts/cart-remove-item',
                [
                    'token' => $this->accessToken,
                    'location' => $this->locationId
                ],
                [
                    'cart_item_id' => $params['cart_item_id'],
                    'product_id' => $params['product_id'],
                    'token' => $this->cartToken
                ]
            );
            if(empty($response['result']['data']['cart_items'])){
                unset($_SESSION['RentMy']['rent_start']);
                unset($_SESSION['RentMy']['rent_end']);
            }
            return $response;
        } catch (Exception $e) {

        }
    }

    /**
     * apply coupon to cart using these method
     * @return mixed|string|null
     */
    function applyCoupon($params)
    {
        try {
            $response = self::httpPost(
                '/carts/apply-coupon',
                [
                    'token' => $this->accessToken,
                    'location' => $this->locationId
                ],
                [
                    'coupon' => $params['coupon'],
                    'token' => $this->cartToken
                ]
            );
            return $response;
        } catch (Exception $e) {

        }
    }
    /**
     * Get cart Token from session
     */
    function getCartToken()
    {
        return $_SESSION['RentMy']['cart_token'];
    }

    /** Set cart token to Session */
    function setCartToken($token)
    {
        $_SESSION['RentMy']['cart_token'] = $token;
    }

    /** Save cart details into session */
    function setCartSession($data)
    {
        $_SESSION['RentMy']['cart'] = $data;
    }

    /** Get Cart details from session */
    function getCartSession()
    {
        return $_SESSION['RentMy']['cart'];
    }

    // set rent start date
    function setRentStart($date){
        $_SESSION['RentMy']['rent_start'] = $date;
    }

    // set rent end date
    function setRentEnd($date){
        $_SESSION['RentMy']['rent_end'] = $date;
    }

    // get rent start date
    function getRentStart(){
        return $_SESSION['RentMy']['rent_start'];
    }

    // get rent end date
    function getRentEnd(){
        return $_SESSION['RentMy']['rent_end'];
    }

}
