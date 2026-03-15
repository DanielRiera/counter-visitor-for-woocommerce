<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! function_exists( 'wcvisitor_format_stat_number' ) ) {
    function wcvisitor_format_stat_number( $number ) {
        $number = (int) $number;

        if ( $number < 1000 ) {
            return number_format_i18n( $number );
        }

        $compact = round( $number / 1000, $number >= 10000 ? 0 : 1 );

        if ( floor( $compact ) === $compact ) {
            $compact = (int) $compact;
        }

        return str_replace( ',', '.', (string) $compact ) . 'k';
    }
}

if ( ! function_exists( 'wcvisitor_format_status_label' ) ) {
    function wcvisitor_format_status_label( $product ) {
        if ( ! $product || ! method_exists( $product, 'get_status' ) ) {
            return '';
        }

        $status = (string) $product->get_status();

        if ( '' === $status ) {
            return '';
        }

        $status_object = get_post_status_object( $status );

        if ( $status_object && ! empty( $status_object->label ) ) {
            return (string) $status_object->label;
        }

        return ucfirst( $status );
    }
}
?>
<style>
    .wrap.wcvpanel{
        max-width:none;
        width:100%;
        box-sizing:border-box;
        color:#1f2937;
    }
    .wcv-stats-shell{
        width:100%;
        box-sizing:border-box;
        background:linear-gradient(180deg,#f8fafc 0%,#ffffff 100%);
        border:1px solid #dbe3ea;
        border-radius:18px;
        padding:24px;
        box-shadow:0 10px 30px rgba(15,23,42,.06);
    }
    .wcv-stats-header{
        display:flex;
        justify-content:space-between;
        gap:16px;
        align-items:flex-start;
        margin-bottom:22px;
        flex-wrap:wrap;
    }
    .wcv-stats-header h1{
        margin:0 0 8px;
        font-size:30px;
        line-height:1.1;
    }
    .wcv-stats-header p{
        margin:0;
        color:#5b6574;
        max-width:720px;
    }
    .wcv-stats-period-chip{
        padding:10px 14px;
        border-radius:999px;
        background:#e8f3eb;
        color:#23603b;
        font-weight:600;
        white-space:nowrap;
    }
    .wcv-stats-filters{
        margin:0 0 22px;
        display:flex;
        gap:10px;
        flex-wrap:wrap;
    }
    .wcv-stats-filters a{
        text-decoration:none;
        border-radius:999px;
    }
    .wcv-stats-cards{
        display:grid;
        grid-template-columns:repeat(4,minmax(0,1fr));
        gap:14px;
        margin-bottom:24px;
    }
    .wcv-stats-card{
        background:#fff;
        border:1px solid #e5ebf1;
        border-radius:16px;
        padding:18px;
        box-shadow:0 8px 24px rgba(15,23,42,.04);
        min-width:0;
    }
    .wcv-stats-card-label{
        font-size:12px;
        text-transform:uppercase;
        letter-spacing:.08em;
        color:#6b7280;
        margin-bottom:10px;
    }
    .wcv-stats-card-value{
        font-size:28px;
        font-weight:700;
        line-height:1;
        margin-bottom:8px;
        white-space:nowrap;
        overflow:hidden;
        text-overflow:ellipsis;
    }
    .wcv-stats-card-meta{
        color:#64748b;
        font-size:13px;
    }
    .wcv-stats-table-wrap{
        overflow:auto;
        border:1px solid #e5ebf1;
        border-radius:16px;
        background:#fff;
    }
    .wcv-stats-table{
        width:100%;
        border-collapse:separate;
        border-spacing:0;
        min-width:760px;
    }
    .wcv-stats-table th{
        text-align:left;
        padding:14px 16px;
        font-size:12px;
        text-transform:uppercase;
        letter-spacing:.08em;
        color:#6b7280;
        background:#f8fafc;
        border-bottom:1px solid #e5ebf1;
    }
    .wcv-stats-table td{
        padding:16px;
        border-bottom:1px solid #eef2f6;
        vertical-align:middle;
    }
    .wcv-stats-table tr:last-child td{
        border-bottom:0;
    }
    .wcv-stats-product{
        display:flex;
        align-items:center;
        gap:14px;
        min-width:280px;
    }
    .wcv-stats-thumb{
        width:56px;
        height:56px;
        border-radius:14px;
        overflow:hidden;
        background:#eef2f6;
        flex:0 0 56px;
        display:flex;
        align-items:center;
        justify-content:center;
    }
    .wcv-stats-thumb img{
        width:100%;
        height:100%;
        object-fit:cover;
        display:block;
    }
    .wcv-stats-thumb-placeholder{
        font-size:20px;
        font-weight:700;
        color:#94a3b8;
    }
    .wcv-stats-product-title{
        margin:0 0 4px;
        font-size:15px;
        font-weight:600;
    }
    .wcv-stats-product-title a{
        text-decoration:none;
    }
    .wcv-stats-product-meta{
        display:flex;
        gap:10px;
        flex-wrap:wrap;
        font-size:12px;
        color:#64748b;
    }
    .wcv-stats-product-meta a{
        text-decoration:none;
    }
    .wcv-stats-badge{
        display:inline-flex;
        align-items:center;
        padding:6px 10px;
        border-radius:999px;
        background:#eef6ff;
        color:#1d4ed8;
        font-weight:600;
        font-size:12px;
        white-space:nowrap;
    }
    .wcv-stats-badge-live{
        background:#e8f7ef;
        color:#1f7a45;
    }
    .wcv-stats-number{
        font-size:18px;
        font-weight:700;
        white-space:nowrap;
    }
    .wcv-stats-empty{
        padding:36px 24px;
        text-align:center;
        color:#64748b;
    }
    .wcv-stats-pagination{
        margin-top:18px;
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:12px;
        flex-wrap:wrap;
    }
    .wcv-stats-pagination-info{
        color:#64748b;
        font-size:13px;
    }
    .wcv-stats-pagination-actions{
        display:flex;
        gap:8px;
        align-items:center;
    }
    @media (max-width: 1100px){
        .wcv-stats-cards{
            grid-template-columns:repeat(2,minmax(0,1fr));
        }
    }
    @media (max-width: 640px){
        .wcv-stats-shell{
            padding:16px;
        }
        .wcv-stats-cards{
            grid-template-columns:1fr;
        }
    }
</style>

<?php
$period_label = isset( $allowed_periods[ $period ] ) ? $allowed_periods[ $period ] : $allowed_periods['all'];
$base_url     = admin_url( 'admin.php?page=wcvisitor-stats&period=' . $period );
$range_start  = $stats['total'] > 0 ? ( ( $current_page - 1 ) * $stats['per_page'] ) + 1 : 0;
$range_end    = min( $stats['total'], $current_page * $stats['per_page'] );
$is_live      = in_array( $period, array( '2m', '5m' ), true );
?>

<div class="wrap wcvpanel">
    <div class="wcv-stats-shell">
        <div class="wcv-stats-header">
            <div>
                <div style="font-size: 40px; padding-bottom: 15px"><?=esc_html__( 'Visitor Stats', 'counter-visitor-for-woocommerce' )?></div>
                <span style="font-size: 20px; font-style: italic; padding-bottom: 10px">by Counter live visitors for WooCommerce</span>
                <p><?=esc_html__( 'Real statistics are only stored when Fake Mode is disabled. Use this panel to identify products with the most traffic and recurring interest.', 'counter-visitor-for-woocommerce' )?></p>
            </div>
            <div class="wcv-stats-period-chip"><?=esc_html( $period_label )?></div>
        </div>

        <div class="wcv-stats-filters">
            <?php foreach ( $allowed_periods as $period_key => $period_option_label ) { ?>
                <a class="button <?=$period === $period_key ? 'button-primary' : ''?>" href="<?=esc_url( admin_url( 'admin.php?page=wcvisitor-stats&period=' . $period_key ) )?>"><?=esc_html( $period_option_label )?></a>
            <?php } ?>
        </div>

        <div class="wcv-stats-cards">
            <div class="wcv-stats-card">
                <div class="wcv-stats-card-label"><?=esc_html__( 'Tracked products', 'counter-visitor-for-woocommerce' )?></div>
                <div class="wcv-stats-card-value"><?=esc_html( wcvisitor_format_stat_number( $summary['products'] ) )?></div>
                <div class="wcv-stats-card-meta"><?=esc_html__( 'Products with registered traffic in this period', 'counter-visitor-for-woocommerce' )?></div>
            </div>
            <div class="wcv-stats-card">
                <div class="wcv-stats-card-label"><?=esc_html__( 'Total views', 'counter-visitor-for-woocommerce' )?></div>
                <div class="wcv-stats-card-value"><?=esc_html( wcvisitor_format_stat_number( $summary['total_views'] ) )?></div>
                <div class="wcv-stats-card-meta"><?=esc_html__( 'All stored product views for the selected range', 'counter-visitor-for-woocommerce' )?></div>
            </div>
            <div class="wcv-stats-card">
                <div class="wcv-stats-card-label"><?=esc_html__( 'Unique visitors', 'counter-visitor-for-woocommerce' )?></div>
                <div class="wcv-stats-card-value"><?=esc_html( wcvisitor_format_stat_number( $summary['unique_visitors'] ) )?></div>
                <div class="wcv-stats-card-meta"><?=esc_html__( 'Distinct anonymous visitors across tracked products', 'counter-visitor-for-woocommerce' )?></div>
            </div>
            <div class="wcv-stats-card">
                <div class="wcv-stats-card-label"><?=esc_html__( 'Last activity', 'counter-visitor-for-woocommerce' )?></div>
                <div class="wcv-stats-card-value"><?=esc_html( $summary['last_seen_gmt'] ? mysql2date( 'Y-m-d H:i', $summary['last_seen_gmt'], false ) : '—' )?></div>
                <div class="wcv-stats-card-meta"><?=esc_html( $is_live ? __( 'Live window in GMT', 'counter-visitor-for-woocommerce' ) : __( 'Timestamp stored in GMT', 'counter-visitor-for-woocommerce' ) )?></div>
            </div>
        </div>

        <div class="wcv-stats-table-wrap">
            <table class="wcv-stats-table">
                <thead>
                    <tr>
                        <th><?=esc_html__( 'Product', 'counter-visitor-for-woocommerce' )?></th>
                        <th><?=esc_html__( 'Views', 'counter-visitor-for-woocommerce' )?></th>
                        <th><?=esc_html__( 'Unique', 'counter-visitor-for-woocommerce' )?></th>
                        <th><?=esc_html__( 'Status', 'counter-visitor-for-woocommerce' )?></th>
                        <th><?=esc_html__( 'Last activity (GMT)', 'counter-visitor-for-woocommerce' )?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ( empty( $rows ) ) { ?>
                        <tr>
                            <td colspan="5" class="wcv-stats-empty"><?=esc_html__( 'No statistics available for this period.', 'counter-visitor-for-woocommerce' )?></td>
                        </tr>
                    <?php } else { ?>
                        <?php foreach ( $rows as $row ) { ?>
                            <?php
                            $product       = wc_get_product( absint( $row['product_id'] ) );
                            $product_title = $product ? $product->get_name() : '#' . absint( $row['product_id'] );
                            $edit_link     = $product ? get_edit_post_link( $product->get_id() ) : '';
                            $view_link     = $product ? get_permalink( $product->get_id() ) : '';
                            $thumb_id      = $product && method_exists( $product, 'get_image_id' ) ? absint( $product->get_image_id() ) : 0;
                            $thumb_url     = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'thumbnail' ) : '';
                            $status_label  = wcvisitor_format_status_label( $product );
                            $traffic_label = $is_live ? __( 'Live now', 'counter-visitor-for-woocommerce' ) : __( 'Tracked', 'counter-visitor-for-woocommerce' );

                            if ( ! $is_live ) {
                                if ( (int) $row['total_views'] >= 100 ) {
                                    $traffic_label = __( 'High traffic', 'counter-visitor-for-woocommerce' );
                                } elseif ( (int) $row['total_views'] >= 25 ) {
                                    $traffic_label = __( 'Growing', 'counter-visitor-for-woocommerce' );
                                } else {
                                    $traffic_label = __( 'Emerging', 'counter-visitor-for-woocommerce' );
                                }
                            }
                            ?>
                            <tr>
                                <td>
                                    <div class="wcv-stats-product">
                                        <div class="wcv-stats-thumb">
                                            <?php if ( $thumb_url ) { ?>
                                                <img src="<?=esc_url( $thumb_url )?>" alt="<?=esc_attr( $product_title )?>" loading="lazy" />
                                            <?php } else { ?>
                                                <span class="wcv-stats-thumb-placeholder"><?=esc_html( strtoupper( substr( $product_title, 0, 1 ) ) )?></span>
                                            <?php } ?>
                                        </div>
                                        <div>
                                            <p class="wcv-stats-product-title">
                                                <?php if ( $edit_link ) { ?>
                                                    <a href="<?=esc_url( $edit_link )?>"><?=esc_html( $product_title )?></a>
                                                <?php } else { ?>
                                                    <?=esc_html( $product_title )?>
                                                <?php } ?>
                                            </p>
                                            <div class="wcv-stats-product-meta">
                                                <span>#<?=absint( $row['product_id'] )?></span>
                                                <?php if ( $view_link ) { ?>
                                                    <a href="<?=esc_url( $view_link )?>" target="_blank" rel="noopener noreferrer"><?=esc_html__( 'View product', 'counter-visitor-for-woocommerce' )?></a>
                                                <?php } ?>
                                                <?php if ( $status_label ) { ?>
                                                    <span><?=esc_html( $status_label )?></span>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="wcv-stats-number"><?=esc_html( wcvisitor_format_stat_number( $row['total_views'] ) )?></span></td>
                                <td><span class="wcv-stats-number"><?=esc_html( wcvisitor_format_stat_number( $row['unique_visitors'] ) )?></span></td>
                                <td>
                                    <span class="wcv-stats-badge <?=$is_live ? 'wcv-stats-badge-live' : ''?>"><?=esc_html( $traffic_label )?></span>
                                </td>
                                <td><?=esc_html( $row['last_seen_gmt'] ? mysql2date( 'Y-m-d H:i:s', $row['last_seen_gmt'], false ) : '—' )?></td>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <?php if ( $total_pages > 1 ) { ?>
            <div class="wcv-stats-pagination">
                <div class="wcv-stats-pagination-info">
                    <?=sprintf( esc_html__( 'Showing %1$d-%2$d of %3$d products', 'counter-visitor-for-woocommerce' ), absint( $range_start ), absint( $range_end ), absint( $stats['total'] ) )?>
                </div>
                <div class="wcv-stats-pagination-actions">
                    <?php if ( $current_page > 1 ) { ?>
                        <a class="button" href="<?=esc_url( $base_url . '&paged=' . ( $current_page - 1 ) )?>"><?=esc_html__( 'Previous', 'counter-visitor-for-woocommerce' )?></a>
                    <?php } ?>
                    <span><?=sprintf( esc_html__( 'Page %1$d of %2$d', 'counter-visitor-for-woocommerce' ), absint( $current_page ), absint( $total_pages ) )?></span>
                    <?php if ( $current_page < $total_pages ) { ?>
                        <a class="button button-primary" href="<?=esc_url( $base_url . '&paged=' . ( $current_page + 1 ) )?>"><?=esc_html__( 'Next', 'counter-visitor-for-woocommerce' )?></a>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
