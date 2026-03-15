<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WCVisitor_API' ) ) {
    class WCVisitor_API {
        public function get_counter() {
            return wcvisitor()->get_service()->get_counter();
        }

        public function render( $product_id ) {
            return wcvisitor()->get_service()->render_counter_block( $product_id );
        }
    }
}
