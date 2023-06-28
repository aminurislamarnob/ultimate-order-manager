<?php

namespace WeLabs\UltimateOrderManagerForWoocommerce;

class SettingsLink {
    protected $plugin;
    public function __construct() {
        $this->plugin = ULTIMATE_ORDER_MANAGER_FOR_WOOCOMMERCE_PLUGIN;
		add_filter( "plugin_action_links_$this->plugin", array( $this, 'settings_link' ) );
	}

	public function settings_link( $links ) {
		$settings_link = '<a href="admin.php?page=wc-settings&tab=uomwoo-settings">Settings</a>';
		array_push( $links, $settings_link );
		return $links;
	}
}
