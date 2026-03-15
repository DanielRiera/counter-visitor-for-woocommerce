<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WCVisitor_Counter_Service {
    private $settings;
    private $repository;
    private $counter = 0;
    private $positions = array(
        'woocommerce_after_add_to_cart_button'      => 'form.cart|inside',
        'woocommerce_before_add_to_cart_button'     => 'form.cart|before',
        'woocommerce_product_meta_end'              => 'div.product_meta|after',
        'woocommerce_before_single_product_summary' => 'div.woocommerce-notices-wrapper|after',
        'woocommerce_after_single_product_summary'  => 'div.woocommerce-tabs|after',
        'woocommerce_single_product_summary'        => 'div.woocommerce-product-details__short-description|inside',
        'woocommerce_product_thumbnails'            => 'div.woocommerce-product-gallery|inside',
        'deactivate'                                => 'false',
    );

    public function __construct( WCVisitor_Settings $settings, WCVisitor_Counter_Repository $repository ) {
        $this->settings   = $settings;
        $this->repository = $repository;
    }

    public function get_positions() {
        return $this->positions;
    }

    public function get_counter() {
        return $this->counter;
    }

    public function should_use_js() {
        return $this->settings->is_enabled( WCVisitor_Settings::OPTION_USE_JS );
    }

    public function should_use_live_mode() {
        return $this->settings->is_enabled( WCVisitor_Settings::OPTION_LIVE_MODE );
    }

    public function should_load_fontawesome() {
        return $this->settings->is_enabled( WCVisitor_Settings::OPTION_FONTAWESOME );
    }

    public function should_show_after_price() {
        return $this->settings->is_enabled( WCVisitor_Settings::OPTION_AFTER_PRICE );
    }

    public function get_position() {
        return $this->settings->get_position();
    }

    public function get_weight() {
        return $this->settings->get_weight();
    }

    public function get_live_seconds() {
        return $this->settings->get_live_seconds();
    }

    public function record_product_view( $product_id ) {
        $product_id = absint( $product_id );

        if ( ! $product_id || $this->settings->is_enabled( WCVisitor_Settings::OPTION_FAKE_MODE ) ) {
            return;
        }

        $visitor_hash = $this->get_visitor_hash();

        if ( ! $visitor_hash ) {
            return;
        }

        $this->repository->record_visit( $product_id, $visitor_hash, gmdate( 'Y-m-d H:i:s' ) );
    }

    public function render_counter_block( $product_id, $args = array() ) {
        $product_id = absint( $product_id );

        if ( ! $product_id ) {
            return false;
        }

        $args = is_array( $args ) ? $args : array();

        if ( $this->settings->is_enabled( WCVisitor_Settings::OPTION_FAKE_MODE ) ) {
            $this->counter = $this->get_fake_counter( $product_id );
        } else {
            $cutoff        = gmdate( 'Y-m-d H:i:s', time() - $this->settings->get_timeout() );
            $this->counter = $this->repository->count_active_visitors( $product_id, $cutoff );
        }

        if ( $this->counter <= 1 ) {
            $this->counter = 1;
            $message       = ! empty( $args['msgone'] ) ? (string) $args['msgone'] : (string) $this->settings->get( WCVisitor_Settings::OPTION_MESSAGE_ONE );
            $message       = str_replace( '1', '<span class="wcvisitor_num">1</span>', $message );
        } else {
            $message = ! empty( $args['msgmore'] ) ? (string) $args['msgmore'] : (string) $this->settings->get( WCVisitor_Settings::OPTION_MESSAGE );
            $message = str_replace( '%n', '<span class="wcvisitor_num">' . $this->counter . '</span>', $message );
        }

        if ( $this->settings->is_enabled( WCVisitor_Settings::OPTION_ONLY_ONE_HIDE ) && 1 === $this->counter ) {
            return false;
        }

        if ( ! empty( $args['onlytext'] ) ) {
            return '<span class="wcv-only-text">' . wp_kses_post( $message ) . '</span>';
        }

        return $this->build_counter_markup(
            array(
                'product' => $product_id,
                'icon'    => $this->settings->get_icon(),
                'msg'     => $message,
            )
        );
    }

    public function get_after_price_text( $product_id ) {
        return $this->render_counter_block(
            $product_id,
            array(
                'onlytext' => true,
            )
        );
    }

    public function get_position_payload() {
        $position = $this->get_position();

        if ( ! isset( $this->positions[ $position ] ) ) {
            return false;
        }

        return explode( '|', $this->positions[ $position ] );
    }

    public function get_stats( $period = 'all', $limit = 20, $page = 1 ) {
        $from_gmt = $this->get_period_start( $period );
        $page     = max( 1, absint( $page ) );
        $limit    = max( 1, absint( $limit ) );
        $offset   = ( $page - 1 ) * $limit;

        return array(
            'rows'         => $this->repository->get_top_products( $from_gmt, $limit, $offset ),
            'total'        => $this->repository->count_top_products( $from_gmt ),
            'per_page'     => $limit,
            'current_page' => $page,
            'summary'      => $this->repository->get_period_summary( $from_gmt ),
        );
    }

    public function get_stats_summary( $product_id, $period = 'all' ) {
        return $this->repository->get_product_stats( $product_id, $this->get_period_start( $period ) );
    }

    private function get_period_start( $period ) {
        $map = array(
            '2m'  => 2 * MINUTE_IN_SECONDS,
            '5m'  => 5 * MINUTE_IN_SECONDS,
            '24h' => DAY_IN_SECONDS,
            '7d'  => WEEK_IN_SECONDS,
            '30d' => 30 * DAY_IN_SECONDS,
        );

        if ( ! isset( $map[ $period ] ) ) {
            return null;
        }

        return gmdate( 'Y-m-d H:i:s', time() - $map[ $period ] );
    }

    private function get_fake_counter( $product_id ) {
        $cookie_name = 'pro_wcv_' . $product_id;

        if ( isset( $_COOKIE[ $cookie_name ] ) ) {
            return absint( wp_unslash( $_COOKIE[ $cookie_name ] ) );
        }

        list( $from, $to ) = $this->settings->get_fake_range();
        $counter           = wp_rand( $from, $to );
        $expiration        = time() + ( $this->should_use_live_mode() ? 5 : 60 );

        $this->set_cookie( $cookie_name, (string) $counter, $expiration );

        return $counter;
    }

    private function set_cookie( $name, $value, $expiration ) {
        if ( headers_sent() ) {
            return;
        }

        $secure   = is_ssl();
        $httponly = true;
        $path     = defined( 'COOKIEPATH' ) && COOKIEPATH ? COOKIEPATH : '/';
        $domain   = defined( 'COOKIE_DOMAIN' ) ? COOKIE_DOMAIN : '';

        setcookie(
            $name,
            $value,
            array(
                'expires'  => (int) $expiration,
                'path'     => $path,
                'domain'   => $domain,
                'secure'   => $secure,
                'httponly' => $httponly,
                'samesite' => 'Lax',
            )
        );

        $_COOKIE[ $name ] = $value;
    }

    private function get_visitor_hash() {
        $ip         = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
        $forwarded  = isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) : '';
        $agent      = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';
        $user_id    = get_current_user_id();
        $raw_string = implode( '|', array( $forwarded, $ip, $agent, $user_id ) );

        if ( '' === trim( $raw_string, '|' ) ) {
            return '';
        }

        return hash( 'sha256', wp_hash( $raw_string ) );
    }

    private function build_counter_markup( $data ) {
        $product_class = 'wcv-product-' . absint( $data['product'] );
        $icon_classes  = implode(
            ' ',
            array_filter(
                array_map(
                    'sanitize_html_class',
                    preg_split( '/\s+/', (string) $data['icon'] )
                )
            )
        );

        return sprintf(
            '<div class="wcv-message %1$s" data-counter="%2$d"><span class="icon"><i class="%3$s"></i></span>%4$s</div>',
            esc_attr( $product_class ),
            absint( $this->counter ),
            esc_attr( $icon_classes ),
            wp_kses_post( $data['msg'] )
        );
    }
}
