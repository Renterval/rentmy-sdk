<?php
namespace RentMy;

class Order extends RentMy{

    private $accessToken;
    private $locationId;

    /**
     * Order constructor.
     * @param $accessToken
     * @param $location_id
     */

    function __construct($accessToken, $location_id) {
       $this->accessToken = $accessToken;
       $this->locationId = $location_id;
    }

    function viewOrderDetails($order_id) {
        try {
            $response = self::httpGet(
                '/orders/'.$order_id.'/complete',
                [
                    'token' => $this->accessToken,
                    'location' => $this->locationId
                ]
            );
            return $response;
        } catch (Exception $e) {

        }
    }

}