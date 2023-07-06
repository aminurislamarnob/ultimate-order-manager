<?php

namespace AiArnob\UltimateOrderManager;

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
        $admin_script       = UWOM_ORDER_MANAGER_PLUGIN_ASSET . '/admin/script.js';
        $frontend_script    = UWOM_ORDER_MANAGER_PLUGIN_ASSET . '/frontend/script.js';

        wp_register_script( 'ultimate_order_manager_admin_script', $admin_script, [], UWOM_ORDER_MANAGER_PLUGIN_VERSION, true );
        wp_register_script( 'ultimate_order_manager_script', $frontend_script, [], UWOM_ORDER_MANAGER_PLUGIN_VERSION, true );
    }

    /**
     * Register styles.
     *
     * @return void
     */
    public function register_styles() {
        $admin_style       = UWOM_ORDER_MANAGER_PLUGIN_ASSET . '/admin/style.css';
        $admin_settings_style       = UWOM_ORDER_MANAGER_PLUGIN_ASSET . '/admin/admin-settings-style.css';
        $frontend_style    = UWOM_ORDER_MANAGER_PLUGIN_ASSET . '/frontend/style.css';

        wp_register_style( 'ultimate_order_manager_admin_style', $admin_style, [], UWOM_ORDER_MANAGER_PLUGIN_VERSION, 'all' );
        wp_register_style( 'ultimate_order_manager_admin_settings_style', $admin_settings_style, [], UWOM_ORDER_MANAGER_PLUGIN_VERSION, 'all' );
        wp_register_style( 'ultimate_order_manager_style', $frontend_style, [], UWOM_ORDER_MANAGER_PLUGIN_VERSION, 'all' );
    }

    /**
     * Enqueue admin scripts.
     *
     * @return void
     */
    public function enqueue_admin_scripts() {
        wp_enqueue_script( 'ultimate_order_manager_admin_script' );
        wp_localize_script( 'ultimate_order_manager_admin_script', 'Ultimate_Order_Manager_Admin', [] );
    }

    /**
     * Enqueue front-end scripts.
     *
     * @return void
     */
    public function enqueue_front_scripts() {
        wp_enqueue_script( 'ultimate_order_manager_script' );
        wp_localize_script( 'ultimate_order_manager_script', 'Ultimate_Order_Manager', [] );
    }
}
