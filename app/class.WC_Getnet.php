<?php defined( 'ABSPATH' ) || exit;

class WC_Getnet
{
    public static function init()
    {
        if (class_exists('WC_Payment_Gateway')) {
            self::requires();

            add_filter('woocommerce_payment_gateways', array(__CLASS__, 'add_gateway'));
            add_filter('woocommerce_available_payment_gateways', array(__CLASS__, 'hides_when_is_outside_brazil'));
            add_filter('woocommerce_billing_fields', array(__CLASS__, 'transparent_checkout_billing_fields'), 9999);
            add_filter('plugin_action_links_' . plugin_basename(WC_GETNET_PLUGIN_FILE), array(__CLASS__, 'plugin_action_links'));

            if (is_admin()) {
                add_action('admin_notices', array(__CLASS__, 'ecfb_missing_notice'));
            }

		    add_action('admin_enqueue_scripts', [__CLASS__, 'admin_scripts']);

        } else {
            add_action('admin_notices', array(__CLASS__, 'woocommerce_missing_notice'));
        }
    }

    public static function OnlyDigits($string)
	{
		return preg_replace('/\D/', '', $string);
	}

	public static function FormatNumber($number)
	{
		return number_format($number, 2, ',', '.');
	}

    public static function admin_scripts()
	{
		$page = (isset($_GET['page'])) ? $_GET['page'] === 'wc-settings' : false;
		$section = (isset($_GET['section'])) ? $_GET['section'] === 'getnet' : false;

		if(!$page || !$section) return;

		wp_enqueue_script('getnet-admin', plugins_url('app/assets/admin.js', WC_GETNET_PLUGIN_FILE));
	}

    public static function get_templates_path()
    {
        return plugin_dir_path(WC_GETNET_PLUGIN_FILE) . 'app/views/';
    }

    public static function plugin_action_links($links)
    {
        $plugin_links   = array();
        $plugin_links[] = '<a href="' . esc_url(admin_url('admin.php?page=wc-settings&tab=checkout&section=getnet')) . '">Configurações</a>';

        return array_merge($plugin_links, $links);
    }

    private static function requires()
    {
        require_once __DIR__ . '/ajax.php';
        require_once __DIR__ . '/class.WC_Getnet_Api.php';
        require_once __DIR__ . '/class.WC_Getnet_Gateway.php';
    }

    public static function add_gateway($methods)
    {
        $methods[] = 'WC_Getnet_Gateway';
        return $methods;
    }

    public static function hides_when_is_outside_brazil($available_gateways)
    {
        if (isset($_REQUEST['country']) && 'BR' !== $_REQUEST['country']) { // WPCS: input var ok, CSRF ok.
            unset($available_gateways['getnet']);
        }

        return $available_gateways;
    }

    public static function transparent_checkout_billing_fields($fields)
    {
        if (!class_exists('Extra_Checkout_Fields_For_Brazil')) return $fields;
        if (isset($fields['billing_neighborhood'])) {
            $fields['billing_neighborhood']['required'] = true;
        }
        if (isset($fields['billing_number'])) {
            $fields['billing_number']['required'] = true;
        }

        return $fields;
    }

    public static function ecfb_missing_notice()
    {
        if (class_exists('Extra_Checkout_Fields_For_Brazil')) return;
        include __DIR__ . '/admin/views/html-notice-missing-ecfb.php';
    }

    public static function woocommerce_missing_notice()
    {
        include __DIR__ . '/admin/views/html-notice-missing-woocommerce.php';
    }
}
