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

	$response = wp_remote_post('http://django:8000/transactions/create_invoice_transaction/',$args = [

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
	// print_r($response);
	// print_r($response->'body');	

	echo '<pre>';
	print_r($response['body']->id);
	echo '</pre>';
}
?>


<!-- {	
	"id":"edcc4f1c-9bea-47dc-a2b6-ed73b998ef25",
	"zoop_transaction_id":"34f18e6313e24aafb7c0c9dd4bf9d4c6",
	"status":null,
	"payer":"cadc937e-2ac4-4085-be83-3a5190376b80",
	"receiver":"46fe7215-5983-4d38-b400-1b14fe50d9e0",
	"amount":500,"description":"transação de cartão",
	"payment_method":{
	"id":"c7b6a77a-cc22-439c-b118-f781fbb807cf",
	"zoop_invoice_id":"2e49661a73884f6ebaf29f1ebae75e09",
	"status":"not_paid","barcode":"34191090407069382893431339210002783050000000500",
	"zoop_url":"https://api-boleto-production.s3.amazonaws.com/d77c2258b51d49269191502695f939f4/6095b3e9bd6540c2ba0779f1638e2d0e/5efba6ad99506107d97f761e.html",
	"description":"transação de cartão",
	"expiration_date":"2020-06-25",
	"limit_date":"2020-07-14"
	}
} -->

