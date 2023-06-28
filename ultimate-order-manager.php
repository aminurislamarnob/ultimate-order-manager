<?php
/**
 * Plugin Name: Ultimate Order Manager For Woocommerce
 * Plugin URI:  https://wordpress.org/plugins/ultimate-order-manager/
 * Description: Add extra column on order list table. Add unlimited order status. Customer wise order list.
 * Version: 0.0.1
 * Author: Aminur Islam Arnob
 * Author URI: https://wordpress.org/plugins/ultimate-order-manager/
 * Text Domain: ultimate-order-manager
 * WC requires at least: 5.0.0
 * Domain Path: /languages/
 * License: GPLv2 or later
 */
use AiArnob\UltimateOrderManager\UltimateOrderManager;

// don't call the file directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! defined( 'UWOM_ORDER_MANAGER_FILE' ) ) {
    define( 'UWOM_ORDER_MANAGER_FILE', __FILE__ );
}

require_once __DIR__ . '/vendor/autoload.php';

/**
 * Load Ultimate_Order_Manager Plugin when all plugins loaded
 *
 * @return \AiArnob\UltimateOrderManager\UltimateOrderManager;
 */
function aiarnob_ultimate_order_manager_for_woocommerce() {
    return UltimateOrderManager::init();
}

// Lets Go....
aiarnob_ultimate_order_manager_for_woocommerce();
