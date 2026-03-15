<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WCVisitor_Plugin {
    const DB_VERSION = '1.0.0';

    private $settings;
    private $repository;
    private $service;
    private $admin_controller;
    private $ajax_controller;

    public function __construct() {
        global $wpdb;

        $this->settings         = new WCVisitor_Settings();
        $this->repository       = new WCVisitor_Counter_Repository( $wpdb );
        $this->service          = new WCVisitor_Counter_Service( $this->settings, $this->repository );
        $this->admin_controller = new WCVisitor_Admin_Controller( $this->settings, $this->service );
        $this->ajax_controller  = new WCVisitor_Ajax_Controller( $this->service );

        $this->register_hooks();
    }

    public function get_service() {
        return $this->service;
    }

    public function activate() {
        $this->repository->create_table();
        update_option( 'wcvisitor_db_version', self::DB_VERSION );
    }

    public function deactivate() {
    }

    public function load_textdomain() {
        $domain = 'counter-visitor-for-woocommerce';
        $locale = function_exists( 'determine_locale' ) ? determine_locale() : get_locale();
        $mofile = WCVISITOR_PATH . 'languages/' . $domain . '-' . $locale . '.mo';

        unload_textdomain( $domain );

        if ( file_exists( $mofile ) ) {
            load_textdomain( $domain, $mofile );
            return;
        }

        load_plugin_textdomain( $domain, false, dirname( WCVISITOR_BASENAME ) . '/languages/' );
    }

    public function maybe_upgrade() {
        if ( get_option( 'wcvisitor_db_version' ) !== self::DB_VERSION ) {
            $this->repository->create_table();
            update_option( 'wcvisitor_db_version', self::DB_VERSION );
        }
    }

    public function enqueue_assets() {
        if ( ! is_product() ) {
            return;
        }

        if ( $this->service->should_use_js() || $this->service->should_use_live_mode() ) {
            wp_enqueue_script(
                'wcvisitor-scripts',
                WCVISITOR_URL . 'assets/scripts.js',
                array( 'jquery' ),
                WCVISITOR_VERSION,
                true
            );

            wp_localize_script(
                'wcvisitor-scripts',
                'WCVisitorConfig',
                array(
                    'url' => admin_url( 'admin-ajax.php' ),
                )
            );
        }

        wp_enqueue_style(
            'wcvisitor-style',
            WCVISITOR_URL . 'assets/style.css',
            array(),
            WCVISITOR_VERSION
        );

        if ( $this->service->should_load_fontawesome() ) {
            wp_enqueue_style(
                'wcvisitor-fontawesome',
                WCVISITOR_URL . 'assets/fontawesome/all.min.css',
                array(),
                WCVISITOR_VERSION
            );
        }
    }

    public function maybe_record_current_product() {
        if ( ! is_product() ) {
            return;
        }

        $product_id = get_the_ID();

        if ( ! $product_id ) {
            return;
        }

        $this->service->record_product_view( $product_id );
    }

    public function maybe_render_counter() {
        $product_id = get_the_ID();

        if ( ! $product_id ) {
            return;
        }

        echo $this->service->render_counter_block( $product_id );
    }

    public function render_show_js() {
        if ( ! is_product() ) {
            return;
        }

        $payload = $this->service->get_position_payload();
        $product = get_the_ID();

        if ( ! $payload || ! $product ) {
            return;
        }

        echo '<script>jQuery(function($){WCVisitor.show(' . absint( $product ) . ',' . wp_json_encode( wp_json_encode( $payload ) ) . ');});</script>';
    }

    public function render_live_js() {
        if ( ! is_product() ) {
            return;
        }

        $product = get_the_ID();

        if ( ! $product ) {
            return;
        }

        echo '<script>jQuery(function($){WCVisitor.reload(' . absint( $product ) . ',' . absint( $this->service->get_live_seconds() ) . ');});</script>';
    }

    public function after_price_counter( $price, $product ) {
        $product_id = $product instanceof WC_Product ? $product->get_id() : 0;
        $text       = $this->service->get_after_price_text( $product_id );

        if ( $text ) {
            $price .= ' | ' . $text;
        }

        return $price;
    }

    public function shortcode( $atts ) {
        global $post;

        if ( ! $post || 'product' !== $post->post_type ) {
            return '';
        }

        $args = shortcode_atts(
            array(
                'msgone'  => false,
                'msgmore' => false,
            ),
            $atts,
            'wcvisitor'
        );

        return (string) $this->service->render_counter_block( $post->ID, $args );
    }

    private function register_hooks() {
        $this->admin_controller->register_hooks();
        $this->ajax_controller->register_hooks();

        add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
        add_action( 'init', array( $this, 'maybe_upgrade' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        add_shortcode( 'wcvisitor', array( $this, 'shortcode' ) );

        if ( ! $this->service->should_use_js() ) {
            add_action( 'woocommerce_before_single_product', array( $this, 'maybe_record_current_product' ) );
        }

        if ( ! $this->service->should_use_js() && 'deactivate' !== $this->service->get_position() ) {
            add_action( $this->service->get_position(), array( $this, 'maybe_render_counter' ), $this->service->get_weight() );
        }

        if ( $this->service->should_use_js() ) {
            add_action( 'wp_footer', array( $this, 'render_show_js' ), 99 );
        }

        if ( $this->service->should_use_live_mode() ) {
            add_action( 'wp_footer', array( $this, 'render_live_js' ), 99 );
        }

        if ( $this->service->should_show_after_price() ) {
            add_filter( 'woocommerce_get_price_html', array( $this, 'after_price_counter' ), 10, 2 );
        }

        add_action(
            'before_woocommerce_init',
            function() {
                if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
                    \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', WCVISITOR_FILE, true );
                }
            }
        );

        register_activation_hook( WCVISITOR_FILE, array( $this, 'activate' ) );
        register_deactivation_hook( WCVISITOR_FILE, array( $this, 'deactivate' ) );
    }
}
