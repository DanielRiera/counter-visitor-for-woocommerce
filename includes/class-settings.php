<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WCVisitor_Settings {
    const OPTION_USE_JS = '_wcv_use_js';
    const OPTION_LIVE_MODE = '_wcv_live_mode';
    const OPTION_FONTAWESOME = '_wcv_fontawesome';
    const OPTION_AFTER_PRICE = '_wcvisitor_after_price';
    const OPTION_WEIGHT_BLOCK = '_wcv_weight_block';
    const OPTION_POSITION = '_wcv_position';
    const OPTION_TIMEOUT = '_wcv_timeout_limit';
    const OPTION_ICON = '_wcv_icon';
    const OPTION_MESSAGE = '_wcv_message';
    const OPTION_MESSAGE_ONE = '_wcv_message_one';
    const OPTION_ONLY_ONE_HIDE = '_wcvisitor_only_one_hide';
    const OPTION_FAKE_MODE = '_wcv_fake_mode';
    const OPTION_FAKE_FROM = '_wcv_fake_mode_from';
    const OPTION_FAKE_TO = '_wcv_fake_mode_to';
    const OPTION_LIVE_SECONDS = '_wcv_live_seconds';
    const OPTION_NEWSLETTER = 'counter-visitor-newsletter';

    private $defaults = array(
        self::OPTION_USE_JS       => '0',
        self::OPTION_LIVE_MODE    => '0',
        self::OPTION_FONTAWESOME  => '0',
        self::OPTION_AFTER_PRICE  => '0',
        self::OPTION_WEIGHT_BLOCK => 0,
        self::OPTION_POSITION     => 'woocommerce_after_add_to_cart_button',
        self::OPTION_TIMEOUT      => 300,
        self::OPTION_ICON         => 'dashicons dashicons-visibility',
        self::OPTION_MESSAGE      => '%n people are viewing this product',
        self::OPTION_MESSAGE_ONE  => '1 user are viewing this product',
        self::OPTION_ONLY_ONE_HIDE => '0',
        self::OPTION_FAKE_MODE    => '0',
        self::OPTION_FAKE_FROM    => 0,
        self::OPTION_FAKE_TO      => 0,
        self::OPTION_LIVE_SECONDS => 5,
        self::OPTION_NEWSLETTER   => '0',
    );

    public function get( $key ) {
        $default = isset( $this->defaults[ $key ] ) ? $this->defaults[ $key ] : '';

        return get_option( $key, $default );
    }

    public function is_enabled( $key ) {
        return '1' === (string) $this->get( $key );
    }

    public function get_timeout() {
        return max( 30, absint( $this->get( self::OPTION_TIMEOUT ) ) );
    }

    public function get_live_seconds() {
        return max( 5, absint( $this->get( self::OPTION_LIVE_SECONDS ) ) );
    }

    public function get_weight() {
        return absint( $this->get( self::OPTION_WEIGHT_BLOCK ) );
    }

    public function get_position() {
        return (string) $this->get( self::OPTION_POSITION );
    }

    public function get_icon() {
        return (string) $this->get( self::OPTION_ICON );
    }

    public function get_fake_range() {
        $from = absint( $this->get( self::OPTION_FAKE_FROM ) );
        $to   = absint( $this->get( self::OPTION_FAKE_TO ) );

        if ( $to < $from ) {
            $to = $from;
        }

        return array( $from, $to );
    }
}
