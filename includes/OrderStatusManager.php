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
        $custom_status = self::uomwoo_get_custom_status();

        foreach ( $custom_status as $status ) {
            $encode_status = $status->option_value;
            $status_array = ( json_decode( $encode_status ) );
			register_post_status(
                'wc-' . $status_array->uomwoo_status_slug, array(
					'label'                     => $status_array->uomwoo_status_label,
					'public'                    => true,
					'show_in_admin_status_list' => true,
					'show_in_admin_all_list'    => true,
					'exclude_from_search'       => false,
					'label_count'               => _n_noop( $status_array->uomwoo_status_label . ' <span class="count">(%s)</span>', $status_array->uomwoo_status_label . ' <span class="count">(%s)</span>' ),
                )
			);
		}
    }

    /**
     * Add custom created status to order status dropdown
     *
     * @param array $order_statuses
     * @return void
     */
    public function uomwoo_add_custom_status_to_order_statuses( $order_statuses ) {
        $custom_status = self::uomwoo_get_custom_status();
        $new_order_statuses = array();
        foreach ( $order_statuses as $key => $status ) {
            $new_order_statuses[ $key ] = $status;
            if ( 'wc-processing' === $key ) {
                foreach ( $custom_status as $status ) {
                    $encode_status = $status->option_value;
                    $status_array = ( json_decode( $encode_status ) );
					$new_order_statuses[ 'wc-' . $status_array->uomwoo_status_slug ] = $status_array->uomwoo_status_label;
                }
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
        $custom_status = self::uomwoo_get_custom_status();
        foreach ( $custom_status as $status ) {
            $encode_status = $status->option_value;
            $status_array = ( json_decode( $encode_status ) );
			$bulk_actions[ 'mark_' . $status_array->uomwoo_status_slug ] = 'Change status to ' . $status_array->uomwoo_status_label;
        }
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
        $custom_status = self::uomwoo_get_custom_status();
        foreach ( $custom_status as $status ) {
            $encode_status = $status->option_value;
            $status_array = ( json_decode( $encode_status ) );
			if ( 'mark_' . $status_array->uomwoo_status_slug === $doaction ) {
				// change status of every selected order
				foreach ( $object_ids as $order_id ) {
					$order = wc_get_order( $order_id );
					$order->update_status( 'wc-' . $status_array->uomwoo_status_slug );
				}

				// add query args to URL to show admin notices
				$redirect = add_query_arg(
                    array(
						'bulk_action' => 'marked_' . $status_array->uomwoo_status_slug,
						'changed' => count( $object_ids ),
                    ),
                    $redirect
				);
			}
		}
        return $redirect;
    }
}
