<?php
/*
Plugin Name: WooCommerce Imopay gateway
Plugin URI: https://www.imobanco.com.br/
Description: Plugin para utilizar o gateway de pagamento do Imopay. V1.0
Version: 0.0.2
Author: Imobanco
Author URI: https://www.imobanco.com.br/
License: GPLv2 or later
*/

namespace WoocommerceImobanco;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if(!defined('WOO_IMOPAY_ENVIRONMENT')) {
    define('WOO_IMOPAY_ENVIRONMENT', getenv('WOO_IMOPAY_ENVIRONMENT'));
}

if (!defined('WOO_IMOPAY_SELLER_ID')) {
    define('WOO_IMOPAY_SELLER_ID', getenv('WOO_IMOPAY_SELLER_ID'));
}

if (!defined('WOO_IMOPAY_API_KEY')) {
    define('WOO_IMOPAY_API_KEY', getenv('WOO_IMOPAY_API_KEY'));
}

if (!defined('WOO_IMOPAY_API_URL')) {
    define('WOO_IMOPAY_API_URL', getenv('WOO_IMOPAY_API_URL') ? getenv('WOO_IMOPAY_API_URL') : ('prod' == WOO_IMOPAY_ENVIRONMENT ? 'https://34.196.253.77/' : 'http://test.imopay.com.br') );
}

define('WOO_IMOPAY_PLUGIN_DIR', plugin_dir_path(__FILE__));

define('WOO_IMOPAY_PLUGIN_URL', plugin_dir_url(__FILE__));

if (!defined('WOO_IMOPAY_CREDITCARD_ORDER_DESCRIPTION')) {
    define('WOO_IMOPAY_CREDITCARD_ORDER_DESCRIPTION', getenv('WOO_IMOPAY_CREDITCARD_ORDER_DESCRIPTION') ? getenv('WOO_IMOPAY_CREDITCARD_ORDER_DESCRIPTION') : get_bloginfo('name'). ' - Pedido no cartão de crédito');
}

if (!defined('WOO_IMOPAY_BILLET_ORDER_DESCRIPTION')) {
    define('WOO_IMOPAY_BILLET_ORDER_DESCRIPTION', getenv('WOO_IMOPAY_BILLET_ORDER_DESCRIPTION') ? getenv('WOO_IMOPAY_BILLET_ORDER_DESCRIPTION') : get_bloginfo('name'). ' - Pedido no boleto');
}

if (!defined('WOO_IMOPAY_EXPIRATION_DATE_INCREMENT')) {
    define('WOO_IMOPAY_EXPIRATION_DATE_INCREMENT', getenv('WOO_IMOPAY_EXPIRATION_DATE_INCREMENT') ? getenv('WOO_IMOPAY_EXPIRATION_DATE_INCREMENT') : '+3 days');
}

if (!defined('WOO_IMOPAY_LIMIT_DATE_INCREMENT')) {
    define('WOO_IMOPAY_LIMIT_DATE_INCREMENT', getenv('WOO_IMOPAY_LIMIT_DATE_INCREMENT') ? getenv('WOO_IMOPAY_LIMIT_DATE_INCREMENT') : '+7 days');
}

define('WOO_IMOPAY_PLUGIN_SETTINGS', [
    'menu_title'        => 'Imobanco Integração',
    'tab_title'         => 'Imobanco Integração',
    'capability'        => 'manage_options',
    'icon'              => WOO_IMOPAY_PLUGIN_URL . 'assets/icon.ico" style="width:23px;height:auto',
    'field_labels' => [
        'first_name' => 'Nome',
        'last_name' => 'Sobrenome',
        'phone' => 'Telefone',
        'amount' => 'Valor',
        'description' => 'Descrição',
        'payer' => 'Pagador',
        'receiver' => 'Recebedor',
        'payment_method' => 'Método de pagamento',
        'holder_name' => 'Nome do titular',
        'card_number' => 'Número do cartão',
        'expration_month' => 'Mês de expiração',
        'expiration_year' => 'Ano de expiração',
        'security_code' => 'Código de segurança (CVV)'
    ]
]);

// // somente para testes, remove os dados do imopay do banco do woocommerce
// add_action('init' , function() {
//     delete_user_meta(60, '_imopay_user_id' );
//     delete_user_meta(60, '_imopay_address_id' );
// });

require WOO_IMOPAY_PLUGIN_DIR . 'includes/functions.php';
require WOO_IMOPAY_PLUGIN_DIR . 'includes/Request.php';
require WOO_IMOPAY_PLUGIN_DIR . 'includes/hooks.php';
require WOO_IMOPAY_PLUGIN_DIR . 'includes/api.php';
require WOO_IMOPAY_PLUGIN_DIR . 'includes/forms/customfields.php';
require WOO_IMOPAY_PLUGIN_DIR . 'includes/creditcard.php';
require WOO_IMOPAY_PLUGIN_DIR . 'includes/billet.php';

/** Admin area integration */
// require WOO_IMOPAY_PLUGIN_DIR . 'includes/admin.php';
