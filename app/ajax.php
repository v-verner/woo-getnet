<?php defined( 'ABSPATH' ) || exit;

add_action('wp_ajax_getnet_processCreditCard', 'getnet_processCreditCard');
add_action('wp_ajax_nopriv_getnet_processCreditCard', 'getnet_processCreditCard');
function getnet_processCreditCard()
{
	$res 	= [
		'status'  => 'error',
		'message' => 'Houve um problema ao processar seu cartão. Verifique os dados informados e tente novamente'
	];

    $gateway = new WC_Getnet_Gateway();
	$api = new WC_Getnet_API($gateway);
	$token 	= $api->GetCardToken($_POST['card']);
	if(!$token) exit(json_encode($res));

	$isValid = $api->ValidateCard($token, $_POST['month'], $_POST['year'], $_POST['cvv'], $_POST['name']);

	if(!$isValid) exit(json_encode($res));

	$res['status'] = 'success';
	$res['message'] = $token;

	exit(json_encode($res));
}