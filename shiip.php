<?php

/**
* The plugin bootstrap file
*
* This file is read by WordPress to generate the plugin information in the plugin
* admin area. This file also includes all of the dependencies used by the plugin,
* registers the activation and deactivation functions, and defines a function
* that starts the plugin.
*
* @link              https://goshiip.com/
* @since             1.0.0
* @package           SHIIP
*
* @wordpress-plugin
* Plugin Name:       SHIIP
* Plugin URI:        https://app.goshiip.com/
* Description:       The only shipping app you will ever need for your business. You can “Shiip” with top logistics companies at half their standard rates
* Version:           1.1.1
* Author:            Shiip LLC
* Author URI:        https://goshiip.com/
* License:           GPL-2.0+
* License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
* Text Domain:       shiip
* Domain Path:       /languages
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once plugin_dir_path( __FILE__ ) . 'includes/shiipFunctions.php';



/**
* Currently plugin version.
* Start at version 1.0.0 and use SemVer - https://semver.org
* Rename this for your plugin and update it as you release new versions.
*/
define( 'SHIIP_VERSION', '1.1.1' );


function shiip_add_loginsection( $sections ) {
	
	$sections['shiip-login'] = __( 'SHIIP', 'text-domain' );
	return $sections;
	
}

function shiip_add_loginsettings( $sections, $current_section ) {
	/**
	* Check the current section is what we want
	**/
	if ( $current_section == 'shiip-login' ) {
		
		$settings_catalog_options = array();
		
		// Add Title to the Settings
		//.get_option("wc_shiip_settings_email", "-" )
		$settings_catalog_options[] = array(
			'name' => __( '', 'text-domain' ),
			'type' => 'title',
			'desc' => __( "Don't have an account? Visit <a href='https://app.goshiip.com/sign-up' target='_blank'>https://app.goshiip.com/sign-up</a>", 'text-domain' ),
			'id'   => 'wc_shiip_settings_titleregister'
		);
		
		$settings_catalog_options[] = array(
			'name' => __( 'Shiip Login details', 'text-domain' ),
			'type' => 'title',
			'desc' => __( 'Please fill in your shiip login details below.', 'text-domain' ),
			'id'   => 'wc_shiip_settings_title'
		);
		
		
		// Add second text field option
		$settings_catalog_options[] = array(
			'name'     => __( 'Email Address', 'text-domain' ),
			'desc_tip' => __( 'Your account email', 'text-domain' ),
			'id'       => 'wc_shiip_settings_email',
			'type'     => 'text',
			'desc'     => __( 'Account email!', 'text-domain' ),
		);
		
		// Add second text field option
		$settings_catalog_options[] = array(
			'name'     => __( 'Password', 'text-domain' ),
			'desc_tip' => __( 'Your account email', 'text-domain' ),
			'id'       => 'wc_shiip_settings_password',
			'type'     => 'text',
			'desc'     => __( 'Account password', 'text-domain' ),
		);
		
		// Add second text field option
		$settings_catalog_options[] = array(
			'name'     => __( 'Origin: ', 'text-domain' ),
			'desc_tip' => __( 'Where are you shipping from?', 'text-domain' ),
			'id'       => 'wc_shiip_settings_originLocation',
			'type'     => 'text',
			'desc'     => __( 'Origin Location - Where are you shipping from? Please enter a full address.', 'text-domain' ),
		);
		
		$settings_catalog_options[] = array(
			'name'     => __( 'Phone Number: ', 'text-domain' ),
			'desc_tip' => __( 'Contact number', 'text-domain' ),
			'id'       => 'wc_shiip_settings_contactnumber',
			'type'     => 'text',
			'desc'     => __( 'Contact phone number?', 'text-domain' ),
		);
		
		$settings_catalog_options[] = array( 'type' => 'sectionend', 'id' => 'shiip-login' );
		
		return $settings_catalog_options;
		
		/**
		* If not, return the standard settings
		**/
	} else {
		return $settings;
	}
	
}



/**
* The code that runs during plugin activation.
* This action is documented in includes/class-plugin-name-activator.php
*/
function activate_shiip() {
	// require_once plugin_dir_path( __FILE__ ) . 'includes/class-plugin-name-activator.php';
	// Plugin_Name_Activator::activate();
	
	// getAccessToken();
	
	
	
}

/**
* The code that runs during plugin deactivation.
* This action is documented in includes/class-plugin-name-deactivator.php
*/
function deactivate_shiip() {
	
	delete_option("wc_shiip_user");
	delete_option("wc_shiip_user_token");
	delete_option("wc_shiip_user_name");
	
	
}

register_activation_hook( __FILE__, 'activate_shiip' );
register_deactivation_hook( __FILE__, 'deactivate_shiip' );




function shiip_showrates(){
	
	echo '
	<div class="trust-badge-message-ifeoluwa-popoola-popson"> <a href="https://goshiip.com/" target="_blank"> Shipping handled by SHIIP. 1.1.1.</a> </div>';
	
}


function getAccessToken(){
    $email = get_option("wc_shiip_settings_email", "-" );
    $password = get_option("wc_shiip_settings_password", "-" );
 
    
    $body = [
        'email_phone' => $email,
        'password' => $password,
    ];
    
    $body = wp_json_encode( $body );

    // error_log($body);

    
    $options = [
        'body'        => $body,
        'headers'     => [
            'Content-Type' => 'application/json',
        ],
        'timeout'     => 60,
        'redirection' => 5,
        'blocking'    => true,
        'httpversion' => '1.0',
        'sslverify'   => false,
        'data_format' => 'body',
    ];
    
    $response = wp_remote_post( "https://delivery.apiideraos.com/api/v2/auth/login", $options );
    $responseBody = wp_remote_retrieve_body( $response );

    $json = json_decode($responseBody);
    
    
    // error_log($responseBody);
    
    if($json->status){
        add_option("wc_shiip_user",  $responseBody);
        add_option("wc_shiip_user_token",  $json->data->token);
        add_option("wc_shiip_user_name",  $json->data->user->firstname.' '.$json->data->user->lastname);

		update_option("wc_shiip_user",  $responseBody);
        update_option("wc_shiip_user_token",  $json->data->token);
        update_option("wc_shiip_user_name",  $json->data->user->firstname.' '.$json->data->user->lastname);
        
        
    }
    
}
function convertToUSD($amount){
	$xchangeRate  = get_option("wc_shiip_exchangerate_usd");

	return $amount / $xchangeRate;
}

function getExchangeRate(){
	// error_log("###############################################################");
    $response = wp_remote_get('https://delivery.apiideraos.com/api/v2/currencies/');
    
    $responseBody = wp_remote_retrieve_body( $response );
    $json = json_decode($responseBody);

    // error_log($responseBody);
    
    if($json->status_code == 200){
		foreach($json->currencies as $current){
			if($current->currency == "USD"){
				add_option("wc_shiip_exchangerate_usd",  $current->exchange_rate);
				break;
			}
			
		}
        
    }
    
}

function shiip_shiping_init() {
	// Your class will go here
	
	
}



function shiip_bookshipment($order_id){
	if ( ! $order_id ) return;
	
	
	// Getting an instance of the order object
	$order = wc_get_order( $order_id );
	
	if($order->is_paid())
	$paid = 'yes';
	else
	return;
	
	// iterating through each order items (getting product ID and the product object) 
	// (work for simple and variable products)
	
	// Iterating through order shipping items
	foreach( $order->get_items( 'shipping' ) as $item_id => $item ){
		// Get the data in an unprotected array
		$item_data = $item->get_data();
		$shipping_data_method_id    = $item_data['method_id'];
		$shipping_metadata  = $item_data['meta_data'];
	}
	
	$jsonString  = json_encode($shipping_metadata);
	
	$someObject = json_decode($jsonString);
	$rateId =  $someObject[0]->value; 
	$redisKey =  $someObject[1]->value; 
	
	$userData = get_option("wc_shiip_user");
	$someObject = json_decode($userData);
	$id = $someObject->data->user->id;
	
	$data  =  '{
		"redis_key": "'.$redisKey.'",
		"user_id": '.$id.',
		"platform": "wordpress - '.get_option('blogname').'",
		"rate_id": "'.$rateId.'",
		"toAddressName": "'.WC()->checkout->get_value( 'billing_first_name' ).' '.WC()->checkout->get_value( 'billing_last_name' ).'",
		"toAddressNumber": "'.WC()->checkout->get_value( 'billing_phone' ).'",
		"toAddressEmail": "'.WC()->checkout->get_value( 'billing_email' ).'"
	}';
	
	$data = str_replace("\n\t\t\t\t\t","",$data);
	$data = str_replace(array("\r", "\n","\t"), '', $data);


	$options = [
		'body'        => $data,
		'headers'     => [
			'Content-Type' => 'application/json',
			'Authorization' => 'Bearer ' . get_option("wc_shiip_user_token"),
		],
		'timeout'     => 60,
		'redirection' => 5,
		'blocking'    => true,
		'httpversion' => '1.0',
		'sslverify'   => false,
		'data_format' => 'body',
	];
	
	$response = wp_remote_post( "https://delivery.apiideraos.com/api/v2/shipments", $options );
	$responseBody = wp_remote_retrieve_body( $response );
	
	
	
	
	
	
}

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	
	add_filter( 'woocommerce_get_sections_shipping', 'shiip_add_loginsection' );
	add_filter( 'woocommerce_get_settings_shipping', 'shiip_add_loginsettings', 10, 2 );
	
	add_action( 'woocommerce_review_order_after_submit', 'shiip_showrates' );
	add_action( 'woocommerce_shipping_init', 'shiip_shiping_init' );
	
	add_action('woocommerce_thankyou', 'shiip_bookshipment', 10, 1);
	
}


if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	
	function your_shipping_method_init() {
		class WC_ShiipMethod extends WC_Shipping_Method {
			
			
			/**
			* Constructor for your shipping class
			*
			* @access public
			* @return void
			*/
			public function __construct() {
				$this->id = 'kwik_shipping';
			}
			
			/**
			* Init your settings
			*
			* @access public
			* @return void
			*/
			public function init() {
				
			}
			/**
			* calculate_shipping function.
			*
			* @access public
			* @param mixed $package
			* @return void
			*/
			public function calculate_shipping($package = array()) {
				
				
				
				$rates = getRates("kwik",$package);
				
				$parse =  json_decode($rates);
				
				$kwikamt = "0";
				$estDays = "Same Day Delivery";
				$rateId = "";
				$redisKey = "";
				
				if(strcasecmp($parse->message, "OK") == 0 && $parse->status == true ){
					
					$count = 1;
					
					// //check number of companies providing rates
					// $rateSize = sizeof($parse->data->rates);
					
					if($parse == null || $parse->data == null || $parse->data->rates == null){
						return ;
					}
					
					$redisKey = $parse->data->kwik_key;
					
					
					$rate = $parse->data->rates;
					
					
					if(isset($rate->currency) && ($rate->currency == "NGN" || $rate->currency == "USD" )){//only show ngn and USD
						
						$estDays = "Same Day Delivery";
						
						
						if($rate->estimated_days == null){
							$kwikamt = $rate->amount;
							
						}else{
							$estDays = $rate->estimated_days;
							$kwikamt = $rate->amount;
						}
						
						$rateId = $rate->courier->id;
						
					}
					
				}else{//status does exist 
					// error_log("status does exist -".$rateServer->type);
					
				}
				
				
				if( get_option('woocommerce_currency') == "USD"){
					//convert the amount to dollars
					if($rate->currency == "NGN"){//ngn - convert to naira
						$kwikamt = convertToUSD($rate->amount);
					}else{//usd - show the dollar
						$kwikamt = $rate->amount;
					}
				
				}

				if( get_option('woocommerce_currency') == "NGN"){
					//convert the amount to dollars
					if($rate->currency == "NGN"){//ngn
						$kwikamt = $rate->amount;
					}else{//convert to naira
						$kwikamt = $rate->amount * get_option("wc_shiip_exchangerate_usd");
					}
				
				}
				
				$rate2 = array(
					'id' => "kwik_shipping",
					'label' => "Kwik - ".$estDays,
					'cost' => $kwikamt,
					'calc_tax' => 'per_item',
					'method_id' => $redisKey,
					'meta_data' => array("rateId" => $rateId,"redisKey" => $redisKey)
				);
				
				
				// Register the rate
				if(isset($kwikamt) && $kwikamt != NULL && $kwikamt > 0){
					$this->add_rate($rate2);
				}
				
			}
		}
		
		
		class WC_ShiipMethod_FEDEX extends WC_Shipping_Method {
			/**
			* Constructor for your shipping class
			*
			* @access public
			* @return void
			*/
			public function __construct() {
				$this->id = 'fedex_shipping';
				$this->init();
			}
			
			/**
			* Init your settings
			*
			* @access public
			* @return void
			*/
			public function init() {
			}
			/**
			* calculate_shipping function.
			*
			* @access public
			* @param mixed $package
			* @return void
			*/
			public function calculate_shipping($package = array()) {
				
				$rates = getRates("fedex",$package);
				
				$parse =  json_decode($rates);
				
				$kwikamt = "0";
				$estDays = "Same Day Delivery";
				
				$rateId = "";
				$redisKey = "";
				
				if($parse == null || $parse->data == null || $parse->data->rates == null){
					return ;
				}
				
				$redisKey = $parse->data->redis_key;
				
				
				$rate = $parse->data->rates;
				
				if(isset($rate->currency) && ($rate->currency == "NGN" || $rate->currency == "USD" )){//only show ngn and USD
					
					$estDays = "Same Day Delivery";
					
					
					if($rate->estimated_days == null){
						$kwikamt = $rate->amount;
					}else{
						$estDays = $rate->estimated_days;
						$kwikamt = $rate->amount;
					}
					
					$rateId = $rate->courier->id;
					
				}

				if( get_option('woocommerce_currency') == "USD"){
					//convert the amount to dollars
					if($rate->currency == "NGN"){//ngn - convert to naira
						$kwikamt = convertToUSD($rate->amount);
					}else{//usd - show the dollar
						$kwikamt = $rate->amount;
					}
				
				}

				if( get_option('woocommerce_currency') == "NGN"){
					//convert the amount to dollars
					if($rate->currency == "NGN"){//ngn
						$kwikamt = $rate->amount;
					}else{//convert to naira
						$kwikamt = $rate->amount * get_option("wc_shiip_exchangerate_usd");
					}
				
				}
				
				
				$rate = array(
					'id' => "fedex_shipping",
					'label' => "Fedex - ".$estDays,
					'cost' => $kwikamt,
					'calc_tax' => 'per_item',
					'meta_data' => array("rateId" => $rateId,"redisKey" => $redisKey)
				);
				
				
				// Register the rate
				if(isset($kwikamt) && $kwikamt != NULL && $kwikamt > 0){
					$this->add_rate($rate);
				}
				
			}
		}
		
		
		
		class WC_ShiipMethod_DHL extends WC_Shipping_Method {
			/**
			* Constructor for your shipping class
			*
			* @access public
			* @return void
			*/
			public function __construct() {
				$this->id = 'dhl_shipping';
				$this->init();
			}
			
			/**
			* Init your settings
			*
			* @access public
			* @return void
			*/
			public function init() {
			}
			/**
			* calculate_shipping function.
			*
			* @access public
			* @param mixed $package
			* @return void
			*/
			public function calculate_shipping($package = array()) {
				
				
				$rates = getRates("dhl",$package);
				
				$parse =  json_decode($rates);
				
				$kwikamt = "0";
				$estDays = "Same Day Delivery";
				
				$redisKey = "";
				$rateId = "";
				
				
				if($parse == null || $parse->data == null || $parse->data->rates == null){
					return ;
				}
				
				$redisKey = $parse->data->redis_key;
				
				
				$rate = $parse->data->rates;
				
				if(isset($rate->currency) && ($rate->currency == "NGN" || $rate->currency == "USD" )){//only show ngn and USD
					
					$estDays = "Same Day Delivery";
					
					
					if($rate->estimated_days == null){
						
						$kwikamt = $rate->amount;
					}else{
						$estDays = $rate->estimated_days;
						$kwikamt = $rate->amount;
					}
					
					$rateId = $rate->courier->id;
					
				}
				

				if( get_option('woocommerce_currency') == "USD"){
					//convert the amount to dollars
					if($rate->currency == "NGN"){//ngn - convert to naira
						$kwikamt = convertToUSD($rate->amount);
					}else{//usd - show the dollar
						$kwikamt = $rate->amount;
					}
				
				}

				if( get_option('woocommerce_currency') == "NGN"){
					//convert the amount to dollars
					if($rate->currency == "NGN"){//ngn
						$kwikamt = $rate->amount;
					}else{//convert to naira
						$kwikamt = $rate->amount * get_option("wc_shiip_exchangerate_usd");
					}
				
				}
				
				
				$rate = array(
					'id' => "dhl_shipping",
					'label' => "DHL - ".$estDays,
					'cost' => $kwikamt,
					'calc_tax' => 'per_item',
					'meta_data' => array("rateId" => $rateId,"redisKey" => $redisKey)
				);
				
				
				// Register the rate
				if(isset($kwikamt) && $kwikamt != NULL && $kwikamt > 0){
					$this->add_rate($rate);
				}
			}
		}
		
		
		
		class WC_ShiipMethod_GOKADA extends WC_Shipping_Method {
			/**
			* Constructor for your shipping class
			*
			* @access public
			* @return void
			*/
			public function __construct() {
				$this->id = 'gokada_shipping';
				$this->init();
			}
			
			/**
			* Init your settings
			*
			* @access public
			* @return void
			*/
			public function init() {
			}
			/**
			* calculate_shipping function.
			*
			* @access public
			* @param mixed $package
			* @return void
			*/
			public function calculate_shipping($package = array()) {
				
				
				$rates = getRates("gokada",$package);
				
				$parse =  json_decode($rates);
				
				$kwikamt = "0";
				$estDays = "Same Day Delivery";
				
				$redisKey = "";
				$rateId = "";
				
				
				
				if($parse == null || $parse->data == null || $parse->data->rates == null){
					return ;
				}
				
				$redisKey = $parse->data->gokada_key;
				
				
				$rate = $parse->data->rates;
				
				if(isset($rate->currency) && ($rate->currency == "NGN" || $rate->currency == "USD" )){//only show ngn and USD
					
					$estDays = "Same Day Delivery";
					
					
					if($rate->estimated_days == null){
						$kwikamt = $rate->amount;
					}else{
						$estDays = $rate->estimated_days;
						$kwikamt = $rate->amount;
					}
					
					$rateId = $rate->courier->id;
					
				}
				
				if( get_option('woocommerce_currency') == "USD"){
					//convert the amount to dollars
					if($rate->currency == "NGN"){//ngn - convert to naira
						$kwikamt = convertToUSD($rate->amount);
					}else{//usd - show the dollar
						$kwikamt = $rate->amount;
					}
				
				}

				if( get_option('woocommerce_currency') == "NGN"){
					//convert the amount to dollars
					if($rate->currency == "NGN"){//ngn
						$kwikamt = $rate->amount;
					}else{//convert to naira
						$kwikamt = $rate->amount * get_option("wc_shiip_exchangerate_usd");
					}
				
				}
				
				
				
				$rate = array(
					'id' => "gokada_shipping",
					'label' => "Gokada - " .$estDays,
					'cost' => $kwikamt,
					'calc_tax' => 'per_item',
					'meta_data' => array("rateId" => $rateId,"redisKey" => $redisKey)
				);
				
				
				// Register the rate
				if(isset($kwikamt) && $kwikamt != NULL && $kwikamt > 0){
					$this->add_rate($rate);
				}
			}
		}
		
		
		
		class WC_ShiipMethod_KONGAVAN extends WC_Shipping_Method {
			/**
			* Constructor for your shipping class
			*
			* @access public
			* @return void
			*/
			public function __construct() {
				$this->id = 'KXPRESS_VAN';
				$this->init();
			}
			
			/**
			* Init your settings
			*
			* @access public
			* @return void
			*/
			public function init() {
			}
			/**
			* calculate_shipping function.
			*
			* @access public
			* @param mixed $package
			* @return void
			*/
			public function calculate_shipping($package = array()) {
				
				
				$rates = getRates("kongavan",$package);
				
				$parse =  json_decode($rates);
				
				$kwikamt = "0";
				$estDays = "Same Day Delivery";
				
				$redisKey = "";
				$rateId = "";
				
				
				
				if($parse == null || $parse->data == null || $parse->data->rates == null){
					return ;
				}
				
				$redisKey = $parse->data->redis_key;
				
				
				$rate = $parse->data->rates;
				
				if(isset($rate->currency) && ($rate->currency == "NGN" || $rate->currency == "USD" )){//only show ngn and USD
					
					$estDays = "Same Day Delivery";
					
					
					if($rate->estimated_days == null){
						$kwikamt = $rate->amount;
					}else{
						$estDays = $rate->estimated_days;
						$kwikamt = $rate->amount;
					}
					
					$rateId = $rate->courier->id;
					
				}
				
				if( get_option('woocommerce_currency') == "USD"){
					//convert the amount to dollars
					if($rate->currency == "NGN"){//ngn - convert to naira
						$kwikamt = convertToUSD($rate->amount);
					}else{//usd - show the dollar
						$kwikamt = $rate->amount;
					}
				
				}

				if( get_option('woocommerce_currency') == "NGN"){
					//convert the amount to dollars
					if($rate->currency == "NGN"){//ngn
						$kwikamt = $rate->amount;
					}else{//convert to naira
						$kwikamt = $rate->amount * get_option("wc_shiip_exchangerate_usd");
					}
				
				}

				
				$rate = array(
					'id' => "KXPRESS_VAN",
					'label' => "KXPRESS VAN - " .$estDays,
					'cost' => $kwikamt,
					'calc_tax' => 'per_item',
					'meta_data' => array("rateId" => $rateId,"redisKey" => $redisKey)
				);
				
				
				// Register the rate
				if(isset($kwikamt) && $kwikamt != NULL && $kwikamt > 0){
					$this->add_rate($rate);
				}
			}
		}
		
		
		
		class WC_ShiipMethod_KONGABIKE extends WC_Shipping_Method {
			/**
			* Constructor for your shipping class
			*
			* @access public
			* @return void
			*/
			public function __construct() {
				$this->id = 'KXPRESS_BIKE';
				$this->init();
			}
			
			/**
			* Init your settings
			*
			* @access public
			* @return void
			*/
			public function init() {
			}
			/**
			* calculate_shipping function.
			*
			* @access public
			* @param mixed $package
			* @return void
			*/
			public function calculate_shipping($package = array()) {
				
				
				$rates = getRates("kongabike",$package);
				
				$parse =  json_decode($rates);
				
				$kwikamt = "0";
				$estDays = "Same Day Delivery";
				
				$redisKey = "";
				$rateId = "";
				
				
				
				if($parse == null || $parse->data == null || $parse->data->rates == null){
					return ;
				}
				
				$redisKey = $parse->data->redis_key;
				
				
				$rate = $parse->data->rates;
				
				if(isset($rate->currency) && ($rate->currency == "NGN" || $rate->currency == "USD" )){//only show ngn and USD
					
					$estDays = "Same Day Delivery";
					
					
					if($rate->estimated_days == null){
						$kwikamt = $rate->amount;
					}else{
						$estDays = $rate->estimated_days;
						$kwikamt = $rate->amount;
					}
					
					$rateId = $rate->courier->id;
					
				}

				if( get_option('woocommerce_currency') == "USD"){
					//convert the amount to dollars
					if($rate->currency == "NGN"){//ngn - convert to naira
						$kwikamt = convertToUSD($rate->amount);
					}else{//usd - show the dollar
						$kwikamt = $rate->amount;
					}
				
				}

				if( get_option('woocommerce_currency') == "NGN"){
					//convert the amount to dollars
					if($rate->currency == "NGN"){//ngn
						$kwikamt = $rate->amount;
					}else{//convert to naira
						$kwikamt = $rate->amount * get_option("wc_shiip_exchangerate_usd");
					}
				
				}
				
				
				$rate = array(
					'id' => "KXPRESS_BIKE",
					'label' => "KXPRESS BIKE - " .$estDays,
					'cost' => $kwikamt,
					'calc_tax' => 'per_item',
					'meta_data' => array("rateId" => $rateId,"redisKey" => $redisKey)
				);
				
				
				// Register the rate
				if(isset($kwikamt) && $kwikamt != NULL && $kwikamt > 0){
					$this->add_rate($rate);
				}
			}
		}

		class WC_ShiipMethod_JUMIA extends WC_Shipping_Method {
			/**
			* Constructor for your shipping class
			*
			* @access public
			* @return void
			*/
			public function __construct() {
				$this->id = 'JUMIA';
				$this->init();
			}
			
			/**
			* Init your settings
			*
			* @access public
			* @return void
			*/
			public function init() {
			}
			/**
			* calculate_shipping function.
			*
			* @access public
			* @param mixed $package
			* @return void
			*/
			public function calculate_shipping($package = array()) {
				
				
				$rates = getRates("jumia",$package);
				
				$parse =  json_decode($rates);
				
				$kwikamt = "0";
				$estDays = "Same Day Delivery";
				
				$redisKey = "";
				$rateId = "";
				
				
				
				if($parse == null || $parse->data == null || $parse->data->rates == null){
					return ;
				}
				
				$redisKey = $parse->data->redis_key;
				
				
				$rate = $parse->data->rates;
				
				if(isset($rate->currency) && ($rate->currency == "NGN" || $rate->currency == "USD" )){//only show ngn and USD
					
					$estDays = "Same Day Delivery";
					
					
					if($rate->estimated_days == null){
						$kwikamt = $rate->amount;
					}else{
						$estDays = $rate->estimated_days;
						$kwikamt = $rate->amount;
					}
					
					$rateId = $rate->courier->id;
					
				}
				

				if( get_option('woocommerce_currency') == "USD"){
					//convert the amount to dollars
					if($rate->currency == "NGN"){//ngn - convert to naira
						$kwikamt = convertToUSD($rate->amount);
					}else{//usd - show the dollar
						$kwikamt = $rate->amount;
					}
				
				}

				if( get_option('woocommerce_currency') == "NGN"){
					//convert the amount to dollars
					if($rate->currency == "NGN"){//ngn
						$kwikamt = $rate->amount;
					}else{//convert to naira
						$kwikamt = $rate->amount * get_option("wc_shiip_exchangerate_usd");
					}
				
				}
				
				$rate = array(
					'id' => "JUMIA",
					'label' => "JUMIA - " .$estDays,
					'cost' => $kwikamt,
					'calc_tax' => 'per_item',
					'meta_data' => array("rateId" => $rateId,"redisKey" => $redisKey)
				);
				
				
				// Register the rate
				if(isset($kwikamt) && $kwikamt != NULL && $kwikamt > 0){
					$this->add_rate($rate);
				}
			}
		}

		class WC_ShiipMethod_Truq extends WC_Shipping_Method {
			/**
			* Constructor for your shipping class
			*
			* @access public
			* @return void
			*/
			public function __construct() {
				$this->id = 'Truq';
				$this->init();
			}
			
			/**
			* Init your settings
			*
			* @access public
			* @return void
			*/
			public function init() {
			}
			/**
			* calculate_shipping function.
			*
			* @access public
			* @param mixed $package
			* @return void
			*/
			public function calculate_shipping($package = array()) {
				
				
				$rates = getRates("Truq",$package);
				
				$parse =  json_decode($rates);
				
				$kwikamt = "0";
				$estDays = "Same Day Delivery";
				
				$redisKey = "";
				$rateId = "";
				
				
				
				if($parse == null || $parse->data == null || $parse->data->rates == null){
					return ;
				}
				
				$redisKey = $parse->data->redis_key;
				
				
				$rate = $parse->data->rates;
				
				if(isset($rate->currency) && ($rate->currency == "NGN" || $rate->currency == "USD" )){//only show ngn and USD
					
					$estDays = "Same Day Delivery";
					
					
					if($rate->estimated_days == null){
						$kwikamt = $rate->amount;
					}else{
						$estDays = $rate->estimated_days;
						$kwikamt = $rate->amount;
					}
					
					$rateId = $rate->courier->id;
					
				}

				if( get_option('woocommerce_currency') == "USD"){
					//convert the amount to dollars
					if($rate->currency == "NGN"){//ngn - convert to naira
						$kwikamt = convertToUSD($rate->amount);
					}else{//usd - show the dollar
						$kwikamt = $rate->amount;
					}
				
				}

				if( get_option('woocommerce_currency') == "NGN"){
					//convert the amount to dollars
					if($rate->currency == "NGN"){//ngn
						$kwikamt = $rate->amount;
					}else{//convert to naira
						$kwikamt = $rate->amount * get_option("wc_shiip_exchangerate_usd");
					}
				
				}
				
				$rate = array(
					'id' => "Truq",
					'label' => "Truq - " .$estDays,
					'cost' => $kwikamt,
					'calc_tax' => 'per_item',
					'meta_data' => array("rateId" => $rateId,"redisKey" => $redisKey)
				);
				
				
				// Register the rate
				if(isset($kwikamt) && $kwikamt != NULL && $kwikamt > 0){
					$this->add_rate($rate);
				}
			}
		}

		class WC_ShiipMethod_Ups extends WC_Shipping_Method {
			/**
			* Constructor for your shipping class
			*
			* @access public
			* @return void
			*/
			public function __construct() {
				$this->id = 'UPS';
				$this->init();
			}
			
			/**
			* Init your settings
			*
			* @access public
			* @return void
			*/
			public function init() {
			}
			/**
			* calculate_shipping function.
			*
			* @access public
			* @param mixed $package
			* @return void
			*/
			public function calculate_shipping($package = array()) {
				
				
				$rates = getRates("ups",$package);
				
				$parse =  json_decode($rates);
				
				$kwikamt = "0";
				$estDays = "Same Day Delivery";
				
				$redisKey = "";
				$rateId = "";
				
				
				
				if($parse == null || $parse->data == null || $parse->data->rates == null){
					return ;
				}
				
				$redisKey = $parse->data->redis_key;
				
				
				$rate = $parse->data->rates;
				
				if(isset($rate->currency) && ($rate->currency == "NGN" || $rate->currency == "USD" )){//only show ngn and USD
					
					$estDays = "Same Day Delivery";
					
					
					if($rate->estimated_days == null){
						$kwikamt = $rate->amount;
					}else{
						$estDays = $rate->estimated_days;
						$kwikamt = $rate->amount;
					}
					
					$rateId = $rate->courier->id;
					
				}

				if( get_option('woocommerce_currency') == "USD"){
					//convert the amount to dollars
					if($rate->currency == "NGN"){//ngn - convert to naira
						$kwikamt = convertToUSD($rate->amount);
					}else{//usd - show the dollar
						$kwikamt = $rate->amount;
					}
				
				}

				if( get_option('woocommerce_currency') == "NGN"){
					//convert the amount to dollars
					if($rate->currency == "NGN"){//ngn
						$kwikamt = $rate->amount;
					}else{//convert to naira
						$kwikamt = $rate->amount * get_option("wc_shiip_exchangerate_usd");
					}
				
				}
				
				$rate = array(
					'id' => "UPS",
					'label' => "UPS - " .$estDays,
					'cost' => $kwikamt,
					'calc_tax' => 'per_item',
					'meta_data' => array("rateId" => $rateId,"redisKey" => $redisKey)
				);
				
				
				// Register the rate
				if(isset($kwikamt) && $kwikamt != NULL && $kwikamt > 0){
					$this->add_rate($rate);
				}
			}
		}

		class WC_ShiipMethod_CourierPlus extends WC_Shipping_Method {
			/**
			* Constructor for your shipping class
			*
			* @access public
			* @return void
			*/
			public function __construct() {
				$this->id = 'CourierPlus';
				$this->init();
			}
			
			/**
			* Init your settings
			*
			* @access public
			* @return void
			*/
			public function init() {
			}
			/**
			* calculate_shipping function.
			*
			* @access public
			* @param mixed $package
			* @return void
			*/
			public function calculate_shipping($package = array()) {
				
				
				$rates = getRates("courierplus",$package);
				
				$parse =  json_decode($rates);
				
				$kwikamt = "0";
				$estDays = "Same Day Delivery";
				
				$redisKey = "";
				$rateId = "";
				
				
				
				if($parse == null || $parse->data == null || $parse->data->rates == null){
					return ;
				}
				
				$redisKey = $parse->data->redis_key;
				
				
				$rate = $parse->data->rates;
				
				if(isset($rate->currency) && ($rate->currency == "NGN" || $rate->currency == "USD" )){//only show ngn and USD
					
					$estDays = "Same Day Delivery";
					
					
					if($rate->estimated_days == null){
						$kwikamt = $rate->amount;
					}else{
						$estDays = $rate->estimated_days;
						$kwikamt = $rate->amount;
					}
					
					$rateId = $rate->courier->id;
					
				}
				

				if( get_option('woocommerce_currency') == "USD"){
					//convert the amount to dollars
					if($rate->currency == "NGN"){//ngn - convert to naira
						$kwikamt = convertToUSD($rate->amount);
					}else{//usd - show the dollar
						$kwikamt = $rate->amount;
					}
				
				}

				if( get_option('woocommerce_currency') == "NGN"){
					//convert the amount to dollars
					if($rate->currency == "NGN"){//ngn
						$kwikamt = $rate->amount;
					}else{//convert to naira
						$kwikamt = $rate->amount * get_option("wc_shiip_exchangerate_usd");
					}
				
				}
				
				$rate = array(
					'id' => "CourierPlus",
					'label' => "Courier Plus - " .$estDays,
					'cost' => $kwikamt,
					'calc_tax' => 'per_item',
					'meta_data' => array("rateId" => $rateId,"redisKey" => $redisKey)
				);
				
				
				// Register the rate
				if(isset($kwikamt) && $kwikamt != NULL && $kwikamt > 0){
					$this->add_rate($rate);
				}
			}
		}

		class WC_ShiipMethod_Fez extends WC_Shipping_Method {
			/**
			* Constructor for your shipping class
			*
			* @access public
			* @return void
			*/
			public function __construct() {
				$this->id = 'Fez';
				$this->init();
			}
			
			/**
			* Init your settings
			*
			* @access public
			* @return void
			*/
			public function init() {
			}
			/**
			* calculate_shipping function.
			*
			* @access public
			* @param mixed $package
			* @return void
			*/
			public function calculate_shipping($package = array()) {
				
				
				$rates = getRates("Fez",$package);
				
				$parse =  json_decode($rates);
				
				$kwikamt = "0";
				$estDays = "Same Day Delivery";
				
				$redisKey = "";
				$rateId = "";
				
				
				
				if($parse == null || $parse->data == null || $parse->data->rates == null){
					return ;
				}
				
				$redisKey = $parse->data->redis_key;
				
				
				$rate = $parse->data->rates;
				
				if(isset($rate->currency) && ($rate->currency == "NGN" || $rate->currency == "USD" )){//only show ngn and USD
					
					$estDays = "Same Day Delivery";
					
					
					if($rate->estimated_days == null){
						$kwikamt = $rate->amount;
					}else{
						$estDays = $rate->estimated_days;
						$kwikamt = $rate->amount;
					}
					
					$rateId = $rate->courier->id;
					
				}

				if( get_option('woocommerce_currency') == "USD"){
					//convert the amount to dollars
					if($rate->currency == "NGN"){//ngn - convert to naira
						$kwikamt = convertToUSD($rate->amount);
					}else{//usd - show the dollar
						$kwikamt = $rate->amount;
					}
				
				}

				if( get_option('woocommerce_currency') == "NGN"){
					//convert the amount to dollars
					if($rate->currency == "NGN"){//ngn
						$kwikamt = $rate->amount;
					}else{//convert to naira
						$kwikamt = $rate->amount * get_option("wc_shiip_exchangerate_usd");
					}
				
				}
				
				
				$rate = array(
					'id' => "Fez",
					'label' => "Fez - " .$estDays,
					'cost' => $kwikamt,
					'calc_tax' => 'per_item',
					'meta_data' => array("rateId" => $rateId,"redisKey" => $redisKey)
				);
				
				
				// Register the rate
				if(isset($kwikamt) && $kwikamt != NULL && $kwikamt > 0){
					$this->add_rate($rate);
				}
			}
		}

		class WC_ShiipMethod_ARAMEX extends WC_Shipping_Method {
			/**
			* Constructor for your shipping class
			*
			* @access public
			* @return void
			*/
			public function __construct() {
				$this->id = 'aramex';
				$this->init();
			}
			
			/**
			* Init your settings
			*
			* @access public
			* @return void
			*/
			public function init() {
			}
			/**
			* calculate_shipping function.
			*
			* @access public
			* @param mixed $package
			* @return void
			*/
			public function calculate_shipping($package = array()) {
				
				
				$rates = getRates("aramex",$package);
				
				$parse =  json_decode($rates);
				
				$kwikamt = "0";
				$estDays = "Same Day Delivery";
				
				$redisKey = "";
				$rateId = "";
				
				
				
				if($parse == null || $parse->data == null || $parse->data->rates == null){
					return ;
				}
				
				$redisKey = $parse->data->redis_key;
				
				
				$rate = $parse->data->rates;
				
				if( isset($rate->currency) && ($rate->currency == "NGN" || $rate->currency == "USD" )){//only show ngn and USD
					
					$estDays = "Same Day Delivery";
					
					
					if($rate->estimated_days == null){
						$kwikamt = $rate->amount;
					}else{
						$estDays = $rate->estimated_days;
						$kwikamt = $rate->amount;
					}
					
					$rateId = $rate->courier->id;
					
				}

				if( get_option('woocommerce_currency') == "USD"){
					//convert the amount to dollars
					if($rate->currency == "NGN"){//ngn - convert to naira
						$kwikamt = convertToUSD($rate->amount);
					}else{//usd - show the dollar
						$kwikamt = $rate->amount;
					}
				
				}

				if( get_option('woocommerce_currency') == "NGN"){
					//convert the amount to dollars
					if($rate->currency == "NGN"){//ngn
						$kwikamt = $rate->amount;
					}else{//convert to naira
						$kwikamt = $rate->amount * get_option("wc_shiip_exchangerate_usd");
					}
				
				}
				
				
				$rate = array(
					'id' => "Aramex",
					'label' => "Aramex - " .$estDays,
					'cost' => $kwikamt,
					'calc_tax' => 'per_item',
					'meta_data' => array("rateId" => $rateId,"redisKey" => $redisKey)
				);
				
				
				// Register the rate
				if(isset($kwikamt) && $kwikamt != NULL && $kwikamt > 0){
					$this->add_rate($rate);
				}
			}
		}

		class WC_ShiipMethod_TRANEX extends WC_Shipping_Method {
			/**
			* Constructor for your shipping class
			*
			* @access public
			* @return void
			*/
			public function __construct() {
				$this->id = 'Tranex';
				$this->init();
			}
			
			/**
			* Init your settings
			*
			* @access public
			* @return void
			*/
			public function init() {
			}
			/**
			* calculate_shipping function.
			*
			* @access public
			* @param mixed $package
			* @return void
			*/
			public function calculate_shipping($package = array()) {
				
				
				$rates = getRates("tranex",$package);
				
				$parse =  json_decode($rates);
				
				$kwikamt = "0";
				$estDays = "Same Day Delivery";
				
				$redisKey = "";
				$rateId = "";
				
				
				
				if($parse == null || $parse->data == null || $parse->data->rates == null){
					return ;
				}
				
				$redisKey = $parse->data->redis_key;
				
				
				$rate = $parse->data->rates;
				
				if(isset($rate->currency) && ($rate->currency == "NGN" || $rate->currency == "USD" )){//only show ngn and USD
					
					$estDays = "Same Day Delivery";
					
					
					if($rate->estimated_days == null){
						$kwikamt = $rate->amount;
					}else{
						$estDays = $rate->estimated_days;
						$kwikamt = $rate->amount;
					}
					
					$rateId = $rate->courier->id;
					
				}

				if( get_option('woocommerce_currency') == "USD"){
					//convert the amount to dollars
					if($rate->currency == "NGN"){//ngn - convert to naira
						$kwikamt = convertToUSD($rate->amount);
					}else{//usd - show the dollar
						$kwikamt = $rate->amount;
					}
				
				}

				if( get_option('woocommerce_currency') == "NGN"){
					//convert the amount to dollars
					if($rate->currency == "NGN"){//ngn
						$kwikamt = $rate->amount;
					}else{//convert to naira
						$kwikamt = $rate->amount * get_option("wc_shiip_exchangerate_usd");
					}
				
				}
				
				
				$rate = array(
					'id' => "Tranex",
					'label' => "Tranex - " .$estDays,
					'cost' => $kwikamt,
					'calc_tax' => 'per_item',
					'meta_data' => array("rateId" => $rateId,"redisKey" => $redisKey)
				);
				
				
				// Register the rate
				if(isset($kwikamt) && $kwikamt != NULL && $kwikamt > 0){
					$this->add_rate($rate);
				}
			}
		}
		class WC_ShiipMethod_ERRANDLR extends WC_Shipping_Method {
			/**
			* Constructor for your shipping class
			*
			* @access public
			* @return void
			*/
			public function __construct() {
				$this->id = 'errandlr';
				$this->init();
			}
			
			/**
			* Init your settings
			*
			* @access public
			* @return void
			*/
			public function init() {
			}
			/**
			* calculate_shipping function.
			*
			* @access public
			* @param mixed $package
			* @return void
			*/
			public function calculate_shipping($package = array()) {
				
				
				$rates = getRates("errandlr",$package);
				
				$parse =  json_decode($rates);
				
				$kwikamt = "0";
				$estDays = "Same Day Delivery";
				
				$redisKey = "";
				$rateId = "";
				
				
				
				if($parse == null || $parse->data == null || $parse->data->rates == null){
					return ;
				}
				
				$redisKey = $parse->data->redis_key;
				
				
				$rate = $parse->data->rates;

				$rate = ((array)$rate)[0];

				// error_log("Get rates payload -" .$rate->estimated_days);
				
				if(isset($rate->currency) && ($rate->currency == "NGN" || $rate->currency == "USD" )){//only show ngn and USD
					
					$estDays = "Same Day Delivery";
					
					
					if($rate->estimated_days == null){
						$kwikamt = $rate->amount;
					}else{
						$estDays = $rate->estimated_days;
						$kwikamt = $rate->amount;
					}
					
					$rateId = $rate->courier->id;
					
				}
				
				if( get_option('woocommerce_currency') == "USD"){
					//convert the amount to dollars
					if($rate->currency == "NGN"){//ngn - convert to naira
						$kwikamt = convertToUSD($rate->amount);
					}else{//usd - show the dollar
						$kwikamt = $rate->amount;
					}
				
				}

				if( get_option('woocommerce_currency') == "NGN"){
					//convert the amount to dollars
					if($rate->currency == "NGN"){//ngn
						$kwikamt = $rate->amount;
					}else{//convert to naira
						$kwikamt = $rate->amount * get_option("wc_shiip_exchangerate_usd");
					}
				
				}
				
				
				
				$rate = array(
					'id' => "errandlr_shipping",
					'label' => "Errandlr - " .$estDays,
					'cost' => $kwikamt,
					'calc_tax' => 'per_item',
					'meta_data' => array("rateId" => $rateId,"redisKey" => $redisKey)
				);
				
				
				// Register the rate
				if(isset($kwikamt) && $kwikamt != NULL && $kwikamt > 0){
					$this->add_rate($rate);
				}
			}
		}
		
		class WC_ShiipMethod_ABC extends WC_Shipping_Method {
			/**
			* Constructor for your shipping class
			*
			* @access public
			* @return void
			*/
			public function __construct() {
				$this->id = 'abc';
				$this->init();
			}
			
			/**
			* Init your settings
			*
			* @access public
			* @return void
			*/
			public function init() {
			}
			/**
			* calculate_shipping function.
			*
			* @access public
			* @param mixed $package
			* @return void
			*/
			public function calculate_shipping($package = array()) {
				
				
				$rates = getRates("abc",$package);
				
				$parse =  json_decode($rates);
				
				$kwikamt = "0";
				$estDays = "Same Day Delivery";
				
				$redisKey = "";
				$rateId = "";
				
				
				
				if($parse == null || $parse->data == null || $parse->data->rates == null){
					return ;
				}
				
				$redisKey = $parse->data->redis_key;
				
				
				$rate = $parse->data->rates;
				
				if(isset($rate->currency) && ($rate->currency == "NGN" || $rate->currency == "USD" )){//only show ngn and USD
					
					$estDays = "Same Day Delivery";
					
					
					if($rate->estimated_days == null){
						$kwikamt = $rate->amount;
					}else{
						$estDays = $rate->estimated_days;
						$kwikamt = $rate->amount;
					}
					
					$rateId = $rate->courier->id;
					
				}
				
				
				if( get_option('woocommerce_currency') == "USD"){
					//convert the amount to dollars
					if($rate->currency == "NGN"){//ngn - convert to naira
						$kwikamt = convertToUSD($rate->amount);
					}else{//usd - show the dollar
						$kwikamt = $rate->amount;
					}
				
				}

				if( get_option('woocommerce_currency') == "NGN"){
					//convert the amount to dollars
					if($rate->currency == "NGN"){//ngn
						$kwikamt = $rate->amount;
					}else{//convert to naira
						$kwikamt = $rate->amount * get_option("wc_shiip_exchangerate_usd");
					}
				
				}
				
				
				
				$rate = array(
					'id' => "abc_shipping",
					'label' => "ABC - " .$estDays,
					'cost' => $kwikamt,
					'calc_tax' => 'per_item',
					'meta_data' => array("rateId" => $rateId,"redisKey" => $redisKey)
				);
				
				
				// Register the rate
				if(isset($kwikamt) && $kwikamt != NULL && $kwikamt > 0){
					$this->add_rate($rate);
				}
			}
		}
		

		
	}
	
	add_action( 'woocommerce_shipping_init', 'your_shipping_method_init' );
	function shiip_shipping_methods( $methods ) {
		getAccessToken();
		getExchangeRate();

		if(get_option('woocommerce_currency') == "NGN" || get_option('woocommerce_currency') == "USD"){
			//only work if the platform is using naira or dollar
			
			// $methods['shiip'] = 'WC_ShiipMethod';
			//  -- this is kwik above
			$methods['shiipDHL'] = 'WC_ShiipMethod_DHL';
			$methods['shiipJUMIA'] = 'WC_ShiipMethod_JUMIA';
			$methods['shiipFEDEX'] = 'WC_ShiipMethod_FEDEX';
			$methods['shiipTruq'] = 'WC_ShiipMethod_Truq';
			$methods['shiipUps'] = 'WC_ShiipMethod_Ups';
			// $methods['shiipCourierPlus'] = 'WC_ShiipMethod_CourierPlus';

			// $methods['shiipFez'] = 'WC_ShiipMethod_Fez';

			// $methods['shiipGIG'] = 'WC_ShiipMethod_GIG';
			$methods['shiipGOKADA'] = 'WC_ShiipMethod_GOKADA';
			// $methods['shiipKXPRESSBIKE'] = 'WC_ShiipMethod_KONGABIKE';
			$methods['shiipKXPRESSVAN'] = 'WC_ShiipMethod_KONGAVAN';

			$methods['shiipTranex'] = 'WC_ShiipMethod_TRANEX';
			$methods['shiipAramex'] = 'WC_ShiipMethod_ARAMEX';
			$methods['shiipErrandlr'] = 'WC_ShiipMethod_ERRANDLR';

			$methods['shiipAbc'] = 'WC_ShiipMethod_ABC';

		}else{
			
		}
		
		
		
		return $methods;
	}
	
	add_filter( 'woocommerce_shipping_methods', 'shiip_shipping_methods' );
	
	
}

/**
* The core plugin class that is used to define internationalization,
* admin-specific hooks, and public-facing site hooks.
*/

/**
* Begins execution of the plugin.
*
* Since everything within the plugin is registered via hooks,
* then kicking off the plugin from this point in the file does
* not affect the page life cycle.
*
* @since    1.0.0
*/

