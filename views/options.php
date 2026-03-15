<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

$currentPosition = get_option( '_wcv_position', 'woocommerce_after_add_to_cart_button' );
?>
<style>
    .wrap.wcvpanel{
        max-width:none;
        width:100%;
        box-sizing:border-box;
        color:#1f2937;
    }
    .wcv-options-shell{
        width:100%;
        box-sizing:border-box;
        background:linear-gradient(180deg,#f8fafc 0%,#ffffff 100%);
        border:1px solid #dbe3ea;
        border-radius:18px;
        padding:24px;
        box-shadow:0 10px 30px rgba(15,23,42,.06);
    }
    .wcv-options-header{
        display:flex;
        justify-content:space-between;
        gap:16px;
        align-items:flex-start;
        margin-bottom:22px;
        flex-wrap:wrap;
    }
    .wcv-options-header h1{
        margin:0 0 8px;
        font-size:30px;
        line-height:1.1;
    }
    .wcv-options-header p{
        margin:0;
        color:#5b6574;
        max-width:760px;
    }
    .wcv-options-chip{
        padding:10px 14px;
        border-radius:999px;
        background:#e8f3eb;
        color:#23603b;
        font-weight:600;
        white-space:nowrap;
    }
    .wcv-options-notices{
        margin-bottom:18px;
    }
    .wcv-options-actions{
        display:flex;
        gap:12px;
        flex-wrap:wrap;
        margin-bottom:24px;
    }
    .wcv-options-donate{
        text-decoration:none;
        display:inline-flex;
        align-items:center;
        gap:8px;
        padding:12px 18px;
        border-radius:999px;
        background:#ffffff;
        border:1px solid #dbe3ea;
        color:#1f2937;
        box-shadow:0 8px 24px rgba(15,23,42,.04);
    }
    .wcv-options-grid{
        display:grid;
        grid-template-columns:minmax(0,2fr) minmax(300px,1fr);
        gap:18px;
        align-items:start;
    }
    .wcv-options-panel{
        background:#ffffff;
        border:1px solid #e5ebf1;
        border-radius:16px;
        box-shadow:0 8px 24px rgba(15,23,42,.04);
        overflow:hidden;
    }
    .wcv-options-panel-header{
        padding:18px 20px 10px;
    }
    .wcv-options-panel-title{
        margin:0 0 6px;
        font-size:20px;
    }
    .wcv-options-panel-text{
        margin:0;
        color:#64748b;
    }
    .wcv-options-table{
        width:100%;
        border-collapse:separate;
        border-spacing:0;
    }
    .wcv-options-table th{
        width:34%;
        padding:18px 20px;
        text-align:left;
        vertical-align:top;
        border-top:1px solid #eef2f6;
        background:#f8fafc;
    }
    .wcv-options-table td{
        padding:18px 20px;
        border-top:1px solid #eef2f6;
        vertical-align:middle;
    }
    .wcv-options-table th strong{
        display:block;
        font-size:14px;
        color:#111827;
        margin-bottom:8px;
    }
    .wcv-options-table .description{
        margin:0;
        color:#64748b;
        font-size:13px;
        line-height:1.5;
    }
    .wcv-options-control{
        display:flex;
        flex-wrap:wrap;
        gap:12px;
        align-items:center;
    }
    .wcv-options-table input[type="email"],
    .wcv-options-table input[type="number"],
    .wcv-options-table input[type="text"],
    .wcv-options-table textarea,
    .wcv-options-table select,
    .wcv-newsletter-form input[type="email"]{
        width:100%;
        max-width:420px;
        padding:12px 14px;
        border:1px solid #d7e0e8;
        border-radius:12px;
        box-sizing:border-box;
        font-size:14px;
        transition:border-color .18s ease, box-shadow .18s ease, background .18s ease;
        background:#ffffff;
    }
    .wcv-options-table textarea{
        max-width:520px;
        min-height:150px;
        resize:vertical;
    }
    .wcv-options-table input:focus,
    .wcv-options-table textarea:focus,
    .wcv-options-table select:focus,
    .wcv-newsletter-form input:focus{
        border-color:#3c853c;
        box-shadow:0 0 0 4px rgba(60,133,60,.12);
        outline:0;
    }
    .wcv-options-table input[type="checkbox"]{
        margin:0;
    }
    .wcv-options-boolean{
        display:inline-flex;
        gap:10px;
        min-height:44px;
    }
    .wcv-options-range{
        display:flex;
        gap:12px;
        flex-wrap:wrap;
    }
    .wcv-options-range label{
        display:flex;
        align-items:center;
        gap:8px;
        color:#374151;
    }
    .wcv-options-submit{
        padding:18px 20px 22px;
        border-top:1px solid #eef2f6;
    }
    .wcv-newsletter-panel,
    .wcv-help-panel{
        padding:20px;
    }
    .wcv-newsletter-panel h3,
    .wcv-help-panel h2{
        margin:0 0 8px;
    }
    .wcv-newsletter-panel p,
    .wcv-help-panel p{
        color:#64748b;
    }
    .wcv-newsletter-form{
        display:flex;
        flex-direction:column;
        gap:14px;
    }
    .wcv-newsletter-form .button,
    .wcv-options-submit .button{
        background:#2f7d32;
        border:0;
        color:#fff;
        padding:12px 18px;
        border-radius:12px;
        cursor:pointer;
        font-size:14px;
        line-height:1.1;
        transition:background .18s ease, transform .18s ease;
        text-decoration:none;
    }
    .wcv-newsletter-form .button:hover,
    .wcv-options-submit .button:hover{
        background:#256428;
        transform:translateY(-1px);
    }
    .wcv-help-code{
        background:#f8fafc;
        border:1px solid #e5ebf1;
        border-radius:14px;
        padding:18px;
        overflow:auto;
        color:#334155;
    }
    #anotheremail{
        position:absolute;
        left:-9999px;
    }
    @media (max-width: 1080px){
        .wcv-options-grid{
            grid-template-columns:1fr;
        }
    }
    @media (max-width: 782px){
        .wcv-options-shell{
            padding:16px;
        }
        .wcv-options-table,
        .wcv-options-table tbody,
        .wcv-options-table tr,
        .wcv-options-table th,
        .wcv-options-table td{
            display:block;
            width:100%;
            box-sizing:border-box;
        }
        .wcv-options-table th{
            border-top:1px solid #eef2f6;
            border-bottom:0;
            padding-bottom:10px;
        }
        .wcv-options-table td{
            border-top:0;
            padding-top:0;
        }
        .wcv-options-table input[type="email"],
        .wcv-options-table input[type="number"],
        .wcv-options-table input[type="text"],
        .wcv-options-table textarea,
        .wcv-options-table select,
        .wcv-newsletter-form input[type="email"]{
            max-width:none;
        }
    }
</style>

<div class="wrap wcvpanel">
    <div class="wcv-options-shell">
        <?php if ( ! empty( $notices ) ) { ?>
            <div class="wcv-options-notices">
                <?php foreach ( $notices as $notice ) { ?>
                    <div class="notice notice-<?=esc_attr($notice['type'] === 'success' ? 'success' : 'error')?>"><p><?=esc_html($notice['message'])?></p></div>
                <?php } ?>
            </div>
        <?php } ?>

        <div class="wcv-options-header">
            <div>
                <h1><?=__('Counter Visitor for Woocommerce', 'counter-visitor-for-woocommerce')?></h1>
                <p><?=__('It is not a simple visitor counter, this counter is shown on each product with the number of users who are currently viewing that same product','counter-visitor-for-woocommerce')?></p>
            </div>
        </div>

        <div class="wcv-options-actions">
            <a class="wcv-options-donate" href="https://www.paypal.com/donate/?hosted_button_id=EZ67DG78KMXWQ" target="_blank" rel="noopener noreferrer"><?=__('Buy a Coffe? :)','counter-visitor-for-woocommerce')?></a>
        </div>

        <div class="wcv-options-grid">
            <div class="wcv-options-panel">
                <div class="wcv-options-panel-header">
                    <h2 class="wcv-options-panel-title"><?=__('Visitor Counter', 'counter-visitor-for-woocommerce')?></h2>
                    <p class="wcv-options-panel-text"><?=__('Configure how the visitor counter is displayed, refreshed and styled across your WooCommerce product pages.', 'counter-visitor-for-woocommerce')?></p>
                </div>

                <form method="post">
                    <input type="hidden" name="action" value="save_options" />
                    <?php wp_nonce_field( 'wcv_nonce', 'save_option_nonce' ); ?>
                    <table class="wcv-options-table">
                        <tr>
                            <th scope="row">
                                <strong><?=__('Your site use cache system?', 'counter-visitor-for-woocommerce')?></strong>
                                <p class="description"><?=__('Activate this option if your site uses some type of cache and add \'wcvisitor\' to the plugin cache exceptions','counter-visitor-for-woocommerce')?></p>
                            </th>
                            <td>
                                <label class="wcv-options-boolean">
                                    <input type="checkbox" name="_wcv_use_js" value="1" <?=checked('1', get_option('_wcv_use_js', '0'))?> />
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <strong><?=__('Show message after price', 'counter-visitor-for-woocommerce')?></strong>
                                <p class="description"><?=__('Active this options for show counter after price with | separated','counter-visitor-for-woocommerce')?></p>
                            </th>
                            <td>
                                <label class="wcv-options-boolean">
                                    <input type="checkbox" name="_wcvisitor_after_price" value="1" <?=checked('1', get_option('_wcvisitor_after_price', '0'))?> />
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <strong><?=__('Hide counter if only one visitor', 'counter-visitor-for-woocommerce')?></strong>
                                <p class="description"><?=__('Active this options for hide counter when only one visitor on product','counter-visitor-for-woocommerce')?></p>
                            </th>
                            <td>
                                <label class="wcv-options-boolean">
                                    <input type="checkbox" name="_wcvisitor_only_one_hide" value="1" <?=checked('1', get_option('_wcvisitor_only_one_hide', '0'))?> />
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <strong><?=__('Live Mode: Do you want to show users in real time?', 'counter-visitor-for-woocommerce')?></strong>
                                <p class="description"><?=__('This option adds a call per user every X seconds, check its operation on your server, for security less than 5 seconds are not allowed. Use this option considering the resources of your server.','counter-visitor-for-woocommerce')?></p>
                            </th>
                            <td>
                                <label class="wcv-options-boolean">
                                    <input type="checkbox" name="_wcv_live_mode" value="1" <?=checked('1', get_option('_wcv_live_mode', '0'))?> />
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <strong><?=__('How often to update the number of users in the product?', 'counter-visitor-for-woocommerce')?></strong>
                                <p class="description"><?=__('Seconds, min 5 seconds.. (Require Live Move)','counter-visitor-for-woocommerce')?></p>
                            </th>
                            <td>
                                <div class="wcv-options-control">
                                    <input type="number" name="_wcv_live_seconds" value="<?=esc_attr(get_option('_wcv_live_seconds','5'))?>" min="5" />
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <strong><?=__('Duration', 'counter-visitor-for-woocommerce')?></strong>
                                <p class="description"><?=__('Time since last activity of an users to be considered inactive','counter-visitor-for-woocommerce')?></p>
                            </th>
                            <td>
                                <div class="wcv-options-control">
                                    <input type="number" min="30" max="99999999" name="_wcv_timeout_limit" value="<?=esc_attr(get_option('_wcv_timeout_limit', '300'))?>" />
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <strong><?=__('Position', 'counter-visitor-for-woocommerce')?></strong>
                            </th>
                            <td>
                                <div class="wcv-options-control">
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
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <strong><?=__('Weight block', 'counter-visitor-for-woocommerce')?></strong>
                                <p class="description"><?=__('The heavier the weight, the lower the block is displayed','counter-visitor-for-woocommerce')?></p>
                            </th>
                            <td>
                                <div class="wcv-options-control">
                                    <input type="number" min="0" max="300" name="_wcv_weight_block" value="<?=esc_attr(get_option('_wcv_weight_block', '0'))?>" />
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <strong><?=__('Fake Mode', 'counter-visitor-for-woocommerce')?></strong>
                                <p class="description"><?=__('Use Random numbers between from / to','counter-visitor-for-woocommerce')?></p>
                            </th>
                            <td>
                                <label class="wcv-options-boolean">
                                    <input type="checkbox" name="_wcv_fake_mode" value="1" <?=checked("1", get_option('_wcv_fake_mode','0'))?> />
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <strong><?=__('Random Numbers', 'counter-visitor-for-woocommerce')?></strong>
                                <p class="description"><?=__('Need Fake mode, for visitors this value is saved for 25 minutes','counter-visitor-for-woocommerce')?></p>
                            </th>
                            <td>
                                <div class="wcv-options-range">
                                    <label>
                                        <span><?=__('From:','counter-visitor-for-woocommerce')?></span>
                                        <input type="number" min="0" name="_wcv_fake_mode_from" value="<?=esc_attr(get_option('_wcv_fake_mode_from','0'))?>" />
                                    </label>
                                    <label>
                                        <span><?=__('To:','counter-visitor-for-woocommerce')?></span>
                                        <input type="number" min="0" name="_wcv_fake_mode_to" value="<?=esc_attr(get_option('_wcv_fake_mode_to','0'))?>" />
                                    </label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <strong><?=__('Icon', 'counter-visitor-for-woocommerce')?></strong>
                                <p class="description"><?=__('You can use always icon, fontawesome, only class name for example: fas fa-eye','counter-visitor-for-woocommerce')?></p>
                            </th>
                            <td>
                                <div class="wcv-options-control">
                                    <input type="text" name="_wcv_icon" value="<?=esc_attr(get_option('_wcv_icon','dashicons dashicons-visibility'))?>" />
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <strong><?=__('¿Problem with Icon?', 'counter-visitor-for-woocommerce')?></strong>
                                <p class="description"><?=__('Load FontAwesome Library.','counter-visitor-for-woocommerce')?></p>
                            </th>
                            <td>
                                <label class="wcv-options-boolean">
                                    <input type="checkbox" name="_wcv_fontawesome" value="1" <?=checked('1', get_option('_wcv_fontawesome', '0'))?> />
                                    <span>Font Awesome</span>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <strong><?=__('Message more than one user', 'counter-visitor-for-woocommerce')?></strong>
                                <p class="description"><?=__('%n is replaced by number visitors','counter-visitor-for-woocommerce')?></p>
                            </th>
                            <td>
                                <div class="wcv-options-control">
                                    <textarea name="_wcv_message"><?=esc_textarea(get_option('_wcv_message', __('%n people are viewing this product')))?></textarea>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <strong><?=__('Message only one user', 'counter-visitor-for-woocommerce')?></strong>
                            </th>
                            <td>
                                <div class="wcv-options-control">
                                    <textarea name="_wcv_message_one"><?=esc_textarea(get_option('_wcv_message_one', __('1 user are viewing this product')))?></textarea>
                                </div>
                            </td>
                        </tr>
                    </table>
                    <div class="wcv-options-submit">
                        <input type="submit" class="button" value="<?=esc_attr(__('Save','counter-visitor-for-woocommerce'))?>" />
                    </div>
                </form>
            </div>

            <div class="wcv-options-sidebar">
                <?php if($newsletterCounterLive == '0') { ?>
                    <div class="wcv-options-panel wcv-newsletter-panel">
                        <h3><?=__('Do you want to receive the latest?','counter-visitor-for-woocommerce')?></h3>
                        <p><?=__('Thank you very much for using our plugin, if you want to receive the latest news, offers, promotions, discounts, etc ... Sign up for our newsletter. :)', 'counter-visitor-for-woocommerce')?></p>
                        <form class="wcv-newsletter-form" id="new_subscriber" novalidate="novalidate" accept-charset="UTF-8" method="post">
                            <input name="utf8" type="hidden" value="&#x2713;" />
                            <input type="hidden" name="action" value="adsub" />
                            <?php wp_nonce_field( 'wcv_nonce', 'add_sub_nonce' ); ?>
                            <input class="form-control string email required" type="email" name="e" id="subscriber_email" value="<?=esc_attr($user->user_email)?>" placeholder="<?=esc_attr__('Required', 'counter-visitor-for-woocommerce')?>" />
                            <input type="hidden" name="n" value="<?=esc_attr(get_bloginfo('name'))?>" />
                            <input type="hidden" name="w" value="<?=esc_url(get_bloginfo('url'))?>" />
                            <input type="hidden" name="g" value="1,6" />
                            <input type="text" name="anotheremail" id="anotheremail" tabindex="-1" autocomplete="off" />
                            <input type="submit" name="commit" value="<?=esc_attr__('Submit', 'counter-visitor-for-woocommerce')?>" class="button" data-disable-with="<?=esc_attr__('Processing', 'counter-visitor-for-woocommerce')?>" />
                        </form>
                    </div>
                <?php } ?>

                <div class="wcv-options-panel wcv-help-panel">
                    <h2><?=__('Need style?', 'counter-visitor-for-woocommerce')?></h2>
                    <p><?=__('Enjoy! Paste this CSS code into your Customizer and edit as you like','counter-visitor-for-woocommerce')?></p>
                    <pre class="wcv-help-code">.wcv-message {

}
.wcv-message span.icon {

}

.wcv-message span.wcvisitor_num {

}</pre>
                </div>
            </div>
        </div>
    </div>
</div>
