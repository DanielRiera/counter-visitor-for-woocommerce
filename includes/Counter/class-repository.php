<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WCVisitor_Counter_Repository {
    private $wpdb;
    private $table_name;

    public function __construct( wpdb $wpdb ) {
        $this->wpdb       = $wpdb;
        $this->table_name = $wpdb->prefix . 'wcvisitor_activity';
    }

    public function get_table_name() {
        return $this->table_name;
    }

    public function create_table() {
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $charset_collate = $this->wpdb->get_charset_collate();
        $table_name      = $this->table_name;

        $sql = "CREATE TABLE {$table_name} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            product_id bigint(20) unsigned NOT NULL,
            visitor_hash char(64) NOT NULL,
            viewed_at_gmt datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY product_time (product_id, viewed_at_gmt),
            KEY viewed_product (viewed_at_gmt, product_id),
            KEY product_hash (product_id, visitor_hash),
            KEY product_hash_time (product_id, visitor_hash, viewed_at_gmt),
            KEY viewed_at_gmt (viewed_at_gmt)
        ) {$charset_collate};";

        dbDelta( $sql );
    }

    public function record_visit( $product_id, $visitor_hash, $viewed_at_gmt ) {
        return false !== $this->wpdb->insert(
            $this->table_name,
            array(
                'product_id'    => absint( $product_id ),
                'visitor_hash'  => $visitor_hash,
                'viewed_at_gmt' => $viewed_at_gmt,
            ),
            array( '%d', '%s', '%s' )
        );
    }

    public function count_active_visitors( $product_id, $cutoff_gmt ) {
        $sql = $this->wpdb->prepare(
            "SELECT COUNT(DISTINCT visitor_hash) FROM {$this->table_name} WHERE product_id = %d AND viewed_at_gmt >= %s",
            absint( $product_id ),
            $cutoff_gmt
        );

        return (int) $this->wpdb->get_var( $sql );
    }

    public function get_product_stats( $product_id, $from_gmt = null ) {
        $product_id = absint( $product_id );

        if ( $from_gmt ) {
            $sql = $this->wpdb->prepare(
                "SELECT COUNT(*) AS total_views, COUNT(DISTINCT visitor_hash) AS unique_visitors FROM {$this->table_name} WHERE product_id = %d AND viewed_at_gmt >= %s",
                $product_id,
                $from_gmt
            );
        } else {
            $sql = $this->wpdb->prepare(
                "SELECT COUNT(*) AS total_views, COUNT(DISTINCT visitor_hash) AS unique_visitors FROM {$this->table_name} WHERE product_id = %d",
                $product_id
            );
        }

        $row = $this->wpdb->get_row( $sql, ARRAY_A );

        return array(
            'total_views'     => isset( $row['total_views'] ) ? (int) $row['total_views'] : 0,
            'unique_visitors' => isset( $row['unique_visitors'] ) ? (int) $row['unique_visitors'] : 0,
        );
    }

    public function get_period_summary( $from_gmt = null ) {
        if ( $from_gmt ) {
            $sql = $this->wpdb->prepare(
                "SELECT COUNT(*) AS total_views, COUNT(DISTINCT visitor_hash) AS unique_visitors, COUNT(DISTINCT product_id) AS products, MAX(viewed_at_gmt) AS last_seen_gmt
                FROM {$this->table_name}
                WHERE viewed_at_gmt >= %s",
                $from_gmt
            );
        } else {
            $sql = "SELECT COUNT(*) AS total_views, COUNT(DISTINCT visitor_hash) AS unique_visitors, COUNT(DISTINCT product_id) AS products, MAX(viewed_at_gmt) AS last_seen_gmt
                FROM {$this->table_name}";
        }

        $row = $this->wpdb->get_row( $sql, ARRAY_A );

        return array(
            'total_views'     => isset( $row['total_views'] ) ? (int) $row['total_views'] : 0,
            'unique_visitors' => isset( $row['unique_visitors'] ) ? (int) $row['unique_visitors'] : 0,
            'products'        => isset( $row['products'] ) ? (int) $row['products'] : 0,
            'last_seen_gmt'   => isset( $row['last_seen_gmt'] ) ? (string) $row['last_seen_gmt'] : '',
        );
    }

    public function count_top_products( $from_gmt = null ) {
        if ( $from_gmt ) {
            $sql = $this->wpdb->prepare(
                "SELECT COUNT(*) FROM (
                    SELECT product_id
                    FROM {$this->table_name}
                    WHERE viewed_at_gmt >= %s
                    GROUP BY product_id
                ) AS grouped_products",
                $from_gmt
            );
        } else {
            $sql = "SELECT COUNT(*) FROM (
                SELECT product_id
                FROM {$this->table_name}
                GROUP BY product_id
            ) AS grouped_products";
        }

        return (int) $this->wpdb->get_var( $sql );
    }

    public function get_top_products( $from_gmt = null, $limit = 20, $offset = 0 ) {
        $limit = max( 1, absint( $limit ) );
        $offset = max( 0, absint( $offset ) );

        if ( $from_gmt ) {
            $sql = $this->wpdb->prepare(
                "SELECT product_id, COUNT(*) AS total_views, COUNT(DISTINCT visitor_hash) AS unique_visitors, MAX(viewed_at_gmt) AS last_seen_gmt
                FROM {$this->table_name}
                WHERE viewed_at_gmt >= %s
                GROUP BY product_id
                ORDER BY total_views DESC, unique_visitors DESC, last_seen_gmt DESC
                LIMIT %d OFFSET %d",
                $from_gmt,
                $limit,
                $offset
            );
        } else {
            $sql = $this->wpdb->prepare(
                "SELECT product_id, COUNT(*) AS total_views, COUNT(DISTINCT visitor_hash) AS unique_visitors, MAX(viewed_at_gmt) AS last_seen_gmt
                FROM {$this->table_name}
                GROUP BY product_id
                ORDER BY total_views DESC, unique_visitors DESC, last_seen_gmt DESC
                LIMIT %d OFFSET %d",
                $limit,
                $offset
            );
        }

        return $this->wpdb->get_results( $sql, ARRAY_A );
    }
}
