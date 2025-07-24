<?php
if(!defined('ABSPATH')) { exit; }
global $WCVISITOR_MAIN;
/**Acciones */
if(isset($_POST['action'])) {
    if ( isset($_POST['save_option_nonce']) && wp_verify_nonce(  $_POST['save_option_nonce'], 'wcv_nonce' ) ) {
        if($_POST['action'] == 'save_options') {
           update_option('_wcv_timeout_limit',sanitize_text_field( $_POST['_wcv_timeout_limit'] ));
           update_option('_wcv_position',sanitize_text_field( $_POST['_wcv_position'] ));
           update_option('_wcv_icon',sanitize_text_field( $_POST['_wcv_icon'] ));
           if( $_POST['_wcv_weight_block'] == '') {
                //Prevent
                $_POST['_wcv_weight_block'] = 0;
           }
           update_option('_wcv_weight_block',sanitize_text_field( $_POST['_wcv_weight_block'] ));
           

           update_option('_wcv_message',sanitize_textarea_field( $_POST['_wcv_message'] ));
           update_option('_wcv_message_one',sanitize_textarea_field( $_POST['_wcv_message_one'] ));
            if(isset($_POST['_wcv_use_js'])) {
                update_option('_wcv_use_js','1');
            }else{
                update_option('_wcv_use_js','0');
            }
            if(isset($_POST['_wcvisitor_after_price'])) {
                update_option('_wcvisitor_after_price','1');
            }else{
                update_option('_wcvisitor_after_price','0');
            }

            if(isset($_POST['_wcvisitor_only_one_hide'])) {
                update_option('_wcvisitor_only_one_hide','1');
            }else{
                update_option('_wcvisitor_only_one_hide','0');
            }
            
            
            if(isset($_POST['_wcv_fake_mode'])) {
                update_option('_wcv_fake_mode','1');
            }else{
                update_option('_wcv_fake_mode','0');
            }

            update_option('_wcv_fake_mode_from', sanitize_text_field($_POST['_wcv_fake_mode_from']));
            update_option('_wcv_fake_mode_to', sanitize_text_field($_POST['_wcv_fake_mode_to']));

            /**
             * @since 1.1.4
             * Live mode added
             */
            if(isset($_POST['_wcv_live_mode'])) {
                update_option('_wcv_live_mode','1');
            }else{
                update_option('_wcv_live_mode','0');
            }

            if(isset($_POST['_wcv_fontawesome'])) {
                update_option('_wcv_fontawesome','1');
            }else{
                update_option('_wcv_fontawesome','0');
            }
            
            $seconds = intval(sanitize_text_field($_POST['_wcv_live_seconds']));

            if($seconds < 5) {
                $seconds = 5;
            }
            update_option('_wcv_live_seconds', $seconds);
            
        }
    }


    if ( isset($_POST['action']) && isset($_POST['add_sub_nonce']) && $_POST['action'] == 'adsub' && wp_verify_nonce(  $_POST['add_sub_nonce'], 'wcv_nonce' ) ) {
        $sub = wp_remote_post( 'https://mailing.danielriera.net', [
            'method'      => 'POST',
            'timeout'     => 2000,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking'    => true,
            'headers'     => array(),
            'body'        => array(
                'm' => $_POST['action'],
                'd' => base64_encode(json_encode($_POST))
            ),
            'cookies'     => array()
        ]);
        $result = json_decode($sub['body'],true);

        if($result['error']) {
            $class = 'notice notice-error';
            $message = __( 'An error has occurred, try again.', 'counter-visitor-for-woocommerce' );
            printf( '<div class="%s"><p>%s</p></div>', $class, $message );
        }else{
            $class = 'notice notice-success';
            $message = __( 'Welcome newsletter :)', 'counter-visitor-for-woocommerce' );
            
            printf( '<div class="%s"><p>%s</p></div>', $class, $message );

            update_option('counter-visitor-newsletter' , '1');
        }
    }

    if ( isset($_POST['action']) && isset($_POST['add_sub_nonce']) && $_POST['action'] == 'delete_old_files' && wp_verify_nonce(  $_POST['add_sub_nonce'], 'wcv_nonce' ) ) {
        if(current_user_can('administrator')) {
            $files_deleted = $WCVISITOR_MAIN->wcvisitor_delete_old_files(WCVisitor_TEMP_FILES, true);
            $class = 'notice notice-success';
            $message = $files_deleted . ' ' . __( 'files deleted success', 'counter-visitor-for-woocommerce' );
            printf( '<div class="%s"><p>%s</p></div>', $class, $message );
        }else{
            $class = 'notice notice-error';
            $message = __( 'Permission Failed, need administrator rol for delete old files', 'counter-visitor-for-woocommerce' );
            printf( '<div class="%s"><p>%s</p></div>', $class, $message );
        }
    }
}
$newsletterCounterLive = get_option('counter-visitor-newsletter', '0');
$user = wp_get_current_user();
?>
<style>
    /* ---------- Variables ---------- */
    :root{
        --bg:#ffffff;
        --bg-alt:#f7f7f7;
        --text:#222;
        --text-light:#555;
        --primary:#3c853c;
        --primary-dark:#2f6a2f;
        --border:#dcdcdc;
        --radius:12px;
        --radius-sm:8px;
        --shadow:0 2px 6px rgba(0,0,0,.08);
        --shadow-hover:0 4px 14px rgba(0,0,0,.12);
        --transition:.18s ease;
        --font:system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif;
    }
    .wrap.wcvpanel{
        font-family:var(--font);
        color:var(--text);
        line-height:1.45;
        max-width:1100px;
    }
    .wrap.wcvpanel h1,
    .wrap.wcvpanel h2,
    .wrap.wcvpanel h3{margin:.6em 0 .4em;color:var(--text);}
    .wrap.wcvpanel p{color:var(--text-light);margin:.4em 0 1em;}

    /* ---------- Newsletter form ---------- */
    form#new_subscriber{
        background:var(--bg);
        padding:22px 26px 26px;
        margin:32px 0 50px;
        border-radius:var(--radius);
        border:1px solid var(--border);
        width:100%;
        max-width:420px;
        box-shadow:var(--shadow);
        text-align:left;
        transition:box-shadow var(--transition);
    }
    form#new_subscriber:hover{box-shadow:var(--shadow-hover);}
    form#new_subscriber h3{margin-top:0;}
    form#new_subscriber .form-group{margin-bottom:14px;}
    form#new_subscriber label.control-label{
        font-size:.9rem;
        color:var(--text-light);
        display:block;
        margin-bottom:4px;
    }
    form#new_subscriber input.email,
    form#new_subscriber input.form-control,
    form#new_subscriber input[type="email"],
    form#new_subscriber input[type="number"],
    form#new_subscriber input[type="text"],
    form#new_subscriber textarea{
        width:100%;
        padding:12px 14px;
        border:1px solid var(--border);
        border-radius:var(--radius-sm);
        box-sizing:border-box;
        font-size:15px;
        transition:border-color var(--transition), box-shadow var(--transition);
    }
    form#new_subscriber input:focus,
    form#new_subscriber textarea:focus{
        border-color:var(--primary);
        box-shadow:0 0 0 3px rgba(60,133,60,.15);
        outline:0;
    }
    form#new_subscriber input[type='submit'],
    form#new_subscriber .button[type='submit'],
    form#new_subscriber input.button{
        width:100%;
        margin-top:16px;
        border:0;
        background:var(--primary);
        color:#fff;
        padding:13px 16px;
        border-radius:var(--radius-sm);
        font-size:16px;
        cursor:pointer;
        transition:background var(--transition), transform var(--transition);
    }
    form#new_subscriber input[type='submit']:hover{
        background:var(--primary-dark);
        transform:translateY(-1px);
    }
    .submit-wrapper{margin-top:10px;}

    /* ---------- Donate button ---------- */
    .wrap.wcvpanel a[href*="paypal.com/donate"]{
        text-decoration:none;
        font-size:16px;
        border:1px solid var(--border);
        padding:12px 18px;
        display:inline-block;
        border-radius:var(--radius-sm);
        background:var(--bg);
        box-shadow:var(--shadow);
        transition:box-shadow var(--transition), transform var(--transition);
    }
    .wrap.wcvpanel a[href*="paypal.com/donate"]:hover{
        box-shadow:var(--shadow-hover);
        transform:translateY(-1px);
    }

    /* ---------- Tabla de opciones ---------- */
    .form-table{
        width:100%;
        border-collapse:separate;
        border-spacing:0 8px;
    }
    .form-table th{
        width:320px;
        text-align:left;
        padding:16px 18px;
        vertical-align:top;
        background:var(--bg-alt);
        border:1px solid var(--border);
        border-right:0;
        border-radius:var(--radius-sm) 0 0 var(--radius-sm);
        box-sizing:border-box;
    }
    .form-table td{
        padding:16px 18px;
        background:var(--bg);
        border:1px solid var(--border);
        border-left:0;
        border-radius:0 var(--radius-sm) var(--radius-sm) 0;
        box-sizing:border-box;
    }
    .form-table .description{
        font-size:.85rem;
        color:var(--text-light);
        margin:.4em 0 0;
    }
    .form-table input[type="checkbox"]{
        transform:scale(1.2);
        margin-right:6px;
    }
    .form-table input[type="number"],
    .form-table input[type="text"],
    .form-table textarea,
    .form-table select{
        padding:8px 10px;
        border:1px solid var(--border);
        border-radius:var(--radius-sm);
        font-size:14px;
        width:auto;
        max-width:100%;
        transition:border-color var(--transition), box-shadow var(--transition);
    }
    .form-table textarea{
        width:250px;
        height:180px;
        resize:vertical;
    }
    .form-table input:focus,
    .form-table textarea:focus,
    .form-table select:focus{
        border-color:var(--primary);
        box-shadow:0 0 0 3px rgba(60,133,60,.15);
        outline:0;
    }

    /* ---------- Botones genéricos ---------- */
    .wrap.wcvpanel .button{
        background:var(--primary);
        border:0;
        color:#fff;
        padding:10px 16px;
        border-radius:var(--radius-sm);
        cursor:pointer;
        font-size:14px;
        transition:background var(--transition), transform var(--transition);
    }
    .wrap.wcvpanel .button:hover{
        background:var(--primary-dark);
        transform:translateY(-1px);
    }

    /* ---------- Code block ---------- */
    pre{
        background:var(--bg-alt);
        padding:18px;
        border-radius:var(--radius-sm);
        border:1px solid var(--border);
        overflow:auto;
        box-shadow:var(--shadow);
    }

    /* ---------- Utilidades ---------- */
    .clear_site{clear:both;height:0;}
    #anotheremail{position:absolute;left:-9999px;} /* honeypot */

    /* ---------- Responsive ---------- */
    @media (max-width:782px){
        .form-table th,
        .form-table td{
            display:block;
            width:100%!important;
            border-radius:var(--radius-sm);
            border-left:1px solid var(--border);
            border-right:1px solid var(--border);
            margin:0;
        }
        .form-table th{border-bottom:0;border-radius:var(--radius-sm) var(--radius-sm) 0 0;}
        .form-table td{border-top:0;border-radius:0 0 var(--radius-sm) var(--radius-sm);}
        form#new_subscriber{max-width:100%;}
    }
</style>

<div class="wrap wcvpanel">

    <h1><?=__('Counter Visitor for Woocommerce', 'counter-visitor-for-woocommerce')?></h1>
    <p><?=__('It is not a simple visitor counter, this counter is shown on each product with the number of users who are currently viewing that same product','counter-visitor-for-woocommerce')?></p>

    <?php if($newsletterCounterLive == '0') { ?>
        <form class="simple_form form form-vertical" id="new_subscriber" novalidate="novalidate" accept-charset="UTF-8" method="post">
            <input name="utf8" type="hidden" value="&#x2713;" />
            <input type="hidden" name="action" value="adsub" />
            <?php wp_nonce_field( 'wcv_nonce', 'add_sub_nonce' ); ?>
            <h3><?=__('Do you want to receive the latest?','counter-visitor-for-woocommerce')?></h3>
            <p><?=__('Thank you very much for using our plugin, if you want to receive the latest news, offers, promotions, discounts, etc ... Sign up for our newsletter. :)', 'counter-visitor-for-woocommerce')?></p>
            <div class="form-group email required subscriber_email">
                <label class="control-label email required" for="subscriber_email">
                    <abbr title="<?=__('Required', 'counter-visitor-for-woocommerce')?>"> </abbr>
                </label>
                <input class="form-control string email required" type="email" name="e" id="subscriber_email" value="<?=$user->user_email?>" />
            </div>
            <input type="hidden" name="n" value="<?=bloginfo('name')?>" />
            <input type="hidden" name="w" value="<?=bloginfo('url')?>" />
            <input type="hidden" name="g" value="1,6" />
            <input type="text" name="anotheremail" id="anotheremail" tabindex="-1" autocomplete="off" />
            <div class="submit-wrapper">
                <input type="submit" name="commit" value="<?=__('Submit', 'counter-visitor-for-woocommerce')?>" class="button" data-disable-with="<?=__('Processing', 'counter-visitor-for-woocommerce')?>" />
            </div>
        </form>
    <?php } //END Newsletter ?>

    <?php
    $tab = 'general';
    if($tab == 'general') {
        $currentPosition = get_option('_wcv_position','woocommerce_after_add_to_cart_button');
        ?>

        <!--Donate button-->
        <div>
            <a href="https://www.paypal.com/donate/?hosted_button_id=EZ67DG78KMXWQ" target="_blank"><?=__('Buy a Coffe? :)','counter-visitor-for-woocommerce')?></a>
        </div>
        <div class="clear_site"> </div>
        <?php
        $oldFiles = $WCVISITOR_MAIN->wcvisitor_delete_old_files(WCVisitor_TEMP_FILES);
        if($oldFiles > 0) {
            echo '<form novalidate="novalidate" method="post">
                    <h3>'.__('You can delete the old files generated more than 1 hour old','counter-visitor-for-woocommerce').'</h3>
                    <input type="hidden" name="action" value="delete_old_files" />
                    '.wp_nonce_field( 'wcv_nonce', 'add_sub_nonce' ).'
                    <input class="button" type="submit" value="'.__('Delete old files','counter-visitor-for-woocommerce').' ('.$oldFiles.')" />
                </form>';
        }
        ?>

        <form method="post">
            <input type="hidden" name="action" value="save_options" />
            <?php wp_nonce_field( 'wcv_nonce', 'save_option_nonce' ); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?=__('Your site use cache system?', 'counter-visitor-for-woocommerce')?>
                        <p class="description"><?=__('Activate this option if your site uses some type of cache and add \'wcvisitor\' to the plugin cache exceptions','counter-visitor-for-woocommerce')?></p>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox" name="_wcv_use_js" value="1" <?=checked('1', get_option('_wcv_use_js', '0'))?> />
                        </label>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?=__('Show message after price', 'counter-visitor-for-woocommerce')?>
                        <p class="description"><?=__('Active this options for show counter after price with | separated','counter-visitor-for-woocommerce')?></p>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox" name="_wcvisitor_after_price" value="1" <?=checked('1', get_option('_wcvisitor_after_price', '0'))?> />
                        </label>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?=__('Hide counter if only one visitor', 'counter-visitor-for-woocommerce')?>
                        <p class="description"><?=__('Active this options for hide counter when only one visitor on product','counter-visitor-for-woocommerce')?></p>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox" name="_wcvisitor_only_one_hide" value="1" <?=checked('1', get_option('_wcvisitor_only_one_hide', '0'))?> />
                        </label>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?=__('Live Mode: Do you want to show users in real time?', 'counter-visitor-for-woocommerce')?>
                        <p class="description"><?=__('This option adds a call per user every X seconds, check its operation on your server, for security less than 5 seconds are not allowed. Use this option considering the resources of your server.','counter-visitor-for-woocommerce')?></p>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox" name="_wcv_live_mode" value="1" <?=checked('1', get_option('_wcv_live_mode', '0'))?> />
                        </label>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?=__('How often to update the number of users in the product?', 'counter-visitor-for-woocommerce')?>
                        <p class="description"><?=__('Seconds, min 5 seconds.. (Require Live Move)','counter-visitor-for-woocommerce')?></p>
                    </th>
                    <td>
                        <label>
                            <input type="number" name="_wcv_live_seconds" value="<?=get_option('_wcv_live_seconds','5')?>" min="5" />
                        </label>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?=__('Duration', 'counter-visitor-for-woocommerce')?>
                        <p class="description"><?=__('Time since last activity of an users to be considered inactive','counter-visitor-for-woocommerce')?></p>
                    </th>
                    <td>
                        <label>
                            <input type="number" min="30" max="99999999" name="_wcv_timeout_limit" value="<?=get_option('_wcv_timeout_limit', '300')?>" />
                        </label>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?=__('Position', 'counter-visitor-for-woocommerce')?></th>
                    <td>
                        <label>
                            <select name="_wcv_position">
                                <option value="woocommerce_after_add_to_cart_button" <?=selected('woocommerce_after_add_to_cart_button',$currentPosition);?>><?=__('After cart button','counter-visitor-for-woocommerce')?></option>
                                <option value="woocommerce_before_add_to_cart_button" <?=selected('woocommerce_before_add_to_cart_button',$currentPosition);?>><?=__('Before cart button','counter-visitor-for-woocommerce')?></option>
                                <option value="woocommerce_product_meta_end" <?=selected('woocommerce_product_meta_end',$currentPosition);?>><?=__('After product meta','counter-visitor-for-woocommerce')?></option>
                                <option value="woocommerce_before_single_product_summary" <?=selected('woocommerce_before_single_product_summary',$currentPosition);?>><?=__('Before product summary','counter-visitor-for-woocommerce')?></option>
                                <option value="woocommerce_after_single_product_summary" <?=selected('woocommerce_after_single_product_summary',$currentPosition);?>><?=__('After product summary','counter-visitor-for-woocommerce')?></option>
                                <option value="woocommerce_product_thumbnails" <?=selected('woocommerce_product_thumbnails',$currentPosition);?>><?=__('Product Thumbnail (may not work)','counter-visitor-for-woocommerce')?></option>
                                <option value="woocommerce_single_product_summary" <?=selected('woocommerce_single_product_summary',$currentPosition);?>><?=__('After short description','counter-visitor-for-woocommerce')?></option>
                                <option value="deactivate" <?=selected('deactivate',$currentPosition);?>><?=__('Deactivate','counter-visitor-for-woocommerce')?></option>
                            </select>
                        </label>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?=__('Weight block', 'counter-visitor-for-woocommerce')?>
                        <p class="description"><?=__('The heavier the weight, the lower the block is displayed','counter-visitor-for-woocommerce')?></p>
                    </th>
                    <td>
                        <label>
                            <input type="number" min="0" max="300" name="_wcv_weight_block" value="<?=get_option('_wcv_weight_block', '0')?>" />
                        </label>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?=__('Fake Mode', 'counter-visitor-for-woocommerce')?>
                        <p class="description"><?=__('Use Random numbers between from / to','counter-visitor-for-woocommerce')?></p>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox" name="_wcv_fake_mode" value="1" <?=checked("1", get_option('_wcv_fake_mode','0'))?> />
                        </label>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?=__('Random Numbers', 'counter-visitor-for-woocommerce')?>
                        <p class="description"><?=__('Need Fake mode, for visitors this value is saved for 25 minutes','counter-visitor-for-woocommerce')?></p>
                    </th>
                    <td>
                        <label>
                            <?=__('From:','');?> <input type="number" min="0" name="_wcv_fake_mode_from" value="<?=get_option('_wcv_fake_mode_from','0')?>" />
                            <?=__('To:','');?> <input type="number" min="0" name="_wcv_fake_mode_to" value="<?=get_option('_wcv_fake_mode_to','0')?>" />
                        </label>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?=__('Icon', 'counter-visitor-for-woocommerce')?>
                        <p class="description"><?=__('You can use always icon, fontawesome, only class name for example: fas fa-eye','counter-visitor-for-woocommerce')?></p>
                    </th>
                    <td>
                        <label>
                            <input type="text" name="_wcv_icon" value="<?=get_option('_wcv_icon','dashicons dashicons-visibility')?>" />
                        </label>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?=__('¿Problem with Icon?', 'counter-visitor-for-woocommerce')?>
                        <p class="description"><?=__('Load FontAwesome Library.','counter-visitor-for-woocommerce')?></p>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox" name="_wcv_fontawesome" value="1" <?=checked('1', get_option('_wcv_fontawesome', '0'))?> />
                        </label>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?=__('Message more than one user', 'counter-visitor-for-woocommerce')?>
                        <p class="description"><?=__('%n is replaced by number visitors','counter-visitor-for-woocommerce')?></p>
                    </th>
                    <td>
                        <label>
                            <textarea type="text" style="width:250px;height:250px;" name="_wcv_message"><?=get_option('_wcv_message', __('%n people are viewing this product'))?></textarea>
                        </label>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?=__('Message only one user', 'counter-visitor-for-woocommerce')?></th>
                    <td>
                        <label>
                            <textarea type="text" style="width:250px;height:250px;" name="_wcv_message_one"><?=get_option('_wcv_message_one', __('1 user are viewing this product'))?></textarea>
                        </label>
                    </td>
                </tr>
            </table>
            <input type="submit" class="button" value="<?=__('Save','counter-visitor-for-woocommerce')?>" />
        </form>

        <h2><?=__('Need style?', 'counter-visitor-for-woocommerce')?></h2>
        <p><?=__('Enjoy! Paste this CSS code into your Customizer and edit as you like','counter-visitor-for-woocommerce')?></p>
        <pre>
.wcv-message {

}
.wcv-message span.icon {

}

.wcv-message span.wcvisitor_num {

}
        </pre>
    <?php } ?>

</div>
