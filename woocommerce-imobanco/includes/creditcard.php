<?php

/**
 * Custom Payment Gateway.
 *
 * Provides a Custom Payment Gateway, mainly for testing purposes.
 */
add_action('plugins_loaded', 'init_imopay_creditcard_gateway_class');
function init_imopay_creditcard_gateway_class(){

    class WC_Gateway_Imopay_Creditcard extends WC_Payment_Gateway {

        public $domain;

        /**
         * Constructor for the gateway.
         */
        public function __construct() {

            $this->domain = 'imopay_credit_card_payment';

            $this->id                 = 'imopay_credit_card';
            $this->icon               = apply_filters('woocommerce_imopay_credit_card_gateway_icon', '');
            $this->has_fields         = false;
            $this->method_title       = __( 'Imopay - Credit card', $this->domain );
            $this->method_description = __( 'Habilita o pagamento por cartão de crédito do Imopay.', $this->domain );

            // Load the settings.
            $this->init_form_fields();
            $this->init_settings();

            // Define user set variables
            $this->title        = $this->get_option( 'title' );
            $this->description  = $this->get_option( 'description' );
            $this->instructions = $this->get_option( 'instructions', $this->description );
            $this->order_status = $this->get_option( 'order_status', 'completed' );

            // Actions
            add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
            add_action( 'woocommerce_thankyou_' . $this->id, array( $this, 'thankyou_page' ) );

            // Customer Emails
            add_action( 'woocommerce_email_before_order_table', array( $this, 'email_instructions' ), 10, 3 );
        }

        /**
         * Initialise Gateway Settings Form Fields.
         */
        public function init_form_fields() {

            $this->form_fields = array(
                'enabled' => array(
                    'title'   => __( 'Enable/Disable', $this->domain ),
                    'type'    => 'checkbox',
                    'label'   => __( 'Enable Custom Payment', $this->domain ),
                    'default' => 'yes'
                ),
                'title' => array(
                    'title'       => __( 'Title', $this->domain ),
                    'type'        => 'text',
                    'description' => __( 'This controls the title which the user sees during checkout.', $this->domain ),
                    'default'     => __( 'Custom Payment', $this->domain ),
                    'desc_tip'    => true,
                ),
                'order_status' => array(
                    'title'       => __( 'Order Status', $this->domain ),
                    'type'        => 'select',
                    'class'       => 'wc-enhanced-select',
                    'description' => __( 'Choose whether status you wish after checkout.', $this->domain ),
                    'default'     => 'wc-completed',
                    'desc_tip'    => true,
                    'options'     => wc_get_order_statuses()
                ),
                'description' => array(
                    'title'       => __( 'Description', $this->domain ),
                    'type'        => 'textarea',
                    'description' => __( 'Payment method description that the customer will see on your checkout.', $this->domain ),
                    'default'     => __('Payment Information', $this->domain),
                    'desc_tip'    => true,
                ),
                'instructions' => array(
                    'title'       => __( 'Instructions', $this->domain ),
                    'type'        => 'textarea',
                    'description' => __( 'Instructions that will be added to the thank you page and emails.', $this->domain ),
                    'default'     => '',
                    'desc_tip'    => true,
                ),
            );
        }

        /**
         * Output for the order received page.
         */
        public function thankyou_page() {
            if ( $this->instructions )
                echo wpautop( wptexturize( $this->instructions ) );
        }

        /**
         * Add content to the WC emails.
         *
         * @access public
         * @param WC_Order $order
         * @param bool $sent_to_admin
         * @param bool $plain_text
         */
        public function email_instructions( $order, $sent_to_admin, $plain_text = false ) {
            if ( $this->instructions && ! $sent_to_admin && 'imopay_credit_card' === $order->payment_method && $order->has_status( 'wc-pending' ) ) {
                echo wpautop( wptexturize( $this->instructions ) ) . PHP_EOL;
            }
        }

        public function payment_fields(){

            if ( $description = $this->get_description() ) {
                echo wpautop( wptexturize( $description ) );
            }

            require WOO_IMOPAY_PLUGIN_DIR . 'includes/forms/creditcard_form.php';
        }

        /**
         * Process the payment and return the result.
         *
         * @param int $order_id
         * @return array
         */
        public function process_payment( $order_id ) {

            $order = wc_get_order( $order_id );
            $customer = $order->get_customer_id();

            $payer_id = imopay_get_user_id($customer);
            $address_id = imopay_get_address_id($customer);

            if (null == $payer_id || false == $payer_id) {
                // usuário ainda não registrado no imopay
                try {
                    $register = imopay_register_user(imopay_get_user_from_formdata(), $customer);
                    if (!isset($register->id))
                    {
                        wc_add_notice('Não foi possível registra o seu usuário no gateway de pagamentos. Tente novamente mais tarde ou entre em contato com o suporte', 'error');
                        return false;
                    }
                    $payer_id = $register->id;
                } catch (\Exception $e) {
                    wc_add_notice($e->getMessage(), 'error');
                    return false;
                }
            } else {
                try {
                    imopay_update_user($payer_id, imopay_get_user_from_formdata($payer_id), $customer);
                } catch (\Exception $e) {}
            }

            if (null == $address_id || false == $address_id) {
                try {
                    $register = imopay_register_address($payer_id, imopay_get_address_from_formdata($payer_id), $customer);
                    if (!isset($register->id))
                    {
                        wc_add_notice('Não foi possível registra o seu endereço no gateway de pagamentos. Tente novamente mais tarde ou entre em contato com o suporte', 'error');
                        return false;
                    }
                    $address_id = $register->id;
                } catch (\Exception $e) {
                    wc_add_notice($e->getMessage(), 'error');
                    return false;
                }
            }

            $data = [
                'payer' => $payer_id,
                'description' => WOO_IMOPAY_CREDITCARD_ORDER_DESCRIPTION,
                'receiver' => WOO_IMOPAY_SELLER_ID,
                'amount' => number_format($order->get_total(), 2, '',''), // 18,00 => 1800
                'payment_method' => [
                    'holder_name' => $_POST['holder_name'],
                    'card_number' => $_POST['card_number'],
                    'expiration_month' => $_POST['expiration_month'],
                    'expiration_year' => $_POST['expiration_year'],
                    'security_code' => $_POST['security_code']
                ],
                'reference_id' => $order_id,
                'metadata' => [
                    'woocommerce' => [
                        'order' => [
                            'customer_id' => $customer,
                            'order_key' => $order->get_data() ['order_key'],
                            'customer_user_agent' => $order->get_data() ['customer_user_agent'],
                            'cart_hash' => $order->get_data() ['cart_hash']
                        ],
                        'blogname' => get_bloginfo('name')
                    ]
                ]
            ];

            // request API imopay
            $request = new \WoocommerceImobanco\Request();
            $response = $request->post('transactions/create_card_present_transaction/', $data);
            error_log("\nhooks: process payment ".json_encode($response)."\n");

            if (null == $response) {
                wc_add_notice('Ocorreu um erro ao tentar conectar ao gateway de pagamentos. Tente novamente mais tarde', 'error');
                return false;
            }

            if (isset($response->status_code) && $response->status_code == 400) {

                wc_add_notice('<strong>Ocorreu um erro ao tentar processar seu pagamento</strong>', 'error');

                if (isset($response->non_field_errors)) {
                    wc_add_notice($response->non_field_errors, 'error');
                }

                if (isset($response->payer[0])) {
                    wc_add_notice($response->payer[0], 'error');
                }

                if (isset($response->payment_method)) {
                    foreach($response->payment_method as $key => $item) {
                        wc_add_notice(str_replace('Este campo', 'O '.WOO_IMOPAY_PLUGIN_SETTINGS['field_labels'][$key], $item[0]), 'error');
                    }
                }

                return false;
            }

            update_post_meta($order_id, '_imopay_order_id', $response->id );
            error_log('order No ' . $response->id . ' id '. $order_id );

            // $status = 'wc-' === substr( $this->order_status, 0, 3 ) ? substr( $this->order_status, 3 ) : $this->order_status;
            $status = 'wc-pending';

            // Set order status
            $order->update_status( $status, __( 'Checkout imopay com cartão de crédito. ', $this->domain ) );

            // Reduce stock levels
            $order->reduce_order_stock();

            // Remove cart
            WC()->cart->empty_cart();

            // Return thankyou redirect
            return array(
                'result'    => 'success',
                'redirect'  => $this->get_return_url( $order )
            );
        }
    }
}

add_filter( 'woocommerce_payment_gateways', 'add_creditcard_gateway_class' );
function add_creditcard_gateway_class( $methods ) {
    $methods[] = 'WC_Gateway_Imopay_Creditcard';
    return $methods;
}

add_action('woocommerce_checkout_process', 'process_creditcard_payment');
function process_creditcard_payment(){

    if($_POST['payment_method'] != 'imopay_credit_card')
        return;

    $holder_name = $_POST['holder_name'] ?? '';
    $card_number = preg_replace('/\D/', '', $_POST['card_number'] ?? '');
    $expiration_month = preg_replace('/\D/', '', $_POST['expiration_month'] ?? '');
    $expiration_year = preg_replace('/\D/', '', $_POST['expiration_year'] ?? '');
    $security_code = preg_replace('/\D/', '', $_POST['security_code'] ?? '');

    if (!isset($card_number[14])) {
        wc_add_notice('Por favor, confirme o número do cartão', 'error');
    }

    if (empty($holder_name)) {
        wc_add_notice('Por favor, verifique o nome do cartão', 'error');
    }

    if (empty($expiration_month) || $expiration_month > 12) {
        wc_add_notice('Por favor, verifique o mêx de expiração do cartão', 'error');
    }

    if (empty($expiration_year) || $expiration_year < date('Y')) {
        wc_add_notice('Por favor, verifique o ano de expiração do cartão', 'error');
    }

    if (empty($security_code)) {
        wc_add_notice('Por favor, verifique código de segurança', 'error');
    }
}

/**
 * Update the order meta with field value
 */
// add_action( 'woocommerce_checkout_update_order_meta', 'creditcard_payment_update_order_meta' );
// function creditcard_payment_update_order_meta( $order_id ) {

//     if($_POST['payment_method'] != 'custom')
//         return;

//     // echo "<pre>";
//     // print_r($_POST);
//     // echo "</pre>";
//     // exit();

//     // update_post_meta( $order_id, 'mobile', $_POST['mobile'] );
//     // update_post_meta( $order_id, 'transaction', $_POST['transaction'] );
// }

/**
 * Display field value on the order edit page
 */
// add_action( 'woocommerce_admin_order_data_after_billing_address', 'creditcard_checkout_field_display_admin_order_meta', 10, 1 );
// function creditcard_checkout_field_display_admin_order_meta($order){
//     $method = get_post_meta( $order->id, '_payment_method', true );
//     if($method != 'imopay_credit_card')
//         return;

//     $mobile = get_post_meta( $order->id, 'mobile', true );
//     $transaction = get_post_meta( $order->id, 'transaction', true );

//     echo '<p><strong>'.__( 'Mobile Number' ).':</strong> ' . $mobile . '</p>';
//     echo '<p><strong>'.__( 'Transaction ID').':</strong> ' . $transaction . '</p>';
// }
// add_action('plugins_loaded', function(){
//     var_dump(imopay_get_address_id(52));
// });