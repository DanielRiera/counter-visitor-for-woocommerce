<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WCVisitor_Admin_Controller {
    const OPTION_LEGACY_NOTICE_DISMISSED = 'wcvisitor_legacy_notice_dismissed';

    private $settings;
    private $service;
    private $notices = array();

    public function __construct( WCVisitor_Settings $settings, WCVisitor_Counter_Service $service ) {
        $this->settings = $settings;
        $this->service  = $service;
    }

    public function register_hooks() {
        add_action( 'admin_menu', array( $this, 'register_menu' ) );
        add_action( 'admin_init', array( $this, 'handle_actions' ) );
        add_action( 'admin_notices', array( $this, 'render_admin_feedback' ) );
        add_action( 'admin_notices', array( $this, 'render_legacy_notice' ) );
    }

    public function register_menu() {
        add_submenu_page(
            'woocommerce',
            __( 'Visitor Counter', 'counter-visitor-for-woocommerce' ),
            __( 'Visitor Counter', 'counter-visitor-for-woocommerce' ),
            'manage_options',
            'wcvisitor-options',
            array( $this, 'render_options_page' )
        );

        add_submenu_page(
            'woocommerce',
            __( 'Visitor Stats', 'counter-visitor-for-woocommerce' ),
            __( 'Visitor Stats', 'counter-visitor-for-woocommerce' ),
            'manage_options',
            'wcvisitor-stats',
            array( $this, 'render_stats_page' )
        );
    }

    public function handle_actions() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $action = isset( $_POST['action'] ) ? sanitize_key( wp_unslash( $_POST['action'] ) ) : '';

        if ( ! $action ) {
            return;
        }

        if ( 'save_options' === $action ) {
            $this->save_options();
            return;
        }

        if ( 'wcvisitor_legacy_delete' === $action ) {
            $this->delete_legacy_directory();
            return;
        }

        if ( 'wcvisitor_legacy_dismiss' === $action ) {
            $this->dismiss_legacy_notice();
            return;
        }

        if ( 'adsub' === $action ) {
            $this->subscribe_newsletter();
        }
    }

    public function get_notices() {
        return $this->notices;
    }

    public function render_options_page() {
        $newsletterCounterLive = $this->settings->get( WCVisitor_Settings::OPTION_NEWSLETTER );
        $user                  = wp_get_current_user();
        $notices               = $this->get_notices();

        require WCVISITOR_PATH . 'views/options.php';
    }

    public function render_stats_page() {
        $allowed_periods = array(
            '2m'  => __( 'Live 2 min', 'counter-visitor-for-woocommerce' ),
            '5m'  => __( 'Live 5 min', 'counter-visitor-for-woocommerce' ),
            'all' => __( 'All time', 'counter-visitor-for-woocommerce' ),
            '24h' => __( 'Last 24 hours', 'counter-visitor-for-woocommerce' ),
            '7d'  => __( 'Last 7 days', 'counter-visitor-for-woocommerce' ),
            '30d' => __( 'Last 30 days', 'counter-visitor-for-woocommerce' ),
        );
        $period          = isset( $_GET['period'] ) ? sanitize_key( wp_unslash( $_GET['period'] ) ) : 'all';

        if ( ! isset( $allowed_periods[ $period ] ) ) {
            $period = 'all';
        }

        $current_page = isset( $_GET['paged'] ) ? max( 1, absint( wp_unslash( $_GET['paged'] ) ) ) : 1;
        $stats        = $this->service->get_stats( $period, 20, $current_page );
        $rows         = $stats['rows'];
        $total_pages  = max( 1, (int) ceil( $stats['total'] / $stats['per_page'] ) );
        $summary      = $stats['summary'];

        require WCVISITOR_PATH . 'views/stats.php';
    }

    private function save_options() {
        $nonce = isset( $_POST['save_option_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['save_option_nonce'] ) ) : '';

        if ( ! wp_verify_nonce( $nonce, 'wcv_nonce' ) ) {
            $this->add_notice( 'error', __( 'Security validation failed.', 'counter-visitor-for-woocommerce' ) );
            return;
        }

        update_option( WCVisitor_Settings::OPTION_TIMEOUT, max( 30, absint( wp_unslash( $_POST[ WCVisitor_Settings::OPTION_TIMEOUT ] ?? 300 ) ) ) );
        update_option( WCVisitor_Settings::OPTION_POSITION, sanitize_text_field( wp_unslash( $_POST[ WCVisitor_Settings::OPTION_POSITION ] ?? 'woocommerce_after_add_to_cart_button' ) ) );
        update_option( WCVisitor_Settings::OPTION_ICON, sanitize_text_field( wp_unslash( $_POST[ WCVisitor_Settings::OPTION_ICON ] ?? 'dashicons dashicons-visibility' ) ) );
        update_option( WCVisitor_Settings::OPTION_WEIGHT_BLOCK, absint( wp_unslash( $_POST[ WCVisitor_Settings::OPTION_WEIGHT_BLOCK ] ?? 0 ) ) );
        update_option( WCVisitor_Settings::OPTION_MESSAGE, sanitize_textarea_field( wp_unslash( $_POST[ WCVisitor_Settings::OPTION_MESSAGE ] ?? '' ) ) );
        update_option( WCVisitor_Settings::OPTION_MESSAGE_ONE, sanitize_textarea_field( wp_unslash( $_POST[ WCVisitor_Settings::OPTION_MESSAGE_ONE ] ?? '' ) ) );
        update_option( WCVisitor_Settings::OPTION_USE_JS, isset( $_POST[ WCVisitor_Settings::OPTION_USE_JS ] ) ? '1' : '0' );
        update_option( WCVisitor_Settings::OPTION_AFTER_PRICE, isset( $_POST[ WCVisitor_Settings::OPTION_AFTER_PRICE ] ) ? '1' : '0' );
        update_option( WCVisitor_Settings::OPTION_ONLY_ONE_HIDE, isset( $_POST[ WCVisitor_Settings::OPTION_ONLY_ONE_HIDE ] ) ? '1' : '0' );
        update_option( WCVisitor_Settings::OPTION_FAKE_MODE, isset( $_POST[ WCVisitor_Settings::OPTION_FAKE_MODE ] ) ? '1' : '0' );
        update_option( WCVisitor_Settings::OPTION_FAKE_FROM, absint( wp_unslash( $_POST[ WCVisitor_Settings::OPTION_FAKE_FROM ] ?? 0 ) ) );
        update_option( WCVisitor_Settings::OPTION_FAKE_TO, absint( wp_unslash( $_POST[ WCVisitor_Settings::OPTION_FAKE_TO ] ?? 0 ) ) );
        update_option( WCVisitor_Settings::OPTION_LIVE_MODE, isset( $_POST[ WCVisitor_Settings::OPTION_LIVE_MODE ] ) ? '1' : '0' );
        update_option( WCVisitor_Settings::OPTION_FONTAWESOME, isset( $_POST[ WCVisitor_Settings::OPTION_FONTAWESOME ] ) ? '1' : '0' );
        update_option( WCVisitor_Settings::OPTION_LIVE_SECONDS, max( 5, absint( wp_unslash( $_POST[ WCVisitor_Settings::OPTION_LIVE_SECONDS ] ?? 5 ) ) ) );

        $this->add_notice( 'success', __( 'Settings saved.', 'counter-visitor-for-woocommerce' ) );
    }

    public function render_legacy_notice() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        if ( '1' === get_option( self::OPTION_LEGACY_NOTICE_DISMISSED, '0' ) ) {
            return;
        }

        $legacy = $this->get_legacy_directory_state();

        if ( ! $legacy['should_warn'] ) {
            return;
        }

        ?>
        <div class="notice notice-warning">
            <p>
                <strong><?=esc_html__( 'Counter Visitor for Woocommerce', 'counter-visitor-for-woocommerce' )?>:</strong>
                <?=esc_html( sprintf( __( 'We found %d legacy visitor files from the previous storage system.', 'counter-visitor-for-woocommerce' ), $legacy['files'] ) )?>
                <?=esc_html__( 'They are no longer used because the plugin now stores visits in the database, so deleting them helps free disk space and remove old data that is no longer needed.', 'counter-visitor-for-woocommerce' )?>
            </p>
            <p>
                <form method="post" style="display:inline-block;margin-right:8px;">
                    <input type="hidden" name="action" value="wcvisitor_legacy_delete" />
                    <?php wp_nonce_field( 'wcvisitor_legacy_delete', 'wcvisitor_legacy_nonce' ); ?>
                    <button type="submit" class="button button-primary"><?=esc_html__( 'Delete legacy files', 'counter-visitor-for-woocommerce' )?></button>
                </form>
                <form method="post" style="display:inline-block;">
                    <input type="hidden" name="action" value="wcvisitor_legacy_dismiss" />
                    <?php wp_nonce_field( 'wcvisitor_legacy_dismiss', 'wcvisitor_legacy_nonce' ); ?>
                    <button type="submit" class="button"><?=esc_html__( 'Dismiss notice', 'counter-visitor-for-woocommerce' )?></button>
                </form>
            </p>
        </div>
        <?php
    }

    public function render_admin_feedback() {
        if ( empty( $this->notices ) ) {
            return;
        }

        $page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';

        if ( 'wcvisitor-options' === $page ) {
            return;
        }

        foreach ( $this->notices as $notice ) {
            printf(
                '<div class="notice notice-%1$s"><p>%2$s</p></div>',
                esc_attr( 'success' === $notice['type'] ? 'success' : 'error' ),
                esc_html( $notice['message'] )
            );
        }
    }

    private function subscribe_newsletter() {
        $nonce = isset( $_POST['add_sub_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['add_sub_nonce'] ) ) : '';

        if ( ! wp_verify_nonce( $nonce, 'wcv_nonce' ) ) {
            $this->add_notice( 'error', __( 'Security validation failed.', 'counter-visitor-for-woocommerce' ) );
            return;
        }

        $payload = array(
            'm' => 'adsub',
            'd' => base64_encode(
                wp_json_encode(
                    array(
                        'e' => sanitize_email( wp_unslash( $_POST['e'] ?? '' ) ),
                        'n' => sanitize_text_field( wp_unslash( $_POST['n'] ?? '' ) ),
                        'w' => esc_url_raw( wp_unslash( $_POST['w'] ?? '' ) ),
                        'g' => sanitize_text_field( wp_unslash( $_POST['g'] ?? '' ) ),
                    )
                )
            ),
        );

        $response = wp_remote_post(
            'https://mailing.danielriera.net',
            array(
                'method'      => 'POST',
                'timeout'     => 20,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking'    => true,
                'body'        => $payload,
            )
        );

        if ( is_wp_error( $response ) ) {
            $this->add_notice( 'error', __( 'An error has occurred, try again.', 'counter-visitor-for-woocommerce' ) );
            return;
        }

        $body   = wp_remote_retrieve_body( $response );
        $result = json_decode( $body, true );

        if ( empty( $result ) || ! empty( $result['error'] ) ) {
            $this->add_notice( 'error', __( 'An error has occurred, try again.', 'counter-visitor-for-woocommerce' ) );
            return;
        }

        update_option( WCVisitor_Settings::OPTION_NEWSLETTER, '1' );
        $this->add_notice( 'success', __( 'Welcome newsletter :)', 'counter-visitor-for-woocommerce' ) );
    }

    private function dismiss_legacy_notice() {
        $nonce = isset( $_POST['wcvisitor_legacy_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['wcvisitor_legacy_nonce'] ) ) : '';

        if ( ! wp_verify_nonce( $nonce, 'wcvisitor_legacy_dismiss' ) ) {
            $this->add_notice( 'error', __( 'Security validation failed.', 'counter-visitor-for-woocommerce' ) );
            return;
        }

        update_option( self::OPTION_LEGACY_NOTICE_DISMISSED, '1' );
        $this->add_notice( 'success', __( 'Legacy files notice dismissed.', 'counter-visitor-for-woocommerce' ) );
    }

    private function delete_legacy_directory() {
        $nonce = isset( $_POST['wcvisitor_legacy_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['wcvisitor_legacy_nonce'] ) ) : '';

        if ( ! wp_verify_nonce( $nonce, 'wcvisitor_legacy_delete' ) ) {
            $this->add_notice( 'error', __( 'Security validation failed.', 'counter-visitor-for-woocommerce' ) );
            return;
        }

        $legacy = $this->get_legacy_directory_state();

        if ( ! $legacy['path'] || ! $legacy['exists'] ) {
            $this->add_notice( 'error', __( 'Legacy directory not found.', 'counter-visitor-for-woocommerce' ) );
            return;
        }

        $resolved_path = $this->get_safe_legacy_path( $legacy['path'] );

        if ( ! $resolved_path ) {
            $this->add_notice( 'error', __( 'Legacy directory path is not valid.', 'counter-visitor-for-woocommerce' ) );
            return;
        }

        if ( ! $this->delete_directory_recursively( $resolved_path, $resolved_path ) ) {
            $this->add_notice( 'error', __( 'Unable to delete legacy files.', 'counter-visitor-for-woocommerce' ) );
            return;
        }

        delete_option( self::OPTION_LEGACY_NOTICE_DISMISSED );
        $this->add_notice( 'success', __( 'Legacy visitor files deleted successfully.', 'counter-visitor-for-woocommerce' ) );
    }

    private function add_notice( $type, $message ) {
        $this->notices[] = array(
            'type'    => $type,
            'message' => $message,
        );
    }

    private function get_legacy_directory_state() {
        $upload_dir = wp_upload_dir();
        $path       = trailingslashit( $upload_dir['basedir'] ) . 'wcvtemp';
        $files      = $this->count_legacy_files( $path );

        return array(
            'path'        => $path,
            'exists'      => is_dir( $path ),
            'files'       => $files,
            'should_warn' => is_dir( $path ) && $files > 0,
        );
    }

    private function count_legacy_files( $path ) {
        if ( ! is_dir( $path ) || ! is_readable( $path ) ) {
            return 0;
        }

        $entries = scandir( $path );

        if ( false === $entries ) {
            return 0;
        }

        $count = 0;

        foreach ( $entries as $entry ) {
            if ( '.' === $entry || '..' === $entry ) {
                continue;
            }

            $entry_path = trailingslashit( $path ) . $entry;

            if ( is_dir( $entry_path ) ) {
                $count += $this->count_legacy_files( $entry_path );
                continue;
            }

            $count++;
        }

        return $count;
    }

    private function get_safe_legacy_path( $path ) {
        $upload_dir    = wp_upload_dir();
        $expected_root = realpath( trailingslashit( $upload_dir['basedir'] ) . 'wcvtemp' );

        if ( false === $expected_root ) {
            return false;
        }

        $resolved = realpath( $path );

        if ( false === $resolved ) {
            return false;
        }

        $normalized_expected = wp_normalize_path( untrailingslashit( $expected_root ) );
        $normalized_resolved = wp_normalize_path( untrailingslashit( $resolved ) );

        if ( $normalized_expected !== $normalized_resolved ) {
            return false;
        }

        return $normalized_resolved;
    }

    private function delete_directory_recursively( $path, $root_path ) {
        $root_path = wp_normalize_path( untrailingslashit( $root_path ) );
        $path      = wp_normalize_path( untrailingslashit( $path ) );

        if ( 0 !== strpos( $path, $root_path ) ) {
            return false;
        }

        if ( is_link( $path ) ) {
            return false;
        }

        if ( is_file( $path ) ) {
            return wp_delete_file( $path );
        }

        if ( ! is_dir( $path ) ) {
            return true;
        }

        $entries = scandir( $path );

        if ( false === $entries ) {
            return false;
        }

        foreach ( $entries as $entry ) {
            if ( '.' === $entry || '..' === $entry ) {
                continue;
            }

            $entry_path = trailingslashit( $path ) . $entry;

            if ( is_dir( $entry_path ) ) {
                if ( ! $this->delete_directory_recursively( $entry_path, $root_path ) ) {
                    return false;
                }
                continue;
            }

            if ( is_link( $entry_path ) ) {
                return false;
            }

            if ( ! wp_delete_file( $entry_path ) ) {
                return false;
            }
        }

        return rmdir( $path );
    }
}
