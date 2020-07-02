<?php

/*

Plugin Name: WooCommerce Imobanco
Plugin URI: https://www.imobanco.com.br/
Description: Plugin para utilizar o gateway de pagamento do Imobanco
Version: 0.0.1
Author: Imobanco
Author URI: https://www.imobanco.com.br/
License: GPLv2 or later

*/

/** Step 2 (from text above). */
add_action( 'admin_menu', 'my_plugin_menu' );

/** Step 1. */
function my_plugin_menu() {
	add_options_page( 'My Plugin Options', 'Requisição', 'manage_options', 'my-unique-identifier', 'my_plugin_options' );
}

/** Step 3. */
function my_plugin_options() {
	if ( !current_user_can( 'manage_options' ) )  {
        wp_die( __( 'Você não tem permissão suficiente para acessar essa pagina' ) );        
	}

	$url = 'http://django:8000/transactions/create_invoice_transaction/';

	 

	$response = wp_remote_post($url,$args = [

		'headers' =>[
			'Content-Type' => 'application/json',
			'Authorization' => 'Api-Key 2MHFG1yr.t0t2243G9nSSuOqM90JkbA4Ndx9JwmCK'
			]
		,
		'body' => json_encode([ 
			'amount' => 500,
			'description' => 'transação de cartão',
			'payer' => 'cadc937e-2ac4-4085-be83-3a5190376b80',
			'receiver' => '46fe7215-5983-4d38-b400-1b14fe50d9e0',
			'payment_method' => [			
				'expiration_date' => '2020-06-25',
				'limit_date' => '2020-07-14'
			]			
		])
	]
			
	);
	
	$http_code = wp_remote_retrieve_response_code( $response );
		
	$body = json_decode($response['body'],true);
	echo '<pre>';
	var_dump($body['payment_method']['id']);
	echo '</pre>';
}
?>




