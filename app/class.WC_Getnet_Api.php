<?php defined('ABSPATH') || exit();

class WC_Getnet_Api
{
    protected $gateway;

    public function __construct($gateway = null)
    {
        $this->gateway = $gateway;
        $this->getnet_url = $this->gateway->IsSandbox() ? 'https://api-sandbox.getnet.com.br/' : 'https://api.getnet.com.br/';
    }

    public function FetchGetnetData(string $endpoint, $data)
    {
        $url = $this->getnet_url . $endpoint;

        $req = curl_init($url);
        curl_setopt($req, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json; charset=utf-8',
            'Authorization: ' . $this->GetAuthToken(),
            'seller_id: ' . $this->gateway->seller_id
        ]);
        curl_setopt($req, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($req, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($req, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($req, CURLOPT_ENCODING, "gzip");
        $res = json_decode(curl_exec($req), true);
        curl_close($req);

        return $res;
    }

    public function GetCardToken(string $cardNumber)
    {
        $data = (object) [
            'card_number' => WC_Getnet::OnlyDigits($cardNumber)
        ];

        $res = $this->FetchGetnetData('v1/tokens/card', $data);

        if (isset($res['status_code'])) {
            error_log('GETNET: não foi possível gerar o token do cartão devido a ' . $res['message']);
            error_log(print_r($res, true));
            wc_add_notice('GETNET: Número do cartão inválido', 'error');
            return false;
        }

        return $res['number_token'];
    }

    public function ValidateCard($token, $month, $year, $cvv, $name)
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
            error_log('GETNET: não foi possível validar o cartão devido a ' . $res['message']);
            error_log(print_r($res, true));
            wc_add_notice('GETNET: Cartão inválido', 'error');
            return false;
        }

        if ($res['status'] !== 'VERIFIED') return false;

        $this->gateway->SetCardData($data);

        return true;
    }

    private function SetAuthToken($tokenData)
    {
        $token = $tokenData['token_type'] . ' ' . $tokenData['access_token'];
        set_transient('getnet-auth-token', $token, $tokenData['expires_in']);
        return $token;
    }

    private function GetAuthToken()
    {
        $cache = get_transient('getnet-auth-token');
        if ($cache) return $cache;

        $url = $this->getnet_url . 'auth/oauth/v2/token';
        $req = curl_init($url);
        curl_setopt($req, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
        curl_setopt($req, CURLOPT_USERPWD, $this->gateway->client_id . ':' . $this->gateway->client_secret);
        curl_setopt($req, CURLOPT_POSTFIELDS, 'scope=oob&grant_type=client_credentials');
        curl_setopt($req, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($req, CURLOPT_RETURNTRANSFER, true);
        $res = json_decode(curl_exec($req), true);
        curl_close($req);

        if (isset($res['error'])) {
            error_log('GETNET: não foi possível gerar o token de autorização devido a ' . $res['error_description']);
            error_log(print_r($res));
            wc_add_notice('GETNET: Erro interno na requisição.', 'error');
            return '';
        }

        $token = $this->SetAuthToken($res);
        return $token;
    }
}
