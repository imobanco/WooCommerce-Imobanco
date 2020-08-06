<?php
use WoocommerceImobanco\Request;

function imopay_integration_customer_actions($id) {

    if (null == WC()->session) return;

    error_log('\n\n session: ' . json_encode(WC()->session) . '\n\n');
    error_log('\n\n customer: '. $id . '\n\n');

    $customer = WC()->session->get('customer') ['id'] ?? '';
    $meta = get_user_meta($customer);
    $imopay_id = $meta['_imopay_user_id'][0] ?? null;
    $address_id = $meta['_imopay_address_id'][0] ?? null;

    if (isset($meta['billing_phone'][0], $meta['billing_birth_date'][0])) {

        $data = [
            'birthdate'             => $meta['billing_birth_date'][0],
            'cpf_cnpj'              => $meta['billing_cpf'][0] ?? $meta['billing_cnpj'][0],
            'email'                 => $meta['billing_email'][0],
            'first_name'            => $meta['billing_first_name'][0],
            'last_name'             => $meta['billing_last_name'][0],
            'mobile_phone'          => $meta['billing_phone'][0],
            'phone'                 => $meta['billing_cellphone'][0]
        ];

        if (null == $imopay_id) {
            // usuário ainda não preencheu o endereço
            try {
               imopay_register_user($data, $customer);
            } catch (\Exception $e) {}
        } else {
            try {
               imopay_update_user($imopay_id, $data, $customer);
            } catch (\Exception $e) {}
        }
    }

    if (null == $address_id && null != $imopay_id) {
        if (isset($meta['billing_address_1'][0]) && !empty($meta['billing_address_1'][0])) {
            $data = [
                'owner'                     => $imopay_id,
                'uf'                        => $meta['billing_state'][0],
                'city'                      => ucwords(strtolower($meta['billing_city'][0])),
                'neighborhood'              => $meta['billing_neighborhood'][0],
                'street'                    => $meta['billing_address_1'][0],
                'zip_code'                  => str_replace('-', '', trim($meta['billing_postcode'][0])),
            ];

            try {
                imopay_register_address($imopay_id, $data, $customer);
            } catch (\Exception $e) {}
        }
    }
}
add_action('woocommerce_created_customer', 'imopay_integration_customer_actions');
add_action('woocommerce_update_customer', 'imopay_integration_customer_actions');

/** required neighborhood */
add_filter( 'woocommerce_checkout_fields' , 'imopay_neighborhood_required_rule' );
function imopay_neighborhood_required_rule( $fields ) {

    if (isset($fields['billing']['billing_neighborhood'])) {
        $fields['billing']['billing_neighborhood']['required'] = 1;
    }

    return $fields;
}

/**
 * On order created
 */
add_action('woocommerce_thankyou', function($order_id) {

    $order = wc_get_order( $order_id );
    $billet = json_decode(get_post_meta($order_id, '_imopay_billet', true));

    if (!empty($billet)) {
        require WOO_IMOPAY_PLUGIN_DIR . 'includes/forms/billet.php';
    }
});
