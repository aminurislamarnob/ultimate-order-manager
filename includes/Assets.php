<?php

namespace AiArnob\UltimateOrderManagerForWoocommerce;

class Assets {
    /**
     * The constructor.
     */
    public function __construct() {
        add_action( 'init', [ $this, 'register_all_scripts' ], 10 );

        if ( is_admin() ) {
            add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_scripts' ], 10 );
        } else {
            add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_front_scripts' ] );
        }
    }

    /**
     * Register all Dokan scripts and styles.
     *
     * @return void
     */
    public function register_all_scripts() {
        $this->register_styles();
        $this->register_scripts();
    }

    /**
     * Register scripts.
     *
     * @param array $scripts
     *
     * @return void
     */
    public function register_scripts() {
        $admin_script       = ULTIMATE_ORDER_MANAGER_FOR_WOOCOMMERCE_PLUGIN_ASSET . '/admin/script.js';
        $frontend_script    = ULTIMATE_ORDER_MANAGER_FOR_WOOCOMMERCE_PLUGIN_ASSET . '/frontend/script.js';

        wp_register_script( 'ultimate_order_manager_for_woocommerce_admin_script', $admin_script, [], filemtime( ULTIMATE_ORDER_MANAGER_FOR_WOOCOMMERCE_DIR . '/assets/admin/script.js' ), true );
        wp_register_script( 'ultimate_order_manager_for_woocommerce_script', $frontend_script, [], filemtime( ULTIMATE_ORDER_MANAGER_FOR_WOOCOMMERCE_DIR . '/assets/frontend/script.js' ), true );
    }

    /**
     * Register styles.
     *
     * @return void
     */
    public function register_styles() {
        $admin_style       = ULTIMATE_ORDER_MANAGER_FOR_WOOCOMMERCE_PLUGIN_ASSET . '/admin/style.css';
        $frontend_style    = ULTIMATE_ORDER_MANAGER_FOR_WOOCOMMERCE_PLUGIN_ASSET . '/frontend/style.css';

        wp_register_style( 'ultimate_order_manager_for_woocommerce_admin_style', $admin_style, [], filemtime( ULTIMATE_ORDER_MANAGER_FOR_WOOCOMMERCE_DIR . '/assets/admin/style.css' ) );
        wp_register_style( 'ultimate_order_manager_for_woocommerce_style', $frontend_style, [], filemtime( ULTIMATE_ORDER_MANAGER_FOR_WOOCOMMERCE_DIR . '/assets/frontend/style.css' ) );
    }

    /**
     * Enqueue admin scripts.
     *
     * @return void
     */
    public function enqueue_admin_scripts() {
        wp_enqueue_script( 'ultimate_order_manager_for_woocommerce_admin_script' );
        wp_localize_script(
            'ultimate_order_manager_for_woocommerce_admin_script', 'Ultimate_Order_Manager_For_Woocommerce_Admin', []
        );
    }

    /**
     * Enqueue front-end scripts.
     *
     * @return void
     */
    public function enqueue_front_scripts() {
        wp_enqueue_script( 'ultimate_order_manager_for_woocommerce_script' );
        wp_localize_script(
            'ultimate_order_manager_for_woocommerce_script', 'Ultimate_Order_Manager_For_Woocommerce', []
        );
    }
}
