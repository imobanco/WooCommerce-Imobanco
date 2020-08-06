<?php

// Adding a custom checkout date field
add_filter( 'woocommerce_billing_fields', 'add_birth_date_billing_field', 20, 1 );
function add_birth_date_billing_field($billing_fields) {

    $billing_fields['billing_birth_date'] = array(
        'type'        => 'date',
        'label'       => 'Data de nascimento',
        'class'       => array('form-row-wide'),
        'priority'    => 25,
        'required'    => true,
        'clear'       => true,
    );
    return $billing_fields;
}

function imopay_require_neighborhood_field( $fields ) {
    $fields['neighborhood']['required'] = true;
    return $fields;
}
add_filter( 'woocommerce_default_address_fields', 'imopay_require_neighborhood_field' );

// // Check customer age
// add_action('woocommerce_checkout_process', 'check_birth_date');
// function check_birth_date() {
//     // Check billing city 2 field
//     if( isset($_POST['billing_birth_date']) && ! empty($_POST['billing_birth_date']) ){
//         // Get customer age from birthdate
//         $age = date_diff(date_create($_POST['billing_birth_date']), date_create('now'))->y;

//         // Checking age and display an error notice avoiding checkout (and emptying cart)
//         if( $age < 18 ){
//             wc_add_notice( __( "You need at least to be 18 years old, to be able to checkout." ), "error" );

//             WC()->cart->empty_cart(); // <== Empty cart (optional)
//         }
//     }
// }