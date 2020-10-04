<?php
use WoocommerceImobanco\Request;

function imopay_register_user($data, $customer = null)
{
    if (null == $customer) {
        $customer = WC()->session->get('customer') ['id'] ?? 0;
    }

    $imopay_id = imopay_get_user_id($customer);
    $request = new Request();

    if (null != $imopay_id) {
        return $request->get('buyers/' . $imopay_id);
    }

    $response = $request->post('buyers/', $data);

    error_log('register user ' . json_encode($response));

    if (null == $response) {
        throw new \Exception('O gateway de pagamentos não pôde ser acessado');
        return;
    }

    if (isset($response->status_code)) {
        if (isset($response->non_field_errors)) {
            throw new \Exception($response->non_field_errors);
        }

        if (isset($response->payer[0])) {
            throw new \Exception(str_replace('Este campo', 'O '.WOO_IMOPAY_PLUGIN_SETTINGS['field_labels']['payer'], $response->payer[0]));
        }

        if (isset($response->payment_method)) {
            foreach($response->payment_method as $key => $item) {
                throw new \Exception(str_replace('Este campo', 'O '.WOO_IMOPAY_PLUGIN_SETTINGS['field_labels'][$key], $item[0]));
            }
        }

        foreach ($response as $key => $error)
        {
            if (isset(WOO_IMOPAY_PLUGIN_SETTINGS['field_labels'][$key])) {
                throw new \Exception(str_replace('Este campo', 'O '.WOO_IMOPAY_PLUGIN_SETTINGS['field_labels'][$key], $error[0]));

                return false;
            }
        }

        return $response;
    }

    imopay_save_user_id($response->id, $customer);

    return $response;
}

function imopay_update_user($imopay_id, $data, $customer = null)
{
    if (null == $customer) {
        $customer = WC()->session->get('customer');
    }

    $request = new Request();
    $response = $request->put('buyers/'.$imopay_id, $data);

    if (null == $response) {
        throw new \Exception('O gateway de pagamentos não pôde ser acessado');
        return;
    }

    if (isset($response->status_code)) {
        if (isset($response->non_field_errors)) {
            throw new \Exception($response->non_field_errors);
        }

        if (isset($response->payer[0])) {
            throw new \Exception(str_replace('Este campo', 'O '.WOO_IMOPAY_PLUGIN_SETTINGS['field_labels']['payer'], $response->payer[0]));
        }

        if (isset($response->payment_method)) {
            foreach($response->payment_method as $key => $item) {
                throw new \Exception(str_replace('Este campo', 'O '.WOO_IMOPAY_PLUGIN_SETTINGS['field_labels'][$key], $item[0]));
            }
        }

        return $response;
    }

    imopay_save_user_id($imopay_id, $customer);

    return $response;
}

function imopay_register_address($imopay_id, $data, $customer = null)
{
    if (null == $customer) {
        $customer = WC()->session->get('customer') ['id'] ?? 0;
    }

    $request = new Request();

    $meta = get_user_meta($customer);

    $cpf_cnpj = $meta['billing_cpf'] ?? $meta['billing_cnpj'];

    $response = $request->post('addresses/get_by_document/', [
        'cpf_cnpj' => $cpf_cnpj[0]
    ]);

    if (isset($response->id)) {
        imopay_save_address_id($response->id, $customer);
        return $response;
    }

    $data['owner'] = $imopay_id;

    $response = $request->post('addresses/create_by_name_and_uf', $data);

    if (isset($response->id)) {
        imopay_save_address_id($response->id, $customer);
    }

    return $response;
}

function imopay_get_user_from_formdata()
{
    if (empty($_POST)) {
        return [];
    }

    return [
        'birthdate'             => $_POST['billing_birth_date'],
        'cpf_cnpj'              => $_POST['billing_cpf'] ?? $_POST['billing_cnpj'],
        'email'                 => $_POST['billing_email'],
        'first_name'            => $_POST['billing_first_name'],
        'last_name'             => $_POST['billing_last_name'],
        'mobile_phone'          => $_POST['billing_phone'],
        'phone'                 => $_POST['billing_cellphone']
    ];
}

function imopay_get_address_from_formdata($imopay_id)
{
    if (empty($_POST)) {
        return [];
    }

    $data = [
        'owner'                     => $imopay_id,
        'uf'                        => $_POST['billing_state'],
        'city'                      => ucwords(strtolower($_POST['billing_city'])),
        'neighborhood'              => $_POST['billing_neighborhood'],
        'street'                    => $_POST['billing_address_1'],
        'zip_code'                  => str_replace('-', '', trim($_POST['billing_postcode'])),
        'number'                    => $_POST['billing_number'],
        'complement'                => $_POST['billing_address_2'] ?? ''
    ];

    return $data;
}

function imopay_save_user_id($id, $customer = null)
{
    if (null == $customer) {
        $customer = WC()->session->get('customer') ['id'] ?? 0;
    }

    error_log('Save user_id '. $customer . ' => '.$id);

    update_user_meta($customer, '_imopay_user_id', $id);
}

function imopay_get_user_id($customer = null)
{
    if (null == $customer) {
        $customer = WC()->session->get('customer') ['id'] ?? 0;
    }

    return get_user_meta($customer, '_imopay_user_id', true);
}


function imopay_save_address_id($id, $customer = null)
{
    if (null == $customer) {
        $customer = WC()->session->get('customer') ['id'] ?? 0;
    }

    error_log('Save address_id  '. $customer . ' => '.$id);

    update_user_meta($customer, '_imopay_address_id', $id);
}

function imopay_get_address_id($customer = null)
{
    if (null == $customer) {
        $customer = WC()->session->get('customer');
    }

    return get_user_meta($customer, '_imopay_address_id', true);
}