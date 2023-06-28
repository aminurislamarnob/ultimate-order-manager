<?php
namespace AiArnob\UltimateOrderManager;

use WC_Admin_Settings;

class OrderTableWooSettings {

    public $tab_id = 'uomwoo-settings';

    public function __construct() {
        add_filter( 'woocommerce_settings_tabs_array', array( $this, 'uomwoo_general_settings_tab' ), 50 );
        add_action( 'woocommerce_sections_' . $this->tab_id, array( $this, 'output_uomwoo_settings_sections' ) );
        add_filter( 'woocommerce_settings_' . $this->tab_id, array( $this, 'uomwoo_settings_fields_output' ) );
        add_action( 'woocommerce_settings_save_' . $this->tab_id, array( $this, 'uomwoo_save_settings' ) );
        add_filter( 'gettext', array( $this, 'uomwoo_change_status_save_settings_text' ), 20, 3 );
        add_action( 'admin_enqueue_scripts', array( $this, 'uomwoo_admin_style' ) );
    }

    /**
     * Enqueue admin custom style
     *
     * @return void
     */
    public function uomwoo_admin_style() {
        wp_enqueue_style( 'ultimate_order_manager_for_woocommerce_admin_style' );
    }

    /**
     * Add Plugin specific tab to woo tabs array
     *
     * @param array $tabs
     * @return void
     */
    public function uomwoo_general_settings_tab( $tabs ) {
        $tabs[ $this->tab_id ] = __( 'Ultimate Order Manager', 'ultimate-order-manager' );
        return $tabs;
    }

    /**
	 *  Get sections
	 *
	 *  @return array
	 */
	public function get_sections() {
		$sections = array(
			'' => __( 'General Settings', 'ultimate-order-manager' ),
			'status_manager' => __( 'Status Manager', 'ultimate-order-manager' ),
		);

		return $sections;
	}

	/**
	 *  Output sections tab
	 */
	public function output_uomwoo_settings_sections() {
		global $current_section;

		$sections = $this->get_sections();
		if ( empty( $sections ) || 1 === count( $sections ) ) {
			return;
		}
		echo '<ul class="subsubsub">';
		$array_keys = array_keys( $sections );
		foreach ( $sections as $id => $label ) {
			echo '<li><a href="' . admin_url( 'admin.php?page=wc-settings&tab=' . $this->tab_id . '&section=' . sanitize_title( $id ) ) . '" class="' . ( $current_section === $id ? 'current' : '' ) . '">' . $label . '</a> ' . ( end( $array_keys ) === $id ? '' : '|' ) . ' </li>';
		}
		echo '</ul><br class="clear" />';
	}

    public function uomwoo_status_list() {
        $added_status = OrderStatusManager::uomwoo_get_custom_status();
        ?>
        <h2>All Status <a href="<?php echo admin_url( 'admin.php?page=wc-settings&tab=uomwoo-settings&section=status_manager&status=new' ); ?>" class="page-title-action">Add new status</a></h2>
        <p>List of all added status</p>
        <table class="wp-list-table widefat fixed striped table-view-list">
            <thead>
                <tr>
                    <th>Label</th>
                    <th>Slug</th>
                    <th>Color</th>
                </tr>
            </thead>
            <tbody>
                <?php
				foreach ( $added_status as $status ) {
					$encode_status = $status->option_value;
					$status_array = ( json_decode( $encode_status ) );
                    // dd($status);
					?>
                <tr valign="top">
                    <td>
                        <strong><a href="<?php echo esc_url( admin_url( 'admin.php?page=wc-settings&tab=uomwoo-settings&section=status_manager&status_id=' . $status_array->uomwoo_status_id ) ); ?>"><?php echo esc_html__( $status_array->uomwoo_status_label, 'ultimate-order-manager' ); ?></a></strong>
                        <div class="row-actions">
                            <span class="edit">
                                <a href="<?php echo esc_url( admin_url( 'admin.php?page=wc-settings&tab=uomwoo-settings&section=status_manager&status_id=' . $status_array->uomwoo_status_id ) ); ?>" aria-label="Edit status">Edit</a> | 
                            </span>
                            <span class="trash">
                                <a href="#" class="submitdelete" aria-label="Move “Officiis reiciendis” to the Trash">Trash</a>
                            </span>
                        </div>
                    </td>
                    <td class="forminp forminp-text"><?php echo esc_html( $status_array->uomwoo_status_slug ); ?></td>
                    <td class="forminp forminp-text"><?php echo esc_html( $status_array->uomwoo_status_color ); ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <?php
    }

    public function uomwoo_add_status_form_fields() {
        $settings = array(
            'section_title' => array(
                'name'     => __( 'Status Settings', 'ultimate-order-manager' ),
                'type'     => 'title',
                'desc'     => __( 'Add or update custom status for you store.', 'ultimate-order-manager' ),
                'id'       => 'uomwoo_status_settings_section_title',
                'class' => 'uomwoo-section-title',
            ),
            'uomwoo_status_label' => array(
                'title'    => __( 'Status Label (required)', 'ultimate-order-manager' ),
                'desc'     => __( 'Enter new status name', 'ultimate-order-manager' ),
                'id'       => 'uomwoo_status_label',
                'type'     => 'text',
                'css'      => 'width: 250px;',
                'autoload' => false,
                'desc_tip' => true,
                'custom_attributes' => array( 'required' => 'required' )
            ),
            'uomwoo_status_slug' => array(
                'title'    => __( 'Status Slug (required)', 'ultimate-order-manager' ),
                'desc'     => __( 'Status slug without wc- prefix', 'ultimate-order-manager' ),
                'id'       => 'uomwoo_status_slug',
                'type'     => 'text',
                'css'      => 'width: 250px;',
                'autoload' => false,
                'custom_attributes' => array( 'required' => 'required' ),
            ),
            'uomwoo_status_color' => array(
                'title'    => __( 'Status Color (required)', 'ultimate-order-manager' ),
                'desc'     => __( 'Pick your status color', 'ultimate-order-manager' ),
                'id'       => 'uomwoo_status_color',
                'type'     => 'text',
                'css'      => 'width: 250px;',
                'autoload' => false,
                'desc_tip' => true,
                'default'  => '#777777',
                'class' => 'colorpick',
                'custom_attributes' => array( 'required' => 'required' ),
            ),
            'section_end' => array(
                'type' => 'sectionend',
                'id' => 'uomwoo_status_settings_section_end',
            ),
        );
        return $settings;
    }

    public function uomwoo_edit_status_form_fields( $status_id ) {
        $status = OrderStatusManager::uomwoo_get_custom_status_by_id( $status_id );
        $encode_status = $status->option_value;
        $status_array = ( json_decode( $encode_status ) );
        
        $settings = array(
            'section_title' => array(
                'name'     => __( 'Status Settings', 'ultimate-order-manager' ),
                'type'     => 'title',
                'desc'     => __( 'Add or update custom status for you store.', 'ultimate-order-manager' ),
                'id'       => 'uomwoo_status_settings_section_title',
                'class' => 'uomwoo-section-title',
            ),
            'uomwoo_status_label' => array(
                'title'    => __( 'Status Label (required)', 'ultimate-order-manager' ),
                'desc'     => __( 'Enter new status name', 'ultimate-order-manager' ),
                'id'       => 'uomwoo_status_label',
                'type'     => 'text',
                'default'     => sanitize_text_field( $status_array->uomwoo_status_label ),
                'css'      => 'width: 250px;',
                'autoload' => false,
                'desc_tip' => true,
                'custom_attributes' => array( 'required' => 'required' )
            ),
            'uomwoo_status_slug' => array(
                'title'    => __( 'Status Slug (required)', 'ultimate-order-manager' ),
                'desc'     => __( 'Status slug without wc- prefix', 'ultimate-order-manager' ),
                'id'       => 'uomwoo_status_slug',
                'type'     => 'text',
                'default'     => sanitize_text_field( $status_array->uomwoo_status_slug ),
                'css'      => 'width: 250px;',
                'autoload' => false,
                'custom_attributes' => array( 'required' => 'required' ),
            ),
            'uomwoo_status_color' => array(
                'title'    => __( 'Status Color (required)', 'ultimate-order-manager' ),
                'desc'     => __( 'Pick your status color', 'ultimate-order-manager' ),
                'id'       => 'uomwoo_status_color',
                'type'     => 'text',
                'default'     => sanitize_text_field( $status_array->uomwoo_status_color ),
                'css'      => 'width: 250px;',
                'autoload' => false,
                'desc_tip' => true,
                'default'  => '#777777',
                'class' => 'colorpick',
                'custom_attributes' => array( 'required' => 'required' ),
            ),
            'section_end' => array(
                'type' => 'sectionend',
                'id' => 'uomwoo_status_settings_section_end',
            ),
        );
        return $settings;
    }

    public function uomwoo_add_general_settings_form_fields() {
        $switch_markup = '<span class="uomwoo-switch"><span class="switch-text on">On</span><span class="switch-text off">Off</span></span>';

            $settings = array(
                'section_title' => array(
                    'name'     => __( 'Order Table General Settings', 'ultimate-order-manager' ),
                    'type'     => 'title',
                    'desc'     => __( 'Configure the settings below to show or hide WooCommerce order columns.', 'ultimate-order-manager' ),
                    'id'       => 'uomwoo_general_settings_section_title',
                    'class' => 'uomwoo-section-title',
                ),
                'uomwoo_order_summary' => array(
                    'name'     => __( 'Show Order Summary', 'ultimate-order-manager' ),
                    'id'       => 'uomwoo_order_summary',
                    'type'     => 'checkbox',
                    'class' => 'uomwoo-checkbox',
                    'desc' => $switch_markup,
                ),
                'uomwoo_order_shipping_cost' => array(
                    'name'     => __( 'Show Order Shipping Cost', 'ultimate-order-manager' ),
                    'id'       => 'uomwoo_order_shipping_cost',
                    'type'     => 'checkbox',
                    'class' => 'uomwoo-checkbox',
                    'desc' => $switch_markup,
                ),
                'uomwoo_order_note' => array(
                    'name'     => __( 'Show Customer Note', 'ultimate-order-manager' ),
                    'id'       => 'uomwoo_order_note',
                    'type'     => 'checkbox',
                    'class' => 'uomwoo-checkbox',
                    'desc' => $switch_markup,
                ),
                'uomwoo_order_pm' => array(
                    'name'     => __( 'Show Payment Method', 'ultimate-order-manager' ),
                    'id'       => 'uomwoo_order_pm',
                    'type'     => 'checkbox',
                    'class' => 'uomwoo-checkbox',
                    'desc' => $switch_markup,
                ),
                'uomwoo_order_coupon' => array(
                    'name'     => __( 'Show Applied Coupon', 'ultimate-order-manager' ),
                    'id'       => 'uomwoo_order_coupon',
                    'type'     => 'checkbox',
                    'class' => 'uomwoo-checkbox',
                    'desc' => $switch_markup,
                ),
                'uomwoo_compact_mode' => array(
                    'name'     => __( 'Compact Order Table', 'ultimate-order-manager' ),
                    'id'       => 'uomwoo_compact_mode',
                    'type'     => 'checkbox',
                    'class' => 'uomwoo-checkbox',
                    'desc' => $switch_markup,
                ),
                'uomwoo_customer_ip' => array(
                    'name'     => __( 'Show Customer IP', 'ultimate-order-manager' ),
                    'id'       => 'uomwoo_customer_ip',
                    'type'     => 'checkbox',
                    'class' => 'uomwoo-checkbox',
                    'desc' => $switch_markup,
                ),
                'uomwoo_customer_os_browser' => array(
                    'name'     => __( 'Show Customer OS & Browser', 'ultimate-order-manager' ),
                    'id'       => 'uomwoo_customer_os_browser',
                    'type'     => 'checkbox',
                    'class' => 'uomwoo-checkbox',
                    'desc' => $switch_markup,
                ),
                'uomwoo_customer_phone' => array(
                    'name'     => __( 'Show Customer Phone', 'ultimate-order-manager' ),
                    'id'       => 'uomwoo_customer_phone',
                    'type'     => 'checkbox',
                    'class' => 'uomwoo-checkbox',
                    'desc' => $switch_markup,
                ),
                'uomwoo_customer_email' => array(
                    'name'     => __( 'Show Customer Email', 'ultimate-order-manager' ),
                    'id'       => 'uomwoo_customer_email',
                    'type'     => 'checkbox',
                    'class' => 'uomwoo-checkbox',
                    'desc' => $switch_markup,
                ),
                'section_end' => array(
                    'type' => 'sectionend',
                    'id' => 'uomwoo_general_settings_section_end',
                ),
            );
            return apply_filters( 'uomwoo_settings_tab_general_settings', $settings );
    }

    public function uomwoo_get_general_settings_fields() {
        global $current_section, $hide_save_button;

		if ( 'status_manager' === $current_section ) {
            if ( isset( $_REQUEST['status'] ) && $_REQUEST['status'] === 'new' ) {
                return $this->uomwoo_add_status_form_fields();
            }else if ( isset( $_REQUEST['status_id'] ) ) {
                return $this->uomwoo_edit_status_form_fields( sanitize_text_field( $_REQUEST['status_id'] ) );
            } else {
                $hide_save_button = true;
                $this->uomwoo_status_list();
                return [];
            }
		} else {
            return $this->uomwoo_add_general_settings_form_fields();
        }
    }

    /**
     * Output plugin settings fields
     *
     * @return void
     */
    public function uomwoo_settings_fields_output() {
        WC_Admin_Settings::output_fields( $this->uomwoo_get_general_settings_fields() );
    }

    /**
     * Save plugin settings
     *
     * @return void
     */
    public function uomwoo_save_settings() {
        global $current_section;
        if ( 'status_manager' === $current_section && isset( $_REQUEST['status'] ) && 'new' === $_REQUEST['status'] ) {
            if ( isset( $_REQUEST['uomwoo_status_label'], $_REQUEST['uomwoo_status_slug'], $_REQUEST['uomwoo_status_color'] ) && ! empty( $_REQUEST['uomwoo_status_label'] ) && ! empty( $_REQUEST['uomwoo_status_slug'] ) && ! empty( $_REQUEST['uomwoo_status_color'] ) ) {
                $added_status = OrderStatusManager::uomwoo_get_custom_status();
                $status_counter = count( $added_status ) > 0 ? count( $added_status ) + 1 : 1;
                $new_status = [];
                $new_status['uomwoo_status_id'] = sanitize_text_field( $status_counter );
                $new_status['uomwoo_status_label'] = sanitize_text_field( $_REQUEST['uomwoo_status_label'] );
                $new_status['uomwoo_status_slug'] = sanitize_text_field( $_REQUEST['uomwoo_status_slug'] );
                $new_status['uomwoo_status_color'] = sanitize_text_field( $_REQUEST['uomwoo_status_color'] );
                update_option( 'uomwoo_custom_status_' . $status_counter, json_encode( $new_status ) );
            } else {
                // Redirect to status add page to avoid settings save actions.
                wp_safe_redirect( admin_url( 'admin.php?page=wc-settings&tab=uomwoo-settings&section=status_manager' ) );
                exit();
            }
        }else if ( 'status_manager' === $current_section && isset( $_REQUEST['status_id'] ) ) {
            if ( isset( $_REQUEST['uomwoo_status_label'], $_REQUEST['uomwoo_status_slug'], $_REQUEST['uomwoo_status_color'] ) && ! empty( $_REQUEST['uomwoo_status_label'] ) && ! empty( $_REQUEST['uomwoo_status_slug'] ) && ! empty( $_REQUEST['uomwoo_status_color'] ) ) {
                $new_status = [];
                $new_status['uomwoo_status_label'] = sanitize_text_field( $_REQUEST['uomwoo_status_label'] );
                $new_status['uomwoo_status_slug'] = sanitize_text_field( $_REQUEST['uomwoo_status_slug'] );
                $new_status['uomwoo_status_color'] = sanitize_text_field( $_REQUEST['uomwoo_status_color'] );
                update_option( 'uomwoo_custom_status_' . sanitize_text_field( $_REQUEST['status_id'] ), json_encode( $new_status ) );
            } else {
                // Redirect to status add page to avoid settings save actions.
                wp_safe_redirect( admin_url( 'admin.php?page=wc-settings&tab=uomwoo-settings&section=status_manager' ) );
                exit();
            }
        } else {
            WC_Admin_Settings::save_fields( self::uomwoo_get_general_settings_fields() );
        }

        if ( $current_section ) {
            do_action( 'woocommerce_update_options_' . $this->tab_id . '_' . $current_section );
        }
    }

    /**
     * Update status save success message
     *
     * @param string $translated_text
     * @param string $text
     * @param string $domain
     * @return void
     */
    public function uomwoo_change_status_save_settings_text( $translated_text, $text, $domain ) {
        if ( ( $domain === 'woocommerce' ) && isset( $_REQUEST['section'] ) && ( $_REQUEST['section'] === 'status_manager' ) ) {
            switch ( $translated_text ) {
                case 'Your settings have been saved.':
                    $translated_text = __( 'Status successfully added.', 'ultimate-order-manager' );
                    break;
            }
        }
        return $translated_text;
    }
}