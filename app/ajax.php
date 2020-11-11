<?php defined( 'ABSPATH' ) || exit;

add_action('wp_ajax_getnet_processCreditCard', 'getnet_processCreditCard');
add_action('wp_ajax_nopriv_getnet_processCreditCard', 'getnet_processCreditCard');
function getnet_processCreditCard()
{
    $gateway = new WC_Getnet_Gateway();
    $api 	 = new WC_Getnet_API($gateway);
    
    $card 	 = sanitize_text_field($_POST['card']);
    $token 	 = $api->getCardToken($card);
    if(!$token): 
        WC_Getnet::log('Erro ao processar o cartão devido a falta de token');
        wp_send_json_error(['message' => __('There was a problem processing your card. Verify the given informations and try again.', 'vverner-getnet')]);
    endif;

    $month 	 = sanitize_text_field($_POST['month']);
    $year 	 = sanitize_text_field($_POST['year']);
    $cvv 	 = sanitize_text_field($_POST['cvv']);
    $name 	 = sanitize_text_field($_POST['name']);
    
    $isValid = $api->validateCard($token, $month, $year, $cvv, $name);
    
    if(!$isValid): 
        WC_Getnet::log('erro ao processar o cartão devido a cartão inválido');
        wp_send_json_error(['message' => __('There was a problem processing your card. Verify the given informations and try again.', 'vverner-getnet')]);
    endif;
    
    $res['message'] = [
        'token'     => $token,
        'expMonth'  => str_pad($month, 2, '0', STR_PAD_LEFT),
        'expYear'   => str_pad($year , 2, '0', STR_PAD_LEFT)
    ];
    
    wp_send_json_success($res);
}

