<?php

namespace RentMy;

class RentMy
{
    public static $apiUrl = 'http://client-api-stage.rentmy.leaperdev.rocks/api';

    public static $S3URL = 'https://s3.us-east-2.amazonaws.com/pimg.rentmy.co/products/';

    public function __construct()
    {
    }

    public static function httpGet($slashedPath = null, $token = null, $queryParams = [])
    {
        // Create a new cURL resource
        $curl = curl_init();
        $get_fields_string = null;
        $html = null;
        $error = null;

        if (!$curl) {
            $error = "Couldn't initialize a cURL handle";
            return $error;
        }

        if (!$slashedPath) {
            $error = "API PATH is not specified properly";
            return $error;
        }

        //url-ify the data for the GET
        if (!empty($queryParams)) {
            foreach ($queryParams as $key => $value) {
                $get_fields_string .= $key . '=' . $value . '&';
            }
            rtrim($get_fields_string, '&');
            $get_fields_string = '?' . $get_fields_string;
        }


        if (!empty($token)) {
            $headers_array = [
                'Accept: application/json, text/plain, */*',
                'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.90 Safari/537.36'
            ];
            if (is_array($token)) {
                if (!empty($token['token'])) {
                    $authorization = "Authorization: Bearer " . $token['token'];
                    array_push($headers_array, $authorization);
                }
                if (!empty($token['location'])) {
                    $location = "Location: " . $token['location'];
                    array_push($headers_array, $location);
                }
            } else {
                $authorization = "Authorization: Bearer " . $token;
                array_push($headers_array, $authorization);
            }
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers_array);
        }

        $api_url = self::$apiUrl . $slashedPath . $get_fields_string;

        // Set the file URL to fetch through cURL
        curl_setopt($curl, CURLOPT_URL, $api_url);
        // Follow redirects, if any
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        // Fail the cURL request if response code = 400 (like 404 errors)
        curl_setopt($curl, CURLOPT_FAILONERROR, true);
        // Return the actual result of the curl result instead of success code
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        // Wait for 10 seconds to connect, set 0 to wait indefinitely
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        // Execute the cURL request for a maximum of 50 seconds
        curl_setopt($curl, CURLOPT_TIMEOUT, 50);
        // Do not check the SSL certificates
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        // Fetch the URL and save the content in $html variable
        $html = curl_exec($curl);
        // Check if any error has occurred
        if (curl_errno($curl)) {
            $error = 'cURL error: ' . curl_error($curl);
        } else {
            // cURL executed successfully
            $error = curl_getinfo($curl);
        }
        // close cURL resource to free up system resources
        curl_close($curl);
        return !empty($html) ? json_decode($html, true) : $error;

    }

    public static function httpPost($slashedPath = null, $token = null, $postFields = [])
    {
        // Create a new cURL resource
        $curl = curl_init();
        $post_fields_string = null;
        $html = null;
        $error = null;

        if (!$curl) {
            $error = "Couldn't initialize a cURL handle";
            return $error;
        }

        if (!$slashedPath) {
            $error = "API PATH is not specified properly";
            return $error;
        }

        //url-ify the data for the POST
        if (!empty($postFields)) {
            foreach ($postFields as $key => $value) {
                //$post_fields_string .= $key . '=' . $value . '&';
            }
            $post_fields_string = urldecode(http_build_query($postFields));
            rtrim($post_fields_string, '&');
            curl_setopt($curl, CURLOPT_POST, count($postFields));
            curl_setopt($curl, CURLOPT_POSTFIELDS, $post_fields_string);
        }


        if (!empty($token)) {
            $headers_array = [
                'Accept: application/json, text/plain, */*',
                'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.90 Safari/537.36'
            ];
            if (is_array($token)) {
                if (!empty($token['token'])) {
                    $authorization = "Authorization: Bearer " . $token['token'];
                    array_push($headers_array, $authorization);
                }
                if (!empty($token['location'])) {
                    $location = "Location: " . $token['location'];
                    array_push($headers_array, $location);
                }
            } else {
                $authorization = "Authorization: Bearer " . $token;
                array_push($headers_array, $authorization);
            }
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers_array);
        }

        $api_url = self::$apiUrl . $slashedPath;
        // Set the file URL to fetch through cURL
        curl_setopt($curl, CURLOPT_URL, $api_url);
        // Follow redirects, if any
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        // Fail the cURL request if response code = 400 (like 404 errors)
        curl_setopt($curl, CURLOPT_FAILONERROR, true);
        // Return the actual result of the curl result instead of success code
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        // Wait for 10 seconds to connect, set 0 to wait indefinitely
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        // Execute the cURL request for a maximum of 50 seconds
        curl_setopt($curl, CURLOPT_TIMEOUT, 50);
        // Do not check the SSL certificates
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        // Fetch the URL and save the content in $html variable
        $html = curl_exec($curl);
        // Check if any error has occurred
        if (curl_errno($curl)) {
            $error = 'cURL error: ' . curl_error($curl);
        } else {
            // cURL executed successfully
            $error = curl_getinfo($curl);
        }
        // close cURL resource to free up system resources
        curl_close($curl);
        return !empty($html) ? json_decode($html, true) : $error;
    }

    /**
     * Debug any array
     * @param $var
     */
    public static function pr($var)
    {
        print_r("<pre>");print_r($var);print_r("</pre>");
    }

    /**
     * Pagination
     * @param int $page
     * @param $total
     * @param int $limit
     * @return string
     */

    public static function pagination($page=1, $total=0, $limit=10){
        $lastpage = ceil($total / $limit);
        $prev = $page - 1;                           
        $next = $page + 1;
        $lpm1 = $lastpage - 1;
        $track = 0;
        $track2 = 0;
        $count = 0;
        $pagination = '<div class="pagination">';
        if($page <= 1){
        $pagination	.= '<a href="#" onclick="return false;">&laquo;</a>';
	    }else{
	        $pagination	.= '<a href="products.php?page_no='.$prev.'">&laquo;</a>';
	    }

        for ($i=1; $i <= $lastpage; $i++) { 
        	if($lastpage <= 6){
	        	if($i==$page){
					$pagination .= '<a href="products.php?page_no='.$i.'" class="active">'.$i.'</a>';
	        	}else{
					$pagination .= '<a href="products.php?page_no='.$i.'">'.$i.'</a>';
	        	}
        	}else{
        		if ($i<=1) {
		        	if($i==$page){
						$pagination .= '<a href="products.php?page_no='.$i.'" class="active">'.$i.'</a>';
					$pagination .= '<a href="products.php?page_no='.($i+1).'">'.($i+1).'</a>';
					$pagination .= '<a onclick="return false;">...</a>';

		        	}else{
						$pagination .= '<a href="products.php?page_no='.$i.'">'.$i.'</a>';
						if($page >= 3){
							$pagination .= '<a href="products.php?page_no='.($i+1).'">'.($i+1).'</a>';
						}
		        	}
		        	$count++;
        		}
        		if($track == 0 && $i>2 && $page > 4){
        		    $pagination .= '<a onclick="return false;">...</a>';
        		    $track = 1;
        		}

        		if($page>=2 && $page <= ($lastpage -1)){
		        	if($i==$page-1 && $page > 3){
						$pagination .= '<a href="products.php?page_no='.$i.'">'.$i.'</a>';
		        	}
		        	if($i==$page){
						$pagination .= '<a href="products.php?page_no='.$i.'" class="active">'.$i.'</a>';
		        	}
		        	if($i==$page+1 && $page < ($lastpage -2)){
						$pagination .= '<a href="products.php?page_no='.$i.'">'.$i.'</a>';
						$track2 = 2;
		        	}
        		}
        		if($track2 == 2 && $page < ($lastpage-3) ){
        			$pagination .= '<a onclick="return false;">...</a>';
        			$track2 = 1;
        		}
        		
        		if ($i>= ($lastpage)) {
		        	if($i==$page){
		        		$pagination .= '<a href="products.php?page_no='.($i-1).'">'.($i-1).'</a>';
						$pagination .= '<a href="products.php?page_no='.$i.'" class="active">'.$i.'</a>';
		        	}else{
		        		if($page != ($lastpage-1)){
		        			$pagination .= '<a href="products.php?page_no='.($i-1).'">'.($i-1).'</a>';
		        		}
						$pagination .= '<a href="products.php?page_no='.$i.'">'.$i.'</a>';
		        	}
        		}



        	}
        	
        }
        if($page >= $lastpage){
        $pagination	.= '<a href="#" onclick="return false;">&raquo;</a>';
	    }else{
	        $pagination	.= '<a href="products.php?page_no='.$next.'">&raquo;</a>';
	    }

        $pagination .= '</div>';

        return $pagination;
    }

    /**
     * Pagination
     * @param int $product_id
     * @param $imageName
     * @param int $limit
     * @return string
     */
    public static function productImageLink($product_id, $storeId, $imageName, $defaultImg){
        $s3_url = self::$S3URL;
    
        if(!empty($imageName)){
                $imageLink =  $s3_url . $storeId . '/' . $product_id . '/' . $imageName;
    
        }else{
            // $imageLink = 'img/no_image.jpg';
            $imageLink = $defaultImg;
        }
    
        return $imageLink;
    
    }

    public static function currency($amount = 0, $pre_class = 'pre', $amount_class = 'amount', $post_class = 'post')
    {
        $config = $_SESSION['config'];
        $currency = !empty($config['currency_format']) ? $config['currency_format'] : '';
        if (empty($amount)) {
            $amount = 0;
        }
        $amount = number_format($amount, 2);
        if (!empty($currency)) {
            $html = '';
            if ($currency['pre']) {
                $html .= "<span class='" . $pre_class . "'>" . $currency['symbol'] . "</span>";
            }
            $html .= "<span class='" . $amount_class . "'>" . $amount . "</span>";
            if ($currency['post']) {
                $html .= "<span class='" . $post_class . "'>" . $currency['code'] . " </span>";
            }
        } else {
            $html = "<span class='" . $pre_class . "'>$</span><span class='" . $amount_class . "'>" . $amount . "</span><span class='" . $post_class . "'> USD</span>";
        }
        return $html;
    }

}

?>