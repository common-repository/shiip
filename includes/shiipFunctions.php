<?php
//increase timeout
function shiip_http_request_timeout( ) {
    return 100000;
}
add_filter( 'http_request_timeout', 'shiip_http_request_timeout' );

function getRates($provider,$package = array()){

    // error_log("Entered get rates - line 24");
    
    //provider is kwik,gig,gokada etc.
    $pack = $package;
    
    $item_name = "";
    foreach ( WC()->cart->get_cart() as $cart_item ) {

        $item_name = $cart_item['data']->get_title();
        $quantity = $cart_item['quantity'];
        $price = $cart_item['data']->get_price();

        
        
    }

    //i need to get the weight of all the items then add them together
    $floatWeight = WC()->cart->get_cart_contents_weight();
    
    
    
    //get my account details
    $acctData  = get_option("wc_shiip_user");
    
    $acctJson = json_decode($acctData);
    //end of get aaccount details
   
    
    $toCountry =  WC()->countries->countries[$pack['destination']['country']]; //To get country name by code
    $toState =  WC()->countries->states[$pack['destination']['country']][$pack['destination']['state']]; //to get State name by state code
    $toAddress = $pack['destination']['address']." ".$pack['destination']['city'] . " ".$toState . " ".$toCountry;
    $itemCost = $pack['contents_cost'];
    
    
    $fromAddress = get_option("wc_shiip_settings_originLocation");
    
    
    // $fromDetail = getAddressDetails(get_option("wc_shiip_settings_originLocation"));
    // $toDetail = getAddressDetails($toAddress);
    
    
    $phone = get_option("wc_shiip_settings_contactnumber");

    $base_uri = "https://delivery.apiideraos.com/api/v2/tariffs/getpricesingle/".$provider;

    if($floatWeight == 0){
        $floatWeight = 5;
    }

    $basePayload = '{
        "type": "local",
        "toAddress": {
          "name": "'.get_option('blogname').'",
          "email": "'.get_option("wc_shiip_settings_email").'",
          "address": "'.$toAddress.'",
          "phone": "'.$phone.'"
        },
        "fromAddress": {
          "name": "'.get_option('blogname').'",
          "email": "'.get_option("wc_shiip_settings_email").'",
          "address": "'.$fromAddress.'",
          "phone": "'.$phone.'"
        },
        "parcels": {
          "width": 20.5,
          "length": 20.5,
          "height": 20.5,
          "weight": "'.$floatWeight.'"
        },
        "items": [
          {
            "name": "'.$item_name.'",
            "description": "Wordpress-'.$item_name.'",
            "weight": "'.$floatWeight.'",
            "category": "others",
            "amount": "'.$itemCost.'",
            "quantity": "1"
          }
        ]
      }';

    //   error_log("Get rates url -" . $base_uri);
      // error_log("Get rates payload -" . $basePayload);

      // Set up the request arguments
      $options = [
		'body'        => str_replace("\n","",$basePayload) ,
		'headers'     => [
			'Content-Type' => 'application/json',
			'Authorization' => 'Bearer ' . get_option("wc_shiip_user_token"),
		],
		'timeout'     => 60,
		'redirection' => 2,
		'blocking'    => true,
		'httpversion' => '1.0',
		'sslverify'   => true,
		'data_format' => 'body',
	];

      $response = wp_remote_post($base_uri, $options);

    $body = wp_remote_retrieve_body( $response );
    
    // error_log($provider."resp22.".$body."\n\n");

    if ( is_wp_error( $response ) ) {
     
        $message = $response->get_error_message();
        
        error_log("################-ErrorShiip.".$message);
        error_log("################End");
    }

    
    return $body;
    
    
    
}


?>