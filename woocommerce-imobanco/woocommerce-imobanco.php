<?php

/*

Plugin Name: WooCommerce Imobanco
Plugin URI: https://www.imobanco.com.br/
Description: Plugin para utilizar o gateway de pagamento do Imobanco
Version: 0.0.2
Author: Imobanco
Author URI: https://www.imobanco.com.br/
License: GPLv2 or later

*/

/*Trecho do codigo para DEBUG*/

/** função que cria a pagina extra */
// function my_plugin_menu()
// {
//     add_options_page('My Plugin Options', 'Requisição', 'manage_options', 'my-unique-identifier', 'my_plugin_options');
// }

// /** adiciona o menu no dashboard */
// add_action('admin_menu', 'my_plugin_menu');

// /** função que vai fazer alguma coisa  */
// function my_plugin_options()
// {
//     if (!current_user_can('manage_options')) {
//         wp_die(__('Você não tem permissão suficiente para acessar essa pagina'));
//     }

//     $current_user_id = get_current_user_id();
//     $user_meta = get_user_meta($current_user_id);
//     $user_data = get_userdata($current_user_id);


//     $bday = $user_meta['birthdate']['0'];
//     $cpf_cnpj = $user_meta['cpfcnpj']['0'];
//     $email = $user_data->data->user_email;
//     $firstName = $user_meta['first_name']['0'];
//     $lastName = $user_meta['last_name']['0'];

//     echo 'try<br>';

//     $imopay_id = $user_meta['imopay_id']['0'];

//     echo "pos imopay_id |$imopay_id|<br>";

//     echo '<br><br><br>';

//     $url = 'http://service-django-dev:8000/buyers/';
//     $api_key = 'Api-Key 8eIPr75z.7VDE85JQpskc5L2Krm1mrPUPuQ0U8cOu';

//     if ($imopay_id == null) {
//         echo 'Fazer o POST<br>';

//         $response = wp_remote_post($url, $args = [

//             'headers' => [
//                 'Content-Type' => 'application/json',
//                 'Authorization' => $api_key
//             ],
//             'body' => json_encode([
//                 'birthdate' => $bday,
//                 'cpf_cnpj' => $cpf_cnpj,
//                 'email' => $email,
//                 'first_name' => $firstName,
//                 'last_name' => $lastName,
//                 'mobile_phone' => '84999999999'

//             ])
//         ]);
//     } else {
//         echo 'Fazer o PATCH<br>';
//         //faz PUT
//         $url_put = $url . "$imopay_id/";

//         $response = wp_remote_post($url_put, $args = [
//             'method'      => 'PATCH',
//             'headers' => [
//                 'Content-Type' => 'application/json',
//                 'Authorization' => $api_key
//             ],
//             'body' => json_encode([
//                 'birthdate' => $bday,
//                 'cpf_cnpj' => $cpf_cnpj,
//                 'email' => $email,
//                 'first_name' => $firstName,
//                 'last_name' => $lastName,
//                 'mobile_phone' => '84999999999'

//             ])
//         ]);
//     }

//     echo '<br><br><br>';

//     var_dump($response);

//     echo '<br><br><br>';

//     $http_code = wp_remote_retrieve_response_code($response);
//     $body = json_decode($response['body'], true);


//     echo '<br><br><br>';
//     var_dump($http_code);

//     echo '<br><br><br>';
//     var_dump($body);

//     echo '<br><br><br>';
//     echo '|';
//     echo $http_code === 200;
//     echo '|';

//     echo '<br><br><br>';
//     echo '|';
//     echo $http_code === 201;
//     echo '|';

//     echo '<br><br><br>';
//     echo '|';
//     echo $http_code === 200 || $http_code === 201;
//     echo '|';

//     echo '<br><br><br>';

//     if ($http_code === 200 || $http_code === 201) {
//         echo 'Entrou if!';
//         return update_user_meta(
//             $current_user_id,
//             'imopay_id',
//             $body['id']
//         );
//     } else {
//         echo 'Entrou else! Faça sua lógica para retornar pro usuário!';
//         echo '<pre>';
//         print_r($http_code);
//         echo '</pre>';
//     }
// }

/* Trecho de codigo sem DEBUG ---------------------------------------------------------------------*/

add_action('profile_update', 'chama_api', 10, 2);

function chama_api($user_id)
{

    // $url = 'http://django:8000/buyers/';
    $url = 'http://service-django-dev:8000/buyers/';
    $api_key = 'Api-Key 8eIPr75z.7VDE85JQpskc5L2Krm1mrPUPuQ0U8cOu';

    $user_meta = get_user_meta($user_id);
    $user_data = get_userdata($user_id);

    $bday = $user_meta['birthdate']['0'];
    $cpf_cnpj = $user_meta['cpfcnpj']['0'];
    $email = $user_data->data->user_email;
    $firstName = $user_meta['first_name']['0'];
    $lastName = $user_meta['last_name']['0'];
    $phone = $user_meta['phone']['0'];

    $imopay_id = $user_meta['imopay_id']['0'];

    $request_header = [
        'Content-Type' => 'application/json',
        'Authorization' => $api_key
    ];

    $request_body = json_encode([
        'birthdate' => $bday,
        'cpf_cnpj' => $cpf_cnpj,
        'email' => $email,
        'first_name' => $firstName,
        'last_name' => $lastName,
        'mobile_phone' => $phone
    ]);


    if ($imopay_id == null) {
        $request_url = $url;
        $request_method = 'POST';
    } else {
        $request_url = $url . "$imopay_id/";
        $request_method = 'PATCH';
    }

    $response = wp_remote_post($request_url, $args = [
        'method' => $request_method,
        'headers' => $request_header,
        'body' => $request_body
    ]);

    $http_code = wp_remote_retrieve_response_code($response);
    $body = json_decode($response['body'], true);

    if ($http_code === 200 || $http_code === 201) {
        return update_user_meta(
            $user_id,
            'imopay_id',
            $body['id']
        );
    } else {
        echo '<pre>';
        print_r("Erro na Requisição: $http_code");
        echo '</pre>';
    }
}

/* INPUTS DE DATA/CPF/TELEFONE---------------------------------------------------------------------*/

/* INICIO DO CAMPO DE BIRTHDATE*/
/**
 * The field on the editing screens.
 *
 * @param $user WP_User user object
 */
function wporg_usermeta_form_field_birthdate($user)
{
?>
    <h3>Birthdate</h3>
    <table class="form-table">
        <tr>
            <th>
                <label for="birthdate">Birthdate</label>
            </th>
            <td>
                <input type="date" class="regular-text ltr" id="birthdate" name="birthdate" value="<?= esc_attr(get_user_meta($user->ID, 'birthdate', true)) ?>" title="Please use YYYY-MM-DD as the date format." pattern="(19[0-9][0-9]|20[0-9][0-9])-(1[0-2]|0[1-9])-(3[01]|[21][0-9]|0[1-9])" required>
                <p class="description">
                    Please enter your birthdate.
                </p>
            </td>
        </tr>
    </table>
<?php
}

/**
 * The save action.
 *
 * @param $user_id int the ID of the current user.
 *
 * @return bool Meta ID if the key didn't exist, true on successful update, false on failure.
 */

function wporg_usermeta_form_field_birthdate_update($user_id)
{
    // check that the current user have the capability to edit the $user_id
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }

    // create/update user meta for the $user_id
    return update_user_meta(
        $user_id,
        'birthdate',
        $_POST['birthdate']
    );
}

// Add the field to user's own profile editing screen.
add_action(
    'show_user_profile',
    'wporg_usermeta_form_field_birthdate'
);

// Add the field to user profile editing screen.
add_action(
    'edit_user_profile',
    'wporg_usermeta_form_field_birthdate'
);

// Add the save action to user's own profile editing screen update.
add_action(
    'personal_options_update',
    'wporg_usermeta_form_field_birthdate_update'
);

// Add the save action to user profile editing screen update.
add_action(
    'edit_user_profile_update',
    'wporg_usermeta_form_field_birthdate_update'
);

/* FIM DO CAMPO DE BIRTHDATE*/

/* INICIO DO CAMPO DE CPF/CNPJ*/
/**
 * The field on the editing screens.
 *
 * @param $user WP_User user object
 */

function wporg_usermeta_form_field_cpf_cnpj($user)
{
?>
    <h3>It's Your CPF/CNPJ</h3>
    <table class="form-table">
        <tr>
            <th>
                <label for="cpfcnpj">CPF/CNPJ</label>
            </th>
            <td>
                <input type="number" class="regular-text ltr" id="cpfcnpj" name="cpfcnpj" value="<?= esc_attr(get_user_meta($user->ID, 'cpfcnpj', true)) ?>" title="Please use only numbers." required>
                <p class="description">
                    Please enter your CPF/CNPJ without dot or slash.
                </p>
            </td>
        </tr>
    </table>
<?php
}

/**
 * The save action.
 *
 * @param $user_id int the ID of the current user.
 *
 * @return bool Meta ID if the key didn't exist, true on successful update, false on failure.
 */

function wporg_usermeta_form_field_cpf_cnpj_update($user_id)
{
    // check that the current user have the capability to edit the $user_id
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }

    // create/update user meta for the $user_id
    return update_user_meta(
        $user_id,
        'cpfcnpj',
        $_POST['cpfcnpj']
    );
}

// Add the field to user's own profile editing screen.
add_action(
    'show_user_profile',
    'wporg_usermeta_form_field_cpf_cnpj'
);

// Add the field to user profile editing screen.
add_action(
    'edit_user_profile',
    'wporg_usermeta_form_field_cpf_cnpj'
);

// Add the save action to user's own profile editing screen update.
add_action(
    'personal_options_update',
    'wporg_usermeta_form_field_cpf_cnpj_update'
);

// Add the save action to user profile editing screen update.
add_action(
    'edit_user_profile_update',
    'wporg_usermeta_form_field_cpf_cnpj_update'
);

/* FIM DO CAMPO DE CPF/CNPJ*/

/* INICIO DO CAMPO DE PHONE NUMBER*/
/**
 * The field on the editing screens.
 *
 * @param $user WP_User user object
 */

function wporg_usermeta_form_field_phone($user)
{
?>
    <h3>It's Your Phone</h3>
    <table class="form-table">
        <tr>
            <th>
                <label for="phone">Phone Number</label>
            </th>
            <td>
                <input type="number" class="regular-text ltr" id="phone" name="phone" value="<?= esc_attr(get_user_meta($user->ID, 'phone', true)) ?>" title="Please use only numbers." required>
                <p class="description">
                    Please enter your phone without any caracter special.
                </p>
            </td>
        </tr>
    </table>
<?php
}

/**
 * The save action.
 *
 * @param $user_id int the ID of the current user.
 *
 * @return bool Meta ID if the key didn't exist, true on successful update, false on failure.
 */

function wporg_usermeta_form_field_phone_update($user_id)
{
    // check that the current user have the capability to edit the $user_id
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }

    // create/update user meta for the $user_id
    return update_user_meta(
        $user_id,
        'phone',
        $_POST['phone']
    );
}

// Add the field to user's own profile editing screen.
add_action(
    'show_user_profile',
    'wporg_usermeta_form_field_phone'
);

// Add the field to user profile editing screen.
add_action(
    'edit_user_profile',
    'wporg_usermeta_form_field_phone'
);

// Add the save action to user's own profile editing screen update.
add_action(
    'personal_options_update',
    'wporg_usermeta_form_field_phone_update'
);

// Add the save action to user profile editing screen update.
add_action(
    'edit_user_profile_update',
    'wporg_usermeta_form_field_phone_update'
);

/* FIM DO CAMPO DE PHONE NUMBER*/

?>