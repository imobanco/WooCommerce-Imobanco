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

/** Step 1. */
//   function my_plugin_menu() {
//   	add_options_page( 'My Plugin Options', 'Requisição', 'manage_options', 'my-unique-identifier', 'my_plugin_options' );
//   }

/** Step 2 (from text above). */
//   add_action( 'admin_menu', 'my_plugin_menu' );

/** Step 3. */
//   function my_plugin_options() {
//   	if ( !current_user_can( 'manage_options' ) )  {
//          wp_die( __( 'Você não tem permissão suficiente para acessar essa pagina' ) );        
//   	}

//       $url = 'http://django:8000/buyers/';
      	 
//       $current_user_id = get_current_user_id();
//       $user_meta = get_user_meta($current_user_id);
//       $user_data = get_userdata($current_user_id);
            
//     //   $user_data_email = $user_data->data->user_email; // forma de acessar os attrs quando for stdclass

//       $bday = $user_meta['birthday']['0']; // forma de acessar os attr quando for array
//       $cpf_cnpj = $user_meta['cpfcnpj']['0'];
//       $email = $user_data->data->user_email;
//       $firstName = $user_meta['first_name']['0'];
//       $lastName = $user_meta['last_name']['0'];
//     //  $phone = 
      
//  	 $response = wp_remote_post($url,$args = [

//  	 	'headers' =>[
// 	 		'Content-Type' => 'application/json',
//  	 		'Authorization' => 'Api-Key 2MHFG1yr.t0t2243G9nSSuOqM90JkbA4Ndx9JwmCK'
//  	 		]
//  	 	,
//  	 	'body' => json_encode([ 
//  	 		'birthdate' => $bday,
//  	 		'cpf_cnpj' => $cpf_cnpj,
//  	 		'email' => $email,
//             'first_name' => $firstName,
//             'last_name' => $lastName,
//             'mobile_phone' => '84999999999'
 	 				
// 	 	])
//  	 ]
			
//       );         
      
      

//  	  $http_code = wp_remote_retrieve_response_code( $response );		
//        $body = json_decode($response['body'],true);
       
//        return update_user_meta(
//         $current_user_id,
//         'imopay_id',
//         $body['id']
//     );

//  	   echo '<pre>';
//  	   print_r($body);
//  	   echo '</pre>';
//   }

// essa função cria um paragrafo na tela principal do WP
/*function ola_plugin(){	
	?>
	<div>
		<p>Teste de Atualização!<p>
	<div>
	<?php
}*/

// add_action('all_admin_notices', 'ola_plugin');

/* Trecho de codigo sem DEBUG */

add_action( 'profile_update', 'chama_api', 10, 2 );

function chama_api($user_id) {
	
	$url = 'http://django:8000/buyers/';
   
    $user_meta = get_user_meta($user_id);
    $user_data = get_userdata($user_id);
    
    $bday = $user_meta['birthdate']['0'];
    $cpf_cnpj = $user_meta['cpfcnpj']['0'];
    $email = $user_data->data->user_email;
    $firstName = $user_meta['first_name']['0'];
    $lastName = $user_meta['last_name']['0'];
    $phone = $user_meta['phone']['0'];

	$response = wp_remote_post($url,$args = [

        'headers' =>[
           'Content-Type' => 'application/json',
            'Authorization' => 'Api-Key 2MHFG1yr.t0t2243G9nSSuOqM90JkbA4Ndx9JwmCK'
            ]
        ,
        'body' => json_encode([ 
          'birthdate' => $bday,
          'cpf_cnpj' => $cpf_cnpj,
          'email' => $email,
          'first_name' => $firstName,
          'last_name' => $lastName,
          'mobile_phone' => $phone
                    
       ])
    ]
			
	);
	
	$http_code = wp_remote_retrieve_response_code( $response );		
	$body = json_decode($response['body'],true);	

	return update_user_meta(
        $user_id,
        'imopay_id',
        $body['id']
    );

}

/* INICIO DO CAMPO DE BIRTHDATE*/
/**
 * The field on the editing screens.
 *
 * @param $user WP_User user object
 */
function wporg_usermeta_form_field_birthday( $user ){
	?>
    <h3>Birthdate</h3>
    <table class="form-table">
        <tr>
            <th>
                <label for="birthdate">Birthdate</label>
            </th>
            <td>
                <input type="date"
                       class="regular-text ltr"
                       id="birthdate"
                       name="birthdate"
                       value="<?= esc_attr( get_user_meta( $user->ID, 'birthdate', true ) ) ?>"
                       title="Please use YYYY-MM-DD as the date format."
                       pattern="(19[0-9][0-9]|20[0-9][0-9])-(1[0-2]|0[1-9])-(3[01]|[21][0-9]|0[1-9])"
                       required>
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

function wporg_usermeta_form_field_birthdate_update( $user_id )
{
    // check that the current user have the capability to edit the $user_id
    if ( ! current_user_can( 'edit_user', $user_id ) ) {
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

function wporg_usermeta_form_field_cpf_cnpj( $user )
{
    ?>
    <h3>It's Your CPF/CNPJ</h3>
    <table class="form-table">
        <tr>
            <th>
                <label for="cpfcnpj">CPF/CNPJ</label>
            </th>
            <td>
                <input type="number"
                       class="regular-text ltr"
                       id="cpfcnpj"
                       name="cpfcnpj"
                       value="<?= esc_attr( get_user_meta( $user->ID, 'cpfcnpj', true ) ) ?>"
                       title="Please use only numbers."                       
                       required>
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

function wporg_usermeta_form_field_cpf_cnpj_update( $user_id )
{
    // check that the current user have the capability to edit the $user_id
    if ( ! current_user_can( 'edit_user', $user_id ) ) {
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

function wporg_usermeta_form_field_phone( $user )
{
    ?>
    <h3>It's Your Phone</h3>
    <table class="form-table">
        <tr>
            <th>
                <label for="phone">Phone Number</label>
            </th>
            <td>
                <input type="number"
                       class="regular-text ltr"
                       id="phone"
                       name="phone"
                       value="<?= esc_attr( get_user_meta( $user->ID, 'phone', true ) ) ?>"
                       title="Please use only numbers."                       
                       required>
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

function wporg_usermeta_form_field_phone_update( $user_id )
{
    // check that the current user have the capability to edit the $user_id
    if ( ! current_user_can( 'edit_user', $user_id ) ) {
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
