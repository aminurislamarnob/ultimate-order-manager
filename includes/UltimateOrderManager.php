<?php

namespace AiArnob\UltimateOrderManager;

/**
 * UltimateOrderManager class
 *
 * @class UltimateOrderManager The class that holds the entire UltimateOrderManager plugin
 */
final class UltimateOrderManager {

    /**
     * Plugin version
     *
     * @var string
     */
    public $version = '0.0.1';

    /**
     * Instance of self
     *
     * @var UltimateOrderManager
     */
    private static $instance = null;

    /**
     * Holds various class instances
     *
     * @since 2.6.10
     *
     * @var array
     */
    private $container = [];

    /**
     * Constructor for the UltimateOrderManager class
     *
     * Sets up all the appropriate hooks and actions
     * within our plugin.
     */
    private function __construct() {
        $this->define_constants();

        register_activation_hook( UWOM_ORDER_MANAGER_FILE, [ $this, 'activate' ] );
        register_deactivation_hook( UWOM_ORDER_MANAGER_FILE, [ $this, 'deactivate' ] );

        add_action( 'plugins_loaded', [ $this, 'init_plugin' ] );
        add_action( 'woocommerce_flush_rewrite_rules', [ $this, 'flush_rewrite_rules' ] );
        add_action( 'ultimate_order_manager_loaded', [ $this, 'check_plugin_dependency' ] );
    }

    /**
     * Initializes the UltimateOrderManager() class
     *
     * Checks for an existing UltimateOrderManager instance
     * and if it doesn't find one, creates it.
     */
    public static function init() {
        if ( self::$instance === null ) {
			self::$instance = new self();
		}

        return self::$instance;
    }

    /**
     * Magic getter to bypass referencing objects
     *
     * @since 2.6.10
     *
     * @param string $prop
     *
     * @return Class Instance
     */
    public function __get( $prop ) {
		if ( array_key_exists( $prop, $this->container ) ) {
            return $this->container[ $prop ];
		}
    }

    /**
     * Placeholder for activation function
     *
     * Nothing being called here yet.
     */
    public function activate() {
        $this->create_uom_wc_status_table(); //create plugin required table on plugin activation
    }

    /**
     * Flush rewrite rules after ultimate_order_manager is activated or woocommerce is activated
     *
     * @since 3.2.8
     */
    public function flush_rewrite_rules() {
        // fix rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Placeholder for deactivation function
     *
     * Nothing being called here yet.
     */
    public function deactivate() {     }

    /**
     * Define all constants
     *
     * @return void
     */
    public function define_constants() {
        $this->define( 'UWOM_ORDER_MANAGER_PLUGIN_VERSION', $this->version );
        $this->define( 'UWOM_ORDER_MANAGER_DIR', dirname( UWOM_ORDER_MANAGER_FILE ) );
        $this->define( 'UWOM_ORDER_MANAGER_INC_DIR', UWOM_ORDER_MANAGER_DIR . '/includes' );
        $this->define( 'UWOM_ORDER_MANAGER_TEMPLATE_DIR', UWOM_ORDER_MANAGER_DIR . '/templates' );
        $this->define( 'UWOM_ORDER_MANAGER_PLUGIN_ASSET', plugins_url( 'assets', UWOM_ORDER_MANAGER_FILE ) );
        $this->define( 'UWOM_ORDER_MANAGER_PLUGIN', plugin_basename( UWOM_ORDER_MANAGER_FILE ) );

        // give a way to turn off loading styles and scripts from parent theme
        $this->define( 'UWOM_ORDER_MANAGER_LOAD_STYLE', true );
        $this->define( 'UWOM_ORDER_MANAGER_LOAD_SCRIPTS', true );
    }

    /**
     * Define constant if not already defined
     *
     * @param string      $name
     * @param string|bool $value
     *
     * @return void
     */
    private function define( $name, $value ) {
        if ( ! defined( $name ) ) {
            define( $name, $value );
		}
    }

    /**
     * Load the plugin after WP User Frontend is loaded
     *
     * @return void
     */
    public function init_plugin() {
        $this->includes();
        $this->init_hooks();

        do_action( 'ultimate_order_manager_loaded' );
    }

    /**
     * Initialize the actions
     *
     * @return void
     */
    public function init_hooks() {
        // initialize the classes
        add_action( 'init', [ $this, 'init_classes' ], 4 );
        add_action( 'plugins_loaded', [ $this, 'after_plugins_loaded' ] );
    }

    /**
     * Include all the required files
     *
     * @return void
     */
    public function includes() {
        // include_once STUB_PLUGIN_DIR . '/functions.php';
    }

    /**
     * Init all the classes
     *
     * @return void
     */
    public function init_classes() {
        $this->container['scripts'] = new Assets();
        new SettingsLink();
        new OrderTableManager();
        new OrderStatusManager();
        new OrderTableWooSettings();
    }

    /**
     * Executed after all plugins are loaded
     *
     * At this point ultimate_order_manager Pro is loaded
     *
     * @since 2.8.7
     *
     * @return void
     */
    public function after_plugins_loaded() {
        //Executed after all plugins are loaded
    }

    public function check_plugin_dependency() {
		// check dependency
		if ( ! $this->is_woocommerce_installed() ) {
			add_action( 'admin_notices', [ $this, 'umo_woo_dependency_notice' ] );
		}
    }

    /**
     * Check whether woocommerce is installed and active
     *
     * @since 2.9.16
     *
     * @return bool
     */
    public function has_woocommerce() {
        return class_exists( 'WooCommerce' );
    }

    /**
     * Check whether woocommerce is installed
     *
     * @since 3.2.8
     *
     * @return bool
     */
    public function is_woocommerce_installed() {
        $filter_active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
		return in_array( 'woocommerce/woocommerce.php', $filter_active_plugins, true );
    }

    /**
     * Plugin dependency notice
     *
     * @return void
     */
    public function umo_woo_dependency_notice() {
        $class = 'notice notice-error umo-error-notice';
        $title = __( 'Ultimate Order Manager For Woocommerce is almost ready.', 'ultimate-order-manager' );
        $message = __( 'You just need to active the WooCommerce plugin to make it functional.', 'ultimate-order-manager' );

        printf( '<div class="%1$s"><h2>%2$s</h2><p>%3$s</p></div>', esc_attr( $class ), esc_html( $title ), esc_html( $message ) );
    }

    /**
     * Custom status table
     *
     * @return void
     */
    public function create_uom_wc_status_table() {
        global $wpdb;

		$collate = '';

		if ( $wpdb->has_cap( 'collation' ) ) {
            $collate = $wpdb->get_charset_collate();
        }

        $sql = "CREATE TABLE {$wpdb->prefix}uom_wc_status (
            id bigint(20) unsigned NOT NULL auto_increment,
            name varchar(200) NOT NULL,
            slug varchar(200) NOT NULL,
            description text NOT NULL,
            bg_color varchar(20) NOT NULL,
            text_color varchar(20) NOT NULL,
            show_after varchar(20) NOT NULL,
            PRIMARY KEY  (id)
          ) $collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );
    }
}
