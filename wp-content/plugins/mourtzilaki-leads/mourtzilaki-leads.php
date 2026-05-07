<?php
/**
 * Plugin Name:       Mourtzilaki Leads
 * Plugin URI:        https://mourtzilakilaw.gr
 * Description:       Συγκεντρώνει emails και στοιχεία επικοινωνίας από κάθε υποβολή φόρμας (CF7) σε δικό του πίνακα. Λίστα leads με αναζήτηση, filters, καταστάσεις και export σε CSV.
 * Version:           1.0.0
 * Requires PHP:      8.0
 * Requires at least: 6.0
 * Author:            Mourtzilaki Law
 * Text Domain:       mourtzilaki-leads
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'MZ_LEADS_VER',     '1.0.0' );
define( 'MZ_LEADS_TABLE',   'mz_leads' );
define( 'MZ_LEADS_DB_VER',  'mz_leads_db_version' );

/* =====================================================================
 * Activation: create / migrate the leads table.
 * ================================================================== */
register_activation_hook( __FILE__, 'mz_leads_install' );
function mz_leads_install() {
    global $wpdb;
    $table = $wpdb->prefix . MZ_LEADS_TABLE;
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE {$table} (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        email VARCHAR(190) NOT NULL DEFAULT '',
        name VARCHAR(190) NOT NULL DEFAULT '',
        phone VARCHAR(60) NOT NULL DEFAULT '',
        subject VARCHAR(190) NOT NULL DEFAULT '',
        message LONGTEXT NOT NULL,
        page_url VARCHAR(500) NOT NULL DEFAULT '',
        page_title VARCHAR(190) NOT NULL DEFAULT '',
        form_source VARCHAR(60) NOT NULL DEFAULT 'cf7',
        form_id BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
        form_title VARCHAR(190) NOT NULL DEFAULT '',
        ip VARCHAR(64) NOT NULL DEFAULT '',
        user_agent VARCHAR(500) NOT NULL DEFAULT '',
        status VARCHAR(20) NOT NULL DEFAULT 'new',
        notes TEXT NOT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        KEY email (email),
        KEY status (status),
        KEY created_at (created_at)
    ) {$charset_collate};";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );
    update_option( MZ_LEADS_DB_VER, MZ_LEADS_VER );
}

/* Run migration check on plugin load in case the file was uploaded
 * without re-activation. */
add_action( 'plugins_loaded', function () {
    if ( get_option( MZ_LEADS_DB_VER ) !== MZ_LEADS_VER ) {
        mz_leads_install();
    }
} );

/* =====================================================================
 * Capture CF7 submissions.
 * Fires before mail is sent so we can persist even if mail delivery fails.
 * ================================================================== */
add_action( 'wpcf7_before_send_mail', 'mz_leads_capture_cf7', 10, 1 );
function mz_leads_capture_cf7( $contact_form ) {
    if ( ! class_exists( 'WPCF7_Submission' ) ) { return; }
    $submission = WPCF7_Submission::get_instance();
    if ( ! $submission ) { return; }

    $data = (array) $submission->get_posted_data();
    $row  = mz_leads_extract_fields( $data );
    if ( '' === $row['email'] && '' === $row['message'] && '' === $row['name'] ) {
        return; // Empty / honeypot only — skip.
    }

    $page_url   = (string) $submission->get_meta( 'url' );
    $page_id    = url_to_postid( $page_url );
    $page_title = $page_id ? get_the_title( $page_id ) : '';

    $row['page_url']    = $page_url;
    $row['page_title']  = $page_title;
    $row['form_source'] = 'cf7';
    $row['form_id']     = (int) $contact_form->id();
    $row['form_title']  = (string) $contact_form->title();
    $row['ip']          = (string) $submission->get_meta( 'remote_ip' );
    $row['user_agent']  = mz_leads_truncate( (string) $submission->get_meta( 'user_agent' ), 500 );

    mz_leads_insert( $row );
}

/* Generic field extraction from a posted-data array. */
function mz_leads_extract_fields( array $data ) {
    $get = function ( array $keys, array $data ) {
        foreach ( $keys as $k ) {
            if ( isset( $data[ $k ] ) ) {
                $v = $data[ $k ];
                if ( is_array( $v ) ) { $v = implode( ', ', array_filter( array_map( 'strval', $v ) ) ); }
                $v = trim( (string) $v );
                if ( '' !== $v ) { return $v; }
            }
        }
        return '';
    };

    $email = $get( array( 'your-email', 'email', 'e-mail', 'mail', 'user_email', 'contact-email' ), $data );
    if ( $email && ! is_email( $email ) ) {
        // Try to recover something plausible if the value contains an email.
        if ( preg_match( '/[^\s,;<>@]+@[^\s,;<>@]+\.[^\s,;<>@]+/', $email, $m ) ) {
            $email = $m[0];
        }
    }

    return array(
        'email'   => mz_leads_truncate( sanitize_email( $email ), 190 ),
        'name'    => mz_leads_truncate( $get( array( 'your-name', 'name', 'full-name', 'fullname', 'user-name' ), $data ), 190 ),
        'phone'   => mz_leads_truncate( $get( array( 'your-phone', 'phone', 'tel', 'telephone', 'mobile' ), $data ), 60 ),
        'subject' => mz_leads_truncate( $get( array( 'your-subject', 'subject', 'topic', 'thema' ), $data ), 190 ),
        'message' => $get( array( 'your-message', 'message', 'msg', 'comments', 'inquiry', 'body' ), $data ),
    );
}

function mz_leads_truncate( $str, $len ) {
    $str = (string) $str;
    if ( function_exists( 'mb_substr' ) ) {
        return mb_substr( $str, 0, $len );
    }
    return substr( $str, 0, $len );
}

function mz_leads_insert( array $row ) {
    global $wpdb;
    $table = $wpdb->prefix . MZ_LEADS_TABLE;
    $defaults = array(
        'email' => '', 'name' => '', 'phone' => '', 'subject' => '',
        'message' => '', 'page_url' => '', 'page_title' => '',
        'form_source' => '', 'form_id' => 0, 'form_title' => '',
        'ip' => '', 'user_agent' => '', 'status' => 'new', 'notes' => '',
    );
    $row = array_merge( $defaults, $row );
    $row['created_at'] = current_time( 'mysql' );
    $wpdb->insert( $table, $row, array(
        '%s','%s','%s','%s','%s','%s','%s','%s','%d','%s','%s','%s','%s','%s','%s'
    ) );
    return (int) $wpdb->insert_id;
}

/* =====================================================================
 * Admin: menu + list table page.
 * ================================================================== */
add_action( 'admin_menu', function () {
    add_menu_page(
        'Leads',
        'Leads',
        'manage_options',
        'mz-leads',
        'mz_leads_render_page',
        'dashicons-email-alt2',
        4
    );
} );

function mz_leads_handle_actions() {
    if ( ! current_user_can( 'manage_options' ) ) { return; }
    if ( empty( $_REQUEST['page'] ) || 'mz-leads' !== $_REQUEST['page'] ) { return; }

    /* CSV export */
    if ( isset( $_GET['mz_leads_action'] ) && 'export_csv' === $_GET['mz_leads_action'] ) {
        check_admin_referer( 'mz_leads_export' );
        mz_leads_export_csv( mz_leads_filters_from_request() );
        exit;
    }

    /* Bulk + single actions (status update / delete) */
    if ( ! empty( $_POST['mz_leads_bulk'] ) && check_admin_referer( 'mz_leads_bulk', 'mz_leads_nonce' ) ) {
        $ids    = array_map( 'absint', (array) ( $_POST['ids'] ?? array() ) );
        $action = sanitize_key( $_POST['mz_leads_bulk'] );
        if ( ! empty( $ids ) ) {
            mz_leads_bulk( $action, $ids );
            wp_safe_redirect( add_query_arg( array( 'updated' => count( $ids ), 'action_done' => $action ), menu_page_url( 'mz-leads', false ) ) );
            exit;
        }
    }
}
add_action( 'admin_init', 'mz_leads_handle_actions' );

function mz_leads_bulk( $action, array $ids ) {
    global $wpdb;
    $table = $wpdb->prefix . MZ_LEADS_TABLE;
    $ids   = array_filter( array_map( 'intval', $ids ) );
    if ( empty( $ids ) ) { return; }
    $placeholders = implode( ',', array_fill( 0, count( $ids ), '%d' ) );

    if ( 'delete' === $action ) {
        $wpdb->query( $wpdb->prepare( "DELETE FROM {$table} WHERE id IN ($placeholders)", $ids ) );
        return;
    }
    $valid = array( 'mark_new' => 'new', 'mark_contacted' => 'contacted', 'mark_archived' => 'archived' );
    if ( isset( $valid[ $action ] ) ) {
        $status = $valid[ $action ];
        $wpdb->query( $wpdb->prepare(
            "UPDATE {$table} SET status = %s WHERE id IN ($placeholders)",
            array_merge( array( $status ), $ids )
        ) );
    }
}

function mz_leads_filters_from_request() {
    return array(
        'search' => isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '',
        'status' => isset( $_GET['status'] ) ? sanitize_key( $_GET['status'] ) : '',
        'from'   => isset( $_GET['from'] ) ? sanitize_text_field( $_GET['from'] ) : '',
        'to'     => isset( $_GET['to'] )   ? sanitize_text_field( $_GET['to'] )   : '',
    );
}

function mz_leads_query( array $f, $limit = 50, $offset = 0, $for_count = false ) {
    global $wpdb;
    $table = $wpdb->prefix . MZ_LEADS_TABLE;

    $where  = array( '1=1' );
    $params = array();

    if ( '' !== $f['search'] ) {
        $like = '%' . $wpdb->esc_like( $f['search'] ) . '%';
        $where[] = '(email LIKE %s OR name LIKE %s OR phone LIKE %s OR subject LIKE %s OR message LIKE %s)';
        array_push( $params, $like, $like, $like, $like, $like );
    }
    if ( '' !== $f['status'] && in_array( $f['status'], array( 'new', 'contacted', 'archived' ), true ) ) {
        $where[]  = 'status = %s';
        $params[] = $f['status'];
    }
    if ( '' !== $f['from'] ) {
        $where[]  = 'created_at >= %s';
        $params[] = $f['from'] . ' 00:00:00';
    }
    if ( '' !== $f['to'] ) {
        $where[]  = 'created_at <= %s';
        $params[] = $f['to'] . ' 23:59:59';
    }
    $where_sql = implode( ' AND ', $where );

    if ( $for_count ) {
        $sql = "SELECT COUNT(*) FROM {$table} WHERE {$where_sql}";
        return (int) ( empty( $params ) ? $wpdb->get_var( $sql ) : $wpdb->get_var( $wpdb->prepare( $sql, $params ) ) );
    }

    $sql = "SELECT * FROM {$table} WHERE {$where_sql} ORDER BY created_at DESC LIMIT %d OFFSET %d";
    $params[] = (int) $limit;
    $params[] = (int) $offset;
    return $wpdb->get_results( $wpdb->prepare( $sql, $params ) );
}

function mz_leads_export_csv( array $f ) {
    global $wpdb;
    $rows = mz_leads_query( $f, 100000, 0 );

    nocache_headers();
    header( 'Content-Type: text/csv; charset=utf-8' );
    header( 'Content-Disposition: attachment; filename="leads-' . date( 'Ymd-Hi' ) . '.csv"' );
    $out = fopen( 'php://output', 'w' );
    /* UTF-8 BOM so Excel opens Greek correctly */
    fwrite( $out, "\xEF\xBB\xBF" );
    fputcsv( $out, array( 'ID', 'Date', 'Email', 'Name', 'Phone', 'Subject', 'Message', 'Page', 'Form', 'Status', 'IP' ) );
    foreach ( (array) $rows as $r ) {
        fputcsv( $out, array(
            $r->id,
            $r->created_at,
            $r->email,
            $r->name,
            $r->phone,
            $r->subject,
            $r->message,
            $r->page_url,
            $r->form_title,
            $r->status,
            $r->ip,
        ) );
    }
    fclose( $out );
}

function mz_leads_render_page() {
    if ( ! current_user_can( 'manage_options' ) ) { wp_die(); }
    $f       = mz_leads_filters_from_request();
    $per     = 25;
    $page    = max( 1, isset( $_GET['paged'] ) ? (int) $_GET['paged'] : 1 );
    $offset  = ( $page - 1 ) * $per;
    $rows    = mz_leads_query( $f, $per, $offset );
    $total   = mz_leads_query( $f, 0, 0, true );
    $pages   = max( 1, (int) ceil( $total / $per ) );

    $counts = mz_leads_status_counts();

    $export_url = wp_nonce_url(
        add_query_arg( array_merge( $f, array( 'page' => 'mz-leads', 'mz_leads_action' => 'export_csv' ) ), admin_url( 'admin.php' ) ),
        'mz_leads_export'
    );
    ?>
    <div class="wrap mz-leads-wrap">
        <h1 class="wp-heading-inline">Leads</h1>
        <a href="<?php echo esc_url( $export_url ); ?>" class="page-title-action">
            <span class="dashicons dashicons-download" style="margin:3px 4px 0 0;font-size:16px"></span>
            Export CSV
        </a>
        <hr class="wp-header-end">

        <?php if ( isset( $_GET['updated'] ) ) : ?>
            <div class="notice notice-success is-dismissible"><p>
                Ενημερώθηκαν <?php echo (int) $_GET['updated']; ?> εγγραφές
                <?php if ( ! empty( $_GET['action_done'] ) ) {
                    $map = array( 'mark_new'=>'(σήμανση: νέα)', 'mark_contacted'=>'(σήμανση: contacted)', 'mark_archived'=>'(σήμανση: archived)', 'delete'=>'(διαγραφή)' );
                    echo esc_html( $map[ sanitize_key( $_GET['action_done'] ) ] ?? '' );
                } ?>.
            </p></div>
        <?php endif; ?>

        <ul class="subsubsub">
            <li><a class="<?php echo '' === $f['status'] ? 'current' : ''; ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=mz-leads' ) ); ?>">Όλα <span class="count">(<?php echo (int) $counts['all']; ?>)</span></a> |</li>
            <li><a class="<?php echo 'new' === $f['status'] ? 'current' : ''; ?>" href="<?php echo esc_url( add_query_arg( 'status', 'new', admin_url( 'admin.php?page=mz-leads' ) ) ); ?>">Νέα <span class="count">(<?php echo (int) $counts['new']; ?>)</span></a> |</li>
            <li><a class="<?php echo 'contacted' === $f['status'] ? 'current' : ''; ?>" href="<?php echo esc_url( add_query_arg( 'status', 'contacted', admin_url( 'admin.php?page=mz-leads' ) ) ); ?>">Contacted <span class="count">(<?php echo (int) $counts['contacted']; ?>)</span></a> |</li>
            <li><a class="<?php echo 'archived' === $f['status'] ? 'current' : ''; ?>" href="<?php echo esc_url( add_query_arg( 'status', 'archived', admin_url( 'admin.php?page=mz-leads' ) ) ); ?>">Archived <span class="count">(<?php echo (int) $counts['archived']; ?>)</span></a></li>
        </ul>

        <form method="get" class="mz-leads-filter">
            <input type="hidden" name="page" value="mz-leads">
            <?php if ( $f['status'] ) : ?><input type="hidden" name="status" value="<?php echo esc_attr( $f['status'] ); ?>"><?php endif; ?>
            <p class="search-box">
                <label class="screen-reader-text" for="mz-leads-s">Αναζήτηση</label>
                <input type="search" id="mz-leads-s" name="s" value="<?php echo esc_attr( $f['search'] ); ?>" placeholder="email, όνομα, τηλέφωνο, μήνυμα…">
                <input type="date" name="from" value="<?php echo esc_attr( $f['from'] ); ?>" title="Από">
                <input type="date" name="to"   value="<?php echo esc_attr( $f['to'] ); ?>" title="Έως">
                <input type="submit" class="button" value="Φίλτρο">
                <?php if ( $f['search'] || $f['from'] || $f['to'] ) : ?>
                    <a class="button-link" href="<?php echo esc_url( admin_url( 'admin.php?page=mz-leads' ) ); ?>">Καθαρισμός</a>
                <?php endif; ?>
            </p>
        </form>

        <form method="post">
            <?php wp_nonce_field( 'mz_leads_bulk', 'mz_leads_nonce' ); ?>
            <input type="hidden" name="page" value="mz-leads">

            <div class="tablenav top">
                <div class="alignleft actions bulkactions">
                    <select name="mz_leads_bulk" id="mz_leads_bulk_top">
                        <option value="">Μαζικές ενέργειες</option>
                        <option value="mark_new">Σήμανση: νέα</option>
                        <option value="mark_contacted">Σήμανση: contacted</option>
                        <option value="mark_archived">Σήμανση: archived</option>
                        <option value="delete">Διαγραφή</option>
                    </select>
                    <input type="submit" class="button action" value="Εφαρμογή">
                </div>
                <?php mz_leads_pagination_nav( $page, $pages, $total ); ?>
            </div>

            <table class="wp-list-table widefat fixed striped table-view-list mz-leads-table">
                <thead>
                    <tr>
                        <td class="manage-column column-cb check-column"><input type="checkbox" id="mz-leads-cb-select-all"></td>
                        <th class="manage-column">Email</th>
                        <th class="manage-column">Όνομα</th>
                        <th class="manage-column">Τηλέφωνο</th>
                        <th class="manage-column">Θέμα / Σελίδα</th>
                        <th class="manage-column" style="width:120px">Status</th>
                        <th class="manage-column" style="width:160px">Ημερομηνία</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ( empty( $rows ) ) : ?>
                    <tr><td colspan="7" style="text-align:center;padding:32px 16px">Δεν βρέθηκαν leads.</td></tr>
                <?php else : foreach ( $rows as $r ) : ?>
                    <tr>
                        <th scope="row" class="check-column"><input type="checkbox" name="ids[]" value="<?php echo (int) $r->id; ?>"></th>
                        <td>
                            <strong><a href="mailto:<?php echo esc_attr( $r->email ); ?>"><?php echo esc_html( $r->email ?: '—' ); ?></a></strong>
                            <?php if ( $r->message ) : ?>
                                <div class="row-actions">
                                    <span><a href="#" class="mz-leads-toggle" data-target="msg-<?php echo (int) $r->id; ?>">Μήνυμα</a></span>
                                </div>
                                <div id="msg-<?php echo (int) $r->id; ?>" class="mz-leads-msg" hidden>
                                    <?php echo nl2br( esc_html( $r->message ) ); ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td><?php echo esc_html( $r->name ?: '—' ); ?></td>
                        <td>
                            <?php if ( $r->phone ) : ?>
                                <a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $r->phone ) ); ?>"><?php echo esc_html( $r->phone ); ?></a>
                            <?php else : ?>—<?php endif; ?>
                        </td>
                        <td>
                            <?php if ( $r->subject ) : ?><div><?php echo esc_html( $r->subject ); ?></div><?php endif; ?>
                            <?php if ( $r->page_url ) : ?>
                                <a href="<?php echo esc_url( $r->page_url ); ?>" target="_blank" rel="noopener" class="mz-leads-page">
                                    <?php echo esc_html( $r->page_title ?: $r->page_url ); ?>
                                </a>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="mz-leads-status mz-leads-status--<?php echo esc_attr( $r->status ); ?>">
                                <?php echo esc_html( mz_leads_status_label( $r->status ) ); ?>
                            </span>
                        </td>
                        <td>
                            <?php
                            $ts = strtotime( $r->created_at );
                            echo esc_html( date_i18n( 'j M Y', $ts ) );
                            ?>
                            <div class="mz-leads-time"><?php echo esc_html( date_i18n( 'H:i', $ts ) ); ?></div>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>

            <div class="tablenav bottom">
                <div class="alignleft actions bulkactions">
                    <select name="mz_leads_bulk_b" id="mz_leads_bulk_bottom">
                        <option value="">Μαζικές ενέργειες</option>
                        <option value="mark_new">Σήμανση: νέα</option>
                        <option value="mark_contacted">Σήμανση: contacted</option>
                        <option value="mark_archived">Σήμανση: archived</option>
                        <option value="delete">Διαγραφή</option>
                    </select>
                    <input type="submit" class="button action" value="Εφαρμογή"
                        onclick="document.getElementById('mz_leads_bulk_top').value=document.getElementById('mz_leads_bulk_bottom').value;">
                </div>
                <?php mz_leads_pagination_nav( $page, $pages, $total ); ?>
            </div>
        </form>
    </div>

    <style>
        .mz-leads-wrap .search-box { display:flex; gap:6px; align-items:center; margin:12px 0; flex-wrap:wrap; }
        .mz-leads-wrap .search-box input[type="search"] { min-width: 280px; }
        .mz-leads-status {
            display:inline-flex; align-items:center; gap:6px;
            padding: 3px 10px; border-radius: 999px;
            font-size: 12px; font-weight: 500;
            background:#eef2f7; color:#1f2937;
        }
        .mz-leads-status--new       { background:#fef3c7; color:#92400e; }
        .mz-leads-status--contacted { background:#dbeafe; color:#1e40af; }
        .mz-leads-status--archived  { background:#e5e7eb; color:#374151; }
        .mz-leads-table tbody tr:hover { background:#fafbfc; }
        .mz-leads-msg {
            background:#f6f7f9; border:1px solid #e6e9ec; border-radius:6px;
            padding:10px 12px; margin-top:6px; max-width:680px;
            font-size:13px; line-height:1.5; color:#374151; white-space:pre-wrap;
        }
        .mz-leads-time { font-size:11px; color:#6b7280; }
        .mz-leads-page { font-size:12px; color:#6b7280; display:inline-block; margin-top:2px; }
        .mz-leads-page:hover { color:#1f2937; }
    </style>
    <script>
    (function(){
        document.querySelectorAll('.mz-leads-toggle').forEach(function(btn){
            btn.addEventListener('click', function(e){
                e.preventDefault();
                var t = document.getElementById(btn.dataset.target);
                if (t) { t.hidden = !t.hidden; }
            });
        });
        var sa = document.getElementById('mz-leads-cb-select-all');
        if (sa) sa.addEventListener('change', function(){
            document.querySelectorAll('input[name="ids[]"]').forEach(function(cb){ cb.checked = sa.checked; });
        });
    })();
    </script>
    <?php
}

function mz_leads_status_label( $status ) {
    $map = array( 'new' => 'Νέο', 'contacted' => 'Contacted', 'archived' => 'Archived' );
    return $map[ $status ] ?? $status;
}

function mz_leads_status_counts() {
    global $wpdb;
    $table = $wpdb->prefix . MZ_LEADS_TABLE;
    $rows  = $wpdb->get_results( "SELECT status, COUNT(*) AS c FROM {$table} GROUP BY status", ARRAY_A );
    $out   = array( 'all' => 0, 'new' => 0, 'contacted' => 0, 'archived' => 0 );
    foreach ( (array) $rows as $r ) {
        $out[ $r['status'] ] = (int) $r['c'];
        $out['all']         += (int) $r['c'];
    }
    return $out;
}

function mz_leads_pagination_nav( $page, $pages, $total ) {
    if ( $pages < 2 ) {
        echo '<div class="tablenav-pages"><span class="displaying-num">' . esc_html( $total ) . ' εγγραφές</span></div>';
        return;
    }
    $base = remove_query_arg( 'paged' );
    $prev = max( 1, $page - 1 );
    $next = min( $pages, $page + 1 );
    ?>
    <div class="tablenav-pages">
        <span class="displaying-num"><?php echo (int) $total; ?> εγγραφές</span>
        <span class="pagination-links">
            <a class="prev-page button" <?php echo 1 === $page ? 'aria-disabled="true" style="pointer-events:none;opacity:.5"' : ''; ?>
               href="<?php echo esc_url( add_query_arg( 'paged', $prev, $base ) ); ?>">‹</a>
            <span class="paging-input">Σελίδα <?php echo (int) $page; ?> από <?php echo (int) $pages; ?></span>
            <a class="next-page button" <?php echo $page === $pages ? 'aria-disabled="true" style="pointer-events:none;opacity:.5"' : ''; ?>
               href="<?php echo esc_url( add_query_arg( 'paged', $next, $base ) ); ?>">›</a>
        </span>
    </div>
    <?php
}
