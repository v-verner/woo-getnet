<?php defined( 'ABSPATH' ) || exit;

add_action('wp_ajax_getnet_processCreditCard', 'getnet_processCreditCard');
add_action('wp_ajax_nopriv_getnet_processCreditCard', 'getnet_processCreditCard');
function getnet_processCreditCard()
{
	$res 	= [
		'status'  => 'error',
		'message' => 'Houve um problema ao processar seu cartão. Verifique os dados informados e tente novamente'
	];

	$card 	 = sanitize_text_field($_POST['card']);
    $gateway = new WC_Getnet_Gateway();
	$api 	 = new WC_Getnet_API($gateway);
	$token 	 = $api->GetCardToken($card);
	if(!$token) exit(json_encode($res));
	
	$month 	 = sanitize_text_field($_POST['month']);
	$year 	 = sanitize_text_field($_POST['year']);
	$cvv 	 = sanitize_text_field($_POST['cvv']);
	$name 	 = sanitize_text_field($_POST['name']);

	$isValid = $api->ValidateCard($token, $month, $year, $cvv, $name);

	if(!$isValid) exit(json_encode($res));

	$res['status'] = 'success';
	$res['message'] = $token;

	exit(json_encode($res));
}