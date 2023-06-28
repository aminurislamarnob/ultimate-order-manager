<?php

namespace AiArnob\UltimateOrderManager;

class OrderTableManager {
    public function __construct() {
        add_filter( 'manage_edit-shop_order_columns', array( $this, 'uomwoo_add_new_order_admin_list_column' ), 10 );
        add_action( 'manage_shop_order_posts_custom_column', array( $this, 'uomwoo_add_new_order_admin_list_column_content' ) );
    }

    public function uomwoo_add_new_order_admin_list_column( $columns ) {
        //Show Order Summary
        $uomwoo_order_summary = get_option( 'uomwoo_order_summary' );

        if ( 'yes' === $uomwoo_order_summary ) {
            $columns['uomwoo_order_items'] = esc_html__( 'Order Items', 'ultimate-order-manager' );
        }

        //Show Order Shipping Cost
        $uomwoo_order_shipping_cost = get_option( 'uomwoo_order_shipping_cost' );
        if ( 'yes' === $uomwoo_order_shipping_cost ) {
            $columns['uomwoo_order_shipping_cost'] = esc_html__( 'Shipping Cost', 'ultimate-order-manager' );
        }

        //Show Order Shipping Cost
        $uomwoo_order_note = get_option( 'uomwoo_order_note' );
        if ( 'yes' === $uomwoo_order_note ) {
            $columns['uomwoo_customer_order_note'] = esc_html__( 'Order Note', 'ultimate-order-manager' );
        }

        //Show Order Payment Method
        $uomwoo_order_pm = get_option( 'uomwoo_order_pm' );
        if ( 'yes' === $uomwoo_order_pm ) {
            $columns['uomwoo_payment_methods'] = esc_html__( 'Payment Method', 'ultimate-order-manager' );
        }

        //Show Applied Coupon
        $uomwoo_order_coupon = get_option( 'uomwoo_order_coupon' );
        if ( 'yes' === $uomwoo_order_coupon ) {
            $columns['uomwoo_order_coupon'] = esc_html__( 'Coupon', 'ultimate-order-manager' );
        }

        //Show customer IP address
        $uomwoo_customer_ip = get_option( 'uomwoo_customer_ip' );
        if ( 'yes' === $uomwoo_customer_ip ) {
            $columns['uomwoo_customer_ip_address'] = esc_html__( 'IP Address', 'ultimate-order-manager' );
        }

        //Show Customer Browser & OS Checkbox
        $uomwoo_customer_os_browser = get_option( 'uomwoo_customer_os_browser' );
        if ( 'yes' === $uomwoo_customer_os_browser ) {
            $columns['uomwoo_customer_user_agent'] = esc_html__( 'Customer Device, OS, Browser', 'ultimate-order-manager' );
        }

        //Show Customer Phone
        $uomwoo_customer_phone = get_option( 'uomwoo_customer_phone' );
        if ( 'yes' === $uomwoo_customer_phone ) {
            $columns['uomwoo_customer_billing_phone'] = esc_html__( 'Phone', 'ultimate-order-manager' );
        }

        //Show Customer Email
        $uomwoo_customer_email = get_option( 'uomwoo_customer_email' );
        if ( 'yes' === $uomwoo_customer_email ) {
            $columns['uomwoo_customer_billing_email'] = esc_html__( 'Email', 'ultimate-order-manager' );
        }

        update_option( 'uomwoo_order_columns', $columns );

        return $columns;
    }

    public function uomwoo_add_new_order_admin_list_column_content( $column ) {
        global $post;
        $order = wc_get_order( $post->ID );
        $browser = new \foroco\BrowserDetection();

        //Show Order Summary [Order Items]
        if ( 'uomwoo_order_items' === $column ) {
            foreach ( $order->get_items() as $item_id => $item ) {
                echo '<a href="' . get_permalink( $item->get_product_id() ) . '" target="_blank">' . $item->get_name() . '</a> x ' . $item->get_quantity() . '<br>';
            }
        }

        //Show Order Shipping Cost
        if ( 'uomwoo_order_shipping_cost' === $column ) {
            echo $order->get_shipping_total();
        }

        //Show Customer Order Note
        if ( 'uomwoo_customer_order_note' === $column ) {
            echo $order->get_customer_note();
        }

        //Show Order Payment Method
        if ( 'uomwoo_payment_methods' === $column ) {
            echo $order->get_payment_method_title();
        }

        //Show Order Payment Method
        if ( 'uomwoo_order_coupon' === $column ) {
            if ( count( $order->get_coupon_codes() ) > 0 ) {
                foreach ( $order->get_coupon_codes() as $key => $coupon_code ) {
                    // Get the WC_Coupon object
                    $coupon = new \WC_Coupon( $coupon_code );
                    $discount_type = $coupon->get_discount_type(); // Get coupon discount type
                    $coupon_amount = $coupon->get_amount(); // Get coupon amount
                    echo sprintf( __( 'Code: %1$s, Type: %2$s, Amount: %3$s', 'ultimate-order-manager' ), $coupon_code, $discount_type, $coupon_amount );

                    if ( ( count( $order->get_coupon_codes() ) - 1 ) !== $key ) {
                        echo wp_kses_post( '<hr/>' );
                    }
                }
            }
        }

        //Show Customer IP Address
        if ( 'uomwoo_customer_ip_address' === $column ) {
            echo $order->get_customer_ip_address();
        }

        //Show Customer Browser & OS
        if ( 'uomwoo_customer_user_agent' === $column ) {
            $os_info = $browser->getOS( $order->get_customer_user_agent() );
            $browser_info = $browser->getBrowser( $order->get_customer_user_agent() );
            $device_type = $browser->getDevice( $order->get_customer_user_agent() );
            echo sprintf( __( 'Device: %1$s. %2$s OS: %3$s. %4$s Browser: %5$s %6$s.', 'ultimate-order-manager' ), ucfirst( $device_type['device_type'] ), '<br>', $os_info['os_name'], '<br>', $browser_info['browser_name'], $browser_info['browser_version'] );
        }

        //Show Customer Phone
        if ( 'uomwoo_customer_billing_phone' === $column ) {
            echo $order->get_billing_phone();
        }

        //Show Customer Email Address
        if ( 'uomwoo_customer_billing_email' === $column ) {
            echo $order->get_billing_email();
        }
    }
}
