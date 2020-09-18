<?php defined('ABSPATH') || exit();

class WC_Getnet_Api
{
    protected $gateway;

    public function __construct($gateway = null)
    {
        $this->gateway = $gateway;
        $this->getnet_url = $this->gateway->IsSandbox() ? 'https://api-sandbox.getnet.com.br/' : 'https://api.getnet.com.br/';
    }

    public function FetchGetnetData(string $endpoint, $data): array
    {
        $url = $this->getnet_url . $endpoint;

        $req = wp_remote_post($url,[
            'body'    => wp_json_encode($data),
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

    public function GetCardToken(string $cardNumber)
    {
        $data = (object) [
            'card_number' => WC_Getnet::OnlyDigits($cardNumber)
        ];

        $res = $this->FetchGetnetData('v1/tokens/card', $data);

        if (isset($res['status_code'])) {
            WC_Getnet::Log('GETNET: não foi possível gerar o token do cartão devido a ' . $res['message']);
            WC_Getnet::Log(print_r($res, true));
            wc_add_notice('GETNET: Número do cartão inválido', 'error');
            return false;
        }

        return $res['number_token'];
    }

    public function ValidateCard($token, $month, $year, $cvv, $name): bool
    {
        $data = (object) [
            'number_token'         => $token,
            'expiration_month'    => $month,
            'expiration_year'    => $year,
            'security_code'        => $cvv,
            'cardholder_name'    => $name
        ];
        $res = $this->FetchGetnetData('v1/cards/verification', $data);

        if (isset($res['status_code'])) {
            WC_Getnet::Log('GETNET: não foi possível validar o cartão devido a ' . $res['message']);
            WC_Getnet::Log(print_r($res, true));

            wc_add_notice('GETNET: Cartão inválido', 'error');
            return false;
        }

        if ($res['status'] !== 'VERIFIED') return false;

        $this->gateway->SetCardData($data);

        return true;
    }

    private function SetAuthToken(array $tokenData): string
    {
        $token = $tokenData['token_type'] . ' ' . $tokenData['access_token'];
        set_transient('getnet-auth-token', $token, $tokenData['expires_in']);
        return $token;
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
            WC_Getnet::Log('GETNET: não foi possível gerar o token de autorização devido a ' . $res['error_description']);
            WC_Getnet::Log(print_r($res, true));
            wc_add_notice('GETNET: Erro interno na requisição.', 'error');
            return '';
        }

        $token = $this->SetAuthToken($res);
        return $token;
    }
}
