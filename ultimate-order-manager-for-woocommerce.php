<?php
/**
 * Plugin Name: Ultimate Order Manager For Woocommerce
 * Plugin URI:  https://wordpress.org/plugins/ultimate-woo-order-manager/
 * Description: Add extra column on order list table. Add unlimited order status. Customer wise order list.
 * Version: 0.0.1
 * Author: Aminur Islam Arnob
 * Author URI: https://wordpress.org/plugins/ultimate-woo-order-manager/
 * Text Domain: ultimate-order-manager-for-woocommerce
 * WC requires at least: 5.0.0
 * Domain Path: /languages/
 * License: GPLv2 or later
 */
use WeLabs\UltimateOrderManagerForWoocommerce\UltimateOrderManagerForWoocommerce;

// don't call the file directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! defined( 'ULTIMATE_ORDER_MANAGER_FOR_WOOCOMMERCE_FILE' ) ) {
    define( 'ULTIMATE_ORDER_MANAGER_FOR_WOOCOMMERCE_FILE', __FILE__ );
}

require_once __DIR__ . '/vendor/autoload.php';

/**
 * Load Ultimate_Order_Manager_For_Woocommerce Plugin when all plugins loaded
 *
 * @return \WeLabs\UltimateOrderManagerForWoocommerce\UltimateOrderManagerForWoocommerce;
 */
function welabs_ultimate_order_manager_for_woocommerce() {
    return UltimateOrderManagerForWoocommerce::init();
}

// Lets Go....
welabs_ultimate_order_manager_for_woocommerce();
