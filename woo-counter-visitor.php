<?php
/**
 * Plugin Name: Counter Visitor for Woocommerce
 * Description: Show number of visitors view a product on Woocommerce
 * Version: 1.2.2
 * Author: Daniel Riera
 * Author URI: https://danielriera.net
 * Text Domain: counter-visitor-for-woocommerce
 * Domain Path: /languages
 * WC requires at least: 3.0
 * WC tested up to: 5.2.2
 * Required WP: 5.0
 * Tested WP: 5.7.2
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

if(!defined('ABSPATH')) { exit; }

define('WCVisitor_PATH', dirname(__FILE__).'/');
define('WCVisitor_USE_JS', get_option('_wcv_use_js', '0'));
define('WCVisitor_USE_Live', get_option('_wcv_live_mode', '0'));
define('WCVisitor_Fontawesome', get_option('_wcv_fontawesome', '0'));
$uploaddir = wp_upload_dir();
define('WCVisitor_TEMP_FILES', $uploaddir['basedir'] . '/wcvtemp/');
define('WCVisitor_POSITION_SHOW', get_option('_wcv_position', 'woocommerce_after_add_to_cart_button'));
define('WCVisitor_version', '1.2.1');


require_once WCVisitor_PATH . 'includes/class.api.php';

if( !class_exists( 'WCVisitor_MAIN' ) ) {
    class WCVisitor_MAIN {

        private $WCVisitor_IN_USE = false;

        private $counter = 0;

        public $positions = array(
            'woocommerce_after_add_to_cart_button' => 'form.cart|inside',
            'woocommerce_before_add_to_cart_button' => 'form.cart|before',
            'woocommerce_product_meta_end' => 'div.product_meta|after',
            'woocommerce_before_single_product_summary' => 'div.woocommerce-notices-wrapper|after',
            'woocommerce_after_single_product_summary' => 'div.woocommerce-tabs|after',
            'woocommerce_product_thumbnails' => 'div.woocommerce-product-gallery|inside',
        );
    
        function __construct(){
            
            add_action('init', array($this, 'wcvisitor_init'));
            add_action('wp_loader', array($this, 'wcvisitor_cookie'));
            add_action('woocommerce_before_single_product', array($this, 'wcvisitor_record'));
            if(WCVisitor_USE_JS == '0') {
                add_action(WCVisitor_POSITION_SHOW, array($this, 'wcvisitor_show'));
            }else{
                add_action( 'wp_footer', array($this, 'wcvisitor_show_js'), 99 );
            }

            if(WCVisitor_USE_Live == '1') {
                add_action( 'wp_footer', array($this, 'wcvisitor_live_js'), 99 );
            }

            if(WCVisitor_Fontawesome == '1') {
                add_action( 'wp_enqueue_scripts', array($this, 'WCVisitor_load_fontawesome') );
            }
            add_action( 'wp_enqueue_scripts', array($this, 'WCVisitor_load_style') );
            add_action('admin_menu', array($this, 'wcvisitor_menu'));
            add_action('plugins_loaded', array($this, 'wcvisitor_load_textdomain'));
            add_shortcode('wcvisitor', array($this, 'wcvisitor_shortcode'));

            add_action( 'admin_notices', array($this, 'wcvisitor_notices_version') );
            add_action( 'admin_init', array($this, 'wcvisitor_notices_version_dismissed') );
        }

        function wcvisitor_notices_version() {
            $user_id = wp_get_current_user();
            $versionPlugin = get_user_meta( $user_id->ID, 'wcvisitor_version', true );

            if ( $versionPlugin != WCVisitor_version ) 
                echo '<div class="notice notice-warning"><h1>'.sprintf(__('Counter Visitor for WooCommerce %s version', 'counter-visitor-for-woocommerce'), WCVisitor_version).'</h1>
                <p>' . __( 'News options! Live Mode is available', 'counter-visitor-for-woocommerce' ) . '</p>
                <p>'.__('With the new version you can show the number of current users in real time. And it supports Fake Mode!','counter-visitor-for-woocommerce').'</p>
                <p>'.__('We have also added FontAwesome to the plugin, although it is disabled by default, if your theme does not include FontAwesome you can enable this option','counter-visitor-for-woocommerce').'</p>
                <a href="admin.php?page=wcvisitor-options&dismiss-notice-wcvisitor-version" class="button" style="background:green;color:white;">'.__('WOW I want it!','counter-visitor-for-woocommerce').'</a>
                <a href="?dismiss-notice-wcvisitor-version" class="button">'.__('Close','counter-visitor-for-woocommerce').'</a>
                <p></p>
                </div>';
        }
        
        
        function wcvisitor_notices_version_dismissed() {
            if ( isset( $_GET['dismiss-notice-wcvisitor-version'] ) ) {
                $user_id = wp_get_current_user();
                update_user_meta($user_id->ID, 'wcvisitor_version', WCVisitor_version);
            }
        }
        
        function wcvisitor_shortcode($atts) {
            global $post;

            $p = shortcode_atts( array (
                'msgone' => false,
                'msgmore' => false
                ), $atts, 'wcvisitor' );
            
            if($post != NULL and $post->post_type == 'product') {
                return $this->wcvisitor_get_block($post->ID, $p);
            }
        }
        function WCVisitor_load_style() {
            if(is_product() === false) { return; }
            if(WCVisitor_USE_JS == '1' or WCVisitor_USE_Live == '1') {
                wp_enqueue_script( 'wcvisitor-scripts', plugins_url('assets/scripts.js?wcvisitor=true&v='.WCVisitor_version, __FILE__), array('jquery'));
            }
            wp_enqueue_style( 'wcvisitor-style', plugins_url('assets/style.css?wcvisitor=true', __FILE__));

            wp_localize_script( 'wcvisitor-scripts', 'WCVisitorConfig', array(
                'url'    => admin_url( 'admin-ajax.php' )
            ) );
        }

        function WCVisitor_load_fontawesome() {
            wp_enqueue_style( 'wcvisitor-fontawesome', plugins_url('assets/fontawesome/all.min.css?v='.WCVisitor_version, __FILE__));
        }

        function wcvisitor_load_textdomain(){
            load_plugin_textdomain( 'counter-visitor-for-woocommerce', false, dirname( plugin_basename(__FILE__) ) . '/languages/' );
        }
        /**
         * wcvisitor_init
         * Genera la estructura si no existe necesaria para el funcionamiento del plugin
         * @return void
         */
        function wcvisitor_init() {
            if(is_admin()) {
                if (!is_dir(WCVisitor_TEMP_FILES)) {
                    mkdir(WCVisitor_TEMP_FILES);
                }
                $emptyDirectory = $this->wcvisitor_is_empty_directory(WCVisitor_TEMP_FILES);
                if ($emptyDirectory === true) {
                    $folderName = 'users_'.wp_generate_password(8, false);
                    update_option('_WCVisitor_folder_name', $folderName);
                    mkdir(WCVisitor_TEMP_FILES .$folderName);
                    $file = fopen(WCVisitor_TEMP_FILES .$folderName.'/index.php',"w");
                    fwrite($file,"");
                    fclose($file);
                }
            }
        }

        function wcvisitor_menu() {
            add_submenu_page('woocommerce',__('Visitor Counter','counter-visitor-for-woocommerce'),__('Visitor Counter','counter-visitor-for-woocommerce') , 'manage_options', 'wcvisitor-options', array($this, 'wcvisitor_view_page_options'));
        }

        function wcvisitor_view_page_options() {
            require_once(WCVisitor_PATH . 'views/options.php');
        }
        /**
         * wcvisitor_is_empty_directory
         * 
         * Comprueba si un directorio est?? vacio o no.
         * 
         * @param string $dir
         * @return boolean
         */
        private function wcvisitor_is_empty_directory($dir) {
            if (!is_readable($dir)) return NULL;
            return (count(scandir($dir)) == 2);
        }
        /**
         * wcvisitor_getIP
         * Retorna la IP del usuario
         * @return void
         */
        private function wcvisitor_getIP() {
            if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
                    $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
                    return $ip;
            }
            if(isset($_SERVER['REMOTE_ADDR'])){
                    $ip = $_SERVER['REMOTE_ADDR'];
                    return $ip;
            }
            return "0.0.0.0";
        }

        function wcvisitor_record() {
            global $post;
            $folderName = WCVisitor_TEMP_FILES . get_option('_WCVisitor_folder_name');
            $ip = $this->wcvisitor_getIP();
            if (!is_dir($folderName .'/' . $post->ID)) {
                mkdir($folderName .'/' . $post->ID);
            }
            file_put_contents ($folderName .'/' . $post->ID .'/' . $ip . '.txt', "0");
        }

        function wcvisitor_show_js() {
            if(is_product() === true) {
                global $post;
                $selectPosition = explode('|', $this->positions[WCVisitor_POSITION_SHOW]);
                echo '<script>jQuery(document).ready(function($) { WCVisitor.show('.$post->ID.',\''.json_encode($selectPosition).'\'); });</script>';
            }
        }

        function wcvisitor_live_js(){
            if(is_product() === true) {
                global $post;
                $reloadTime = intval(get_option('_wcv_live_seconds', 5));
                echo '<script>jQuery(document).ready(function($) { WCVisitor.reload('.$post->ID.','.$reloadTime.') });</script>';
            }
        }
        private function wcvisitor_in_use() {
            return $this->WCVisitor_IN_USE;
        }
        private function wcvisitor_on(){
            $this->WCVisitor_IN_USE = true;
        }


        function wcvisitor_cookie() {
            global $post;
            if($post->post_type == 'product') {
                $product = $post->ID;
                $this->wcvisitor_set_cookie($product);       
            }
        }

        private function wcvisitor_set_cookie($product, $isApi = false){
            $isFakeMode = get_option('_wcv_fake_mode','0');
                if($isFakeMode == '1') {

                    if($isApi) {
                        $this->counter = rand(intval(get_option('_wcv_fake_mode_from',0)), intval(get_option('_wcv_fake_mode_to',0)));
                        return;
                    }
                    if(isset($_COOKIE['pro_wcv_'.$product])) {
                        $this->counter = intval($_COOKIE['pro_wcv_'.$product]);
                    }else{
                        $this->counter = rand(intval(get_option('_wcv_fake_mode_from',0)), intval(get_option('_wcv_fake_mode_to',0)));

                        $secondsMore = 60;

                        if(get_option('_wcv_live_mode', '0') == '1') {
                            $secondsMore = 5;
                        }
                        setcookie('pro_wcv_'.$product, $this->counter, time()+$secondsMore);
                    }
                }
        }
        private function wcvisitor_get_block($product, $args = false) {

            //Check Fake Mode
            $isFakeMode = get_option('_wcv_fake_mode','0');
            $icon = get_option('_wcv_icon','dashicons dashicons-visibility');

            
            if($isFakeMode == '1') {

                if(isset($_COOKIE['pro_wcv_'.$product])) {
                    $this->counter = intval($_COOKIE['pro_wcv_'.$product]);
                }else{
                    $this->wcvisitor_set_cookie($product);
                }

                if($this->counter > 1) {
                    if($args && $args['msgmore'] !== false) {
                        $msg = $args['msgmore'];
                    }else{
                        $msg = get_option('_wcv_message', __('%n people are viewing this product'));
                    }
                    $msg = str_replace('%n','<span class="wcvisitor_num">'.$this->counter.'</span>', $msg);
                }else{
                    if($args && $args['msgone'] !== false) {
                        $msg = $args['msgone'];
                    }else{
                        $msg = get_option('_wcv_message_one', __('<span class="wcvisitor_num">1</span> user are viewing this product'));
                    }
                }

                $data = array(
                    'product' => $product,
                    'icon' => $icon,
                    'msg' => $msg
                );
                return $this->wcvisitor_show_div($data);
            }
            
            $actualtime=date("U");
            $timeold= get_option('_wcv_timeout_limit',300);
            $folderName = WCVisitor_TEMP_FILES . get_option('_WCVisitor_folder_name') . '/' . $product . '/';
            $this->counter=0;
            $dir = dir($folderName);
            while($temp = $dir->read()){
                if ($temp=="." or $temp==".."){continue;}
                $filecreatedtime=date("U", filemtime($folderName.$temp));
                if ($actualtime>($filecreatedtime+$timeold)){
                        unlink($folderName.$temp);
                }else{
                        $this->counter++;
                }
            }

            if($this->counter > 1) {
                if($args && $args['msgmore'] !== false) {
                    $msg = $args['msgmore'];
                }else{
                    $msg = get_option('_wcv_message', __('%n people are viewing this product'));
                }
                $msg = str_replace('%n','<span class="wcvisitor_num">'.$this->counter.'</span>', $msg);
            }else{

                if($args && $args['msgone'] !== false) {
                    $msg = $args['msgone'];
                }else{
                    $msg = get_option('_wcv_message_one', __('<span class="wcvisitor_num">1</span> user are viewing this product'));
                    /**
                     * @since 1.1.2
                     */
                    $msg = str_replace('1','<span class="wcvisitor_num">1</span>', $msg);
                }
            }

            $data = array(
                'product' => $product,
                'icon' => $icon,
                'msg' => $msg
            );
            return $this->wcvisitor_show_div($data);
        }
        private function wcvisitor_show_div($data) {
            return '<div class="wcv-message wcv-product-'.$data['product'].'" data-counter="'.$this->counter.'"><span class="icon"><i class="'.$data['icon'].'"></i></span>'.$data['msg'].'</div>';
        }

        function wcvisitor_get_counter() {
            return $this->counter;
        }
        function wcvisitor_show() {
            global $post;
            $product = $post->ID;
            echo $this->wcvisitor_get_block($product);
        }
        function wcvisitor_show_api($product) {
            return $this->wcvisitor_get_block($product);                
        }
    }

    $WCVISITOR_MAIN = new WCVisitor_MAIN();
}
