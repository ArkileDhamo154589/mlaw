<?php
/**
 * One-shot seeder: blog articles + Athens→Thessaloniki DB swap.
 *
 * Trigger from a logged-in admin URL:
 *   /wp-admin/?mz_seed_run=articles   → create the 10 demo posts (idempotent: skips existing slugs)
 *   /wp-admin/?mz_seed_run=athens     → swap Αθήνα/Athens variants in posts + postmeta + options
 *   /wp-admin/?mz_seed_run=all        → both
 *
 * The Athens swap writes a JSON backup of every changed value to
 *   wp-content/uploads/mz-seeder-backup-{timestamp}.json
 * before applying any change.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

add_action( 'admin_init', 'mourtzilaki_seeder_dispatch' );
function mourtzilaki_seeder_dispatch() {
    if ( empty( $_GET['mz_seed_run'] ) ) { return; }
    if ( ! current_user_can( 'manage_options' ) ) { return; }

    $what = sanitize_key( wp_unslash( $_GET['mz_seed_run'] ) );
    $report = array();

    if ( 'articles' === $what || 'all' === $what ) {
        $report['articles'] = mourtzilaki_seeder_articles();
    }
    if ( 'athens' === $what || 'all' === $what ) {
        $report['athens'] = mourtzilaki_seeder_athens_swap();
    }

    if ( empty( $report ) ) {
        wp_die( 'Unknown action. Use ?mz_seed_run=articles | athens | all', 'Seeder', 400 );
    }

    nocache_headers();
    header( 'Content-Type: text/html; charset=utf-8' );
    echo '<!doctype html><meta charset="utf-8"><title>Mourtzilaki Seeder</title>';
    echo '<body style="font:14px/1.5 system-ui;padding:24px;max-width:900px;margin:auto">';
    echo '<h1 style="margin:0 0 12px">Seeder report</h1>';
    echo '<pre style="background:#f6f6f6;border:1px solid #ddd;padding:16px;white-space:pre-wrap;word-break:break-word">';
    echo esc_html( wp_json_encode( $report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ) );
    echo '</pre>';
    echo '<p><a href="' . esc_url( admin_url() ) . '">← Back to admin</a></p>';
    echo '</body>';
    exit;
}

/* =====================================================================
 * Articles
 * ================================================================== */

function mourtzilaki_seeder_articles_data() {
    return array(
        array(
            'slug'    => 'gdpr-2026-praktikos-odigos-epicheirisis',
            'title'   => 'GDPR 2026: πρακτικός οδηγός συμμόρφωσης για επιχειρήσεις',
            'excerpt' => 'Οι αλλαγές της φετινής χρονιάς, οι ρεαλιστικές υποχρεώσεις και το πώς ένας μικρός οργανισμός χτίζει ένα στιβαρό σύστημα προστασίας δεδομένων χωρίς υπερβολές.',
            'image_id'=> '1589994965851-a8f479c573a9',
            'image_alt' => 'Σφυρί δικαστή πάνω σε νομικά βιβλία',
        ),
        array(
            'slug'    => 'symvasis-misthosis-akinitou-ti-na-prosechete',
            'title'   => 'Συμβάσεις μίσθωσης ακινήτων: τι να προσέχετε πριν υπογράψετε',
            'excerpt' => 'Οι 7 ρήτρες που στην πράξη γεννούν τις περισσότερες διαφορές, και ο τρόπος να κατοχυρώσετε δικαιώματα και υποχρεώσεις χωρίς ανατροπές.',
            'image_id'=> '1450101499163-c8848c66ca85',
            'image_alt' => 'Δικηγορικό γραφείο με στυλό και έγγραφα',
        ),
        array(
            'slug'    => 'forologikos-elenchos-dikaiomata-ypochreoseis',
            'title'   => 'Φορολογικός έλεγχος: τα δικαιώματα και οι υποχρεώσεις σας',
            'excerpt' => 'Από την προσκόμιση εγγράφων έως την κατάθεση ενδικοφανούς προσφυγής — τι μπορείτε να ζητήσετε και τι οφείλετε σε κάθε στάδιο.',
            'image_id'=> '1521791136064-7986c2920216',
            'image_alt' => 'Επαγγελματίες σε σύσκεψη',
        ),
        array(
            'slug'    => 'apodochi-apopoiisi-klironomias',
            'title'   => 'Κληρονομιές: αποδοχή ή αποποίηση — η σωστή απόφαση στις προθεσμίες',
            'excerpt' => 'Πότε συμφέρει η αποποίηση, ποιο είναι το ευεργέτημα της απογραφής και γιατί η πίστη στην προθεσμία των τεσσάρων μηνών δεν είναι αυτονόητη.',
            'image_id'=> '1505664194779-8beaceb93744',
            'image_alt' => 'Άγαλμα της Δικαιοσύνης με ζυγαριά',
        ),
        array(
            'slug'    => 'ptochevtikos-kodikas-deyteri-eykairia',
            'title'   => 'Πτωχευτικός κώδικας: η δεύτερη ευκαιρία για ιδιώτες και επιχειρήσεις',
            'excerpt' => 'Πώς λειτουργεί στην πράξη η απαλλαγή χρεών, ποια περιουσιακά στοιχεία προστατεύονται, και γιατί η ταχύτητα στις ενέργειες είναι κρίσιμη.',
            'image_id'=> '1589216532372-1c2a367900d9',
            'image_alt' => 'Νομικά βιβλία στη βιβλιοθήκη',
        ),
        array(
            'slug'    => 'ilektronika-egklimata-nomikh-prostasia',
            'title'   => 'Ηλεκτρονικά εγκλήματα: νομική προστασία και ποινική απάντηση',
            'excerpt' => 'Phishing, εκβιασμοί με προσωπικό υλικό, παραβίαση λογαριασμών — οι σύγχρονες πρακτικές αντιμετώπισης και η συλλογή ψηφιακών αποδεικτικών στοιχείων.',
            'image_id'=> '1593115057322-e94b77572f20',
            'image_alt' => 'Νομική έρευνα σε υπολογιστή',
        ),
        array(
            'slug'    => 'synaisetiko-diazyvio-symvolaiografos',
            'title'   => 'Συναινετικό διαζύγιο μέσω συμβολαιογράφου: όλα τα βήματα',
            'excerpt' => 'Από τη συμφωνία επιμέλειας μέχρι τη μεταγραφή — οι ρεαλιστικοί χρόνοι, τα έγγραφα και οι συνηθισμένες παγίδες της διαδικασίας.',
            'image_id'=> '1589391886645-d51941baf7fb',
            'image_alt' => 'Υπογραφή νομικού εγγράφου',
        ),
        array(
            'slug'    => 'asfalistika-metra-pote-kai-pos',
            'title'   => 'Ασφαλιστικά μέτρα: πότε είναι ο σωστός χρόνος και πώς ασκούνται',
            'excerpt' => 'Σε ποιες περιπτώσεις το επείγον δικαιολογεί προσωρινή δικαστική προστασία, ποιες αποδείξεις χρειάζονται και τι κίνδυνους κρύβει η βιαστική προσφυγή.',
            'image_id'=> '1573497019940-1c28c88b4f3e',
            'image_alt' => 'Χειραψία επαγγελματιών',
        ),
        array(
            'slug'    => 'pneymatikh-idioktisia-prostasia-online',
            'title'   => 'Πνευματική ιδιοκτησία: αποτελεσματική προστασία στο διαδίκτυο',
            'excerpt' => 'Take-down, DMCA, αγωγές προσβολής δικαιώματος — ποιες ενέργειες αποδίδουν στην πράξη όταν το έργο σας αναπαράγεται χωρίς άδεια.',
            'image_id'=> '1554224155-6726b3ff858f',
            'image_alt' => 'Σφυρί δικαστή σε γραφείο',
        ),
        array(
            'slug'    => 'poiniki-dikaiosyni-stadia-diadikasias',
            'title'   => 'Ποινική διαδικασία: τα βασικά στάδια από τη μήνυση έως την έφεση',
            'excerpt' => 'Προκαταρκτική, κύρια ανάκριση, ακροατήριο, ένδικα μέσα — μια καθαρή χαρτογράφηση της πορείας μιας ποινικής υπόθεσης.',
            'image_id'=> '1589829545856-d10d557cf95f',
            'image_alt' => 'Νεοκλασικό κτίριο δικαστηρίου',
        ),
    );
}

function mourtzilaki_seeder_article_body( $title ) {
    $intro = "<p>Η παρούσα ενημέρωση συνοψίζει τη νομική πραγματικότητα γύρω από το συγκεκριμένο ζήτημα και επικεντρώνεται στα σημεία που στην πράξη κάνουν τη διαφορά. Η πρόθεσή μας είναι να μπορεί ο αναγνώστης να αντιληφθεί τι διακυβεύεται και ποιες ενέργειες έχει νόημα να ξεκινήσει — όχι να υποκαταστήσει την εξατομικευμένη συμβουλή.</p>";
    $h2_1  = '<h2>Το πλαίσιο σε λίγες γραμμές</h2>';
    $p1    = "<p>Στη Θεσσαλονίκη και ευρύτερα στη Βόρεια Ελλάδα, οι υποθέσεις αυτής της κατηγορίας παρουσιάζουν συγκεκριμένα χαρακτηριστικά. Η νομολογία των τοπικών δικαστηρίων έχει διαμορφώσει σταθερά κριτήρια κρίσης που είναι σκόπιμο να ληφθούν υπόψη ήδη από το στάδιο της προετοιμασίας του φακέλου.</p>";
    $p2    = "<p>Το γραφείο μας αναλαμβάνει υποθέσεις σε όλη τη Βόρεια Ελλάδα και διατηρεί συνεργασίες με νομικούς συμβούλους εξωτερικού για διασυνοριακά ζητήματα. Η συμμετοχή μας στο Δικηγορικό Σύλλογο Θεσσαλονίκης μάς δίνει άμεση πρόσβαση στις τοπικές διαδικασίες και τα αρμόδια όργανα.</p>";
    $h2_2  = '<h2>Πρακτικά βήματα</h2>';
    $list  = "<ul>"
           . "<li>Καταγραφή των πραγματικών περιστατικών με τα έγγραφα που τα υποστηρίζουν.</li>"
           . "<li>Έλεγχος των προθεσμιών — η πιο συχνή αιτία απώλειας δικαιωμάτων είναι η καθυστέρηση.</li>"
           . "<li>Αξιολόγηση εξωδικαστικών διεξόδων πριν την προσφυγή στο δικαστήριο.</li>"
           . "<li>Σχεδιασμός στρατηγικής με ρεαλιστικά σενάρια έκβασης και κόστους.</li>"
           . "</ul>";
    $h2_3  = '<h2>Τι να ζητήσετε από τον δικηγόρο σας</h2>';
    $p3    = "<p>Ζητήστε γραπτή αξιολόγηση των πιθανοτήτων επιτυχίας, του εκτιμώμενου χρόνου και του εύρους της δαπάνης. Μια σοβαρή νομική σχέση χτίζεται με διαφάνεια από το πρώτο ραντεβού — όχι με αορίστους ισχυρισμούς ή με υπερβολικές υποσχέσεις.</p>";
    $p4    = "<p>Αν επιθυμείτε να συζητήσουμε την υπόθεσή σας υπό αυστηρή εμπιστευτικότητα, χρησιμοποιήστε τη φόρμα επικοινωνίας της σελίδας ή καλέστε απευθείας το γραφείο. Ο πρώτος συντονισμός μπορεί να γίνει και διαδικτυακά για πελάτες εκτός Θεσσαλονίκης.</p>";
    return $intro . $h2_1 . $p1 . $p2 . $h2_2 . $list . $h2_3 . $p3 . $p4;
}

function mourtzilaki_seeder_articles() {
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    $created = array();
    $skipped = array();
    $errors  = array();

    foreach ( mourtzilaki_seeder_articles_data() as $idx => $art ) {
        $existing = get_page_by_path( $art['slug'], OBJECT, 'post' );
        if ( $existing ) {
            $skipped[] = array( 'slug' => $art['slug'], 'reason' => 'slug exists', 'id' => $existing->ID );
            continue;
        }

        $body = mourtzilaki_seeder_article_body( $art['title'] );

        // Stagger publish dates so the archive feels populated.
        $minutes_ago = 60 * ( $idx + 1 ) * 24; // 1 day per article into the past.
        $post_date = current_time( 'mysql', false );
        $post_date_obj = new DateTime( $post_date );
        $post_date_obj->modify( '-' . $minutes_ago . ' minutes' );
        $post_date_str = $post_date_obj->format( 'Y-m-d H:i:s' );

        $post_id = wp_insert_post( array(
            'post_type'    => 'post',
            'post_status'  => 'publish',
            'post_title'   => $art['title'],
            'post_name'    => $art['slug'],
            'post_excerpt' => $art['excerpt'],
            'post_content' => $body,
            'post_author'  => get_current_user_id() ?: 1,
            'post_date'    => $post_date_str,
            'post_date_gmt'=> get_gmt_from_date( $post_date_str ),
        ), true );

        if ( is_wp_error( $post_id ) ) {
            $errors[] = array( 'slug' => $art['slug'], 'error' => $post_id->get_error_message() );
            continue;
        }

        // Fetch + sideload featured image.
        $img_url = 'https://images.unsplash.com/photo-' . $art['image_id'] . '?w=1600&q=80&auto=format&fit=crop';
        $att_id  = mourtzilaki_seeder_sideload_image( $img_url, $art['slug'], $art['image_alt'], $post_id );
        if ( is_wp_error( $att_id ) ) {
            $errors[] = array( 'slug' => $art['slug'], 'image_error' => $att_id->get_error_message() );
        } else {
            set_post_thumbnail( $post_id, $att_id );
        }

        $created[] = array(
            'slug'   => $art['slug'],
            'id'     => $post_id,
            'image'  => is_wp_error( $att_id ) ? null : $att_id,
            'date'   => $post_date_str,
        );
    }

    return array(
        'created_count' => count( $created ),
        'skipped_count' => count( $skipped ),
        'error_count'   => count( $errors ),
        'created'       => $created,
        'skipped'       => $skipped,
        'errors'        => $errors,
    );
}

function mourtzilaki_seeder_sideload_image( $url, $slug, $alt, $parent_post_id ) {
    $tmp = download_url( $url, 30 );
    if ( is_wp_error( $tmp ) ) { return $tmp; }

    $file = array(
        'name'     => 'mz-' . $slug . '.jpg',
        'tmp_name' => $tmp,
    );
    $att_id = media_handle_sideload( $file, $parent_post_id, $alt );
    if ( file_exists( $tmp ) ) { @unlink( $tmp ); }
    if ( is_wp_error( $att_id ) ) { return $att_id; }

    update_post_meta( $att_id, '_wp_attachment_image_alt', $alt );
    return $att_id;
}

/* =====================================================================
 * Athens → Thessaloniki swap
 * ================================================================== */

function mourtzilaki_seeder_athens_replacements() {
    // Order matters: longer/more-specific keys first to avoid partial double-replacement.
    return array(
        'ΑΘΗΝΩΝ'      => 'ΘΕΣΣΑΛΟΝΙΚΗΣ',
        'ΑΘΗΝΑΣ'      => 'ΘΕΣΣΑΛΟΝΙΚΗΣ',
        'ΑΘΗΝΑ'       => 'ΘΕΣΣΑΛΟΝΙΚΗ',
        'Αθηνών'      => 'Θεσσαλονίκης',
        'Αθήνας'      => 'Θεσσαλονίκης',
        'Αθήνα'       => 'Θεσσαλονίκη',
        'αθηνών'      => 'θεσσαλονίκης',
        'αθήνας'      => 'θεσσαλονίκης',
        'αθήνα'       => 'θεσσαλονίκη',
        'αθηναϊκ'     => 'θεσσαλονικ',
        'Αθηναϊκ'     => 'Θεσσαλονικ',
        'ΑΘΗΝΑΪΚ'     => 'ΘΕΣΣΑΛΟΝΙΚ',
        'Athens'      => 'Thessaloniki',
        'athens'      => 'thessaloniki',
        'ATHENS'      => 'THESSALONIKI',
    );
}

function mourtzilaki_seeder_apply_replacements( $value ) {
    if ( ! is_string( $value ) ) { return $value; }
    return strtr( $value, mourtzilaki_seeder_replacements_cached() );
}

function mourtzilaki_seeder_replacements_cached() {
    static $cache = null;
    if ( null === $cache ) { $cache = mourtzilaki_seeder_athens_replacements(); }
    return $cache;
}

function mourtzilaki_seeder_walk_replace( $data ) {
    if ( is_string( $data ) ) {
        return mourtzilaki_seeder_apply_replacements( $data );
    }
    if ( is_array( $data ) ) {
        foreach ( $data as $k => $v ) {
            $data[ $k ] = mourtzilaki_seeder_walk_replace( $v );
        }
        return $data;
    }
    if ( is_object( $data ) ) {
        foreach ( get_object_vars( $data ) as $k => $v ) {
            $data->$k = mourtzilaki_seeder_walk_replace( $v );
        }
        return $data;
    }
    return $data;
}

function mourtzilaki_seeder_athens_swap() {
    global $wpdb;

    $backup = array(
        'started_at' => current_time( 'mysql' ),
        'posts'      => array(),
        'postmeta'   => array(),
        'options'    => array(),
    );
    $stats = array( 'posts' => 0, 'postmeta' => 0, 'options' => 0 );

    /* posts: title, content, excerpt */
    $rows = $wpdb->get_results( "SELECT ID, post_title, post_content, post_excerpt FROM {$wpdb->posts} WHERE post_status IN ('publish','draft','private','pending') AND ( post_title LIKE '%Αθήν%' OR post_content LIKE '%Αθήν%' OR post_excerpt LIKE '%Αθήν%' OR post_title LIKE '%Athen%' OR post_content LIKE '%Athen%' OR post_excerpt LIKE '%Athen%' OR post_title LIKE '%ΑΘΗΝ%' OR post_content LIKE '%ΑΘΗΝ%' )" );
    foreach ( (array) $rows as $row ) {
        $new_title   = mourtzilaki_seeder_apply_replacements( $row->post_title );
        $new_content = mourtzilaki_seeder_apply_replacements( $row->post_content );
        $new_excerpt = mourtzilaki_seeder_apply_replacements( $row->post_excerpt );
        if ( $new_title !== $row->post_title || $new_content !== $row->post_content || $new_excerpt !== $row->post_excerpt ) {
            $backup['posts'][] = array(
                'ID'       => (int) $row->ID,
                'title'    => $row->post_title,
                'content'  => $row->post_content,
                'excerpt'  => $row->post_excerpt,
            );
            $wpdb->update(
                $wpdb->posts,
                array(
                    'post_title'   => $new_title,
                    'post_content' => $new_content,
                    'post_excerpt' => $new_excerpt,
                ),
                array( 'ID' => (int) $row->ID )
            );
            $stats['posts']++;
        }
    }

    /* postmeta: ACF strings + serialized arrays */
    $meta_rows = $wpdb->get_results( "SELECT meta_id, post_id, meta_key, meta_value FROM {$wpdb->postmeta} WHERE meta_value LIKE '%Αθήν%' OR meta_value LIKE '%Athen%' OR meta_value LIKE '%ΑΘΗΝ%' OR meta_value LIKE '%αθηναϊκ%' OR meta_value LIKE '%Αθηναϊκ%'" );
    foreach ( (array) $meta_rows as $row ) {
        $original = $row->meta_value;
        $new_value = $original;

        if ( is_serialized( $original ) ) {
            $unser = @unserialize( $original );
            if ( false !== $unser || 'b:0;' === $original ) {
                $walked = mourtzilaki_seeder_walk_replace( $unser );
                $new_value = serialize( $walked );
            }
        } else {
            $new_value = mourtzilaki_seeder_apply_replacements( $original );
        }

        if ( $new_value !== $original ) {
            $backup['postmeta'][] = array(
                'meta_id'   => (int) $row->meta_id,
                'post_id'   => (int) $row->post_id,
                'meta_key'  => $row->meta_key,
                'meta_value'=> $original,
            );
            $wpdb->update(
                $wpdb->postmeta,
                array( 'meta_value' => $new_value ),
                array( 'meta_id'    => (int) $row->meta_id )
            );
            $stats['postmeta']++;
        }
    }

    /* options: only short string values likely to hold address/labels */
    $opt_rows = $wpdb->get_results( "SELECT option_id, option_name, option_value FROM {$wpdb->options} WHERE option_value LIKE '%Αθήν%' OR option_value LIKE '%Athen%' OR option_value LIKE '%ΑΘΗΝ%'" );
    foreach ( (array) $opt_rows as $row ) {
        $original = $row->option_value;
        $new_value = $original;

        if ( is_serialized( $original ) ) {
            $unser = @unserialize( $original );
            if ( false !== $unser || 'b:0;' === $original ) {
                $walked = mourtzilaki_seeder_walk_replace( $unser );
                $new_value = serialize( $walked );
            }
        } else {
            $new_value = mourtzilaki_seeder_apply_replacements( $original );
        }

        if ( $new_value !== $original ) {
            $backup['options'][] = array(
                'option_id'    => (int) $row->option_id,
                'option_name'  => $row->option_name,
                'option_value' => $original,
            );
            update_option( $row->option_name, maybe_unserialize( $new_value ) );
            $stats['options']++;
        }
    }

    /* Persist backup file */
    $upload = wp_upload_dir();
    $backup_path = trailingslashit( $upload['basedir'] ) . 'mz-seeder-backup-' . date( 'YmdHis' ) . '.json';
    @file_put_contents( $backup_path, wp_json_encode( $backup, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ) );

    return array(
        'stats'        => $stats,
        'backup_file'  => str_replace( ABSPATH, '/', $backup_path ),
        'backup_url'   => trailingslashit( $upload['baseurl'] ) . basename( $backup_path ),
    );
}
