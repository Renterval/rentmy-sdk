<?php
namespace RentMy;
/**
 * Class RentMy_Category
 */
Class Category extends RentMy
{

    private $accessToken;
    private $locationId;

    function __construct($accessToken, $location_id)
    {
        $this->accessToken = $accessToken;
        $this->locationId = $location_id;
    }
    /**
     * Get categories list
     * @return mixed|string|null
     */
    function categories()
    {
        try {
            $response = self::httpGet(
                '/categories',
                [
                    'token' => $this->accessToken,
                    'location' => $this->locationId
                ]
            );
            return !empty($response['data']) ? $response['data'] : null;
        } catch (Exception $e) {

        }

    }

    /**
     * Get category details
     * @param $id
     * @return |null
     */
    function getCategoryDetails($id){
        try {
            $response = self::httpGet(
                '/categories/' .$id,
                [
                    'token' => $this->accessToken,
                ]
            );
            return !empty($response['data']) ? $response['data'] : null;
        } catch (Exception $e) {

        }
    }

    /**
     * Get children category list
     * @return mixed|string|null
     */
    function children($parent_category_uid)
    {
        try {
            $response = self::httpGet(
                '/get/child-categories/' . $parent_category_uid,
                [
                    'token' => $this->accessToken,
                    'location' => $this->locationId
                ]
            );
            return !empty($response['data']) ? $response['data'] : null;
        } catch (Exception $e) {

        }

    }

    /**
     * Get children tags list
     * @return mixed|string|null
     */
    function tags()
    {
        try {
            $response = self::httpGet(
                '/tags',
                [
                    'token' => $this->accessToken,
                    'location' => $this->locationId
                ]
            );
            return $response['data'];
        } catch (Exception $e) {

        }

    }
}
