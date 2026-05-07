<?php
/**
 * Plugin Name:       Mourtzilaki Analytics
 * Plugin URI:        https://mourtzilakilaw.gr
 * Description:       Self-hosted, privacy-respecting analytics για το γραφείο. Καταγραφή προβολών ανά σελίδα, υποβολές φορμών, και dashboard με γραφικά μέσα στο WP admin.
 * Version:           1.0.0
 * Requires PHP:      8.0
 * Requires at least: 6.0
 * Author:            Mourtzilaki Law
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class Mourtzilaki_Analytics {

    const META_VIEWS    = '_mz_views';
    const OPT_DAILY     = 'mz_views_log';
    const OPT_FORM_LOG  = 'mz_form_log';
    const OPT_FORM_TOTAL = 'mz_form_total';

    public static function init() {
        $self = new self();
        add_action( 'template_redirect',  array( $self, 'track_view' ) );
        add_action( 'wpcf7_mail_sent',    array( $self, 'track_form' ) );
        add_action( 'admin_menu',         array( $self, 'add_admin_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $self, 'enqueue_assets' ) );
    }

    /* ---------- Tracking --------------------------------------- */

    public function track_view() {
        if ( is_admin() ) { return; }
        if ( ! is_singular() ) { return; }
        if ( is_preview() || is_customize_preview() ) { return; }
        if ( current_user_can( 'edit_posts' ) ) { return; }
        if ( $this->is_bot() ) { return; }

        $post_id = get_queried_object_id();
        if ( ! $post_id ) { return; }

        // Per-post counter.
        $current = (int) get_post_meta( $post_id, self::META_VIEWS, true );
        update_post_meta( $post_id, self::META_VIEWS, $current + 1 );

        // Daily aggregate (last 90 days).
        $log   = (array) get_option( self::OPT_DAILY, array() );
        $today = current_time( 'Y-m-d' );
        $log[ $today ] = ( isset( $log[ $today ] ) ? (int) $log[ $today ] : 0 ) + 1;

        $cutoff = strtotime( '-89 days', current_time( 'timestamp' ) );
        foreach ( $log as $date => $_ ) {
            if ( strtotime( $date ) < $cutoff ) { unset( $log[ $date ] ); }
        }
        update_option( self::OPT_DAILY, $log, false );
    }

    public function track_form( $contact_form ) {
        $total = (int) get_option( self::OPT_FORM_TOTAL, 0 );
        update_option( self::OPT_FORM_TOTAL, $total + 1, false );

        $log = (array) get_option( self::OPT_FORM_LOG, array() );

        $submission = class_exists( 'WPCF7_Submission' ) ? WPCF7_Submission::get_instance() : null;
        $data       = $submission ? $submission->get_posted_data() : array();
        $name       = isset( $data['your-name'] )    ? sanitize_text_field( $data['your-name'] )    : '';
        $email      = isset( $data['your-email'] )   ? sanitize_email( $data['your-email'] )         : '';
        $subject    = isset( $data['your-subject'] ) ? sanitize_text_field( $data['your-subject'] ) : '';

        array_unshift( $log, array(
            'time'    => current_time( 'mysql' ),
            'name'    => $name,
            'email'   => $email,
            'subject' => $subject,
        ) );
        $log = array_slice( $log, 0, 50 );
        update_option( self::OPT_FORM_LOG, $log, false );
    }

    private function is_bot() {
        $ua = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '';
        return (bool) preg_match( '/bot|crawl|spider|wget|curl|facebookexternalhit|slurp|bingpreview/i', $ua );
    }

    /* ---------- Admin page ------------------------------------- */

    public function add_admin_menu() {
        add_menu_page(
            'Analytics',
            'Analytics',
            'manage_options',
            'mz-analytics',
            array( $this, 'render_dashboard' ),
            'dashicons-chart-line',
            3
        );
    }

    public function enqueue_assets( $hook ) {
        if ( 'toplevel_page_mz-analytics' !== $hook ) { return; }
        wp_enqueue_style(  'mz-analytics', plugins_url( 'assets/admin.css', __FILE__ ), array(), '1.0.0' );
        wp_enqueue_script( 'chartjs', 'https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js', array(), '4.4.1', true );
        wp_enqueue_script( 'mz-analytics', plugins_url( 'assets/admin.js', __FILE__ ), array( 'chartjs' ), '1.0.0', true );

        $data = $this->get_dashboard_data();
        wp_localize_script( 'mz-analytics', 'MZ_ANALYTICS', array(
            'series_labels' => array_keys( $data['thirty_days'] ),
            'series_values' => array_values( $data['thirty_days'] ),
        ) );
    }

    public function get_dashboard_data() {
        $log = (array) get_option( self::OPT_DAILY, array() );

        $thirty = array();
        for ( $i = 29; $i >= 0; $i-- ) {
            $d = date( 'Y-m-d', strtotime( "-{$i} days", current_time( 'timestamp' ) ) );
            $thirty[ $d ] = isset( $log[ $d ] ) ? (int) $log[ $d ] : 0;
        }

        $total_views = array_sum( $log );
        $today_views = isset( $log[ current_time( 'Y-m-d' ) ] ) ? (int) $log[ current_time( 'Y-m-d' ) ] : 0;

        $week = 0; $prev_week = 0;
        for ( $i = 0; $i < 7; $i++ ) {
            $d = date( 'Y-m-d', strtotime( "-{$i} days", current_time( 'timestamp' ) ) );
            $week += isset( $log[ $d ] ) ? (int) $log[ $d ] : 0;
        }
        for ( $i = 7; $i < 14; $i++ ) {
            $d = date( 'Y-m-d', strtotime( "-{$i} days", current_time( 'timestamp' ) ) );
            $prev_week += isset( $log[ $d ] ) ? (int) $log[ $d ] : 0;
        }
        $week_change = $prev_week > 0 ? round( ( ( $week - $prev_week ) / $prev_week ) * 100 ) : ( $week > 0 ? 100 : 0 );

        $top_posts = get_posts( array(
            'post_type'      => array( 'post', 'page', 'mz_service' ),
            'posts_per_page' => 8,
            'meta_key'       => self::META_VIEWS,
            'orderby'        => 'meta_value_num',
            'order'          => 'DESC',
            'post_status'    => 'publish',
        ) );

        $form_log    = (array) get_option( self::OPT_FORM_LOG, array() );
        $form_total  = (int) get_option( self::OPT_FORM_TOTAL, 0 );
        $form_today  = 0;
        $today_str   = current_time( 'Y-m-d' );
        foreach ( $form_log as $entry ) {
            if ( ! empty( $entry['time'] ) && substr( $entry['time'], 0, 10 ) === $today_str ) {
                $form_today++;
            }
        }

        $content_count = wp_count_posts()->publish
            + ( wp_count_posts( 'page' )->publish ?? 0 )
            + ( wp_count_posts( 'mz_service' )->publish ?? 0 );

        $conv_rate = $total_views > 0 ? round( ( $form_total / $total_views ) * 100, 1 ) : 0;

        return array(
            'thirty_days'   => $thirty,
            'total_views'   => $total_views,
            'today_views'   => $today_views,
            'week_views'    => $week,
            'week_change'   => $week_change,
            'top_posts'     => $top_posts,
            'form_total'    => $form_total,
            'form_today'    => $form_today,
            'form_log'      => array_slice( $form_log, 0, 10 ),
            'content_count' => (int) $content_count,
            'conv_rate'     => $conv_rate,
        );
    }

    public function render_dashboard() {
        $d = $this->get_dashboard_data();
        ?>
        <div class="wrap mz-an-wrap">

            <div class="mz-an-head">
                <div>
                    <h1>Analytics</h1>
                    <p>Επισκεψιμότητα και υποβολές φόρμας — τελευταίες 30 ημέρες.</p>
                </div>
                <span class="mz-an-when"><?php echo esc_html( wp_date( 'j M Y · H:i' ) ); ?></span>
            </div>

            <div class="mz-an-kpis">
                <div class="mz-an-kpi">
                    <span class="mz-an-kpi-lab">Συνολικές προβολές</span>
                    <span class="mz-an-kpi-val"><?php echo number_format_i18n( $d['total_views'] ); ?></span>
                    <span class="mz-an-kpi-sub <?php echo $d['week_change'] >= 0 ? 'up' : 'down'; ?>">
                        <?php echo $d['week_change'] >= 0 ? '↑' : '↓'; ?> <?php echo abs( $d['week_change'] ); ?>% τη βδομάδα
                    </span>
                </div>
                <div class="mz-an-kpi">
                    <span class="mz-an-kpi-lab">Σήμερα</span>
                    <span class="mz-an-kpi-val"><?php echo number_format_i18n( $d['today_views'] ); ?></span>
                    <span class="mz-an-kpi-sub"><?php echo number_format_i18n( $d['week_views'] ); ?> · 7 ημέρες</span>
                </div>
                <div class="mz-an-kpi">
                    <span class="mz-an-kpi-lab">Φόρμες</span>
                    <span class="mz-an-kpi-val"><?php echo number_format_i18n( $d['form_total'] ); ?></span>
                    <span class="mz-an-kpi-sub"><?php echo number_format_i18n( $d['form_today'] ); ?> σήμερα</span>
                </div>
                <div class="mz-an-kpi">
                    <span class="mz-an-kpi-lab">Conversion</span>
                    <span class="mz-an-kpi-val"><?php echo esc_html( $d['conv_rate'] ); ?>%</span>
                    <span class="mz-an-kpi-sub"><?php echo number_format_i18n( $d['content_count'] ); ?> items</span>
                </div>
            </div>

            <div class="mz-an-card">
                <div class="mz-an-card-h">
                    <h2>Επισκεψιμότητα · 30 ημέρες</h2>
                    <span class="mz-an-tag">Daily pageviews</span>
                </div>
                <div class="mz-an-chart">
                    <canvas id="mz-views-chart"></canvas>
                </div>
            </div>

            <div class="mz-an-grid">
                <div class="mz-an-card">
                    <div class="mz-an-card-h">
                        <h2>Top προβολές</h2>
                        <span class="mz-an-tag">Όλες οι ώρες</span>
                    </div>
                    <?php if ( empty( $d['top_posts'] ) ) : ?>
                        <div class="mz-an-empty">Δεν υπάρχουν αρκετά δεδομένα ακόμη.</div>
                    <?php else : ?>
                    <ol class="mz-an-list">
                        <?php $rank = 0; foreach ( $d['top_posts'] as $p ) :
                            $views = (int) get_post_meta( $p->ID, self::META_VIEWS, true );
                            if ( ! $views ) { continue; }
                            $rank++;
                            $type = get_post_type_object( $p->post_type );
                            $type_label = $type ? $type->labels->singular_name : $p->post_type;
                        ?>
                            <li>
                                <span class="mz-an-rank"><?php echo (int) $rank; ?>.</span>
                                <a class="mz-an-title" href="<?php echo esc_url( get_permalink( $p ) ); ?>" target="_blank" rel="noopener" title="<?php echo esc_attr( $type_label ); ?>">
                                    <?php echo esc_html( get_the_title( $p ) ); ?>
                                </a>
                                <span class="mz-an-count"><?php echo number_format_i18n( $views ); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                    <?php endif; ?>
                </div>

                <div class="mz-an-card">
                    <div class="mz-an-card-h">
                        <h2>Πρόσφατες υποβολές φόρμας</h2>
                        <span class="mz-an-tag">Τελευταίες 10</span>
                    </div>
                    <?php if ( empty( $d['form_log'] ) ) : ?>
                        <div class="mz-an-empty-state">
                            <span class="mz-an-empty-ic" aria-hidden="true">
                                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.4"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            </span>
                            <p>Δεν έχουν παραληφθεί ακόμη μηνύματα.</p>
                        </div>
                    <?php else : ?>
                        <ul class="mz-an-msgs">
                            <?php foreach ( $d['form_log'] as $entry ) :
                                $time = ! empty( $entry['time'] ) ? mysql2date( 'j M · H:i', $entry['time'] ) : '';
                                $name = ! empty( $entry['name'] ) ? $entry['name'] : 'Άγνωστος';
                            ?>
                                <li>
                                    <span class="mz-an-avatar"><?php echo esc_html( mb_strtoupper( mb_substr( $name, 0, 1 ) ) ); ?></span>
                                    <span class="mz-an-msg-body">
                                        <strong><?php echo esc_html( $name ); ?></strong>
                                        <?php if ( ! empty( $entry['email'] ) ) : ?>
                                            <span class="mz-an-msg-email"><?php echo esc_html( $entry['email'] ); ?></span>
                                        <?php endif; ?>
                                    </span>
                                    <span class="mz-an-msg-time"><?php echo esc_html( $time ); ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>

            <p class="mz-an-foot">
                <span>Self-hosted · χωρίς cookies · χωρίς τρίτους.</span>
            </p>
        </div>
        <?php
    }
}

Mourtzilaki_Analytics::init();
