<?php defined('ABSPATH') || exit();

class WC_Getnet_Api
{
    protected $gateway;

    public function __construct($gateway = null)
    {
        $this->gateway = $gateway;
        $this->getnet_url = $this->gateway->isSandbox() ? 'https://api-sandbox.getnet.com.br/' : 'https://api.getnet.com.br/';
    }

    public function processPayment(WC_Order $order, int $installments, array $security)
    {
        $transaction_type = ($installments !== 1) ? 'INSTALL_NO_INTEREST' : 'FULL';

		$data = [
			'seller_id' => $this->gateway->seller_id,
			'amount' => $order->get_total() * 100,
			'order' => [
				'order_id' => (string) $order->get_id()
			],
			'customer' => [
				'customer_id' 		=> (string) $order->get_customer_id(),
				'first_name'		=> $order->get_billing_first_name(),
				'last_name'			=> $order->get_billing_last_name(),
				'billing_address' 	=> (object) []
			],
			'device' => (object) [],
			'shippings' => [
				[
					'address' => (object) []
				]
			],
			'credit' => (object) [
				'delayed' => false,
				'save_card_data' => false,
				'transaction_type' => $transaction_type,
				'number_installments' => $installments,
				'card' => (object) [
                    'number_token'      => $security['token'],
                    'expiration_month'  => $security['expMonth'],
                    'expiration_year'   => $security['expYear'],
                    'cardholder_name'   => $security['name'],
                ]
			]
		];

        $res = $this->fetchGetnetData('v1/payments/credit', $data);

        if (isset($res['status_code'])) {
			WC_Getnet::log('GETNET: não foi possível realizar o pagamento devido a' . $res['message']);
			WC_Getnet::log(print_r($res, true));
		}
        
        return isset($res['payment_id']) ? $res['payment_id'] : null;
    }

    public function refundPayment(string $paymentId): bool
    {
        $endpoint = 'v1/payments/credit/'. $paymentId .'/cancel'; 
        $res = $this->fetchGetnetData($endpoint);
        return isset($res['status_code']) ? false : true;
    }

    public function getCardToken(string $cardNumber)
    {
        $data = (object) [
            'card_number' => WC_Getnet::onlyDigits($cardNumber)
        ];

        $res = $this->fetchGetnetData('v1/tokens/card', $data);

        if (isset($res['status_code'])) {
            WC_Getnet::log('GETNET: não foi possível gerar o token do cartão devido a ' . $res['message']);
            WC_Getnet::log(print_r($res, true));
            return false;
        }

        return $res['number_token'];
    }

    public function validateCard($token, $month, $year, $cvv, $name): bool
    {
        $data = (object) [
            'number_token'      => $token,
            'expiration_month'  => $month,
            'expiration_year'   => $year,
            'security_code'     => $cvv,
            'cardholder_name'   => $name
        ];
        $res = $this->fetchGetnetData('v1/cards/verification', $data);

        if (isset($res['status_code'])) {
            WC_Getnet::log('GETNET: não foi possível validar o cartão devido a ' . $res['message']);
            WC_Getnet::log(print_r($res, true));

            wc_add_notice('GETNET: Cartão inválido', 'error');
            return false;
        }

        return $res['status'] === 'VERIFIED';
    }

    private function SetAuthToken(array $tokenData): string
    {
        $token = $tokenData['token_type'] . ' ' . $tokenData['access_token'];
        set_transient('getnet-auth-token', $token, $tokenData['expires_in']);
        return $token;
    }

    private function fetchGetnetData(string $endpoint, $data = null): array
    {
        $url = $this->getnet_url . $endpoint;

        $req = wp_remote_post($url,[
            'body'    => ($data) ? wp_json_encode($data) : '',
            'headers' => array(
                'Content-Type'  => 'application/json; charset=utf-8',
                'Authorization' => $this->GetAuthToken(),
                'seller_id'     => $this->gateway->seller_id,
            ),
        ]);

        $rawRes = wp_remote_retrieve_body($req);
        $res = json_decode($rawRes, true);
        return $res;
    }

    private function GetAuthToken(): string
    {
        $cache = get_transient('getnet-auth-token');
        if ($cache) return $cache;

        $url = $this->getnet_url . 'auth/oauth/v2/token';
        
        $req = wp_remote_post($url,[
            'body'    => 'scope=oob&grant_type=client_credentials',
            'headers' => array(
                'Content-Type'  => 'application/x-www-form-urlencoded',
                'Authorization' => 'Basic ' . base64_encode($this->gateway->client_id . ':' . $this->gateway->client_secret),
                'seller_id'     => $this->gateway->seller_id,
            ),
        ]);

        $rawRes = wp_remote_retrieve_body($req);
        $res = json_decode($rawRes, true);

        if (isset($res['error'])) {
            WC_Getnet::log('GETNET: não foi possível gerar o token de autorização devido a ' . $res['error_description']);
            WC_Getnet::log(print_r($res, true));
            wc_add_notice('GETNET: Erro interno na requisição.', 'error');
            return '';
        }

        $token = $this->SetAuthToken($res);
        return $token;
    }
}
