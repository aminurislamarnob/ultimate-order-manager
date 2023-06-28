<?php
namespace AiArnob\UltimateOrderManager;

/**
 * Enqueue public/frontend styles and scripts
 */
class OrderStatusManager {

    public function __construct() {
        add_filter( 'init', array( $this, 'uomwoo_add_custom_order_status' ) );
        add_filter( 'wc_order_statuses', array( $this, 'uomwoo_add_custom_status_to_order_statuses' ) );
        add_filter( 'bulk_actions-edit-shop_order', array( $this, 'uomwoo_register_custom_status_bulk_action' ) );
        add_action( 'handle_bulk_actions-edit-shop_order', array( $this, 'uomwoo_bulk_process_custom_status' ), 20, 3 );
        add_action( 'admin_notices', array( $this, 'uomwoo_custom_order_status_admin_notices' ) );
    }

    /**
     * Get custom status list
     *
     * @return object|array
     */
    public static function uomwoo_get_custom_status() {
        global $wpdb;
        return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}options WHERE option_name LIKE 'uomwoo_custom_status_%'" ) );
    }

    /**
     * Get custom status by option id
     *
     * @param number $option_id
     * @return object|array
     */
    public static function uomwoo_get_custom_status_by_id( $status_id ) {
        global $wpdb;
        return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}options WHERE option_name LIKE 'uomwoo_custom_status_{$status_id}'" ) );
    }

    /**
     * Add custom status for woocommerce order
     *
     * @return void
     */
    public function uomwoo_add_custom_order_status() {
        register_post_status(
            'wc-arrival-shipment', array(
				'label'                     => 'Shipment Arrival',
				'public'                    => true,
				'show_in_admin_status_list' => true,
				'show_in_admin_all_list'    => true,
				'exclude_from_search'       => false,
				'label_count'               => _n_noop( 'Shipment Arrival <span class="count">(%s)</span>', 'Shipment Arrival <span class="count">(%s)</span>' ),
            )
        );
    }

    /**
     * Add custom created status to order status dropdown
     *
     * @param array $order_statuses
     * @return void
     */
    public function uomwoo_add_custom_status_to_order_statuses( $order_statuses ) {
        $new_order_statuses = array();
        foreach ( $order_statuses as $key => $status ) {
            $new_order_statuses[ $key ] = $status;
            if ( 'wc-processing' === $key ) {
                $new_order_statuses['wc-arrival-shipment'] = 'Shipment Arrival';
            }
        }
        return $new_order_statuses;
    }

    /**
     * Add custom status bulk action to order list page bulk action dropdown
     *
     * @param array $bulk_actions
     * @return void
     */
    public function uomwoo_register_custom_status_bulk_action( $bulk_actions ) {
        $bulk_actions['mark_shipment_arrival'] = 'Change status to shipment arrival';
	    return $bulk_actions;
    }

    /**
     * Process bulk action for custom status
     *
     * @param string $redirect
     * @param string $doaction
     * @param array $object_ids
     * @return void
     */
    public function uomwoo_bulk_process_custom_status( $redirect, $doaction, $object_ids ) {
        if ( 'mark_shipment_arrival' === $doaction ) {
            // change status of every selected order
            foreach ( $object_ids as $order_id ) {
                $order = wc_get_order( $order_id );
                $order->update_status( 'wc-arrival-shipment' );
            }

            // add query args to URL to show admin notices
            $redirect = add_query_arg(
                array(
                    'bulk_action' => 'marked_shipment_arrival',
                    'changed' => count( $object_ids ),
                ),
                $redirect
            );
        }
        return $redirect;
    }

    /**
     * Order bulk status update admin notice
     *
     * @return void
     */
    public function uomwoo_custom_order_status_admin_notices() {
        if ( isset( $_REQUEST['bulk_action'] )
            && 'marked_shipment_arrival' === $_REQUEST['bulk_action']
            && isset( $_REQUEST['changed'] )
            && $_REQUEST['changed']
        ) {

            // displaying the message
            printf(
                '<div id="message" class="updated notice is-dismissible"><p>' . _n( '%d order status changed.', '%d order statuses changed.', $_REQUEST['changed'] ) . '</p></div>',
                $_REQUEST['changed']
            );
        }
    }
}
