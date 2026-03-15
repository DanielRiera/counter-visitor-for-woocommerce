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

        if ( $product > 0 ) {
            $html = $this->service->render_counter_block( $product );

            if ( $html ) {
                $result = array(
                    'html'    => $html,
                    'counter' => $this->service->get_counter(),
                );
            }
        }

        wp_send_json( $result );
    }
}
