<?php

add_action( 'rest_api_init', function () {

    register_rest_route( 'imobanco/v1', '/order/update', array(
      'methods' => 'GET',
      'callback' => 'woo_imobanco_update_order',
    ));

});

function woo_imobanco_update_order($params) {

    $id = $params->get_param('id');
    $status = $params->get_param('status');

    $order = wc_get_orders([
      'post_type' => 'shop_order',
      'orderby'   => 'date',
      'order'     => 'DESC',
      'meta_key' => '_imopay_order_id',
      'meta_value' => $id,
      'meta_compare' => '=',
      'post_status' => 'closed'
    ]);

      // relação dos status do imopay com o woocommerce
    $status_relationship = [
      'new'               => 'wc-pending',
      'pending'           => 'wc-pending',
      'failed'            => 'wc-failed',
      'pre_authorized'    => 'wc-processing',
      'succeeded'         => 'wc-on-hold',
      'canceled'          => 'wc-refunded',
      'disputed'          => 'wc-failed',
      'charged_back'      => 'wc-refundedd'
    ];

    if (isset($order[0]) && $id && $status)
    {
      $request = new \WoocommerceImobanco\Request();
      $response = $request->get("transactions/{$id}");

      if ($response) {
        if (isset($status_relationship[$response->status])) {
          $order[0]->update_status( $status_relationship[$response->status], $response->status, true );
        }
      }
    }
}