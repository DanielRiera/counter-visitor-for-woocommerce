<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WCVisitor_Ajax_Controller {
    private $service;

    public function __construct( WCVisitor_Counter_Service $service ) {
        $this->service = $service;
    }

    public function register_hooks() {
        add_action( 'wp_ajax_nopriv_wcvisitor_get_counter', array( $this, 'get_counter' ) );
        add_action( 'wp_ajax_wcvisitor_get_counter', array( $this, 'get_counter' ) );
    }

    public function get_counter() {
        $product = isset( $_POST['product'] ) ? absint( wp_unslash( $_POST['product'] ) ) : 0;
        $result  = array();

        if ( ! $product || ! function_exists( 'wc_get_product' ) || 'publish' !== get_post_status( $product ) || post_password_required( $product ) || ! wc_get_product( $product ) ) {
            wp_send_json( $result );
            return;
        }

        $refresh = ! empty( $_POST['refresh'] );
        $this->service->record_product_view( $product, $refresh );
        $args = array();

        foreach ( array( 'msgone', 'msgmore' ) as $key ) {
            if ( isset( $_POST[ $key ] ) && is_string( $_POST[ $key ] ) ) {
                $args[ $key ] = sanitize_textarea_field( wp_unslash( $_POST[ $key ] ) );
            }
        }

        $html = $this->service->render_counter_block( $product, $args );
        $result = array(
            'html'    => (string) $html,
            'text'    => (string) $this->service->get_after_price_text( $product ),
            'counter' => $this->service->get_counter(),
        );

        wp_send_json( $result );
    }
}
