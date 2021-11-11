<?php defined('ABSPATH') || exit;
/**
 * Plugin Name:          VVerner - Getnet Gateway
 * Description:          Includes Getnet as a payment method in WooCommerce. You will need Getnet API keys for the integration to work. Get them from your manager.
 * Author:               VVerner
 * Author URI:           https://vverner.com
 * Version:              1.3.1
 * License:              GPLv3 or later
 * WC requires at least: 5.3
 * WC tested up to:      5.8
 * Requires at least:    5.4
 * Tested up to:         5.8.1
 * Requires PHP:         7.1
 * Text Domain: 		    getnet
 * Domain Path: 		    /app/languages
 *
 * VVerner - Getnet Gateway is free software: you can
 * redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software Foundation,
 * either version 3 of the License, or any later version.
 *
 * VVerner - Getnet Gateway is distributed in the hope that
 * it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with VVerner - Getnet Gateway. If not, see
 * <https://www.gnu.org/licenses/gpl-3.0.txt>.
 */

// Plugin constants.
define('WC_GETNET_VERSION', '1.3.1');
define('WC_GETNET_FILE', __FILE__);
define('WC_GETNET_APP', __DIR__ . '/app');

if (!class_exists('WC_Getnet')):
   require_once WC_GETNET_APP . '/controller/Init.php';
endif;
