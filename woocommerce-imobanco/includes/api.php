<?php

add_action( 'rest_api_init', function () {

    register_rest_route( 'imobanco/v1', '/order/update', array(
      'methods' => 'POST',
      'callback' => 'woo_imobanco_update_order',
    ));

});

function woo_imobanco_update_order($params) {

    $request = new \WoocommerceImobanco\Request();
    error_log('NOTIFICATION INPUT: '. file_get_contents('php://input'));

    try {
      $content = json_decode(file_get_contents('php://input'));
    } catch (\Exception $e) {

      error_log($e->getMessage() . file_get_contents('php://input'));
      return;
    }

    // confirmação de notificação
    if ($content->Type == 'SubscriptionConfirmation') {
      // request de confimação no aws
      error_log($request->get($content->SubscribeURL));
      return;

    }

    if ($content->Type == 'Notification') {
      $id = json_decode($content->Message)->imopay_transaction_id;
      error_log('Notification received '.$id);
    } else {
      return;
    }

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

    if (isset($order[0]) && $id)
    {
      $response = $request->get("transactions/{$id}");

      if ($response) {
        if (isset($status_relationship[$response->status])) {
          $order[0]->update_status( $status_relationship[$response->status], $response->status, true );
        }
      }
    }
}