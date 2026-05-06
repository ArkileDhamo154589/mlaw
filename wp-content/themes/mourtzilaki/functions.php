<?php
/**
 * Mourtzilaki Law theme.
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'MOURTZILAKI_VER', '1.0.0' );

/**
 * Theme setup.
 */
add_action( 'after_setup_theme', function () {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'custom-logo' );
    add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'automatic-feed-links' );

    register_nav_menus( array(
        'primary' => __( 'Primary menu', 'mourtzilaki' ),
        'footer'  => __( 'Footer menu', 'mourtzilaki' ),
    ) );
} );

/**
 * Enqueue assets.
 */
add_action( 'wp_enqueue_scripts', function () {
    wp_enqueue_style( 'mourtzilaki', get_stylesheet_uri(), array(), MOURTZILAKI_VER );

    $needs_slick = is_front_page() || is_page( array( 'reviews' ) );
    if ( $needs_slick ) {
        wp_enqueue_style(  'slick',       'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css', array(), '1.8.1' );
        wp_enqueue_style(  'slick-theme', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css', array( 'slick' ), '1.8.1' );
        wp_enqueue_script( 'slick',       'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', array( 'jquery' ), '1.8.1', true );
    }

    $deps = $needs_slick ? array( 'jquery', 'slick' ) : array();
    wp_enqueue_script( 'mourtzilaki', get_template_directory_uri() . '/assets/js/main.js', $deps, MOURTZILAKI_VER, true );
} );

/**
 * Hide the default WordPress admin bar margin on the front-end so the sticky
 * header doesn't jump.
 */
add_action( 'wp_head', function () {
    if ( is_admin_bar_showing() ) {
        echo '<style>html { margin-top: 0 !important; } @media screen and (min-width: 783px){ .site-header{top:32px} } @media screen and (max-width:782px){ .site-header{top:46px} }</style>';
    }
} );

/**
 * Practice areas — από το CPT mz_service (με hardcoded fallback πριν τη migration).
 */
function mourtzilaki_services() {
    $posts = get_posts( array(
        'post_type'      => 'mz_service',
        'posts_per_page' => -1,
        'orderby'        => 'menu_order date',
        'order'          => 'ASC',
    ) );
    if ( ! empty( $posts ) ) {
        $out = array();
        foreach ( $posts as $p ) {
            $out[] = array(
                'title' => get_the_title( $p ),
                'desc'  => function_exists( 'get_field' ) ? (string) get_field( 'description', $p->ID ) : '',
                'id'    => $p->ID,
            );
        }
        return $out;
    }
    return array(
        array( 'title' => 'Εμπορικό & Εταιρικό Δίκαιο',     'desc' => 'Ίδρυση εταιρειών, μετοχικές συμβάσεις, εξαγορές & συγχωνεύσεις, εταιρική διακυβέρνηση και καθημερινή νομική υποστήριξη επιχειρήσεων.' ),
        array( 'title' => 'Αστικό & Ενοχικό Δίκαιο',         'desc' => 'Συμβάσεις, αδικοπραξίες, διεκδικήσεις απαιτήσεων, ευθύνη από συμβάσεις και εξωσυμβατικές σχέσεις.' ),
        array( 'title' => 'Ακίνητα & Κτηματολόγιο',           'desc' => 'Έλεγχοι τίτλων, μεταβιβάσεις, μισθώσεις, διορθώσεις κτηματολογικών εγγραφών και επίλυση διαφορών.' ),
        array( 'title' => 'Οικογενειακό & Κληρονομικό',       'desc' => 'Διαζύγια, διατροφές, επιμέλεια τέκνων, διαθήκες, αποδοχές κληρονομιάς και κληρονομικές διαφορές.' ),
        array( 'title' => 'Εργατικό Δίκαιο',                  'desc' => 'Συμβάσεις εργασίας, απολύσεις, αποζημιώσεις, mobbing, εκπροσώπηση εργοδοτών και εργαζομένων.' ),
        array( 'title' => 'Ποινικό Δίκαιο',                   'desc' => 'Υπεράσπιση κατηγορουμένων και υποστήριξη παθόντων σε όλα τα στάδια της ποινικής διαδικασίας.' ),
        array( 'title' => 'Διοικητικό & Φορολογικό',          'desc' => 'Προσφυγές κατά διοικητικών πράξεων, φορολογικές διαφορές, ΣΕΠΕ, δημόσιες συμβάσεις.' ),
        array( 'title' => 'Τραπεζικό Δίκαιο',                 'desc' => 'Δάνεια, εγγυήσεις, υπερχρεωμένα νοικοκυριά, εξυγίανση επιχειρήσεων και ρυθμίσεις οφειλών.' ),
    );
}

/**
 * Team — από το CPT mz_member (με hardcoded fallback). Photo είναι πλήρες URL.
 */
function mourtzilaki_team() {
    $posts = get_posts( array(
        'post_type'      => 'mz_member',
        'posts_per_page' => -1,
        'orderby'        => 'menu_order date',
        'order'          => 'ASC',
    ) );
    if ( ! empty( $posts ) ) {
        $out = array();
        foreach ( $posts as $p ) {
            $photo = function_exists( 'get_field' ) ? get_field( 'photo', $p->ID ) : null;
            $url   = is_array( $photo ) ? $photo['url'] : ( $photo ?: get_template_directory_uri() . '/assets/img/team/elena.jpg' );
            $out[] = array(
                'id'    => $p->ID,
                'name'  => get_the_title( $p ),
                'role'  => function_exists( 'get_field' ) ? (string) get_field( 'role',      $p->ID ) : '',
                'bio'   => function_exists( 'get_field' ) ? (string) get_field( 'short_bio', $p->ID ) : '',
                'photo' => $url,
            );
        }
        return $out;
    }
    return array(
        array(
            'name'  => 'Έλενα Μουρτζιλάκη',
            'role'  => 'Δικηγόρος · Ιδρύτρια',
            'bio'   => 'Δικηγόρος παρ\' Αρείω Πάγω, με εξειδίκευση στο αστικό και εμπορικό δίκαιο. Εκπροσωπεί ιδιώτες και επιχειρήσεις σε όλους τους βαθμούς δικαιοδοσίας.',
            'photo' => get_template_directory_uri() . '/assets/img/team/elena.jpg',
        ),
    );
}

/**
 * SVG icons for τομείς δικαίου. Auto-detect από keyword στον τίτλο.
 * Όλα 24×24, stroke 1.5, fill: none.
 */
function mourtzilaki_service_icon( $title ) {
    $t = mb_strtolower( $title );
    $set = array(
        'εμπορικ'  => '<path d="M3 21V8l5-3 5 3v13"/><path d="M13 21V11l5-3 5 3v10"/><path d="M3 21h18"/><path d="M7 12h0M7 16h0M11 12h0M11 16h0M16 14h0M16 18h0"/>',
        'εταιρικ'  => '<path d="M3 21V8l5-3 5 3v13"/><path d="M13 21V11l5-3 5 3v10"/><path d="M3 21h18"/><path d="M7 12h0M7 16h0M11 12h0M11 16h0M16 14h0M16 18h0"/>',
        'αστικ'    => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M9 13h6M9 17h6"/>',
        'ενοχικ'   => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M9 13h6M9 17h6"/>',
        'ακινητ'   => '<path d="M3 11l9-7 9 7v9a2 2 0 0 1-2 2h-4v-7H10v7H6a2 2 0 0 1-2-2v-9z"/>',
        'κτηματ'   => '<path d="M3 11l9-7 9 7v9a2 2 0 0 1-2 2h-4v-7H10v7H6a2 2 0 0 1-2-2v-9z"/>',
        'οικογεν'  => '<circle cx="9" cy="7" r="3"/><path d="M3 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"/><circle cx="17" cy="7" r="3"/><path d="M21 21v-1a4 4 0 0 0-3-3.87"/>',
        'κληρονομ' => '<circle cx="9" cy="7" r="3"/><path d="M3 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"/><circle cx="17" cy="7" r="3"/><path d="M21 21v-1a4 4 0 0 0-3-3.87"/>',
        'εργατ'    => '<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M2 13h20"/>',
        'ποινικ'   => '<path d="M12 2l8 4v6c0 5-3.5 8.5-8 10-4.5-1.5-8-5-8-10V6l8-4z"/><path d="M9 12l2 2 4-4"/>',
        'διοικητ'  => '<path d="M3 21h18"/><path d="M5 21V10M9 21V10M15 21V10M19 21V10"/><path d="M3 10h18"/><path d="M3 10l9-6 9 6"/>',
        'φορολογ'  => '<path d="M3 21h18"/><path d="M5 21V10M9 21V10M15 21V10M19 21V10"/><path d="M3 10h18"/><path d="M3 10l9-6 9 6"/>',
        'τραπεζ'   => '<ellipse cx="12" cy="6" rx="9" ry="3"/><path d="M3 6v6c0 1.7 4 3 9 3s9-1.3 9-3V6"/><path d="M3 12v6c0 1.7 4 3 9 3s9-1.3 9-3v-6"/>',
    );

    foreach ( $set as $key => $svg ) {
        if ( false !== mb_strpos( $t, $key ) ) {
            return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $svg . '</svg>';
        }
    }
    // Default: balance scales.
    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v18M3 7h18M7 7l-3 7c0 2 1.5 3 3 3s3-1 3-3l-3-7zM17 7l-3 7c0 2 1.5 3 3 3s3-1 3-3l-3-7zM7 21h10"/></svg>';
}

/**
 * Helpers.
 */
function mourtzilaki_initials( $name ) {
    $parts = preg_split( '/\s+/', trim( $name ) );
    $out = '';
    foreach ( array_slice( $parts, 0, 2 ) as $p ) {
        $out .= mb_substr( $p, 0, 1 );
    }
    return mb_strtoupper( $out );
}

function mourtzilaki_page_url( $slug ) {
    $page = get_page_by_path( $slug );
    return $page ? get_permalink( $page->ID ) : home_url( '/' . $slug . '/' );
}

/**
 * Auto-seed the six landing pages on theme activation, set the static front
 * page, configure permalinks and create the primary menu.
 */
add_action( 'after_switch_theme', 'mourtzilaki_seed_content' );

function mourtzilaki_seed_content() {
    $pages = array(
        'arxiki'   => array( 'title' => 'Αρχική' ),
        'about'    => array( 'title' => 'Το γραφείο' ),
        'services' => array( 'title' => 'Τομείς εξειδίκευσης' ),
        'team'     => array( 'title' => 'Δικηγόροι' ),
        'bio'      => array( 'title' => 'Βιογραφικό' ),
        'reviews'  => array( 'title' => 'Συστάσεις' ),
        'cases'    => array( 'title' => 'Επιλεγμένες υποθέσεις' ),
        'faq'      => array( 'title' => 'Συχνές Ερωτήσεις' ),
        'glossary' => array( 'title' => 'Νομικό λεξικό' ),
        'blog'     => array( 'title' => 'Άρθρα' ),
        'contact'  => array( 'title' => 'Επικοινωνία' ),
        'privacy'  => array( 'title' => 'Πολιτική Απορρήτου' ),
    );

    $ids = array();
    foreach ( $pages as $slug => $info ) {
        $existing = get_page_by_path( $slug );
        if ( $existing ) {
            $ids[ $slug ] = $existing->ID;
            continue;
        }
        $ids[ $slug ] = wp_insert_post( array(
            'post_title'   => $info['title'],
            'post_name'    => $slug,
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_content' => '',
        ) );
    }

    if ( ! empty( $ids['arxiki'] ) ) {
        update_option( 'show_on_front', 'page' );
        update_option( 'page_on_front', $ids['arxiki'] );
    }

    // Pretty permalinks.
    if ( '' === get_option( 'permalink_structure' ) ) {
        update_option( 'permalink_structure', '/%postname%/' );
        flush_rewrite_rules( false );
    }

    // Build primary menu.
    $menu_name = 'Primary';
    $menu = wp_get_nav_menu_object( $menu_name );
    if ( ! $menu ) {
        $menu_id = wp_create_nav_menu( $menu_name );
    } else {
        $menu_id = $menu->term_id;
        // Clear existing items so we don't duplicate on re-activation.
        $items = wp_get_nav_menu_items( $menu_id );
        if ( is_array( $items ) ) {
            foreach ( $items as $item ) { wp_delete_post( $item->ID, true ); }
        }
    }

    $order = array( 'about', 'services', 'team', 'blog', 'contact' );
    $i = 1;
    foreach ( $order as $slug ) {
        if ( empty( $ids[ $slug ] ) ) { continue; }
        wp_update_nav_menu_item( $menu_id, 0, array(
            'menu-item-title'     => get_the_title( $ids[ $slug ] ),
            'menu-item-object'    => 'page',
            'menu-item-object-id' => $ids[ $slug ],
            'menu-item-type'      => 'post_type',
            'menu-item-status'    => 'publish',
            'menu-item-position'  => $i++,
        ) );
    }

    $locations = get_theme_mod( 'nav_menu_locations', array() );
    $locations['primary'] = $menu_id;
    set_theme_mod( 'nav_menu_locations', $locations );

    mourtzilaki_seed_sample_posts();
}

/**
 * Seed three sample articles only if no posts exist (so we don't keep duplicating).
 */
function mourtzilaki_seed_sample_posts() {
    $existing = get_posts( array( 'post_type' => 'post', 'numberposts' => 1, 'post_status' => 'any' ) );
    if ( ! empty( $existing ) ) { return; }

    $samples = array(
        array(
            'title'   => 'Νέες ρυθμίσεις στο εργατικό δίκαιο: τι αλλάζει στις απολύσεις',
            'excerpt' => 'Με τις πρόσφατες νομοθετικές παρεμβάσεις, αλλάζει το πλαίσιο των ομαδικών και των μεμονωμένων απολύσεων. Τι πρέπει να γνωρίζουν εργοδότες και εργαζόμενοι.',
            'content' => "Με τις πρόσφατες νομοθετικές παρεμβάσεις, το πλαίσιο των ομαδικών και των μεμονωμένων απολύσεων υφίσταται αξιοσημείωτες αλλαγές.\n\nΣτο παρόν άρθρο αναλύουμε τα κρίσιμα σημεία και πώς αυτά επηρεάζουν εργοδότες και εργαζόμενους στην πράξη. Τα βασικά σημεία αφορούν τις προθεσμίες προειδοποίησης, τη βάση υπολογισμού της αποζημίωσης και τις προϋποθέσεις παραμονής στην εργασία.",
        ),
        array(
            'title'   => 'Κτηματολόγιο: διορθώσεις πρώτων εγγραφών — ο πρακτικός οδηγός',
            'excerpt' => 'Η διαδικασία διόρθωσης πρώτων εγγραφών στο Εθνικό Κτηματολόγιο απαιτεί μεθοδικότητα και έγκαιρες ενέργειες. Τι ισχύει σήμερα και ποια είναι τα συνηθέστερα λάθη.',
            'content' => "Η διαδικασία διόρθωσης των πρώτων εγγραφών στο Εθνικό Κτηματολόγιο είναι από τις πιο τεχνικές υποθέσεις του δικαίου ακινήτων.\n\nΣτο άρθρο αναλύουμε τα στάδια, τις προθεσμίες και τα πιο συνηθισμένα λάθη που οδηγούν σε απορρίψεις. Επίσης παρουσιάζουμε checklist με τα έγγραφα που χρειάζονται για την υποβολή της αίτησης.",
        ),
        array(
            'title'   => 'Διαζύγιο και επιμέλεια τέκνων: νέες κατευθύνσεις από τη νομολογία',
            'excerpt' => 'Η νομολογία των τελευταίων ετών έχει διαμορφώσει σαφέστερες κατευθύνσεις για τη συνεπιμέλεια και την επικοινωνία με τα παιδιά. Παρουσιάζουμε τις πιο σημαντικές αποφάσεις.',
            'content' => "Η συνεπιμέλεια έχει εδραιωθεί ως ο κανόνας στις περισσότερες υποθέσεις διαζυγίου, με το συμφέρον του παιδιού να αποτελεί τον βασικό άξονα κρίσης.\n\nΣτο άρθρο αναλύουμε τις πιο πρόσφατες αποφάσεις του Αρείου Πάγου και τις πρακτικές συνέπειες για τις οικογένειες σε διαδικασία διαζυγίου.",
        ),
    );

    foreach ( $samples as $a ) {
        wp_insert_post( array(
            'post_title'   => $a['title'],
            'post_excerpt' => $a['excerpt'],
            'post_content' => $a['content'],
            'post_status'  => 'publish',
            'post_type'    => 'post',
        ) );
    }
}

/**
 * Default menu structure — used as fallback ONLY if no WP menu is assigned
 * to the location. Production sites should manage menus via Appearance → Menus.
 */
function mourtzilaki_menu_items() {
    return array(
        array(
            'slug'  => 'about',
            'label' => 'Το γραφείο',
            'children' => array(
                array( 'slug' => 'about',   'label' => 'Φιλοσοφία & αξίες', 'desc' => 'Τι μας οδηγεί στη δουλειά μας' ),
                array( 'slug' => 'team',    'label' => 'Η ομάδα',           'desc' => 'Ποιοι είμαστε' ),
                array( 'slug' => 'bio',     'label' => 'Βιογραφικό',        'desc' => 'Σπουδές, καριέρα, εξειδίκευση' ),
                array( 'slug' => 'reviews', 'label' => 'Συστάσεις πελατών', 'desc' => 'Τι λένε όσοι μας εμπιστεύτηκαν' ),
                array( 'slug' => 'cases',   'label' => 'Επιλεγμένες υποθέσεις', 'desc' => 'Παραδείγματα δουλειάς μας' ),
            ),
        ),
        array( 'slug' => 'services', 'label' => 'Τομείς δικαίου', 'children_cpt' => 'mz_service' ),
        array(
            'slug'  => 'blog',
            'label' => 'Νομικοί πόροι',
            'children' => array(
                array( 'slug' => 'blog',     'label' => 'Άρθρα & αναλύσεις', 'desc' => 'Νομοθεσία, νομολογία, ενημέρωση' ),
                array( 'slug' => 'faq',      'label' => 'Συχνές ερωτήσεις',  'desc' => 'Απαντήσεις σε όσα ρωτούν' ),
                array( 'slug' => 'glossary', 'label' => 'Νομικό λεξικό',     'desc' => 'Βασικοί όροι σε απλή γλώσσα' ),
            ),
        ),
        array( 'slug' => 'contact',  'label' => 'Επικοινωνία' ),
    );
}

/**
 * Read items assigned to a registered nav menu location, organised
 * top-level → children. Returns array of arrays: [ 'item' => $obj, 'children' => [...] ].
 * Empty if no menu is assigned to that location.
 */
function mourtzilaki_get_menu_tree( $location ) {
    $locations = get_nav_menu_locations();
    if ( empty( $locations[ $location ] ) ) { return array(); }
    $items = wp_get_nav_menu_items( $locations[ $location ] );
    if ( empty( $items ) ) { return array(); }

    $tree = array();
    foreach ( $items as $i ) {
        if ( 0 == $i->menu_item_parent ) {
            $tree[ $i->ID ] = array( 'item' => $i, 'children' => array() );
        }
    }
    foreach ( $items as $i ) {
        if ( $i->menu_item_parent && isset( $tree[ $i->menu_item_parent ] ) ) {
            $tree[ $i->menu_item_parent ]['children'][] = $i;
        }
    }
    return array_values( $tree );
}

function mourtzilaki_primary_menu() {
    $tree = mourtzilaki_get_menu_tree( 'primary' );
    if ( empty( $tree ) ) {
        mourtzilaki_primary_menu_fallback();
        return;
    }

    $caret = '<svg class="caret" viewBox="0 0 24 24" width="10" height="10" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>';

    echo '<ul class="primary-nav">';
    foreach ( $tree as $node ) {
        $item     = $node['item'];
        $children = $node['children'];
        $classes  = is_array( $item->classes ) ? array_filter( $item->classes ) : array();
        $is_mega  = in_array( 'is-mega', $classes, true );

        $cpt_children = array();
        if ( $is_mega ) {
            $cpt_children = get_posts( array(
                'post_type'      => 'mz_service',
                'posts_per_page' => -1,
                'orderby'        => 'menu_order date',
                'order'          => 'ASC',
            ) );
        }

        $has_simple = ! empty( $children );
        $has_mega   = ! empty( $cpt_children );
        $has_dd     = $has_simple || $has_mega;

        echo '<li class="nav-item' . ( $has_dd ? ' has-dropdown' : '' ) . '">';
        echo '<a href="' . esc_url( $item->url ) . '">' . esc_html( $item->title );
        if ( $has_dd ) { echo $caret; }
        echo '</a>';

        if ( $has_mega ) {
            echo '<div class="mega-dropdown" role="region"><div class="mega-grid">';
            foreach ( $cpt_children as $c ) {
                $desc = function_exists( 'get_field' ) ? wp_strip_all_tags( (string) get_field( 'description', $c->ID ) ) : '';
                echo '<a class="mega-item" href="' . esc_url( get_permalink( $c ) ) . '">';
                echo '<span class="mega-icon" aria-hidden="true">' . mourtzilaki_service_icon( get_the_title( $c ) ) . '</span>';
                echo '<span class="mega-text">';
                echo '<span class="mega-title">' . esc_html( get_the_title( $c ) ) . '</span>';
                if ( $desc ) { echo '<span class="mega-desc">' . esc_html( wp_trim_words( $desc, 12, '…' ) ) . '</span>'; }
                echo '</span></a>';
            }
            echo '</div>';
            echo '<a class="mega-foot" href="' . esc_url( $item->url ) . '">Όλοι οι τομείς εξειδίκευσης <span class="arrow">→</span></a>';
            echo '</div>';
        } elseif ( $has_simple ) {
            echo '<div class="simple-dropdown" role="region">';
            foreach ( $children as $sub ) {
                echo '<a class="sd-item" href="' . esc_url( $sub->url ) . '">';
                echo '<span class="sd-title">' . esc_html( $sub->title ) . '</span>';
                if ( ! empty( $sub->description ) ) {
                    echo '<span class="sd-desc">' . esc_html( $sub->description ) . '</span>';
                }
                echo '</a>';
            }
            echo '</div>';
        }
        echo '</li>';
    }
    echo '</ul>';
}

function mourtzilaki_primary_menu_fallback() {
    $items = mourtzilaki_menu_items();
    $caret = '<svg class="caret" viewBox="0 0 24 24" width="10" height="10" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>';

    echo '<ul class="primary-nav">';
    foreach ( $items as $item ) {
        $url = mourtzilaki_page_url( $item['slug'] );
        $manual_children = ! empty( $item['children'] ) ? $item['children'] : array();
        $cpt_children = ! empty( $item['children_cpt'] ) ? get_posts( array(
            'post_type'      => $item['children_cpt'],
            'posts_per_page' => -1,
            'orderby'        => 'menu_order date',
            'order'          => 'ASC',
        ) ) : array();
        $has_simple = ! empty( $manual_children );
        $has_mega   = ! empty( $cpt_children );
        $has_dd     = $has_simple || $has_mega;

        echo '<li class="nav-item' . ( $has_dd ? ' has-dropdown' : '' ) . '">';
        echo '<a href="' . esc_url( $url ) . '">' . esc_html( $item['label'] );
        if ( $has_dd ) { echo $caret; }
        echo '</a>';
        if ( $has_mega ) {
            echo '<div class="mega-dropdown" role="region"><div class="mega-grid">';
            foreach ( $cpt_children as $c ) {
                $desc = function_exists( 'get_field' ) ? wp_strip_all_tags( (string) get_field( 'description', $c->ID ) ) : '';
                echo '<a class="mega-item" href="' . esc_url( get_permalink( $c ) ) . '">';
                echo '<span class="mega-icon" aria-hidden="true">' . mourtzilaki_service_icon( get_the_title( $c ) ) . '</span>';
                echo '<span class="mega-text">';
                echo '<span class="mega-title">' . esc_html( get_the_title( $c ) ) . '</span>';
                if ( $desc ) { echo '<span class="mega-desc">' . esc_html( wp_trim_words( $desc, 12, '…' ) ) . '</span>'; }
                echo '</span></a>';
            }
            echo '</div><a class="mega-foot" href="' . esc_url( $url ) . '">Όλοι οι τομείς εξειδίκευσης <span class="arrow">→</span></a></div>';
        } elseif ( $has_simple ) {
            echo '<div class="simple-dropdown" role="region">';
            foreach ( $manual_children as $sub ) {
                echo '<a class="sd-item" href="' . esc_url( mourtzilaki_page_url( $sub['slug'] ) ) . '">';
                echo '<span class="sd-title">' . esc_html( $sub['label'] ) . '</span>';
                if ( ! empty( $sub['desc'] ) ) { echo '<span class="sd-desc">' . esc_html( $sub['desc'] ) . '</span>'; }
                echo '</a>';
            }
            echo '</div>';
        }
        echo '</li>';
    }
    echo '</ul>';
}

function mourtzilaki_mobile_menu() {
    $tree = mourtzilaki_get_menu_tree( 'primary' );
    if ( empty( $tree ) ) {
        mourtzilaki_mobile_menu_fallback();
        return;
    }

    $caret = '<svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>';

    echo '<ul class="mobile-nav">';
    foreach ( $tree as $node ) {
        $item     = $node['item'];
        $children = $node['children'];
        $classes  = is_array( $item->classes ) ? array_filter( $item->classes ) : array();
        $is_mega  = in_array( 'is-mega', $classes, true );

        $cpt_children = array();
        if ( $is_mega ) {
            $cpt_children = get_posts( array(
                'post_type'      => 'mz_service',
                'posts_per_page' => -1,
                'orderby'        => 'menu_order date',
                'order'          => 'ASC',
            ) );
        }
        $has_dd = ! empty( $children ) || ! empty( $cpt_children );

        if ( $has_dd ) {
            echo '<li class="m-has-dropdown">';
            echo '<details class="m-details"><summary><span>' . esc_html( $item->title ) . '</span>' . $caret . '</summary>';
            echo '<ul class="m-sub">';
            echo '<li><a class="m-sub-all" href="' . esc_url( $item->url ) . '">' . esc_html( $item->title ) . ' — επισκόπηση</a></li>';
            foreach ( $children as $sub ) {
                echo '<li><a href="' . esc_url( $sub->url ) . '">' . esc_html( $sub->title ) . '</a></li>';
            }
            foreach ( $cpt_children as $c ) {
                echo '<li><a href="' . esc_url( get_permalink( $c ) ) . '">' . esc_html( get_the_title( $c ) ) . '</a></li>';
            }
            echo '</ul></details></li>';
        } else {
            echo '<li><a href="' . esc_url( $item->url ) . '">' . esc_html( $item->title ) . '</a></li>';
        }
    }
    echo '</ul>';
}

function mourtzilaki_mobile_menu_fallback() {
    $items = mourtzilaki_menu_items();
    $caret = '<svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>';

    echo '<ul class="mobile-nav">';
    foreach ( $items as $item ) {
        $url = mourtzilaki_page_url( $item['slug'] );
        $manual_children = ! empty( $item['children'] ) ? $item['children'] : array();
        $cpt_children = ! empty( $item['children_cpt'] ) ? get_posts( array(
            'post_type'      => $item['children_cpt'],
            'posts_per_page' => -1,
            'orderby'        => 'menu_order date',
            'order'          => 'ASC',
        ) ) : array();
        $has_dd = ! empty( $manual_children ) || ! empty( $cpt_children );

        if ( $has_dd ) {
            echo '<li class="m-has-dropdown">';
            echo '<details class="m-details"><summary><span>' . esc_html( $item['label'] ) . '</span>' . $caret . '</summary>';
            echo '<ul class="m-sub">';
            echo '<li><a class="m-sub-all" href="' . esc_url( $url ) . '">' . esc_html( $item['label'] ) . ' — επισκόπηση</a></li>';
            foreach ( $manual_children as $sub ) {
                echo '<li><a href="' . esc_url( mourtzilaki_page_url( $sub['slug'] ) ) . '">' . esc_html( $sub['label'] ) . '</a></li>';
            }
            foreach ( $cpt_children as $c ) {
                echo '<li><a href="' . esc_url( get_permalink( $c ) ) . '">' . esc_html( get_the_title( $c ) ) . '</a></li>';
            }
            echo '</ul></details></li>';
        } else {
            echo '<li><a href="' . esc_url( $url ) . '">' . esc_html( $item['label'] ) . '</a></li>';
        }
    }
    echo '</ul>';
}

/**
 * Render the footer column menu (flat list). Falls back to the same set
 * of pages that the hard-coded version used.
 */
function mourtzilaki_footer_menu() {
    $tree = mourtzilaki_get_menu_tree( 'footer' );
    if ( empty( $tree ) ) {
        $fallback = array(
            array( 'slug' => 'about',    'label' => 'Το γραφείο' ),
            array( 'slug' => 'services', 'label' => 'Τομείς εξειδίκευσης' ),
            array( 'slug' => 'team',     'label' => 'Δικηγόροι' ),
            array( 'slug' => 'blog',     'label' => 'Άρθρα' ),
        );
        echo '<ul>';
        foreach ( $fallback as $f ) {
            echo '<li><a href="' . esc_url( mourtzilaki_page_url( $f['slug'] ) ) . '">' . esc_html( $f['label'] ) . '</a></li>';
        }
        echo '</ul>';
        return;
    }

    echo '<ul>';
    foreach ( $tree as $node ) {
        echo '<li><a href="' . esc_url( $node['item']->url ) . '">' . esc_html( $node['item']->title ) . '</a></li>';
    }
    echo '</ul>';
}

/**
 * Excerpt length / read-more.
 */
add_filter( 'excerpt_length', function () { return 28; }, 999 );
add_filter( 'excerpt_more',   function () { return '…'; } );

/* =====================================================================
 * Site Settings — singleton CPT + ACF + admin menu entry.
 * Keeps brand sub, header CTA, footer column titles & legal text
 * editable from a single place: WP Admin → Ρυθμίσεις site.
 * =================================================================== */
add_action( 'init', function () {
    register_post_type( 'mz_site', array(
        'labels' => array(
            'name'          => 'Ρυθμίσεις site',
            'singular_name' => 'Ρυθμίσεις site',
            'edit_item'     => 'Επεξεργασία ρυθμίσεων',
        ),
        'public'              => false,
        'show_ui'             => true,
        'show_in_menu'        => false,
        'show_in_admin_bar'   => false,
        'show_in_nav_menus'   => false,
        'has_archive'         => false,
        'rewrite'             => false,
        'exclude_from_search' => true,
        'supports'            => array( 'title' ),
        'capability_type'     => 'page',
        'map_meta_cap'        => true,
    ) );
}, 5 );

/**
 * Ensure a single Site Settings post exists; cache its ID in an option.
 */
function mourtzilaki_settings_id() {
    $id = (int) get_option( 'mourtzilaki_settings_id' );
    if ( $id && get_post_status( $id ) ) { return $id; }

    $existing = get_posts( array(
        'post_type'      => 'mz_site',
        'posts_per_page' => 1,
        'post_status'    => 'any',
    ) );
    if ( ! empty( $existing ) ) {
        $id = (int) $existing[0]->ID;
    } else {
        $new_id = wp_insert_post( array(
            'post_type'   => 'mz_site',
            'post_status' => 'publish',
            'post_title'  => 'Site Settings',
        ), true );
        $id = is_wp_error( $new_id ) ? 0 : (int) $new_id;
    }
    if ( $id ) { update_option( 'mourtzilaki_settings_id', $id ); }
    return $id;
}
add_action( 'admin_init', 'mourtzilaki_settings_id' );

/**
 * Read a settings field, with default fallback.
 */
function mourtzilaki_setting( $key, $default = '' ) {
    static $id = null;
    if ( null === $id ) { $id = mourtzilaki_settings_id(); }
    if ( ! $id || ! function_exists( 'get_field' ) ) { return $default; }
    $val = get_field( $key, $id );
    if ( is_array( $val ) ) { return $val; }
    $val = (string) $val;
    return '' !== trim( $val ) ? $val : $default;
}

/**
 * Custom admin menu entry that links straight to the settings post edit page.
 */
add_action( 'admin_menu', function () {
    $id = mourtzilaki_settings_id();
    if ( ! $id ) { return; }
    add_menu_page(
        'Ρυθμίσεις site',
        'Ρυθμίσεις site',
        'manage_options',
        'post.php?post=' . $id . '&action=edit',
        '',
        'dashicons-admin-generic',
        3
    );
}, 30 );

// Hide the "Add New" button + "View" link on the singleton edit screen.
add_filter( 'post_row_actions', function ( $actions, $post ) {
    if ( $post && 'mz_site' === $post->post_type ) {
        unset( $actions['view'], $actions['inline hide-if-no-js'], $actions['trash'] );
    }
    return $actions;
}, 10, 2 );

add_action( 'admin_print_styles-post.php', function () {
    $screen = get_current_screen();
    if ( $screen && 'mz_site' === $screen->post_type ) {
        echo '<style>#submitdiv .misc-pub-section:not(.misc-pub-curtime, .misc-pub-section-last), #delete-action, #minor-publishing-actions, #titlediv .inside { } #post-preview, .page-title-action { display:none !important; }</style>';
    }
} );

/* ACF: Site Settings field group. */
add_action( 'acf/init', function () {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) { return; }
    $sid = mourtzilaki_settings_id();
    if ( ! $sid ) { return; }

    acf_add_local_field_group( array(
        'key'      => 'group_mz_site_settings',
        'title'    => 'Ρυθμίσεις site',
        'fields'   => array(
            array( 'key' => 'field_mz_s_tab_header',   'label' => 'Header',   'type' => 'tab' ),
            array( 'key' => 'field_mz_s_brand_sub',    'label' => 'Υπότιτλος brand', 'name' => 'brand_sub', 'type' => 'text',
                   'instructions' => 'Εμφανίζεται κάτω από το όνομα στο header (όταν δεν υπάρχει custom logo). Κενό = να μην εμφανίζεται.' ),
            array( 'key' => 'field_mz_s_cta_label',    'label' => 'Κείμενο κουμπιού CTA', 'name' => 'header_cta_label', 'type' => 'text', 'placeholder' => 'Κλείστε ραντεβού' ),
            array( 'key' => 'field_mz_s_cta_url',      'label' => 'URL κουμπιού CTA',     'name' => 'header_cta_url',   'type' => 'url',  'instructions' => 'Π.χ. /contact/ ή απόλυτο URL.' ),

            array( 'key' => 'field_mz_s_tab_footer',   'label' => 'Footer',   'type' => 'tab' ),
            array( 'key' => 'field_mz_s_foot_brand',   'label' => 'Όνομα brand (override)', 'name' => 'footer_brand', 'type' => 'text',
                   'instructions' => 'Κενό = όνομα του site από Settings → General.' ),
            array( 'key' => 'field_mz_s_foot_about',   'label' => 'Κείμενο brand (footer)', 'name' => 'footer_about_text', 'type' => 'wysiwyg', 'media_upload' => 0, 'toolbar' => 'basic', 'tabs' => 'visual' ),
            array( 'key' => 'field_mz_s_foot_t2',      'label' => 'Τίτλος στήλης «Πλοήγηση»',  'name' => 'footer_col_nav_title',     'type' => 'text', 'placeholder' => 'Πλοήγηση' ),
            array( 'key' => 'field_mz_s_foot_t3',      'label' => 'Τίτλος στήλης «Επικοινωνία»','name' => 'footer_col_contact_title', 'type' => 'text', 'placeholder' => 'Επικοινωνία' ),
            array( 'key' => 'field_mz_s_foot_t4',      'label' => 'Τίτλος στήλης «Ωράριο»',     'name' => 'footer_col_hours_title',   'type' => 'text', 'placeholder' => 'Ωράριο' ),
            array( 'key' => 'field_mz_s_foot_legal',   'label' => 'Footer legal (δεξιά)',       'name' => 'footer_legal_right',       'type' => 'text', 'placeholder' => 'Μέλος του Δικηγορικού Συλλόγου Αθηνών' ),
            array( 'key' => 'field_mz_s_foot_copy',    'label' => 'Copyright text (override)',  'name' => 'footer_copyright',         'type' => 'text',
                   'instructions' => 'Αν αφεθεί κενό: «© [Έτος] [Όνομα site]. Με την επιφύλαξη παντός δικαιώματος.»' ),

            array( 'key' => 'field_mz_s_tab_contact',  'label' => 'Επικοινωνία', 'type' => 'tab' ),
            array( 'key' => 'field_mz_s_c_addr',       'label' => 'Διεύθυνση', 'name' => 'contact_address', 'type' => 'textarea', 'rows' => 2,
                   'instructions' => 'Μία γραμμή ανά πεδίο. Κενό = κράτα την παλιά τιμή από τη Contact page (αν υπάρχει).' ),
            array( 'key' => 'field_mz_s_c_phone',      'label' => 'Τηλέφωνο',  'name' => 'contact_phone', 'type' => 'text' ),
            array( 'key' => 'field_mz_s_c_email',      'label' => 'Email',     'name' => 'contact_email', 'type' => 'email' ),
            array( 'key' => 'field_mz_s_c_hours',      'label' => 'Ωράριο',    'name' => 'contact_hours', 'type' => 'textarea', 'rows' => 3 ),
        ),
        'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'mz_site' ) ) ),
        'menu_order' => 0,
        'position'   => 'normal',
        'style'      => 'default',
        'label_placement' => 'top',
    ) );
} );

/* =====================================================================
 * Auto-seed: build "Mourtzilaki Primary" + "Mourtzilaki Footer" menus
 * the first time the theme runs, and assign them to the menu locations.
 * =================================================================== */
add_action( 'init', 'mourtzilaki_seed_menus', 60 );
function mourtzilaki_seed_menus() {
    if ( '1' === get_option( 'mourtzilaki_menus_seeded' ) ) { return; }

    $primary_id = wp_create_nav_menu( 'Mourtzilaki Primary' );
    if ( is_wp_error( $primary_id ) ) {
        $existing = wp_get_nav_menu_object( 'Mourtzilaki Primary' );
        $primary_id = $existing ? (int) $existing->term_id : 0;
    }
    $footer_id = wp_create_nav_menu( 'Mourtzilaki Footer' );
    if ( is_wp_error( $footer_id ) ) {
        $existing = wp_get_nav_menu_object( 'Mourtzilaki Footer' );
        $footer_id = $existing ? (int) $existing->term_id : 0;
    }
    if ( ! $primary_id || ! $footer_id ) { return; }

    // Only seed items if menu is empty (don't duplicate on repeat).
    if ( empty( wp_get_nav_menu_items( $primary_id ) ) ) {
        $about_id = wp_update_nav_menu_item( $primary_id, 0, array(
            'menu-item-title'  => 'Το γραφείο',
            'menu-item-url'    => mourtzilaki_page_url( 'about' ),
            'menu-item-status' => 'publish',
        ) );
        $about_subs = array(
            array( 'about',   'Φιλοσοφία & αξίες',     'Τι μας οδηγεί στη δουλειά μας' ),
            array( 'team',    'Η ομάδα',               'Ποιοι είμαστε' ),
            array( 'bio',     'Βιογραφικό',            'Σπουδές, καριέρα, εξειδίκευση' ),
            array( 'reviews', 'Συστάσεις πελατών',     'Τι λένε όσοι μας εμπιστεύτηκαν' ),
            array( 'cases',   'Επιλεγμένες υποθέσεις', 'Παραδείγματα δουλειάς μας' ),
        );
        foreach ( $about_subs as $sub ) {
            wp_update_nav_menu_item( $primary_id, 0, array(
                'menu-item-title'       => $sub[1],
                'menu-item-url'         => mourtzilaki_page_url( $sub[0] ),
                'menu-item-description' => $sub[2],
                'menu-item-parent-id'   => $about_id,
                'menu-item-status'      => 'publish',
            ) );
        }

        wp_update_nav_menu_item( $primary_id, 0, array(
            'menu-item-title'   => 'Τομείς δικαίου',
            'menu-item-url'     => mourtzilaki_page_url( 'services' ),
            'menu-item-classes' => 'is-mega',
            'menu-item-status'  => 'publish',
        ) );

        $blog_id = wp_update_nav_menu_item( $primary_id, 0, array(
            'menu-item-title'  => 'Νομικοί πόροι',
            'menu-item-url'    => mourtzilaki_page_url( 'blog' ),
            'menu-item-status' => 'publish',
        ) );
        $blog_subs = array(
            array( 'blog',     'Άρθρα & αναλύσεις', 'Νομοθεσία, νομολογία, ενημέρωση' ),
            array( 'faq',      'Συχνές ερωτήσεις',  'Απαντήσεις σε όσα ρωτούν' ),
            array( 'glossary', 'Νομικό λεξικό',     'Βασικοί όροι σε απλή γλώσσα' ),
        );
        foreach ( $blog_subs as $sub ) {
            wp_update_nav_menu_item( $primary_id, 0, array(
                'menu-item-title'       => $sub[1],
                'menu-item-url'         => mourtzilaki_page_url( $sub[0] ),
                'menu-item-description' => $sub[2],
                'menu-item-parent-id'   => $blog_id,
                'menu-item-status'      => 'publish',
            ) );
        }

        wp_update_nav_menu_item( $primary_id, 0, array(
            'menu-item-title'  => 'Επικοινωνία',
            'menu-item-url'    => mourtzilaki_page_url( 'contact' ),
            'menu-item-status' => 'publish',
        ) );
    }

    if ( empty( wp_get_nav_menu_items( $footer_id ) ) ) {
        $footer_items = array(
            array( 'about',    'Το γραφείο' ),
            array( 'services', 'Τομείς εξειδίκευσης' ),
            array( 'team',     'Δικηγόροι' ),
            array( 'blog',     'Άρθρα' ),
        );
        foreach ( $footer_items as $f ) {
            wp_update_nav_menu_item( $footer_id, 0, array(
                'menu-item-title'  => $f[1],
                'menu-item-url'    => mourtzilaki_page_url( $f[0] ),
                'menu-item-status' => 'publish',
            ) );
        }
    }

    // Force-assign the seeded menus to the locations on first run.
    $locations = (array) get_theme_mod( 'nav_menu_locations', array() );
    $locations['primary'] = $primary_id;
    $locations['footer']  = $footer_id;
    set_theme_mod( 'nav_menu_locations', $locations );

    update_option( 'mourtzilaki_menus_seeded', '1' );
}

/* =====================================================================
 * Testimonial moderation dashboard (admin)
 * =================================================================== */
add_action( 'admin_menu', 'mourtzilaki_register_review_moderation', 20 );
function mourtzilaki_register_review_moderation() {
    $pending = (int) wp_count_posts( 'mz_testimonial' )->pending;
    $badge   = $pending > 0 ? ' <span class="awaiting-mod count-' . $pending . '"><span class="pending-count">' . $pending . '</span></span>' : '';

    add_submenu_page(
        'edit.php?post_type=mz_testimonial',
        'Έγκριση αξιολογήσεων',
        'Έγκριση' . $badge,
        'edit_posts',
        'mz-reviews-moderation',
        'mourtzilaki_render_review_moderation'
    );
}

add_action( 'admin_post_mourtzilaki_approve_review', 'mourtzilaki_handle_review_action' );
add_action( 'admin_post_mourtzilaki_reject_review',  'mourtzilaki_handle_review_action' );
function mourtzilaki_handle_review_action() {
    if ( ! current_user_can( 'edit_posts' ) ) { wp_die( 'forbidden' ); }
    $req = ( isset( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] ) ? $_POST : $_GET;
    $id    = isset( $req['id'] )       ? (int) $req['id'] : 0;
    $nonce = isset( $req['_wpnonce'] ) ? wp_unslash( $req['_wpnonce'] ) : '';
    if ( ! $id || ! wp_verify_nonce( $nonce, 'mz_review_action_' . $id ) ) { wp_die( 'bad nonce' ); }

    $action = current_action();
    $done = '';
    if ( 'admin_post_mourtzilaki_approve_review' === $action ) {
        // If admin edited the quote inline, save it before publishing.
        if ( isset( $_POST['mz_quote'] ) && function_exists( 'update_field' ) ) {
            $quote = mourtzilaki_kses_quote( wp_unslash( $_POST['mz_quote'] ) );
            update_field( 'quote', $quote, $id );
        }
        if ( isset( $_POST['mz_role'] ) ) {
            update_field( 'role', sanitize_text_field( wp_unslash( $_POST['mz_role'] ) ), $id );
        }
        if ( isset( $_POST['mz_name'] ) ) {
            $new_name = sanitize_text_field( wp_unslash( $_POST['mz_name'] ) );
            if ( '' !== $new_name ) {
                wp_update_post( array( 'ID' => $id, 'post_title' => $new_name ) );
            }
        }
        wp_update_post( array( 'ID' => $id, 'post_status' => 'publish' ) );
        $done = 'approved';
    } elseif ( 'admin_post_mourtzilaki_reject_review' === $action ) {
        wp_trash_post( $id );
        $done = 'rejected';
    }
    wp_safe_redirect( admin_url( 'edit.php?post_type=mz_testimonial&page=mz-reviews-moderation&done=' . $done ) );
    exit;
}

function mourtzilaki_render_review_moderation() {
    $pending_q = new WP_Query( array(
        'post_type'      => 'mz_testimonial',
        'post_status'    => 'pending',
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ) );
    $approved_count = (int) wp_count_posts( 'mz_testimonial' )->publish;
    $trashed_count  = (int) wp_count_posts( 'mz_testimonial' )->trash;
    $pending_count  = (int) wp_count_posts( 'mz_testimonial' )->pending;

    $done = isset( $_GET['done'] ) ? sanitize_text_field( wp_unslash( $_GET['done'] ) ) : '';
    ?>
    <style>
        .toplevel_page_mz-reviews-moderation #wpcontent,
        .mz-testimonial_page_mz-reviews-moderation #wpcontent { padding-left: 0 !important; background: #faf6ee; }
        .mz-mod {
            --mz-ink: #1f1a14;
            --mz-ink-2: #4a3f31;
            --mz-muted: #8a7c68;
            --mz-line: #e6dfd2;
            --mz-line-2: #efe9dc;
            --mz-bg: #ffffff;
            --mz-bg-2: #faf6ee;
            --mz-bg-3: #f3ebd9;
            --mz-gold: #b08a3e;
            --mz-gold-2: #8e6e2a;
            --mz-green: #0a7c3e;
            --mz-red: #b3261e;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
            color: var(--mz-ink);
            background: var(--mz-bg-2);
            min-height: 100vh;
            margin: 0 0 0 -20px;
            padding: 28px clamp(20px, 4vw, 44px) 80px;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        .mz-mod *, .mz-mod *::before, .mz-mod *::after { box-sizing: border-box; }
        .mz-mod a { color: var(--mz-ink); }

        /* Top header */
        .mz-mod__head {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 32px;
            flex-wrap: wrap;
            padding: 8px 4px 28px;
            border-bottom: 1px solid var(--mz-line);
            margin-bottom: 28px;
        }
        .mz-mod__eyebrow {
            display: inline-flex; align-items: center; gap: 10px;
            font-size: 11px; letter-spacing: 0.22em; text-transform: uppercase;
            color: var(--mz-gold-2); font-weight: 600; margin-bottom: 12px;
        }
        .mz-mod__eyebrow::before {
            content: ""; width: 22px; height: 1px; background: currentColor; display: inline-block;
        }
        .mz-mod__title {
            font-size: clamp(26px, 3vw, 34px);
            line-height: 1.1;
            letter-spacing: -0.018em;
            font-weight: 500;
            margin: 0 0 10px;
            color: var(--mz-ink);
        }
        .mz-mod__lead {
            color: var(--mz-ink-2); font-size: 14.5px; line-height: 1.6;
            max-width: 64ch; margin: 0;
        }
        .mz-mod__lead code {
            background: var(--mz-bg-3); padding: 1px 6px; border-radius: 2px;
            font-size: 12.5px; color: var(--mz-ink);
        }
        .mz-mod__stats {
            display: flex; gap: 0;
            border: 1px solid var(--mz-line);
            border-radius: 2px;
            background: var(--mz-bg);
            overflow: hidden;
        }
        .mz-mod__stat {
            padding: 14px 22px;
            border-right: 1px solid var(--mz-line);
            min-width: 110px;
        }
        .mz-mod__stat:last-child { border-right: 0; }
        .mz-mod__stat .n {
            font-size: 26px; font-weight: 500; line-height: 1; letter-spacing: -0.02em;
            color: var(--mz-ink); font-variant-numeric: tabular-nums;
        }
        .mz-mod__stat .l {
            font-size: 10.5px; letter-spacing: 0.16em; text-transform: uppercase;
            color: var(--mz-muted); margin-top: 6px;
        }
        .mz-mod__stat--pending .n { color: var(--mz-gold-2); }

        /* Toast / inline message */
        .mz-mod__msg {
            margin-bottom: 24px;
            padding: 14px 18px;
            border-radius: 2px;
            border: 1px solid var(--mz-line);
            border-left: 3px solid var(--mz-green);
            background: var(--mz-bg);
            font-size: 14px;
            color: var(--mz-ink-2);
            display: flex; align-items: center; gap: 10px;
        }
        .mz-mod__msg strong { color: var(--mz-ink); }
        .mz-mod__msg--rejected { border-left-color: var(--mz-red); }
        .mz-mod__msg .i {
            width: 24px; height: 24px; border-radius: 50%;
            display: inline-flex; align-items: center; justify-content: center;
            color: #fff; font-size: 12px;
            background: var(--mz-green);
        }
        .mz-mod__msg--rejected .i { background: var(--mz-red); }

        /* Cards grid */
        .mz-mod__grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(440px, 1fr));
            gap: 22px;
        }
        .mz-card {
            background: var(--mz-bg);
            border: 1px solid var(--mz-line);
            border-radius: 2px;
            padding: 0;
            display: flex; flex-direction: column;
            transition: box-shadow .2s ease, border-color .2s ease;
        }
        .mz-card:hover {
            border-color: #d6cdb8;
            box-shadow: 0 6px 22px -14px rgba(31,26,20,0.18);
        }
        .mz-card__head {
            display: flex; align-items: center; gap: 14px;
            padding: 18px 22px 16px;
            border-bottom: 1px solid var(--mz-line-2);
        }
        .mz-card__avatar {
            width: 42px; height: 42px; border-radius: 50%;
            background: var(--mz-bg-3);
            color: var(--mz-ink);
            display: inline-flex; align-items: center; justify-content: center;
            font-weight: 600; font-size: 15px; letter-spacing: 0.02em;
            flex-shrink: 0;
            border: 1px solid var(--mz-line);
        }
        .mz-card__who { flex: 1; min-width: 0; }
        .mz-card__name {
            color: var(--mz-ink); font-size: 15px; font-weight: 500;
            line-height: 1.2; word-break: break-word;
        }
        .mz-card__role {
            color: var(--mz-muted); font-size: 12.5px; margin-top: 3px;
        }
        .mz-card__when {
            color: var(--mz-muted); font-size: 11.5px; letter-spacing: 0.04em;
            font-variant-numeric: tabular-nums; flex-shrink: 0;
        }

        /* Inline editable fields */
        .mz-card__body { padding: 18px 22px 6px; }
        .mz-field { margin-bottom: 14px; }
        .mz-field__label {
            display: block;
            font-size: 10.5px; letter-spacing: 0.16em; text-transform: uppercase;
            color: var(--mz-muted); font-weight: 600; margin-bottom: 6px;
        }
        .mz-field input[type="text"] {
            width: 100%;
            background: var(--mz-bg);
            border: 1px solid var(--mz-line);
            border-radius: 2px;
            padding: 9px 12px;
            font: inherit; font-size: 14px;
            color: var(--mz-ink);
            transition: border-color .15s ease, box-shadow .15s ease;
        }
        .mz-field input[type="text"]:focus {
            outline: 0;
            border-color: var(--mz-gold);
            box-shadow: 0 0 0 3px rgba(176,138,62,0.12);
        }
        .mz-field--row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        @media (max-width: 700px) { .mz-field--row { grid-template-columns: 1fr; } }

        /* TinyMCE wrapper polish */
        .mz-card .wp-editor-wrap { border: 1px solid var(--mz-line); border-radius: 2px; overflow: hidden; }
        .mz-card .wp-editor-wrap .wp-editor-container { border: 0; }
        .mz-card .wp-editor-wrap .wp-editor-tabs { display: none; }
        .mz-card .wp-editor-wrap .quicktags-toolbar,
        .mz-card .wp-editor-wrap .mce-toolbar-grp { background: var(--mz-bg-3); border-bottom: 1px solid var(--mz-line); }
        .mz-card .wp-editor-wrap .mce-edit-area iframe { background: var(--mz-bg) !important; }
        .mz-card .wp-editor-wrap .wp-editor-area { font-size: 14px; line-height: 1.6; padding: 12px 14px; }

        .mz-card__contact {
            padding: 0 22px 14px;
            font-size: 12.5px; color: var(--mz-ink-2);
        }
        .mz-card__contact a {
            color: var(--mz-ink-2);
            border-bottom: 1px solid var(--mz-line);
        }
        .mz-card__contact a:hover { color: var(--mz-gold-2); border-bottom-color: var(--mz-gold-2); }
        .mz-card__contact .ip {
            color: var(--mz-muted); margin-left: 10px;
            font-family: SFMono-Regular, Consolas, monospace; font-size: 11.5px;
        }

        /* Actions */
        .mz-card__actions {
            display: flex; gap: 8px;
            padding: 14px 22px 18px;
            border-top: 1px solid var(--mz-line-2);
            margin-top: auto;
        }
        .mz-btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 7px;
            padding: 10px 16px;
            border-radius: 2px;
            font: inherit; font-size: 13px; font-weight: 500; letter-spacing: 0.02em;
            cursor: pointer; text-decoration: none;
            border: 1px solid transparent;
            transition: background .15s ease, color .15s ease, border-color .15s ease;
            white-space: nowrap;
        }
        .mz-btn--approve {
            flex: 1;
            background: var(--mz-ink); color: #fff;
        }
        .mz-btn--approve:hover { background: #000; color: #fff; }
        .mz-btn--reject {
            background: var(--mz-bg); color: var(--mz-ink-2); border-color: var(--mz-line);
        }
        .mz-btn--reject:hover { color: var(--mz-red); border-color: var(--mz-red); background: #fff5f5; }
        .mz-btn--edit {
            background: var(--mz-bg); color: var(--mz-muted); border-color: var(--mz-line);
            padding: 10px 12px;
        }
        .mz-btn--edit:hover { color: var(--mz-ink); border-color: var(--mz-gold); }

        /* Empty state */
        .mz-mod__empty {
            text-align: center;
            padding: 90px 32px;
            background: var(--mz-bg);
            border: 1px solid var(--mz-line);
            border-radius: 2px;
        }
        .mz-mod__empty .ic {
            width: 64px; height: 64px; margin: 0 auto 18px;
            border-radius: 50%;
            background: var(--mz-bg-3);
            display: inline-flex; align-items: center; justify-content: center;
            color: var(--mz-gold);
        }
        .mz-mod__empty h3 {
            color: var(--mz-ink); font-weight: 500; font-size: 18px;
            margin: 0 0 6px;
        }
        .mz-mod__empty p { color: var(--mz-muted); margin: 0; font-size: 14px; }

        @media (max-width: 782px) {
            .mz-mod { margin-left: -10px; padding: 16px 16px 60px; }
            .mz-mod__grid { grid-template-columns: 1fr; }
            .mz-mod__stats { width: 100%; }
            .mz-mod__stat { flex: 1; }
        }
    </style>

    <div class="mz-mod">

        <header class="mz-mod__head">
            <div>
                <span class="mz-mod__eyebrow">Συστάσεις · Έγκριση</span>
                <h1 class="mz-mod__title">Αξιολογήσεις προς δημοσίευση</h1>
                <p class="mz-mod__lead">
                    Αξιολογήσεις πελατών που υποβλήθηκαν μέσω της φόρμας στο
                    <code>/reviews/</code>. Επεξεργαστείτε το κείμενο αν χρειάζεται και
                    εγκρίνετε. Μετά τη δημοσίευση είναι ορατές δημόσια.
                </p>
            </div>
            <div class="mz-mod__stats">
                <div class="mz-mod__stat mz-mod__stat--pending">
                    <div class="n"><?php echo esc_html( $pending_count ); ?></div>
                    <div class="l">Σε αναμονή</div>
                </div>
                <div class="mz-mod__stat">
                    <div class="n"><?php echo esc_html( $approved_count ); ?></div>
                    <div class="l">Δημοσιευμένες</div>
                </div>
                <div class="mz-mod__stat">
                    <div class="n"><?php echo esc_html( $trashed_count ); ?></div>
                    <div class="l">Στον κάδο</div>
                </div>
            </div>
        </header>

        <?php if ( 'approved' === $done ) : ?>
            <div class="mz-mod__msg"><span class="i">✓</span> <span><strong>Η αξιολόγηση εγκρίθηκε</strong> και είναι πλέον ορατή στο public site.</span></div>
        <?php elseif ( 'rejected' === $done ) : ?>
            <div class="mz-mod__msg mz-mod__msg--rejected"><span class="i">✕</span> <span><strong>Η αξιολόγηση απορρίφθηκε</strong> και μεταφέρθηκε στον κάδο.</span></div>
        <?php endif; ?>

        <?php if ( $pending_q->have_posts() ) : ?>
            <div class="mz-mod__grid">
                <?php while ( $pending_q->have_posts() ) : $pending_q->the_post();
                    $tid    = get_the_ID();
                    $name   = get_the_title();
                    $role   = function_exists( 'get_field' ) ? (string) get_field( 'role',  $tid ) : '';
                    $quote  = function_exists( 'get_field' ) ? (string) get_field( 'quote', $tid ) : '';
                    $email  = (string) get_post_meta( $tid, '_reviewer_email', true );
                    $ip     = (string) get_post_meta( $tid, '_reviewer_ip', true );
                    $when   = mysql2date( 'd.m.Y · H:i', get_post()->post_date );
                    $initial = mb_strtoupper( mb_substr( $name, 0, 1 ) );

                    $reject_url = wp_nonce_url(
                        admin_url( 'admin-post.php?action=mourtzilaki_reject_review&id=' . $tid ),
                        'mz_review_action_' . $tid
                    );
                    $edit_url    = get_edit_post_link( $tid, 'raw' );
                    $editor_id   = 'mzquote_' . $tid;
                ?>
                    <article class="mz-card">
                        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                            <input type="hidden" name="action" value="mourtzilaki_approve_review">
                            <input type="hidden" name="id" value="<?php echo esc_attr( $tid ); ?>">
                            <?php wp_nonce_field( 'mz_review_action_' . $tid ); ?>

                            <header class="mz-card__head">
                                <span class="mz-card__avatar"><?php echo esc_html( $initial ); ?></span>
                                <div class="mz-card__who">
                                    <div class="mz-card__name"><?php echo esc_html( $name ); ?></div>
                                    <?php if ( $role ) : ?><div class="mz-card__role"><?php echo esc_html( $role ); ?></div><?php endif; ?>
                                </div>
                                <span class="mz-card__when"><?php echo esc_html( $when ); ?></span>
                            </header>

                            <div class="mz-card__body">
                                <div class="mz-field mz-field--row">
                                    <div>
                                        <label class="mz-field__label" for="mz_name_<?php echo esc_attr( $tid ); ?>">Όνομα</label>
                                        <input type="text" id="mz_name_<?php echo esc_attr( $tid ); ?>" name="mz_name" value="<?php echo esc_attr( $name ); ?>">
                                    </div>
                                    <div>
                                        <label class="mz-field__label" for="mz_role_<?php echo esc_attr( $tid ); ?>">Ιδιότητα</label>
                                        <input type="text" id="mz_role_<?php echo esc_attr( $tid ); ?>" name="mz_role" value="<?php echo esc_attr( $role ); ?>">
                                    </div>
                                </div>

                                <div class="mz-field">
                                    <label class="mz-field__label" for="<?php echo esc_attr( $editor_id ); ?>">Κείμενο αξιολόγησης</label>
                                    <?php wp_editor( $quote, $editor_id, array(
                                        'textarea_name' => 'mz_quote',
                                        'textarea_rows' => 5,
                                        'media_buttons' => false,
                                        'tinymce'       => array(
                                            'toolbar1'         => 'bold,italic,underline,bullist,numlist,link,unlink,removeformat',
                                            'toolbar2'         => '',
                                            'menubar'          => false,
                                            'statusbar'        => false,
                                            'resize'           => false,
                                            'wp_autoresize_on' => true,
                                        ),
                                        'quicktags'     => false,
                                    ) ); ?>
                                </div>
                            </div>

                            <?php if ( $email || $ip ) : ?>
                                <div class="mz-card__contact">
                                    <?php if ( $email ) : ?>
                                        <span aria-hidden="true">✉</span>
                                        <a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a>
                                    <?php endif; ?>
                                    <?php if ( $ip ) : ?>
                                        <span class="ip"><?php echo esc_html( $ip ); ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <footer class="mz-card__actions">
                                <button class="mz-btn mz-btn--approve" type="submit"
                                        onclick="return confirm('Αποθήκευση αλλαγών και άμεση δημοσίευση;');">
                                    ✓ Αποθήκευση &amp; έγκριση
                                </button>
                                <a class="mz-btn mz-btn--reject" href="<?php echo esc_url( $reject_url ); ?>"
                                   onclick="return confirm('Απόρριψη και μεταφορά στον κάδο;');">✕ Απόρριψη</a>
                                <a class="mz-btn mz-btn--edit" href="<?php echo esc_url( $edit_url ); ?>" title="Πλήρης επεξεργασία">✎</a>
                            </footer>
                        </form>
                    </article>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        <?php else : ?>
            <div class="mz-mod__empty">
                <div class="ic">
                    <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20,6 9,17 4,12"/></svg>
                </div>
                <h3>Όλα καθαρά</h3>
                <p>Δεν υπάρχουν αξιολογήσεις σε αναμονή.</p>
            </div>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * Public review submission — saved as 'pending' in mz_testimonial CPT.
 * Approval needed by admin (status → publish).
 */
add_action( 'admin_post_mourtzilaki_submit_review',         'mourtzilaki_handle_review_submission' );
add_action( 'admin_post_nopriv_mourtzilaki_submit_review',  'mourtzilaki_handle_review_submission' );

function mourtzilaki_handle_review_submission() {
    $back = wp_get_referer() ?: home_url( '/reviews/' );

    // Honeypot — silently treat as success.
    if ( ! empty( $_POST['website'] ) ) {
        wp_safe_redirect( add_query_arg( 'review_sent', '1', $back ) );
        exit;
    }

    if ( ! isset( $_POST['_review_nonce'] ) ||
         ! wp_verify_nonce( wp_unslash( $_POST['_review_nonce'] ), 'mourtzilaki_review' ) ) {
        wp_safe_redirect( add_query_arg( 'review_error', 'nonce', $back ) );
        exit;
    }

    $name      = isset( $_POST['reviewer_name'] )  ? sanitize_text_field( wp_unslash( $_POST['reviewer_name'] ) )     : '';
    $role      = isset( $_POST['reviewer_role'] )  ? sanitize_text_field( wp_unslash( $_POST['reviewer_role'] ) )     : '';
    $email     = isset( $_POST['reviewer_email'] ) ? sanitize_email( wp_unslash( $_POST['reviewer_email'] ) )          : '';
    $quote_raw = isset( $_POST['reviewer_quote'] ) ? wp_unslash( $_POST['reviewer_quote'] )                            : '';
    $quote     = mourtzilaki_kses_quote( $quote_raw );
    $gdpr      = isset( $_POST['gdpr'] );

    $quote_plain = trim( wp_strip_all_tags( $quote ) );
    if ( '' === $name || '' === $quote_plain || ! $gdpr ) {
        wp_safe_redirect( add_query_arg( 'review_error', 'missing', $back ) );
        exit;
    }
    if ( mb_strlen( $quote_plain ) > 1500 ) {
        $quote = mourtzilaki_kses_quote( mb_substr( $quote_raw, 0, 3000 ) );
    }

    $id = wp_insert_post( array(
        'post_type'   => 'mz_testimonial',
        'post_status' => 'pending',
        'post_title'  => $name,
    ), true );
    if ( is_wp_error( $id ) || ! $id ) {
        wp_safe_redirect( add_query_arg( 'review_error', 'save', $back ) );
        exit;
    }

    if ( function_exists( 'update_field' ) ) {
        update_field( 'quote', $quote, $id );
        update_field( 'role',  $role,  $id );
    }
    if ( $email ) { update_post_meta( $id, '_reviewer_email', $email ); }
    update_post_meta( $id, '_reviewer_ip', isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( $_SERVER['REMOTE_ADDR'] ) : '' );

    // Notify admin.
    $edit_url = admin_url( 'post.php?post=' . $id . '&action=edit' );
    $quote_for_email = trim( wp_strip_all_tags( $quote ) );
    wp_mail(
        get_option( 'admin_email' ),
        'Νέα αξιολόγηση προς έγκριση',
        "Νέα αξιολόγηση από: {$name}\nEmail: {$email}\nΙδιότητα: {$role}\n\nΚείμενο:\n{$quote_for_email}\n\nΈγκριση: {$edit_url}",
        array( 'Reply-To: ' . ( $email ?: get_option( 'admin_email' ) ) )
    );

    wp_safe_redirect( add_query_arg( 'review_sent', '1', $back ) . '#review-form' );
    exit;
}

/* =====================================================================
 * Custom post types — content edited via ACF in WP admin.
 * =================================================================== */
add_action( 'init', function () {
    register_post_type( 'mz_hero', array(
        'labels' => array(
            'name'          => 'Hero Slides',
            'singular_name' => 'Slide',
            'add_new_item'  => 'Νέο slide',
            'edit_item'     => 'Επεξεργασία slide',
            'all_items'     => 'Όλα τα slides',
            'menu_name'     => 'Hero Slides',
        ),
        'public'        => false,
        'show_ui'       => true,
        'show_in_menu'  => true,
        'menu_position' => 22,
        'menu_icon'     => 'dashicons-images-alt2',
        'supports'      => array( 'title', 'page-attributes' ),
        'has_archive'   => false,
        'rewrite'       => false,
    ) );

    register_post_type( 'mz_service', array(
        'labels' => array(
            'name'          => 'Τομείς δικαίου',
            'singular_name' => 'Τομέας',
            'add_new_item'  => 'Νέος τομέας',
            'edit_item'     => 'Επεξεργασία τομέα',
            'all_items'     => 'Όλοι οι τομείς',
            'menu_name'     => 'Τομείς',
        ),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'menu_position'      => 23,
        'menu_icon'          => 'dashicons-portfolio',
        'supports'           => array( 'title', 'page-attributes', 'editor' ),
        'has_archive'        => false,
        'rewrite'            => array( 'slug' => 'tomeas', 'with_front' => false ),
    ) );

    register_post_type( 'mz_case', array(
        'labels' => array(
            'name'          => 'Επιλεγμένες υποθέσεις',
            'singular_name' => 'Υπόθεση',
            'add_new_item'  => 'Νέα υπόθεση',
            'edit_item'     => 'Επεξεργασία υπόθεσης',
            'all_items'     => 'Όλες οι υποθέσεις',
            'menu_name'     => 'Υποθέσεις',
        ),
        'public'        => false,
        'show_ui'       => true,
        'show_in_menu'  => true,
        'menu_position' => 27,
        'menu_icon'     => 'dashicons-portfolio',
        'supports'      => array( 'title', 'page-attributes' ),
        'has_archive'   => false,
        'rewrite'       => false,
    ) );

    register_post_type( 'mz_faq', array(
        'labels' => array(
            'name'          => 'Συχνές ερωτήσεις',
            'singular_name' => 'Ερώτηση',
            'add_new_item'  => 'Νέα ερώτηση',
            'edit_item'     => 'Επεξεργασία ερώτησης',
            'all_items'     => 'Όλες οι ερωτήσεις',
            'menu_name'     => 'FAQ',
        ),
        'public'        => false,
        'show_ui'       => true,
        'show_in_menu'  => true,
        'menu_position' => 26,
        'menu_icon'     => 'dashicons-editor-help',
        'supports'      => array( 'title', 'page-attributes' ),
        'has_archive'   => false,
        'rewrite'       => false,
    ) );

    register_post_type( 'mz_testimonial', array(
        'labels' => array(
            'name'          => 'Testimonials',
            'singular_name' => 'Testimonial',
            'add_new_item'  => 'Νέο testimonial',
            'edit_item'     => 'Επεξεργασία testimonial',
            'all_items'     => 'Όλα τα testimonials',
            'menu_name'     => 'Testimonials',
        ),
        'public'        => false,
        'show_ui'       => true,
        'show_in_menu'  => true,
        'menu_position' => 25,
        'menu_icon'     => 'dashicons-format-quote',
        'supports'      => array( 'title', 'page-attributes' ),
        'has_archive'   => false,
        'rewrite'       => false,
    ) );

    register_post_type( 'mz_member', array(
        'labels' => array(
            'name'          => 'Δικηγόροι',
            'singular_name' => 'Δικηγόρος',
            'add_new_item'  => 'Νέο μέλος',
            'edit_item'     => 'Επεξεργασία μέλους',
            'all_items'     => 'Όλοι οι δικηγόροι',
            'menu_name'     => 'Δικηγόροι',
        ),
        'public'        => false,
        'show_ui'       => true,
        'show_in_menu'  => true,
        'menu_position' => 24,
        'menu_icon'     => 'dashicons-businessperson',
        'supports'      => array( 'title', 'page-attributes' ),
        'has_archive'   => false,
        'rewrite'       => false,
    ) );
} );

/* =====================================================================
 * ACF field groups (registered programmatically).
 * =================================================================== */
add_action( 'acf/init', function () {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) { return; }

    /* Hero slide ----------------------------------------------------- */
    acf_add_local_field_group( array(
        'key'    => 'group_mz_hero',
        'title'  => 'Περιεχόμενο slide',
        'fields' => array(
            array( 'key' => 'field_mz_hero_eyebrow',  'label' => 'Eyebrow',          'name' => 'eyebrow',  'type' => 'text' ),
            array( 'key' => 'field_mz_hero_headline', 'label' => 'Επικεφαλίδα',      'name' => 'headline', 'type' => 'textarea', 'rows' => 2, 'required' => 1 ),
            array( 'key' => 'field_mz_hero_lead',     'label' => 'Υπότιτλος',        'name' => 'lead',     'type' => 'wysiwyg', 'media_upload' => 0, 'toolbar' => 'basic', 'tabs' => 'visual' ),
            array( 'key' => 'field_mz_hero_image',    'label' => 'Εικόνα φόντου',    'name' => 'image',    'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium' ),
            array( 'key' => 'field_mz_hero_cta_l',    'label' => 'Κείμενο κουμπιού', 'name' => 'cta_label', 'type' => 'text' ),
            array( 'key' => 'field_mz_hero_cta_u',    'label' => 'URL κουμπιού',     'name' => 'cta_url',   'type' => 'url' ),
        ),
        'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'mz_hero' ) ) ),
        'menu_order' => 0,
    ) );

    /* Service -------------------------------------------------------- */
    acf_add_local_field_group( array(
        'key'    => 'group_mz_service',
        'title'  => 'Στοιχεία τομέα',
        'fields' => array(
            array( 'key' => 'field_mz_svc_desc', 'label' => 'Σύντομη περιγραφή', 'name' => 'description', 'type' => 'wysiwyg', 'media_upload' => 0, 'toolbar' => 'basic', 'tabs' => 'visual', 'required' => 1 ),
            array( 'key' => 'field_mz_svc_long', 'label' => 'Εκτενής περιγραφή', 'name' => 'long_description', 'type' => 'wysiwyg', 'media_upload' => 0 ),
        ),
        'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'mz_service' ) ) ),
    ) );

    /* Team Member ---------------------------------------------------- */
    acf_add_local_field_group( array(
        'key'    => 'group_mz_member',
        'title'  => 'Στοιχεία δικηγόρου',
        'fields' => array(
            array( 'key' => 'field_mz_mem_role',  'label' => 'Θέση / ρόλος',        'name' => 'role',      'type' => 'text', 'required' => 1 ),
            array( 'key' => 'field_mz_mem_short', 'label' => 'Σύντομο βιογραφικό',  'name' => 'short_bio', 'type' => 'wysiwyg', 'media_upload' => 0, 'toolbar' => 'basic', 'tabs' => 'visual' ),
            array( 'key' => 'field_mz_mem_photo', 'label' => 'Φωτογραφία',          'name' => 'photo',     'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium' ),
            array( 'key' => 'field_mz_mem_email', 'label' => 'Email',               'name' => 'email',     'type' => 'email' ),
            array( 'key' => 'field_mz_mem_phone', 'label' => 'Τηλέφωνο',            'name' => 'phone',     'type' => 'text' ),
        ),
        'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'mz_member' ) ) ),
    ) );

    /* Page hero (όλες οι landing pages) ------------------------------ */
    acf_add_local_field_group( array(
        'key'    => 'group_mz_page_hero',
        'title'  => 'Hero σελίδας',
        'fields' => array(
            array( 'key' => 'field_mz_p_eb',   'label' => 'Eyebrow', 'name' => 'page_eyebrow',   'type' => 'text' ),
            array( 'key' => 'field_mz_p_ttl',  'label' => 'Τίτλος (override)', 'name' => 'page_hero_title', 'type' => 'textarea', 'rows' => 2, 'instructions' => 'Αν αφεθεί κενό, χρησιμοποιείται ο τίτλος της σελίδας.' ),
            array( 'key' => 'field_mz_p_lead', 'label' => 'Lead', 'name' => 'page_hero_lead', 'type' => 'wysiwyg', 'media_upload' => 0, 'toolbar' => 'basic', 'tabs' => 'visual' ),
        ),
        'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'page' ) ) ),
        'position' => 'normal',
        'menu_order' => 0,
    ) );

    /* Contact page extra fields -------------------------------------- */
    $contact = get_page_by_path( 'contact' );
    if ( $contact ) {
        acf_add_local_field_group( array(
            'key'    => 'group_mz_contact',
            'title'  => 'Στοιχεία επικοινωνίας',
            'fields' => array(
                array( 'key' => 'field_mz_c_addr',  'label' => 'Διεύθυνση', 'name' => 'contact_address', 'type' => 'textarea', 'rows' => 2 ),
                array( 'key' => 'field_mz_c_phone', 'label' => 'Τηλέφωνο',  'name' => 'contact_phone',   'type' => 'text' ),
                array( 'key' => 'field_mz_c_email', 'label' => 'Email',     'name' => 'contact_email',   'type' => 'email' ),
                array( 'key' => 'field_mz_c_hours', 'label' => 'Ωράριο',    'name' => 'contact_hours',   'type' => 'textarea', 'rows' => 3 ),
            ),
            'location' => array( array( array( 'param' => 'page', 'operator' => '==', 'value' => (string) $contact->ID ) ) ),
            'position' => 'normal',
        ) );
    }

    /* Case study fields ---------------------------------------------- */
    $svc_choices_for_cases = array( '' => '— επιλογή —' );
    foreach ( get_posts( array( 'post_type' => 'mz_service', 'posts_per_page' => -1, 'orderby' => 'menu_order date', 'order' => 'ASC' ) ) as $svc_p ) {
        $svc_choices_for_cases[ $svc_p->ID ] = get_the_title( $svc_p );
    }
    acf_add_local_field_group( array(
        'key'    => 'group_mz_case',
        'title'  => 'Στοιχεία υπόθεσης',
        'fields' => array(
            array( 'key' => 'field_mz_c_area',     'label' => 'Τομέας δικαίου', 'name' => 'practice_area', 'type' => 'select', 'choices' => $svc_choices_for_cases, 'allow_null' => 0, 'required' => 1, 'return_format' => 'value' ),
            array( 'key' => 'field_mz_c_year',     'label' => 'Έτος ολοκλήρωσης', 'name' => 'year',     'type' => 'text', 'instructions' => 'Π.χ. 2024' ),
            array( 'key' => 'field_mz_c_duration', 'label' => 'Διάρκεια',         'name' => 'duration', 'type' => 'text', 'instructions' => 'Π.χ. «8 μήνες», «1 έτος»' ),
            array( 'key' => 'field_mz_c_outcome',  'label' => 'Αποτέλεσμα',       'name' => 'outcome',  'type' => 'text', 'required' => 1, 'instructions' => 'Σύντομη φράση που συνοψίζει το επίτευγμα. Π.χ. «Κούρεμα οφειλής 35%».' ),
            array( 'key' => 'field_mz_c_desc',     'label' => 'Περιγραφή',        'name' => 'description', 'type' => 'wysiwyg', 'media_upload' => 0, 'toolbar' => 'basic', 'tabs' => 'visual', 'required' => 1, 'instructions' => 'Σύντομη περιγραφή 2-3 προτάσεων χωρίς αναφορά σε ονόματα.' ),
        ),
        'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'mz_case' ) ) ),
    ) );

    /* FAQ fields ------------------------------------------------------ */
    acf_add_local_field_group( array(
        'key'    => 'group_mz_faq',
        'title'  => 'Απάντηση',
        'fields' => array(
            array( 'key' => 'field_mz_faq_answer', 'label' => 'Απάντηση', 'name' => 'answer', 'type' => 'wysiwyg', 'media_upload' => 0, 'required' => 1 ),
            array( 'key' => 'field_mz_faq_cat',    'label' => 'Κατηγορία', 'name' => 'category', 'type' => 'text', 'instructions' => 'Προαιρετικά. Π.χ. «Διαζύγιο», «Επιχειρήσεις». Οι ερωτήσεις της ίδιας κατηγορίας ομαδοποιούνται.' ),
        ),
        'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'mz_faq' ) ) ),
    ) );

    /* Post (Article) custom fields ----------------------------------- */
    $service_choices = array( '' => '— επιλογή —' );
    foreach ( get_posts( array( 'post_type' => 'mz_service', 'posts_per_page' => -1, 'orderby' => 'menu_order date', 'order' => 'ASC' ) ) as $svc ) {
        $service_choices[ $svc->ID ] = get_the_title( $svc );
    }
    acf_add_local_field_group( array(
        'key'    => 'group_mz_post',
        'title'  => 'Ρυθμίσεις άρθρου',
        'fields' => array(
            array( 'key' => 'field_mz_post_tab1', 'label' => 'Παρουσίαση', 'type' => 'tab' ),
            array( 'key' => 'field_mz_post_featured', 'label' => 'Κορυφαίο άρθρο', 'name' => 'is_featured', 'type' => 'true_false',
                   'instructions' => 'Αν ενεργό, το άρθρο εμφανίζεται ως «Επιλεγμένο» στη σελίδα Άρθρων.',
                   'ui' => 1, 'ui_on_text' => 'Ναι', 'ui_off_text' => 'Όχι' ),
            array( 'key' => 'field_mz_post_subt', 'label' => 'Υπότιτλος', 'name' => 'article_subtitle', 'type' => 'text',
                   'instructions' => 'Προαιρετικά. Εμφανίζεται κάτω από τον τίτλο, αντί για το excerpt.' ),

            array( 'key' => 'field_mz_post_tab2', 'label' => 'Περιεχόμενο', 'type' => 'tab' ),
            array( 'key' => 'field_mz_post_takeaways', 'label' => 'Κύρια συμπεράσματα (key takeaways)',
                   'name' => 'key_takeaways', 'type' => 'textarea', 'rows' => 5,
                   'instructions' => 'Μία σύντομη πρόταση ανά γραμμή. Εμφανίζονται σε ξεχωριστό κουτί στην αρχή του άρθρου.' ),
            array( 'key' => 'field_mz_post_pq', 'label' => 'Pull-quote', 'name' => 'pull_quote', 'type' => 'wysiwyg', 'media_upload' => 0, 'toolbar' => 'basic', 'tabs' => 'visual',
                   'instructions' => 'Φράση κλειδί που θα εμφανιστεί διακριτικά μέσα στο άρθρο. Ιδανικά 1-2 προτάσεις.' ),

            array( 'key' => 'field_mz_post_tab3', 'label' => 'Σύνδεση', 'type' => 'tab' ),
            array( 'key' => 'field_mz_post_svc', 'label' => 'Σχετικός τομέας δικαίου',
                   'name' => 'related_service', 'type' => 'select',
                   'choices' => $service_choices, 'allow_null' => 1, 'return_format' => 'value',
                   'instructions' => 'Επιλέξτε τον τομέα δικαίου με τον οποίο σχετίζεται το άρθρο. Ο αναγνώστης θα δει σύνδεσμο προς αυτόν.' ),

            array( 'key' => 'field_mz_post_tab4', 'label' => 'Call to action', 'type' => 'tab' ),
            array( 'key' => 'field_mz_post_cta_t', 'label' => 'Τίτλος CTA',     'name' => 'cta_title', 'type' => 'text',
                   'instructions' => 'Προαιρετικά. Αν αφεθεί κενό, χρησιμοποιείται ο γενικός τίτλος.' ),
            array( 'key' => 'field_mz_post_cta_x', 'label' => 'Κείμενο CTA',    'name' => 'cta_text',  'type' => 'wysiwyg', 'media_upload' => 0, 'toolbar' => 'basic', 'tabs' => 'visual' ),

            array( 'key' => 'field_mz_post_tab5', 'label' => 'Νομικά', 'type' => 'tab' ),
            array( 'key' => 'field_mz_post_disc', 'label' => 'Νομική σημείωση (disclaimer)',
                   'name' => 'disclaimer', 'type' => 'wysiwyg', 'media_upload' => 0, 'toolbar' => 'basic', 'tabs' => 'visual',
                   'instructions' => 'Προαιρετικά. Εμφανίζεται με μικρή γραμματοσειρά στο τέλος του άρθρου.' ),
        ),
        'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'post' ) ) ),
        'position' => 'normal',
    ) );

    /* Testimonial CPT fields ----------------------------------------- */
    acf_add_local_field_group( array(
        'key'    => 'group_mz_testimonial',
        'title'  => 'Testimonial',
        'fields' => array(
            array( 'key' => 'field_mz_t_quote', 'label' => 'Παράθεμα', 'name' => 'quote', 'type' => 'wysiwyg', 'media_upload' => 0, 'toolbar' => 'basic', 'tabs' => 'visual', 'required' => 1 ),
            array( 'key' => 'field_mz_t_role',  'label' => 'Ιδιότητα', 'name' => 'role',  'type' => 'text', 'instructions' => 'π.χ. Διευθύνων Σύμβουλος, εμπορική εταιρεία' ),
        ),
        'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'mz_testimonial' ) ) ),
    ) );

    /* Bio page (full content) ---------------------------------------- */
    $bio = get_page_by_path( 'bio' );
    if ( $bio ) {
        acf_add_local_field_group( array(
            'key'    => 'group_mz_bio',
            'title'  => 'Περιεχόμενο σελίδας Βιογραφικό',
            'fields' => array(
                array( 'key' => 'field_mz_bio_tab_hero', 'label' => 'Hero', 'type' => 'tab' ),
                array( 'key' => 'field_mz_bio_role_override',     'label' => 'Ιδιότητα (override)',    'name' => 'bio_role_override', 'type' => 'text', 'instructions' => 'Αν αφεθεί κενό, χρησιμοποιείται ο ρόλος του πρώτου δικηγόρου.' ),
                array( 'key' => 'field_mz_bio_badges',            'label' => 'Hero badges (μία ανά γραμμή)', 'name' => 'bio_hero_badges', 'type' => 'textarea', 'rows' => 4, 'placeholder' => "Δ.Σ.Α.\nΠαρ' Αρείω Πάγω\nLL.M.\nΔιαμεσολαβήτρια" ),
                array( 'key' => 'field_mz_bio_y_num',             'label' => 'Badge: αριθμός',         'name' => 'bio_years_badge_num',   'type' => 'text', 'placeholder' => '20+' ),
                array( 'key' => 'field_mz_bio_y_lab',             'label' => 'Badge: ετικέτα',         'name' => 'bio_years_badge_label', 'type' => 'text', 'placeholder' => 'χρόνια εμπειρίας' ),
                array( 'key' => 'field_mz_bio_cta1_l',            'label' => 'Πρωτεύον CTA: κείμενο',   'name' => 'bio_cta_primary_label', 'type' => 'text', 'placeholder' => 'Κλείστε ραντεβού' ),
                array( 'key' => 'field_mz_bio_cta1_u',            'label' => 'Πρωτεύον CTA: URL',       'name' => 'bio_cta_primary_url',   'type' => 'url' ),
                array( 'key' => 'field_mz_bio_cta2_l',            'label' => 'Δευτερεύον CTA: κείμενο', 'name' => 'bio_cta_secondary_label','type' => 'text', 'placeholder' => 'Δείτε την πορεία' ),
                array( 'key' => 'field_mz_bio_cta2_u',            'label' => 'Δευτερεύον CTA: anchor',  'name' => 'bio_cta_secondary_anchor','type' => 'text', 'placeholder' => '#bio-timeline' ),

                array( 'key' => 'field_mz_bio_tab_stats', 'label' => 'Στατιστικά', 'type' => 'tab' ),
                array( 'key' => 'field_mz_bio_stats',     'label' => 'Δείκτες (μία ανά γραμμή)',
                       'name' => 'bio_stats', 'type' => 'textarea', 'rows' => 6,
                       'instructions' => 'Φόρμα: Αριθμός | Ετικέτα. Μία γραμμή ανά δείκτη (έως 5).' ),

                array( 'key' => 'field_mz_bio_tab_phil', 'label' => 'Φιλοσοφία', 'type' => 'tab' ),
                array( 'key' => 'field_mz_bio_phil_eb',    'label' => 'Eyebrow', 'name' => 'bio_philosophy_eyebrow', 'type' => 'text' ),
                array( 'key' => 'field_mz_bio_phil_quote', 'label' => 'Παράθεμα', 'name' => 'bio_philosophy_quote', 'type' => 'wysiwyg', 'media_upload' => 0, 'toolbar' => 'basic', 'tabs' => 'visual' ),
                array( 'key' => 'field_mz_bio_phil_attr',  'label' => 'Υπογραφή', 'name' => 'bio_philosophy_attr', 'type' => 'text', 'instructions' => 'Αν κενό, εμφανίζεται το όνομα του πρώτου δικηγόρου.' ),

                array( 'key' => 'field_mz_bio_tab_tl', 'label' => 'Πορεία (timeline)', 'type' => 'tab' ),
                array( 'key' => 'field_mz_bio_tl_eb',    'label' => 'Eyebrow', 'name' => 'bio_timeline_eyebrow', 'type' => 'text' ),
                array( 'key' => 'field_mz_bio_tl_title', 'label' => 'Τίτλος',  'name' => 'bio_timeline_title',   'type' => 'text' ),
                array( 'key' => 'field_mz_bio_tl_lead',  'label' => 'Lead',    'name' => 'bio_timeline_lead',    'type' => 'wysiwyg', 'media_upload' => 0, 'toolbar' => 'basic', 'tabs' => 'visual' ),
                array( 'key' => 'field_mz_bio_tl',       'label' => 'Σταθμοί (μία γραμμή ανά σταθμό)',
                       'name' => 'bio_timeline', 'type' => 'textarea', 'rows' => 12,
                       'instructions' => 'Φόρμα: Έτος | Τύπος (edu/work/cert) | Τίτλος | Οργανισμός | Σημείωση | * (αν θες highlight). Μία γραμμή ανά σταθμό.' ),

                array( 'key' => 'field_mz_bio_tab_exp', 'label' => 'Εξειδίκευση', 'type' => 'tab' ),
                array( 'key' => 'field_mz_bio_exp_eb',    'label' => 'Eyebrow', 'name' => 'bio_expertise_eyebrow', 'type' => 'text' ),
                array( 'key' => 'field_mz_bio_exp_title', 'label' => 'Τίτλος',  'name' => 'bio_expertise_title',   'type' => 'text' ),
                array( 'key' => 'field_mz_bio_exp_t1',    'label' => 'Πρωτεύοντες — επικεφαλίδα', 'name' => 'bio_expertise_t1_title', 'type' => 'text', 'placeholder' => 'Πρωτεύοντες' ),
                array( 'key' => 'field_mz_bio_exp_l1',    'label' => 'Πρωτεύοντες — tags',         'name' => 'bio_expertise_t1_tags',  'type' => 'textarea', 'rows' => 4, 'instructions' => 'Ένα tag ανά γραμμή.' ),
                array( 'key' => 'field_mz_bio_exp_t2',    'label' => 'Δευτερεύοντες — επικεφαλίδα','name' => 'bio_expertise_t2_title', 'type' => 'text', 'placeholder' => 'Δευτερεύοντες' ),
                array( 'key' => 'field_mz_bio_exp_l2',    'label' => 'Δευτερεύοντες — tags',       'name' => 'bio_expertise_t2_tags',  'type' => 'textarea', 'rows' => 4 ),
                array( 'key' => 'field_mz_bio_exp_t3',    'label' => 'Πρόσθετοι — επικεφαλίδα',    'name' => 'bio_expertise_t3_title', 'type' => 'text', 'placeholder' => 'Πρόσθετοι τομείς' ),
                array( 'key' => 'field_mz_bio_exp_l3',    'label' => 'Πρόσθετοι — tags',           'name' => 'bio_expertise_t3_tags',  'type' => 'textarea', 'rows' => 4 ),

                array( 'key' => 'field_mz_bio_tab_mem', 'label' => 'Συμμετοχές', 'type' => 'tab' ),
                array( 'key' => 'field_mz_bio_mem_eb',    'label' => 'Eyebrow', 'name' => 'bio_memberships_eyebrow', 'type' => 'text' ),
                array( 'key' => 'field_mz_bio_mem_title', 'label' => 'Τίτλος',  'name' => 'bio_memberships_title',   'type' => 'text' ),
                array( 'key' => 'field_mz_bio_mem',       'label' => 'Συμμετοχές (μία ανά γραμμή)',
                       'name' => 'bio_memberships', 'type' => 'textarea', 'rows' => 6,
                       'instructions' => 'Φόρμα: Τίτλος | Σημείωση | Εικονίδιο (building/scale/badge/handshake).' ),

                array( 'key' => 'field_mz_bio_tab_lang', 'label' => 'Γλώσσες', 'type' => 'tab' ),
                array( 'key' => 'field_mz_bio_lang_eb',    'label' => 'Eyebrow', 'name' => 'bio_languages_eyebrow', 'type' => 'text' ),
                array( 'key' => 'field_mz_bio_lang_title', 'label' => 'Τίτλος',  'name' => 'bio_languages_title',   'type' => 'text' ),
                array( 'key' => 'field_mz_bio_lang',       'label' => 'Γλώσσες (μία ανά γραμμή)',
                       'name' => 'bio_languages', 'type' => 'textarea', 'rows' => 5,
                       'instructions' => 'Φόρμα: Γλώσσα | Επίπεδο | Ποσοστό (0-100) | CEFR. Π.χ.: Αγγλικά | Άριστη γνώση | 95 | C2.' ),

                array( 'key' => 'field_mz_bio_tab_pub', 'label' => 'Δημοσιεύσεις', 'type' => 'tab' ),
                array( 'key' => 'field_mz_bio_pub_eb',    'label' => 'Eyebrow', 'name' => 'bio_publications_eyebrow', 'type' => 'text' ),
                array( 'key' => 'field_mz_bio_pub_title', 'label' => 'Τίτλος',  'name' => 'bio_publications_title',   'type' => 'text' ),
                array( 'key' => 'field_mz_bio_pub',       'label' => 'Δημοσιεύσεις (μία ανά γραμμή)',
                       'name' => 'bio_publications', 'type' => 'textarea', 'rows' => 8,
                       'instructions' => 'Φόρμα: Έτος | Τύπος | Τίτλος | Έκδοση/χώρος.' ),

                array( 'key' => 'field_mz_bio_tab_cta', 'label' => 'CTA', 'type' => 'tab' ),
                array( 'key' => 'field_mz_bio_cta_eb',    'label' => 'Eyebrow', 'name' => 'bio_cta_eyebrow', 'type' => 'text' ),
                array( 'key' => 'field_mz_bio_cta_title', 'label' => 'Τίτλος',  'name' => 'bio_cta_title',   'type' => 'text', 'instructions' => 'Μπορείτε να βάλετε {name} για το όνομα του δικηγόρου.' ),
                array( 'key' => 'field_mz_bio_cta_lead',  'label' => 'Lead',    'name' => 'bio_cta_lead',    'type' => 'wysiwyg', 'media_upload' => 0, 'toolbar' => 'basic', 'tabs' => 'visual' ),
                array( 'key' => 'field_mz_bio_cta_l1',    'label' => 'Κουμπί φόρμας — κείμενο', 'name' => 'bio_cta_form_label', 'type' => 'text', 'placeholder' => 'Φόρμα επικοινωνίας' ),
                array( 'key' => 'field_mz_bio_cta_u1',    'label' => 'Κουμπί φόρμας — URL',     'name' => 'bio_cta_form_url',   'type' => 'url' ),
            ),
            'location' => array( array( array( 'param' => 'page', 'operator' => '==', 'value' => (string) $bio->ID ) ) ),
            'position' => 'normal',
            'menu_order' => 0,
        ) );
    }

    /* About page (full content) -------------------------------------- */
    $about = get_page_by_path( 'about' );
    if ( $about ) {
        acf_add_local_field_group( array(
            'key'    => 'group_mz_about',
            'title'  => 'Περιεχόμενο σελίδας «Το γραφείο»',
            'fields' => array(
                array( 'key' => 'field_mz_a_tab_man',   'label' => 'Manifesto',  'type' => 'tab' ),
                array( 'key' => 'field_mz_a_man_eb',    'label' => 'Eyebrow',     'name' => 'about_manifesto_eyebrow', 'type' => 'text', 'placeholder' => 'Manifesto' ),
                array( 'key' => 'field_mz_a_man_text',  'label' => 'Κείμενο',     'name' => 'about_manifesto_text',    'type' => 'wysiwyg', 'media_upload' => 0, 'toolbar' => 'basic', 'tabs' => 'visual' ),
                array( 'key' => 'field_mz_a_man_attr',  'label' => 'Υπογραφή',    'name' => 'about_manifesto_attr',    'type' => 'text' ),

                array( 'key' => 'field_mz_a_tab_story', 'label' => 'Ιστορία', 'type' => 'tab' ),
                array( 'key' => 'field_mz_a_story_title','label' => 'Eyebrow ενότητας', 'name' => 'about_story_title',  'type' => 'text', 'placeholder' => 'Η ιστορία μας' ),
                array( 'key' => 'field_mz_a_story_h',    'label' => 'Επικεφαλίδα',     'name' => 'about_story_heading','type' => 'text', 'placeholder' => 'Από μια ιδέα, ένα γραφείο.' ),
                array( 'key' => 'field_mz_a_story_text', 'label' => 'Κείμενο ιστορίας (πρώτη παράγραφος)', 'name' => 'about_story_text', 'type' => 'wysiwyg', 'media_upload' => 0, 'toolbar' => 'basic', 'tabs' => 'visual' ),
                array( 'key' => 'field_mz_a_story_p2',   'label' => 'Δεύτερη παράγραφος', 'name' => 'about_story_p2', 'type' => 'wysiwyg', 'media_upload' => 0, 'toolbar' => 'basic', 'tabs' => 'visual' ),
                array( 'key' => 'field_mz_a_story_year', 'label' => 'Έτος ίδρυσης (badge)', 'name' => 'about_story_year', 'type' => 'text', 'placeholder' => '2005' ),
                array( 'key' => 'field_mz_a_story_mini', 'label' => 'Mini στατιστικά (μία ανά γραμμή)',
                       'name' => 'about_story_mini', 'type' => 'textarea', 'rows' => 4,
                       'instructions' => 'Φόρμα: Αριθμός | Ετικέτα. Μία γραμμή ανά δείκτη (έως 3).' ),

                array( 'key' => 'field_mz_a_tab_pillars', 'label' => 'Πυλώνες (Αξίες)', 'type' => 'tab' ),
                array( 'key' => 'field_mz_a_pil_eb',    'label' => 'Eyebrow', 'name' => 'about_pillars_eyebrow', 'type' => 'text', 'placeholder' => 'Πυλώνες' ),
                array( 'key' => 'field_mz_a_pil_h',     'label' => 'Επικεφαλίδα', 'name' => 'about_pillars_heading', 'type' => 'text', 'placeholder' => 'Οι αρχές που μας οδηγούν' ),
                array( 'key' => 'field_mz_a_pil_lead',  'label' => 'Lead', 'name' => 'about_pillars_lead', 'type' => 'wysiwyg', 'media_upload' => 0, 'toolbar' => 'basic', 'tabs' => 'visual' ),
                array( 'key' => 'field_mz_a_values',    'label' => 'Αξίες (μία ανά γραμμή)',
                       'name' => 'about_values', 'type' => 'textarea', 'rows' => 8,
                       'instructions' => 'Φόρμα: Τίτλος | Περιγραφή. Μία αξία ανά γραμμή.' ),

                array( 'key' => 'field_mz_a_tab_diff', 'label' => 'Διαφοροποίηση', 'type' => 'tab' ),
                array( 'key' => 'field_mz_a_diff_eb',  'label' => 'Eyebrow', 'name' => 'about_diff_eyebrow', 'type' => 'text', 'placeholder' => 'Τι μας ξεχωρίζει' ),
                array( 'key' => 'field_mz_a_diff_h',   'label' => 'Επικεφαλίδα', 'name' => 'about_diff_heading', 'type' => 'text', 'placeholder' => 'Τρεις διαφορές που έχουν σημασία.' ),
                array( 'key' => 'field_mz_a_diff_btn_l','label' => 'Κουμπί — κείμενο', 'name' => 'about_diff_btn_label', 'type' => 'text', 'placeholder' => 'Δείτε τους τομείς' ),
                array( 'key' => 'field_mz_a_diff_btn_u','label' => 'Κουμπί — URL',     'name' => 'about_diff_btn_url',   'type' => 'url' ),
                array( 'key' => 'field_mz_a_diff_items','label' => 'Διαφορές (μία ανά γραμμή)',
                       'name' => 'about_diff_items', 'type' => 'textarea', 'rows' => 6,
                       'instructions' => 'Φόρμα: Εικονίδιο (clock/sun/check) | Τίτλος | Κείμενο. Μία γραμμή ανά διαφορά (3 συνολικά).' ),

                array( 'key' => 'field_mz_a_tab_miss', 'label' => 'Αποστολή', 'type' => 'tab' ),
                array( 'key' => 'field_mz_a_mission_title','label' => 'Eyebrow ενότητας','name' => 'about_mission_title', 'type' => 'text', 'placeholder' => 'Η αποστολή μας' ),
                array( 'key' => 'field_mz_a_mission_h',    'label' => 'Επικεφαλίδα',     'name' => 'about_mission_heading','type' => 'text', 'placeholder' => 'Καθαρές αποφάσεις, χωρίς ορολογίες.' ),
                array( 'key' => 'field_mz_a_mission_text', 'label' => 'Κείμενο αποστολής','name' => 'about_mission_text',  'type' => 'wysiwyg', 'media_upload' => 0, 'toolbar' => 'basic', 'tabs' => 'visual' ),
                array( 'key' => 'field_mz_a_mission_q',    'label' => 'Founder quote',   'name' => 'about_mission_quote', 'type' => 'textarea', 'rows' => 3 ),
                array( 'key' => 'field_mz_a_mission_qc',   'label' => 'Citation',        'name' => 'about_mission_quote_cite', 'type' => 'text', 'placeholder' => '— από συζήτηση με νέο πελάτη, 2024' ),

                array( 'key' => 'field_mz_a_tab_cta', 'label' => 'CTA', 'type' => 'tab' ),
                array( 'key' => 'field_mz_a_cta_h',    'label' => 'Επικεφαλίδα', 'name' => 'about_cta_heading', 'type' => 'text', 'placeholder' => 'Γνωρίστε την ομάδα μας.' ),
                array( 'key' => 'field_mz_a_cta_lead', 'label' => 'Κείμενο',     'name' => 'about_cta_lead',    'type' => 'wysiwyg', 'media_upload' => 0, 'toolbar' => 'basic', 'tabs' => 'visual' ),
                array( 'key' => 'field_mz_a_cta_b1l',  'label' => 'Κουμπί 1 — κείμενο', 'name' => 'about_cta_btn1_label', 'type' => 'text', 'placeholder' => 'Δείτε την ομάδα' ),
                array( 'key' => 'field_mz_a_cta_b1u',  'label' => 'Κουμπί 1 — URL',     'name' => 'about_cta_btn1_url',   'type' => 'url' ),
                array( 'key' => 'field_mz_a_cta_b2l',  'label' => 'Κουμπί 2 — κείμενο', 'name' => 'about_cta_btn2_label', 'type' => 'text', 'placeholder' => 'Επικοινωνία' ),
                array( 'key' => 'field_mz_a_cta_b2u',  'label' => 'Κουμπί 2 — URL',     'name' => 'about_cta_btn2_url',   'type' => 'url' ),
            ),
            'location' => array( array( array( 'param' => 'page', 'operator' => '==', 'value' => (string) $about->ID ) ) ),
            'position' => 'normal',
        ) );
    }

    /* Team page ------------------------------------------------------ */
    $team = get_page_by_path( 'team' );
    if ( $team ) {
        acf_add_local_field_group( array(
            'key'    => 'group_mz_team_page',
            'title'  => 'Περιεχόμενο σελίδας «Δικηγόροι»',
            'fields' => array(
                array( 'key' => 'field_mz_tp_intro_p2',  'label' => 'Συμπληρωματική παράγραφος', 'name' => 'team_lead_p2', 'type' => 'wysiwyg', 'media_upload' => 0, 'toolbar' => 'basic', 'tabs' => 'visual',
                       'instructions' => 'Εμφανίζεται κάτω από το βιογραφικό του πρώτου δικηγόρου.' ),
                array( 'key' => 'field_mz_tp_meta',      'label' => 'Meta στοιχεία (μία ανά γραμμή)', 'name' => 'team_lead_meta', 'type' => 'textarea', 'rows' => 5,
                       'instructions' => 'Φόρμα: Ετικέτα | Τιμή. Π.χ.: Δικηγορικός Σύλλογος | Αθηνών' ),
                array( 'key' => 'field_mz_tp_b1l',       'label' => 'Κουμπί 1 — κείμενο', 'name' => 'team_btn1_label', 'type' => 'text', 'placeholder' => 'Πλήρες βιογραφικό' ),
                array( 'key' => 'field_mz_tp_b1u',       'label' => 'Κουμπί 1 — URL',     'name' => 'team_btn1_url',   'type' => 'url' ),
                array( 'key' => 'field_mz_tp_b2l',       'label' => 'Κουμπί 2 — κείμενο', 'name' => 'team_btn2_label', 'type' => 'text', 'placeholder' => 'Επικοινωνία' ),
                array( 'key' => 'field_mz_tp_b2u',       'label' => 'Κουμπί 2 — URL',     'name' => 'team_btn2_url',   'type' => 'url' ),
                array( 'key' => 'field_mz_tp_net_eb',    'label' => 'Συνεργασίες — eyebrow',     'name' => 'team_net_eyebrow', 'type' => 'text', 'placeholder' => 'Συνεργασίες' ),
                array( 'key' => 'field_mz_tp_net_t',     'label' => 'Συνεργασίες — επικεφαλίδα', 'name' => 'team_net_title',   'type' => 'text', 'placeholder' => 'Δουλεύουμε με αξιόπιστα δίκτυα.' ),
                array( 'key' => 'field_mz_tp_net_l',     'label' => 'Συνεργασίες — κείμενο',     'name' => 'team_net_lead',    'type' => 'wysiwyg', 'media_upload' => 0, 'toolbar' => 'basic', 'tabs' => 'visual' ),
            ),
            'location' => array( array( array( 'param' => 'page', 'operator' => '==', 'value' => (string) $team->ID ) ) ),
            'position' => 'normal',
        ) );
    }

    /* Services page -------------------------------------------------- */
    $services_page = get_page_by_path( 'services' );
    if ( $services_page ) {
        acf_add_local_field_group( array(
            'key'    => 'group_mz_services_page',
            'title'  => 'Περιεχόμενο σελίδας «Τομείς εξειδίκευσης»',
            'fields' => array(
                array( 'key' => 'field_mz_sp_proc_eb',   'label' => 'Διαδικασία — eyebrow',     'name' => 'services_proc_eyebrow', 'type' => 'text', 'placeholder' => 'Διαδικασία' ),
                array( 'key' => 'field_mz_sp_proc_t',    'label' => 'Διαδικασία — επικεφαλίδα', 'name' => 'services_proc_title',   'type' => 'text', 'placeholder' => 'Πώς δουλεύουμε σε κάθε υπόθεση.' ),
                array( 'key' => 'field_mz_sp_proc_steps','label' => 'Βήματα (μία ανά γραμμή)',
                       'name' => 'services_proc_steps', 'type' => 'textarea', 'rows' => 8,
                       'instructions' => 'Φόρμα: Αριθμός | Τίτλος | Περιγραφή. Μία γραμμή ανά βήμα.' ),
                array( 'key' => 'field_mz_sp_cta_t',     'label' => 'CTA — επικεφαλίδα', 'name' => 'services_cta_title', 'type' => 'text', 'placeholder' => 'Δεν είστε σίγουρος αν η υπόθεσή σας εμπίπτει εδώ;' ),
                array( 'key' => 'field_mz_sp_cta_l',     'label' => 'CTA — κείμενο',     'name' => 'services_cta_lead',  'type' => 'wysiwyg', 'media_upload' => 0, 'toolbar' => 'basic', 'tabs' => 'visual' ),
                array( 'key' => 'field_mz_sp_cta_bl',    'label' => 'CTA — κουμπί',      'name' => 'services_cta_btn_label', 'type' => 'text', 'placeholder' => 'Επικοινωνία' ),
                array( 'key' => 'field_mz_sp_cta_bu',    'label' => 'CTA — URL κουμπιού', 'name' => 'services_cta_btn_url',   'type' => 'url' ),
            ),
            'location' => array( array( array( 'param' => 'page', 'operator' => '==', 'value' => (string) $services_page->ID ) ) ),
            'position' => 'normal',
        ) );
    }

    /* Cases page ----------------------------------------------------- */
    $cases_page = get_page_by_path( 'cases' );
    if ( $cases_page ) {
        acf_add_local_field_group( array(
            'key'    => 'group_mz_cases_page',
            'title'  => 'Περιεχόμενο σελίδας «Επιλεγμένες υποθέσεις»',
            'fields' => array(
                array( 'key' => 'field_mz_cp_disc',     'label' => 'Disclaimer', 'name' => 'cases_disclaimer', 'type' => 'wysiwyg', 'media_upload' => 0, 'toolbar' => 'basic', 'tabs' => 'visual' ),
                array( 'key' => 'field_mz_cp_cta_t',    'label' => 'CTA — επικεφαλίδα', 'name' => 'cases_cta_title', 'type' => 'text', 'placeholder' => 'Έχετε παρόμοια υπόθεση;' ),
                array( 'key' => 'field_mz_cp_cta_l',    'label' => 'CTA — κείμενο',     'name' => 'cases_cta_lead',  'type' => 'wysiwyg', 'media_upload' => 0, 'toolbar' => 'basic', 'tabs' => 'visual' ),
                array( 'key' => 'field_mz_cp_cta_bl',   'label' => 'CTA — κουμπί',      'name' => 'cases_cta_btn_label', 'type' => 'text', 'placeholder' => 'Επικοινωνία' ),
                array( 'key' => 'field_mz_cp_cta_bu',   'label' => 'CTA — URL κουμπιού','name' => 'cases_cta_btn_url',   'type' => 'url' ),
            ),
            'location' => array( array( array( 'param' => 'page', 'operator' => '==', 'value' => (string) $cases_page->ID ) ) ),
            'position' => 'normal',
        ) );
    }

    /* FAQ page ------------------------------------------------------- */
    $faq_page = get_page_by_path( 'faq' );
    if ( $faq_page ) {
        acf_add_local_field_group( array(
            'key'    => 'group_mz_faq_page',
            'title'  => 'Περιεχόμενο σελίδας «Συχνές ερωτήσεις»',
            'fields' => array(
                array( 'key' => 'field_mz_fp_resp',     'label' => 'Στατιστικό απόκρισης',
                       'name' => 'faq_response_label', 'type' => 'text', 'placeholder' => '24h' ),
                array( 'key' => 'field_mz_fp_still_eb', 'label' => 'Bottom — eyebrow',     'name' => 'faq_still_eyebrow', 'type' => 'text', 'placeholder' => 'Ακόμη ερωτήματα;' ),
                array( 'key' => 'field_mz_fp_still_t',  'label' => 'Bottom — επικεφαλίδα', 'name' => 'faq_still_title',   'type' => 'text', 'placeholder' => 'Δεν βρήκατε αυτό που ψάχνατε;' ),
                array( 'key' => 'field_mz_fp_still_l',  'label' => 'Bottom — κείμενο',     'name' => 'faq_still_lead',    'type' => 'wysiwyg', 'media_upload' => 0, 'toolbar' => 'basic', 'tabs' => 'visual' ),
                array( 'key' => 'field_mz_fp_b1l',      'label' => 'Κουμπί 1 — κείμενο', 'name' => 'faq_still_btn1_label', 'type' => 'text', 'placeholder' => 'Επικοινωνία' ),
                array( 'key' => 'field_mz_fp_b1u',      'label' => 'Κουμπί 1 — URL',     'name' => 'faq_still_btn1_url',   'type' => 'url' ),
                array( 'key' => 'field_mz_fp_b2l',      'label' => 'Κουμπί 2 — κείμενο', 'name' => 'faq_still_btn2_label', 'type' => 'text', 'placeholder' => 'Νομικό λεξικό' ),
                array( 'key' => 'field_mz_fp_b2u',      'label' => 'Κουμπί 2 — URL',     'name' => 'faq_still_btn2_url',   'type' => 'url' ),
            ),
            'location' => array( array( array( 'param' => 'page', 'operator' => '==', 'value' => (string) $faq_page->ID ) ) ),
            'position' => 'normal',
        ) );
    }

    /* Glossary page -------------------------------------------------- */
    $glossary_page = get_page_by_path( 'glossary' );
    if ( $glossary_page ) {
        acf_add_local_field_group( array(
            'key'    => 'group_mz_glossary_page',
            'title'  => 'Περιεχόμενο σελίδας «Νομικό λεξικό»',
            'fields' => array(
                array( 'key' => 'field_mz_gp_terms', 'label' => 'Όροι', 'name' => 'glossary_terms', 'type' => 'textarea', 'rows' => 30,
                       'instructions' => 'Φόρμα: Γράμμα | Όρος | Ορισμός. Μία γραμμή ανά όρο. Π.χ.: Α | Αγωγή | Το δικόγραφο με το οποίο…' ),
                array( 'key' => 'field_mz_gp_cta_t', 'label' => 'CTA — επικεφαλίδα', 'name' => 'glossary_cta_title', 'type' => 'text', 'placeholder' => 'Δεν βρίσκετε όρο που σας απασχολεί;' ),
                array( 'key' => 'field_mz_gp_cta_l', 'label' => 'CTA — κείμενο',     'name' => 'glossary_cta_lead',  'type' => 'wysiwyg', 'media_upload' => 0, 'toolbar' => 'basic', 'tabs' => 'visual' ),
                array( 'key' => 'field_mz_gp_cta_bl','label' => 'CTA — κουμπί',      'name' => 'glossary_cta_btn_label', 'type' => 'text', 'placeholder' => 'Επικοινωνία' ),
                array( 'key' => 'field_mz_gp_cta_bu','label' => 'CTA — URL κουμπιού','name' => 'glossary_cta_btn_url',   'type' => 'url' ),
            ),
            'location' => array( array( array( 'param' => 'page', 'operator' => '==', 'value' => (string) $glossary_page->ID ) ) ),
            'position' => 'normal',
        ) );
    }

    /* Contact page extras ------------------------------------------- */
    $contact_page = get_page_by_path( 'contact' );
    if ( $contact_page ) {
        acf_add_local_field_group( array(
            'key'    => 'group_mz_contact_page',
            'title'  => 'Περιεχόμενο σελίδας «Επικοινωνία»',
            'fields' => array(
                array( 'key' => 'field_mz_cnp_phone_hint', 'label' => 'Τηλέφωνο — υπότιτλος', 'name' => 'contact_phone_hint', 'type' => 'text', 'placeholder' => 'Δευτ — Παρ, 09:00 — 19:00' ),
                array( 'key' => 'field_mz_cnp_email_hint', 'label' => 'Email — υπότιτλος',    'name' => 'contact_email_hint', 'type' => 'text', 'placeholder' => 'Απάντηση σε 24 ώρες' ),
                array( 'key' => 'field_mz_cnp_addr_hint',  'label' => 'Διεύθυνση — υπότιτλος','name' => 'contact_addr_hint',  'type' => 'text', 'placeholder' => 'Δείτε στον χάρτη →' ),
                array( 'key' => 'field_mz_cnp_form_eb',    'label' => 'Φόρμα — eyebrow',     'name' => 'contact_form_eyebrow', 'type' => 'text', 'placeholder' => 'Φόρμα επικοινωνίας' ),
                array( 'key' => 'field_mz_cnp_form_t',     'label' => 'Φόρμα — επικεφαλίδα', 'name' => 'contact_form_title',   'type' => 'text', 'placeholder' => 'Στείλτε μας μήνυμα' ),
                array( 'key' => 'field_mz_cnp_form_s',     'label' => 'Φόρμα — υπότιτλος',   'name' => 'contact_form_subtitle','type' => 'text', 'placeholder' => 'Απαντάμε εντός 24 ωρών...' ),
                array( 'key' => 'field_mz_cnp_b1_t',       'label' => 'Πλάγια ενότητα 1 — τίτλος', 'name' => 'contact_side1_title', 'type' => 'text', 'placeholder' => 'Ωράριο γραφείου' ),
                array( 'key' => 'field_mz_cnp_b2_t',       'label' => 'Πλάγια ενότητα 2 — τίτλος', 'name' => 'contact_side2_title', 'type' => 'text', 'placeholder' => 'Επείγουσες υποθέσεις' ),
                array( 'key' => 'field_mz_cnp_b2_x',       'label' => 'Πλάγια ενότητα 2 — κείμενο','name' => 'contact_side2_text',  'type' => 'wysiwyg', 'media_upload' => 0, 'toolbar' => 'basic', 'tabs' => 'visual' ),
                array( 'key' => 'field_mz_cnp_b3_t',       'label' => 'Πλάγια ενότητα 3 — τίτλος', 'name' => 'contact_side3_title', 'type' => 'text', 'placeholder' => 'Online συνάντηση' ),
                array( 'key' => 'field_mz_cnp_b3_x',       'label' => 'Πλάγια ενότητα 3 — κείμενο','name' => 'contact_side3_text',  'type' => 'wysiwyg', 'media_upload' => 0, 'toolbar' => 'basic', 'tabs' => 'visual' ),
                array( 'key' => 'field_mz_cnp_social',     'label' => 'Social links (μία ανά γραμμή)',
                       'name' => 'contact_social', 'type' => 'textarea', 'rows' => 4,
                       'instructions' => 'Φόρμα: Πλατφόρμα (linkedin/facebook/instagram) | URL.' ),
                array( 'key' => 'field_mz_cnp_disc_eb',    'label' => 'Bottom — eyebrow',     'name' => 'contact_bottom_eyebrow', 'type' => 'text', 'placeholder' => 'Διακριτικότητα' ),
                array( 'key' => 'field_mz_cnp_disc_t',     'label' => 'Bottom — επικεφαλίδα', 'name' => 'contact_bottom_title',   'type' => 'text', 'placeholder' => 'Όλες οι επικοινωνίες είναι εμπιστευτικές' ),
                array( 'key' => 'field_mz_cnp_disc_l',     'label' => 'Bottom — κείμενο',     'name' => 'contact_bottom_lead',    'type' => 'wysiwyg', 'media_upload' => 0, 'toolbar' => 'basic', 'tabs' => 'visual' ),
            ),
            'location' => array( array( array( 'param' => 'page', 'operator' => '==', 'value' => (string) $contact_page->ID ) ) ),
            'position' => 'normal',
        ) );
    }

    /* Blog (posts) page --------------------------------------------- */
    $blog_page = get_page_by_path( 'blog' );
    if ( $blog_page ) {
        acf_add_local_field_group( array(
            'key'    => 'group_mz_blog_page',
            'title'  => 'Περιεχόμενο σελίδας «Άρθρα»',
            'fields' => array(
                array( 'key' => 'field_mz_bp_feat_lab', 'label' => 'Featured badge', 'name' => 'blog_featured_label', 'type' => 'text', 'placeholder' => 'Επιλεγμένο' ),
                array( 'key' => 'field_mz_bp_all_eb',   'label' => 'Όλα τα άρθρα — eyebrow',     'name' => 'blog_all_eyebrow', 'type' => 'text', 'placeholder' => 'Όλα τα άρθρα' ),
                array( 'key' => 'field_mz_bp_all_t',    'label' => 'Όλα τα άρθρα — επικεφαλίδα', 'name' => 'blog_all_title',   'type' => 'text', 'placeholder' => 'Πρόσφατες δημοσιεύσεις.' ),
                array( 'key' => 'field_mz_bp_nl_eb',    'label' => 'Newsletter — eyebrow',       'name' => 'blog_nl_eyebrow', 'type' => 'text', 'placeholder' => 'Newsletter' ),
                array( 'key' => 'field_mz_bp_nl_t',     'label' => 'Newsletter — επικεφαλίδα',   'name' => 'blog_nl_title',   'type' => 'text', 'placeholder' => 'Λάβετε τις σημαντικές νομικές εξελίξεις στο email σας.' ),
                array( 'key' => 'field_mz_bp_nl_x',     'label' => 'Newsletter — κείμενο',       'name' => 'blog_nl_text',    'type' => 'text', 'placeholder' => 'Μία φορά τον μήνα. Επιλεγμένες αναλύσεις. Καμία διαφήμιση.' ),
                array( 'key' => 'field_mz_bp_nl_btn',   'label' => 'Newsletter — κουμπί',        'name' => 'blog_nl_btn',     'type' => 'text', 'placeholder' => 'Εγγραφή' ),
                array( 'key' => 'field_mz_bp_cta_t',    'label' => 'CTA — επικεφαλίδα', 'name' => 'blog_cta_title', 'type' => 'text', 'placeholder' => 'Έχετε νομικό ερώτημα που απαντά κάποιο άρθρο;' ),
                array( 'key' => 'field_mz_bp_cta_l',    'label' => 'CTA — κείμενο',     'name' => 'blog_cta_lead',  'type' => 'wysiwyg', 'media_upload' => 0, 'toolbar' => 'basic', 'tabs' => 'visual' ),
                array( 'key' => 'field_mz_bp_cta_bl',   'label' => 'CTA — κουμπί',      'name' => 'blog_cta_btn_label', 'type' => 'text', 'placeholder' => 'Επικοινωνία' ),
                array( 'key' => 'field_mz_bp_cta_bu',   'label' => 'CTA — URL κουμπιού','name' => 'blog_cta_btn_url',   'type' => 'url' ),
            ),
            'location' => array( array( array( 'param' => 'page', 'operator' => '==', 'value' => (string) $blog_page->ID ) ) ),
            'position' => 'normal',
        ) );
    }

    /* Front-page content blocks (full set) ---------------------------- */
    acf_add_local_field_group( array(
        'key'    => 'group_mz_front',
        'title'  => 'Περιεχόμενο αρχικής',
        'fields' => array(
            array( 'key' => 'field_mz_f_tab_trust',  'label' => 'Trust strip',   'type' => 'tab' ),
            array( 'key' => 'field_mz_f_trust',      'label' => 'Δείκτες (μία ανά γραμμή)',
                   'name' => 'home_trust_strip',
                   'type' => 'textarea',
                   'rows' => 6,
                   'instructions' => 'Φόρμα: Αριθμός | Ετικέτα. Μία γραμμή ανά δείκτη (έως 5).' ),

            array( 'key' => 'field_mz_f_tab_about',  'label' => 'Φιλοσοφία',     'type' => 'tab' ),
            array( 'key' => 'field_mz_f_about_eb',   'label' => 'Eyebrow',           'name' => 'home_about_eyebrow', 'type' => 'text' ),
            array( 'key' => 'field_mz_f_about_title','label' => 'Τίτλος',            'name' => 'home_about_title',   'type' => 'textarea', 'rows' => 2 ),
            array( 'key' => 'field_mz_f_about_quote','label' => 'Pull-quote',        'name' => 'home_philosophy_quote', 'type' => 'wysiwyg', 'media_upload' => 0, 'toolbar' => 'basic', 'tabs' => 'visual' ),
            array( 'key' => 'field_mz_f_about_year', 'label' => 'Caption έτους',     'name' => 'home_philosophy_year',  'type' => 'text', 'instructions' => 'π.χ. Est. 2005' ),
            array( 'key' => 'field_mz_f_about_text', 'label' => 'Κείμενο',           'name' => 'home_about_text',    'type' => 'wysiwyg', 'media_upload' => 0, 'toolbar' => 'basic', 'tabs' => 'visual' ),

            array( 'key' => 'field_mz_f_tab_svc',    'label' => 'Τομείς',         'type' => 'tab' ),
            array( 'key' => 'field_mz_f_svc_eb',     'label' => 'Eyebrow',           'name' => 'home_svc_eyebrow',   'type' => 'text' ),
            array( 'key' => 'field_mz_f_svc_title',  'label' => 'Τίτλος',            'name' => 'home_svc_title',     'type' => 'textarea', 'rows' => 2 ),
            array( 'key' => 'field_mz_f_svc_lead',   'label' => 'Υπότιτλος',         'name' => 'home_svc_lead',      'type' => 'wysiwyg', 'media_upload' => 0, 'toolbar' => 'basic', 'tabs' => 'visual' ),

            array( 'key' => 'field_mz_f_tab_proc',   'label' => 'Διαδικασία',     'type' => 'tab' ),
            array( 'key' => 'field_mz_f_proc_eb',    'label' => 'Eyebrow',           'name' => 'home_process_eyebrow', 'type' => 'text' ),
            array( 'key' => 'field_mz_f_proc_title', 'label' => 'Τίτλος',            'name' => 'home_process_title',   'type' => 'textarea', 'rows' => 2 ),
            array( 'key' => 'field_mz_f_proc_lead',  'label' => 'Υπότιτλος',         'name' => 'home_process_lead',    'type' => 'wysiwyg', 'media_upload' => 0, 'toolbar' => 'basic', 'tabs' => 'visual' ),
            array( 'key' => 'field_mz_f_proc_steps', 'label' => 'Βήματα (μία ανά γραμμή)',
                   'name' => 'home_process_steps',
                   'type' => 'textarea',
                   'rows' => 8,
                   'instructions' => 'Φόρμα: Αριθμός | Τίτλος | Περιγραφή. Μία γραμμή ανά βήμα.' ),

            array( 'key' => 'field_mz_f_tab_law',    'label' => 'Δικηγόρος',      'type' => 'tab' ),
            array( 'key' => 'field_mz_f_law_eb',     'label' => 'Eyebrow',           'name' => 'home_lawyer_eyebrow', 'type' => 'text' ),
            array( 'key' => 'field_mz_f_law_lead',   'label' => 'Lead κείμενο',      'name' => 'home_lawyer_lead',    'type' => 'wysiwyg', 'media_upload' => 0, 'toolbar' => 'basic', 'tabs' => 'visual' ),
            array( 'key' => 'field_mz_f_law_meta',   'label' => 'Meta στοιχεία (μία ανά γραμμή)',
                   'name' => 'home_lawyer_meta',
                   'type' => 'textarea',
                   'rows' => 5,
                   'instructions' => 'Φόρμα: Ετικέτα | Τιμή. Έως 3 γραμμές.' ),

            array( 'key' => 'field_mz_f_tab_blog',   'label' => 'Άρθρα',          'type' => 'tab' ),
            array( 'key' => 'field_mz_f_blog_eb',    'label' => 'Eyebrow',           'name' => 'home_blog_eyebrow',  'type' => 'text' ),
            array( 'key' => 'field_mz_f_blog_title', 'label' => 'Τίτλος',            'name' => 'home_blog_title',    'type' => 'textarea', 'rows' => 2 ),

            array( 'key' => 'field_mz_f_tab_cta',    'label' => 'CTA',            'type' => 'tab' ),
            array( 'key' => 'field_mz_f_cta_eb',     'label' => 'Eyebrow',           'name' => 'home_cta_eyebrow',   'type' => 'text' ),
            array( 'key' => 'field_mz_f_cta_title',  'label' => 'Τίτλος',            'name' => 'home_cta_title',     'type' => 'textarea', 'rows' => 2 ),
            array( 'key' => 'field_mz_f_cta_text',   'label' => 'Κείμενο',           'name' => 'home_cta_text',      'type' => 'wysiwyg', 'media_upload' => 0, 'toolbar' => 'basic', 'tabs' => 'visual' ),

            array( 'key' => 'field_mz_f_tab_foot',   'label' => 'Footer',         'type' => 'tab' ),
            array( 'key' => 'field_mz_f_foot_about', 'label' => 'Footer about text', 'name' => 'footer_about_text',  'type' => 'wysiwyg', 'media_upload' => 0, 'toolbar' => 'basic', 'tabs' => 'visual' ),
            array( 'key' => 'field_mz_f_foot_legal', 'label' => 'Footer legal (δεξιά)', 'name' => 'footer_legal_right', 'type' => 'text' ),
        ),
        'location' => array( array( array( 'param' => 'page_type', 'operator' => '==', 'value' => 'front_page' ) ) ),
        'position' => 'normal',
    ) );
} );

/* =====================================================================
 * Helpers — return data from CPT (with fallback to hardcoded if empty).
 * =================================================================== */
function mourtzilaki_get_hero_slides() {
    $img_base = get_template_directory_uri() . '/assets/img/hero/';
    $posts = get_posts( array(
        'post_type'      => 'mz_hero',
        'posts_per_page' => -1,
        'orderby'        => 'menu_order date',
        'order'          => 'ASC',
    ) );
    if ( ! empty( $posts ) ) {
        $out = array();
        foreach ( $posts as $p ) {
            $img = function_exists( 'get_field' ) ? get_field( 'image', $p->ID ) : null;
            $out[] = array(
                'eyebrow'   => function_exists( 'get_field' ) ? (string) get_field( 'eyebrow',  $p->ID ) : '',
                'title'     => function_exists( 'get_field' ) ? (string) get_field( 'headline', $p->ID ) : get_the_title( $p ),
                'lead'      => function_exists( 'get_field' ) ? (string) get_field( 'lead',     $p->ID ) : '',
                'img'       => is_array( $img ) ? $img['url'] : ( $img ?: $img_base . '01.jpg' ),
                'cta_label' => function_exists( 'get_field' ) ? (string) get_field( 'cta_label', $p->ID ) : '',
                'cta_url'   => function_exists( 'get_field' ) ? (string) get_field( 'cta_url',   $p->ID ) : '',
            );
        }
        return $out;
    }
    // Fallback (διατηρείται μόνο για πριν την migration).
    return array();
}

function mourtzilaki_get_member( $idx = 0 ) {
    $posts = get_posts( array(
        'post_type'      => 'mz_member',
        'posts_per_page' => -1,
        'orderby'        => 'menu_order date',
        'order'          => 'ASC',
    ) );
    if ( empty( $posts ) ) { return null; }
    if ( ! isset( $posts[ $idx ] ) ) { return null; }
    $p = $posts[ $idx ];
    $photo = function_exists( 'get_field' ) ? get_field( 'photo', $p->ID ) : null;
    return array(
        'id'        => $p->ID,
        'name'      => get_the_title( $p ),
        'role'      => function_exists( 'get_field' ) ? (string) get_field( 'role',      $p->ID ) : '',
        'short_bio' => function_exists( 'get_field' ) ? (string) get_field( 'short_bio', $p->ID ) : '',
        'email'     => function_exists( 'get_field' ) ? (string) get_field( 'email',     $p->ID ) : '',
        'phone'     => function_exists( 'get_field' ) ? (string) get_field( 'phone',     $p->ID ) : '',
        'photo'     => is_array( $photo ) ? $photo['url'] : ( $photo ?: get_template_directory_uri() . '/assets/img/team/elena.jpg' ),
    );
}

function mourtzilaki_get_contact_info() {
    static $cache = null;
    if ( null !== $cache ) { return $cache; }

    $defaults = array(
        'address' => "Λεωφ. Παράδειγμα 12\n10678, Αθήνα",
        'phone'   => '+30 210 000 0000',
        'email'   => 'info@mourtzilakilaw.gr',
        'hours'   => "Δευτέρα — Παρασκευή\n09:00 — 19:00\nΣάββατο: κατόπιν ραντεβού",
    );

    // Resolution order: Site Settings → Contact page ACF → defaults.
    $from_contact = array();
    $contact = get_page_by_path( 'contact' );
    if ( $contact && function_exists( 'get_field' ) ) {
        $from_contact = array(
            'address' => trim( (string) get_field( 'contact_address', $contact->ID ) ),
            'phone'   => trim( (string) get_field( 'contact_phone',   $contact->ID ) ),
            'email'   => trim( (string) get_field( 'contact_email',   $contact->ID ) ),
            'hours'   => trim( (string) get_field( 'contact_hours',   $contact->ID ) ),
        );
    }

    $cache = array(
        'address' => mourtzilaki_setting( 'contact_address', $from_contact['address'] ?? '' ) ?: $defaults['address'],
        'phone'   => mourtzilaki_setting( 'contact_phone',   $from_contact['phone']   ?? '' ) ?: $defaults['phone'],
        'email'   => mourtzilaki_setting( 'contact_email',   $from_contact['email']   ?? '' ) ?: $defaults['email'],
        'hours'   => mourtzilaki_setting( 'contact_hours',   $from_contact['hours']   ?? '' ) ?: $defaults['hours'],
    );
    return $cache;
}

/**
 * BLOCK-context safe HTML for editor fields (testimonial quotes, prose blocks).
 * Wraps plain-text in paragraphs; preserves WYSIWYG HTML. Allows block + inline tags.
 */
function mourtzilaki_kses_quote( $text ) {
    $text = (string) $text;
    if ( '' === trim( $text ) ) { return ''; }
    if ( false === strpos( $text, '<p' ) && false === strpos( $text, '<br' ) ) {
        $text = wpautop( $text );
    }
    return wp_kses( $text, array(
        'p'          => array(),
        'br'         => array(),
        'strong'     => array(),
        'em'         => array(),
        'b'          => array(),
        'i'          => array(),
        'u'          => array(),
        'a'          => array( 'href' => true, 'title' => true, 'rel' => true, 'target' => true ),
        'ul'         => array(),
        'ol'         => array(),
        'li'         => array(),
        'blockquote' => array(),
        'h2'         => array(),
        'h3'         => array(),
        'h4'         => array(),
    ) );
}

/**
 * INLINE-context safe HTML for editor fields used inside an existing block
 * tag (e.g. `<p class="lead">{value}</p>`). Strips outer paragraph wrappers
 * and converts paragraph breaks to <br><br>. Inline tags only.
 */
function mourtzilaki_field_inline( $text ) {
    $text = (string) $text;
    if ( '' === trim( $text ) ) { return ''; }
    // Plain text → escape + preserve newlines as <br>.
    if ( false === strpos( $text, '<' ) ) {
        return nl2br( esc_html( $text ) );
    }
    // HTML from WYSIWYG: paragraph break → double <br>; strip wrapping <p>.
    $text = preg_replace( '#</p>\s*<p[^>]*>#i', '<br><br>', $text );
    $text = preg_replace( '#^\s*<p[^>]*>#i', '', $text );
    $text = preg_replace( '#</p>\s*$#i', '', $text );
    return wp_kses( $text, array(
        'br'     => array(),
        'strong' => array(),
        'em'     => array(),
        'b'      => array(),
        'i'      => array(),
        'u'      => array(),
        'a'      => array( 'href' => true, 'title' => true, 'rel' => true, 'target' => true ),
    ) );
}

/**
 * Parse a textarea where each non-empty line is "col1 | col2 | col3 ...".
 * Returns array of arrays (each row's columns).
 */
function mourtzilaki_parse_lines( $text, $delimiter = '|' ) {
    $out = array();
    if ( empty( $text ) ) { return $out; }
    foreach ( preg_split( "/\r\n|\r|\n/", trim( (string) $text ) ) as $line ) {
        $line = trim( $line );
        if ( '' === $line ) { continue; }
        $parts = array_map( 'trim', explode( $delimiter, $line ) );
        $out[] = $parts;
    }
    return $out;
}

/**
 * Return testimonials, optionally limited.
 */
/**
 * Default fallback image for articles without a featured image.
 */
function mourtzilaki_default_article_image() {
    return apply_filters(
        'mourtzilaki_default_article_image',
        get_template_directory_uri() . '/assets/img/posts/default.jpg'
    );
}

/**
 * Returns the post's featured image URL, or the default fallback.
 */
function mourtzilaki_post_image_url( $post_id = null, $size = 'large' ) {
    if ( null === $post_id ) { $post_id = get_the_ID(); }
    $url = get_the_post_thumbnail_url( $post_id, $size );
    return $url ? $url : mourtzilaki_default_article_image();
}

function mourtzilaki_get_testimonials( $limit = -1 ) {
    $posts = get_posts( array(
        'post_type'      => 'mz_testimonial',
        'posts_per_page' => $limit,
        'orderby'        => 'menu_order date',
        'order'          => 'ASC',
    ) );
    $out = array();
    foreach ( $posts as $p ) {
        $out[] = array(
            'name'  => get_the_title( $p ),
            'role'  => function_exists( 'get_field' ) ? (string) get_field( 'role',  $p->ID ) : '',
            'quote' => function_exists( 'get_field' ) ? (string) get_field( 'quote', $p->ID ) : '',
        );
    }
    return $out;
}

function mourtzilaki_page_hero( $page_id = null ) {
    if ( null === $page_id ) { $page_id = get_queried_object_id(); }
    if ( ! $page_id ) { return array(); }
    if ( ! function_exists( 'get_field' ) ) { return array(); }
    return array(
        'eyebrow' => trim( (string) get_field( 'page_eyebrow',     $page_id ) ),
        'title'   => trim( (string) get_field( 'page_hero_title',  $page_id ) ),
        'lead'    => trim( (string) get_field( 'page_hero_lead',   $page_id ) ),
    );
}

/* =====================================================================
 * One-time migration: hardcoded data → ACF posts + media library.
 * =================================================================== */
add_action( 'init', 'mourtzilaki_migrate_to_cpts', 30 );

function mourtzilaki_migrate_to_cpts() {
    if ( get_option( 'mourtzilaki_cpt_migrated' ) === '1.0' ) { return; }
    if ( ! function_exists( 'acf_add_local_field_group' ) ) { return; }
    if ( ! is_admin() && ! ( defined( 'WP_CLI' ) && WP_CLI ) ) { return; }
    if ( ! current_user_can( 'manage_options' ) && ! ( defined( 'WP_CLI' ) && WP_CLI ) ) { return; }

    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';

    $theme_dir = get_template_directory();

    /* --- Services ------------------------------------------------- */
    if ( ! get_posts( array( 'post_type' => 'mz_service', 'numberposts' => 1, 'fields' => 'ids' ) ) ) {
        $services = array(
            array( 'Εμπορικό & Εταιρικό Δίκαιο',     'Ίδρυση εταιρειών, μετοχικές συμβάσεις, εξαγορές & συγχωνεύσεις, εταιρική διακυβέρνηση και καθημερινή νομική υποστήριξη επιχειρήσεων.' ),
            array( 'Αστικό & Ενοχικό Δίκαιο',         'Συμβάσεις, αδικοπραξίες, διεκδικήσεις απαιτήσεων, ευθύνη από συμβάσεις και εξωσυμβατικές σχέσεις.' ),
            array( 'Ακίνητα & Κτηματολόγιο',           'Έλεγχοι τίτλων, μεταβιβάσεις, μισθώσεις, διορθώσεις κτηματολογικών εγγραφών και επίλυση διαφορών.' ),
            array( 'Οικογενειακό & Κληρονομικό',       'Διαζύγια, διατροφές, επιμέλεια τέκνων, διαθήκες, αποδοχές κληρονομιάς και κληρονομικές διαφορές.' ),
            array( 'Εργατικό Δίκαιο',                  'Συμβάσεις εργασίας, απολύσεις, αποζημιώσεις, mobbing, εκπροσώπηση εργοδοτών και εργαζομένων.' ),
            array( 'Ποινικό Δίκαιο',                   'Υπεράσπιση κατηγορουμένων και υποστήριξη παθόντων σε όλα τα στάδια της ποινικής διαδικασίας.' ),
            array( 'Διοικητικό & Φορολογικό',          'Προσφυγές κατά διοικητικών πράξεων, φορολογικές διαφορές, ΣΕΠΕ, δημόσιες συμβάσεις.' ),
            array( 'Τραπεζικό Δίκαιο',                 'Δάνεια, εγγυήσεις, υπερχρεωμένα νοικοκυριά, εξυγίανση επιχειρήσεων και ρυθμίσεις οφειλών.' ),
        );
        foreach ( $services as $i => $s ) {
            $id = wp_insert_post( array(
                'post_type'   => 'mz_service',
                'post_status' => 'publish',
                'post_title'  => $s[0],
                'menu_order'  => $i + 1,
            ) );
            if ( $id && function_exists( 'update_field' ) ) {
                update_field( 'description', $s[1], $id );
            }
        }
    }

    /* --- Team (Elena) -------------------------------------------- */
    if ( ! get_posts( array( 'post_type' => 'mz_member', 'numberposts' => 1, 'fields' => 'ids' ) ) ) {
        $mid = wp_insert_post( array(
            'post_type'   => 'mz_member',
            'post_status' => 'publish',
            'post_title'  => 'Έλενα Μουρτζιλάκη',
            'menu_order'  => 1,
        ) );
        if ( $mid && function_exists( 'update_field' ) ) {
            update_field( 'role',      'Δικηγόρος · Ιδρύτρια', $mid );
            update_field( 'short_bio', 'Δικηγόρος παρ\' Αρείω Πάγω, με εξειδίκευση στο αστικό και εμπορικό δίκαιο. Εκπροσωπεί ιδιώτες και επιχειρήσεις σε όλους τους βαθμούς δικαιοδοσίας.', $mid );
            $aid = mourtzilaki_attach_image( $theme_dir . '/assets/img/team/elena.jpg' );
            if ( $aid ) { update_field( 'photo', $aid, $mid ); }
        }
    }

    /* --- Hero slides --------------------------------------------- */
    if ( ! get_posts( array( 'post_type' => 'mz_hero', 'numberposts' => 1, 'fields' => 'ids' ) ) ) {
        $slides = array(
            array( '01.jpg', 'Δικηγορικό γραφείο · Αθήνα',    'Νομική υποστήριξη με συνέπεια και διακριτικότητα.', 'Σας εκπροσωπούμε σε όλο το φάσμα του αστικού, εμπορικού και ποινικού δικαίου, με σαφή στρατηγική και εξατομικευμένο χειρισμό κάθε υπόθεσης.' ),
            array( '02.jpg', 'Στρατηγική νομική σκέψη',        'Πίσω από κάθε απόφαση, μια καθαρή στρατηγική.',     'Δεν προτείνουμε ενέργειες πριν αναλύσουμε σε βάθος. Σας παρουσιάζουμε ρεαλιστικά σενάρια και διαφανές κόστος.' ),
            array( '03.jpg', 'Από το 2005 δίπλα σας',          'Εμπειρία στα δικαστήρια και στις διαπραγματεύσεις.', 'Από τα ειρηνοδικεία μέχρι τον Άρειο Πάγο. Από τη συμβατική διαπραγμάτευση μέχρι τη δικαστική εκπροσώπηση.' ),
            array( '04.jpg', 'Ολιστική προσέγγιση',             'Καλύπτουμε όλους τους κρίσιμους τομείς δικαίου.',    'Αστικό, εμπορικό, εργατικό, οικογενειακό, ακίνητα, ποινικό. Σε ένα γραφείο, μία ομάδα, μία στρατηγική.' ),
            array( '05.jpg', 'Συνέπεια & διακριτικότητα',       'Κάθε υπόθεση αντιμετωπίζεται με απόλυτη εμπιστευτικότητα.', 'Η διακριτικότητα δεν είναι policy· είναι ο τρόπος που λειτουργούμε σε κάθε επικοινωνία και κάθε φάκελο.' ),
        );
        foreach ( $slides as $i => $s ) {
            $hid = wp_insert_post( array(
                'post_type'   => 'mz_hero',
                'post_status' => 'publish',
                'post_title'  => $s[2],
                'menu_order'  => $i + 1,
            ) );
            if ( $hid && function_exists( 'update_field' ) ) {
                update_field( 'eyebrow',  $s[1], $hid );
                update_field( 'headline', $s[2], $hid );
                update_field( 'lead',     $s[3], $hid );
                $aid = mourtzilaki_attach_image( $theme_dir . '/assets/img/hero/' . $s[0] );
                if ( $aid ) { update_field( 'image', $aid, $hid ); }
            }
        }
    }

    /* --- Contact page defaults ----------------------------------- */
    $contact = get_page_by_path( 'contact' );
    if ( $contact && function_exists( 'update_field' ) ) {
        if ( '' === (string) get_field( 'contact_address', $contact->ID ) ) {
            update_field( 'contact_address', "Λεωφ. Παράδειγμα 12\n10678, Αθήνα", $contact->ID );
            update_field( 'contact_phone',   '+30 210 000 0000',                  $contact->ID );
            update_field( 'contact_email',   'info@mourtzilakilaw.gr',            $contact->ID );
            update_field( 'contact_hours',   "Δευτέρα — Παρασκευή\n09:00 — 19:00\nΣάββατο: κατόπιν ραντεβού", $contact->ID );
        }
    }

    update_option( 'mourtzilaki_cpt_migrated', '1.0' );
}

/**
 * Δίνει long_description σε κάθε service τομέα — μία φορά αν δεν έχει.
 */
add_action( 'init', 'mourtzilaki_seed_home_defaults', 32 );
function mourtzilaki_seed_home_defaults() {
    if ( get_option( 'mourtzilaki_home_defaults' ) === '1.1' ) { return; }
    if ( ! function_exists( 'update_field' ) ) { return; }

    $front_id = (int) get_option( 'page_on_front' );
    if ( ! $front_id ) { return; }

    $defaults = array(
        'home_trust_strip'    => "20+ | Έτη εμπειρίας\n800+ | Υποθέσεις\n8 | Τομείς δικαίου\nΔΣΑ | Μέλος Συλλόγου\n24h | Απόκριση",
        'home_about_eyebrow'  => 'Φιλοσοφία',
        'home_about_title'    => 'Δίκαιο που σέβεται τον άνθρωπο και την επιχείρηση.',
        'home_philosophy_quote' => 'Πίσω από κάθε υπόθεση υπάρχει ένας άνθρωπος, μια οικογένεια ή μια επιχείρηση. Η δουλειά μας ξεκινάει από εκεί.',
        'home_philosophy_year'  => 'Est. 2005',
        'home_about_text'     => "Το γραφείο ιδρύθηκε με στόχο να προσφέρει νομικές υπηρεσίες υψηλού επιπέδου, με σαφή στρατηγική και βαθιά γνώση των αντικειμένων που χειρίζεται.\n\nΣυνδυάζουμε την κλασική νομική παιδεία με τη σύγχρονη ψηφιακή εργασία, ώστε κάθε υπόθεση να εξελίσσεται με διαφάνεια, ταχύτητα και προβλεψιμότητα.",
        'home_svc_eyebrow'    => 'Τομείς εξειδίκευσης',
        'home_svc_title'      => 'Καλύπτουμε τους κρίσιμους τομείς του δικαίου.',
        'home_svc_lead'       => 'Επιλέξαμε να εμβαθύνουμε στους τομείς όπου μπορούμε να προσφέρουμε ουσιαστική υπεροχή για τους πελάτες μας.',
        'home_process_eyebrow'=> 'Διαδικασία',
        'home_process_title'  => 'Πώς δουλεύουμε σε κάθε υπόθεση.',
        'home_process_lead'   => 'Καθαρή μεθοδολογία τεσσάρων βημάτων — από την πρώτη συνάντηση μέχρι την επίλυση.',
        'home_process_steps'  => "01 | Αρχική συνάντηση | Συζητάμε αναλυτικά την υπόθεση, εντοπίζουμε κρίσιμα σημεία, απαντάμε σε ό,τι σας απασχολεί.\n02 | Νομική ανάλυση | Μελετάμε νομοθεσία, νομολογία και τα συγκεκριμένα δεδομένα πριν προτείνουμε στρατηγική.\n03 | Σχέδιο ενεργειών | Παρουσιάζουμε καθαρά τις επιλογές, με πλεονεκτήματα, κινδύνους και αναμενόμενο κόστος.\n04 | Εκτέλεση & ενημέρωση | Αναλαμβάνουμε εξ ολοκλήρου τη διαχείριση και σας ενημερώνουμε σε κάθε εξέλιξη.",
        'home_lawyer_eyebrow' => 'Γνωρίστε τη δικηγόρο',
        'home_lawyer_lead'    => 'Δικηγόρος παρ’ Αρείω Πάγω, με 20 χρόνια εμπειρία στο αστικό, εμπορικό και ποινικό δίκαιο. Εκπροσωπεί ιδιώτες και επιχειρήσεις σε όλους τους βαθμούς δικαιοδοσίας.',
        'home_lawyer_meta'    => "Σύλλογος | ΔΣ Αθηνών\nΕξειδίκευση | LL.M. Εμπορικό Δίκαιο\nΓλώσσες | EL · EN · FR",
        'home_blog_eyebrow'   => 'Άρθρα',
        'home_blog_title'     => 'Πρόσφατες νομικές αναλύσεις.',
        'home_cta_eyebrow'    => 'Ραντεβού',
        'home_cta_title'      => 'Ας μιλήσουμε για την υπόθεσή σας.',
        'home_cta_text'       => 'Επικοινωνήστε μαζί μας για μια αρχική, εμπιστευτική συζήτηση. Σας απαντάμε εντός 24 ωρών εργάσιμων ημερών.',
        'footer_about_text'   => 'Παρέχουμε ολοκληρωμένη νομική υποστήριξη με συνέπεια, διακριτικότητα και βαθιά γνώση του ελληνικού δικαίου.',
        'footer_legal_right'  => 'Μέλος του Δικηγορικού Συλλόγου Αθηνών',
    );
    foreach ( $defaults as $key => $val ) {
        if ( '' === (string) get_field( $key, $front_id ) ) {
            update_field( $key, $val, $front_id );
        }
    }

    /* About page defaults */
    $about = get_page_by_path( 'about' );
    if ( $about ) {
        $about_defaults = array(
            'about_story_title'   => 'Η ιστορία μας',
            'about_story_text'    => 'Το γραφείο ιδρύθηκε με στόχο να προσφέρει ολοκληρωμένη νομική υποστήριξη σε ιδιώτες και επιχειρήσεις, με βαθιά εξειδίκευση και έντονη προσωπική παρουσία. Η μακρά εμπειρία μας στα δικαστήρια και η συμμετοχή σε υψηλού ρίσκου υποθέσεις έχουν διαμορφώσει τον τρόπο που εργαζόμαστε σήμερα.',
            'about_mission_title' => 'Η αποστολή μας',
            'about_mission_text'  => 'Να μετατρέπουμε σύνθετες νομικές καταστάσεις σε καθαρές αποφάσεις. Στόχος μας είναι ο πελάτης να αντιλαμβάνεται ανά πάσα στιγμή τη θέση του και τις πραγματικές του επιλογές, χωρίς ορολογίες και αοριστίες.',
            'about_values'        => "Συνέπεια | Δουλεύουμε με προσήλωση σε προθεσμίες, λεπτομέρεια και ποιότητα γραπτού λόγου. Η συνέπεια είναι η πρώτη μας υποχρέωση απέναντι στον πελάτη.\nΔιακριτικότητα | Κάθε υπόθεση αντιμετωπίζεται με απόλυτη εμπιστευτικότητα. Η διακριτικότητα δεν είναι policy, είναι η νοοτροπία μας.\nΚαθαρή στρατηγική | Πριν προτείνουμε λύση, σχεδιάζουμε τη συνολική πορεία. Ξέρουμε πού οδηγεί κάθε επιλογή και πόσο κοστίζει σε χρόνο, χρήμα και ψυχική ηρεμία.\nΑνθρωπιά | Η νομική γνώση δεν αρκεί. Καταλαβαίνουμε ότι πίσω από κάθε υπόθεση υπάρχει ένας άνθρωπος, μια οικογένεια ή μια επιχείρηση που χρειάζεται στήριξη.",
        );
        foreach ( $about_defaults as $key => $val ) {
            if ( '' === (string) get_field( $key, $about->ID ) ) {
                update_field( $key, $val, $about->ID );
            }
        }
    }

    /* Seed first testimonial */
    if ( ! get_posts( array( 'post_type' => 'mz_testimonial', 'numberposts' => 1, 'fields' => 'ids' ) ) ) {
        $tid = wp_insert_post( array(
            'post_type'   => 'mz_testimonial',
            'post_status' => 'publish',
            'post_title'  => 'Δ. Παπαδόπουλος',
            'menu_order'  => 1,
        ) );
        if ( $tid ) {
            update_field( 'role',  'Διευθύνων Σύμβουλος, εμπορική εταιρεία', $tid );
            update_field( 'quote', 'Είχαμε μια σύνθετη εταιρική αναδιοργάνωση και χρειαζόμασταν δικηγόρο που θα κατανοούσε όχι μόνο το νομικό πλαίσιο αλλά και την επιχείρησή μας. Η Έλενα έδωσε ακριβώς αυτό — ξεκάθαρες απαντήσεις, ρεαλιστικά χρονοδιαγράμματα και μηδέν εκπλήξεις.', $tid );
        }
    }

    update_option( 'mourtzilaki_home_defaults', '1.1' );
}

add_action( 'init', 'mourtzilaki_seed_service_content', 35 );

add_action( 'init', 'mourtzilaki_seed_articles_v2', 38 );
function mourtzilaki_seed_articles_v2() {
    if ( get_option( 'mourtzilaki_articles_v2' ) === '1.0' ) { return; }
    if ( ! function_exists( 'update_field' ) ) { return; }

    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';

    $theme = get_template_directory();

    // Helper to find service ID by title fragment.
    $svc_id = function ( $needle ) {
        foreach ( get_posts( array( 'post_type' => 'mz_service', 'numberposts' => -1 ) ) as $s ) {
            if ( false !== mb_stripos( get_the_title( $s ), $needle ) ) { return $s->ID; }
        }
        return 0;
    };

    $articles = array(
        array(
            'title'    => 'Νέες ρυθμίσεις στο εργατικό δίκαιο: τι αλλάζει στις απολύσεις',
            'subtitle' => 'Πώς αλλάζει το πλαίσιο των ομαδικών και των μεμονωμένων απολύσεων μετά τις πρόσφατες νομοθετικές παρεμβάσεις — και τι σημαίνει στην πράξη για επιχειρήσεις και εργαζόμενους.',
            'image'    => 'posts/01.jpg',
            'svc'      => 'Εργατικό',
            'pull'     => 'Σχεδόν το 70% των διαφορών μετά από απόλυση χάνονται όχι στην ουσία, αλλά σε λάθη διαδικασίας — προθεσμίες, ειδοποιήσεις και τύπος.',
            'takeaways'=> "Η προειδοποίηση δεν είναι τυπικότητα — επηρεάζει το ύψος της αποζημίωσης.\nΗ απόλυση χωρίς σπουδαίο λόγο σε ορισμένες περιπτώσεις είναι άκυρη.\nΟι προθεσμίες προσφυγής είναι αποκλειστικές: 3 μήνες από την απόλυση.\nΗ καταβολή αποζημίωσης δεν αποκλείει διεκδίκηση δεδουλευμένων.",
            'content'  => "<p>Η εργατική νομοθεσία στην Ελλάδα έχει υποστεί αρκετές τροποποιήσεις τα τελευταία χρόνια, με στόχο τόσο τον εκσυγχρονισμό όσο και τη συμμόρφωση με ευρωπαϊκές οδηγίες. Στο παρόν άρθρο εστιάζουμε στις αλλαγές γύρω από τις απολύσεις — έναν από τους πιο ευαίσθητους τομείς της εργατικής σχέσης.</p>\n\n<h2>Μεμονωμένη απόλυση: τα κρίσιμα σημεία</h2>\n<p>Η απόλυση εργαζομένου με σύμβαση αορίστου χρόνου εξακολουθεί να απαιτεί τήρηση συγκεκριμένης διαδικασίας. Το πιο σημαντικό σημείο που συχνά παραβλέπεται είναι η τήρηση του εγγράφου τύπου: η απόλυση πρέπει να γίνεται γραπτώς και να καταβάλλεται ταυτόχρονα η νόμιμη αποζημίωση, αλλιώς θεωρείται άκυρη.</p>\n<p>Επίσης, η εκ των προτέρων προειδοποίηση μειώνει το ύψος της αποζημίωσης κατά 50% — επομένως αξίζει να εξεταστεί ως επιλογή, ειδικά όταν η σχέση εργασίας έχει μεγάλη διάρκεια.</p>\n\n<h2>Ομαδικές απολύσεις</h2>\n<p>Όταν η απόλυση αφορά πολλούς εργαζόμενους ταυτόχρονα, ενεργοποιείται το πλαίσιο των ομαδικών απολύσεων (πάνω από 6 άτομα/μήνα σε επιχειρήσεις 20-150 εργαζομένων, ή 5% σε μεγαλύτερες). Εδώ απαιτείται διαβούλευση με τους εργαζόμενους και ενημέρωση της Υπουργικής Αρχής.</p>\n<ul><li>Διάρκεια διαβούλευσης: τουλάχιστον 30 ημέρες</li><li>Έγκριση από την αρμόδια αρχή πριν την υλοποίηση</li><li>Πρωτόκολλο τήρησης για κάθε εργαζόμενο</li></ul>\n\n<h2>Τι πρέπει να κάνετε αν απολυθήκατε</h2>\n<p>Η αντίδραση πρέπει να είναι άμεση. Η προθεσμία για να προσφύγετε στο δικαστήριο διεκδικώντας την ακυρότητα της απόλυσης είναι τρίμηνη και αποκλειστική. Αυτό σημαίνει ότι, αν παρέλθει, χάνεται το δικαίωμα ανεξάρτητα από τη βασιμότητα της υπόθεσης.</p>\n<blockquote>Πριν υπογράψετε οτιδήποτε στο πλαίσιο της απόλυσης, ζητήστε να συμβουλευτείτε δικηγόρο. Μια πρόχειρη υπογραφή μπορεί να σας στερήσει δικαιώματα.</blockquote>\n<p>Η αποζημίωση δεν είναι το μοναδικό που μπορείτε να διεκδικήσετε. Παράλληλα μπορούν να ζητηθούν δεδουλευμένες αποδοχές, υπερωρίες, αποζημίωση μη ληφθείσας άδειας και — σε ειδικές περιπτώσεις — αποζημίωση ηθικής βλάβης.</p>\n",
        ),
        array(
            'title'    => 'Κτηματολόγιο: διορθώσεις πρώτων εγγραφών — ο πρακτικός οδηγός',
            'subtitle' => 'Η διαδικασία διόρθωσης πρώτων εγγραφών είναι από τις πιο τεχνικές υποθέσεις του δικαίου ακινήτων. Πώς αποφεύγετε τα συνηθέστερα λάθη.',
            'image'    => 'posts/02.jpg',
            'svc'      => 'Ακίνητα',
            'pull'     => 'Η διόρθωση πρώτης εγγραφής δεν είναι «τεχνικό λάθος» — είναι δικαστική υπόθεση που απαιτεί απόδειξη ιδιοκτησίας.',
            'takeaways'=> "Η προθεσμία διόρθωσης χωρίς δικαστήριο είναι 7 έτη από την οριστικοποίηση των πρώτων εγγραφών.\nΟ έλεγχος τίτλων στο Υποθηκοφυλακείο πρέπει να γίνει για 20 χρόνια πριν.\nΧωρίς τοπογραφικό σύμφωνα με το ΕΓΣΑ '87 δεν προχωράει αίτηση.\nΟι κληρονόμοι μπορούν να διορθώσουν εγγραφή και μετά τη λήξη της προθεσμίας.",
            'content'  => "<p>Με την ολοκλήρωση των πρώτων εγγραφών στο Εθνικό Κτηματολόγιο, οι ιδιοκτησίες αποκτούν επίσημη και τεκμαρτή κατάσταση. Όμως, λάθη και παραλείψεις είναι αναπόφευκτα — και η διόρθωσή τους ακολουθεί συγκεκριμένη διαδικασία.</p>\n\n<h2>Τι σημαίνει «πρώτη εγγραφή»</h2>\n<p>Είναι η αρχική καταχώριση που κάνει το Κτηματολόγιο μετά τη συλλογή των δηλώσεων από τους ιδιοκτήτες. Αν στην πρώτη αυτή καταχώριση υπάρχει κάποιο σφάλμα — λανθασμένος ιδιοκτήτης, λάθος εμβαδόν, παραλειπόμενο δικαίωμα — μιλάμε για διόρθωση πρώτης εγγραφής.</p>\n\n<h2>Δύο διαδρομές: εξωδικαστική και δικαστική</h2>\n<p>Πριν περάσει η αποκλειστική προθεσμία (7 έτη από την έναρξη ισχύος του Κτηματολογίου στην περιοχή σας), η διόρθωση μπορεί να γίνει εξωδικαστικά μέσω αίτησης στο Κτηματολογικό Γραφείο. Μετά την προθεσμία, η μόνη οδός είναι η δικαστική με αγωγή ενώπιον του αρμόδιου Πρωτοδικείου.</p>\n<ul><li><strong>Εξωδικαστική:</strong> ταχύτερη, χαμηλότερο κόστος, αλλά απαιτεί συναίνεση όλων των εμπλεκόμενων</li><li><strong>Δικαστική:</strong> πιο σύνθετη, αλλά απαραίτητη όταν υπάρχει αντίδραση τρίτου ή αμφισβήτηση</li></ul>\n\n<h2>Τι έγγραφα χρειάζεστε</h2>\n<ol><li>Συμβόλαια ιδιοκτησίας (πλήρης σειρά τίτλων)</li><li>Πιστοποιητικά μεταγραφών για τα τελευταία 20 έτη</li><li>Τοπογραφικό διάγραμμα κατά ΕΓΣΑ '87</li><li>Πιστοποιητικά κληρονομικού δικαίου, εφόσον απαιτείται</li><li>ΕΝΦΙΑ ή πιστοποιητικά Δ.Ο.Υ. για τυχόν εκκρεμότητες</li></ol>\n\n<h2>Συνηθέστερα λάθη</h2>\n<p>Πάνω από το 60% των αιτήσεων διόρθωσης που απορρίπτονται έχουν τα ίδια προβλήματα: ελλιπή σειρά τίτλων, μη ενημερωμένο τοπογραφικό, ή απουσία αναγκαίας συναίνεσης τρίτων. Ένας προσεκτικός έλεγχος πριν την κατάθεση εξοικονομεί μήνες αναμονής.</p>\n",
        ),
        array(
            'title'    => 'Διαζύγιο και επιμέλεια τέκνων: νέες κατευθύνσεις από τη νομολογία',
            'subtitle' => 'Πώς έχει εξελιχθεί η νομολογία γύρω από τη συνεπιμέλεια και την επικοινωνία με τα παιδιά μετά τον νόμο 4800/2021.',
            'image'    => 'posts/03.jpg',
            'svc'      => 'Οικογενειακό',
            'pull'     => 'Η συνεπιμέλεια έπαψε να είναι «εξαίρεση» — τώρα είναι ο κανόνας, εκτός αν το συμφέρον του παιδιού επιβάλλει άλλη ρύθμιση.',
            'takeaways'=> "Με τον ν. 4800/2021 η συνεπιμέλεια καθιερώθηκε ως κανόνας.\nΗ επικοινωνία υπολογίζεται σε ώρες και έχει συγκεκριμένο πρόγραμμα.\nΟι γονείς πρέπει να επιλύουν διαφωνίες με βάση το συμφέρον του παιδιού.\nΗ απομάκρυνση παιδιού από τη συνήθη διαμονή του απαιτεί δικαστική απόφαση.",
            'content'  => "<p>Ο νόμος 4800/2021 αποτέλεσε σταθμό για το ελληνικό οικογενειακό δίκαιο. Η συνεπιμέλεια καθιερώθηκε επίσημα ως ο κανόνας μετά τη λύση του γάμου, ενώ προβλέφθηκε ένα συστηματοποιημένο πλαίσιο για την επικοινωνία.</p>\n\n<h2>Τι σημαίνει συνεπιμέλεια στην πράξη</h2>\n<p>Συνεπιμέλεια δεν σημαίνει ότι το παιδί διανυκτερεύει 50/50 στους δύο γονείς. Σημαίνει ότι και οι δύο γονείς λαμβάνουν από κοινού όλες τις σημαντικές αποφάσεις: εκπαίδευση, υγεία, θρησκεία, διεθνή ταξίδια.</p>\n<p>Η καθημερινή φροντίδα μπορεί να είναι κατά κύριο λόγο σε έναν γονέα, με τον άλλο να ασκεί την επικοινωνία.</p>\n\n<h2>Επικοινωνία: από «εύλογη» σε μετρήσιμη</h2>\n<p>Η νομολογία πλέον τείνει να ορίζει συγκεκριμένο αριθμό ωρών επικοινωνίας τον μήνα. Η «εύλογη» επικοινωνία αντικαθίσταται με συγκεκριμένα Σαββατοκύριακα, αργίες και διακοπές. Αυτό προστατεύει τόσο τον γονέα όσο και το παιδί από τη γκρίζα ζώνη της αμφισβήτησης.</p>\n<blockquote>Η εμπειρία δείχνει ότι όσο πιο λεπτομερές το πρόγραμμα επικοινωνίας στη συμφωνία ή στην απόφαση, τόσο λιγότερες οι μελλοντικές διαφορές.</blockquote>\n\n<h2>Πότε αποκλίνει η νομολογία από τον κανόνα της συνεπιμέλειας</h2>\n<ul><li>Σε περιπτώσεις τεκμηριωμένης ενδοοικογενειακής βίας</li><li>Όταν ο ένας γονέας απουσιάζει αποδεδειγμένα για μεγάλο χρονικό διάστημα</li><li>Σε ειδικές περιπτώσεις επιφυλάξεων ως προς την επιρροή ενός γονέα</li></ul>\n<p>Σε αυτές τις περιπτώσεις, η ανάθεση της επιμέλειας αποκλειστικά στον έναν γονέα δεν είναι αυτόματη — απαιτείται πλήρης τεκμηρίωση και η απόφαση λαμβάνεται με γνώμονα το συμφέρον του παιδιού.</p>\n",
        ),
        array(
            'title'    => 'Εξωδικαστικός μηχανισμός ρύθμισης οφειλών: οδηγός για επιχειρήσεις και ιδιώτες',
            'subtitle' => 'Πότε αξίζει να χρησιμοποιηθεί, ποιες είναι οι προϋποθέσεις και τι κρατάμε από τις πρώτες χιλιάδες υποθέσεις.',
            'image'    => 'posts/04.jpg',
            'svc'      => 'Τραπεζικό',
            'pull'     => 'Ο εξωδικαστικός είναι ίσως το πιο ευνοϊκό εργαλείο που έχει στη διάθεσή του ένας υπερχρεωμένος οφειλέτης σήμερα — αρκεί να το χρησιμοποιήσει σωστά.',
            'takeaways'=> "Καλύπτει οφειλές προς Δημόσιο, ΕΦΚΑ και τράπεζες σε ένα ενιαίο σχέδιο.\nΗ ρύθμιση μπορεί να φτάσει έως και τα 240 δόσεις για ιδιώτες.\nΑπαραίτητα η συνέπεια στις δόσεις — μία αστοχία ακυρώνει τη ρύθμιση.\nΗ διαδικασία είναι πλήρως ηλεκτρονική μέσω της πλατφόρμας της ΕΓΔΙΧ.",
            'content'  => "<p>Ο εξωδικαστικός μηχανισμός του ν. 4738/2020 αλλάζει ριζικά τον τρόπο που ένας οφειλέτης μπορεί να αντιμετωπίσει τις χρηματοοικονομικές του υποχρεώσεις. Πρόκειται για μια ενιαία διαδικασία που περιλαμβάνει Δημόσιο, ασφαλιστικά ταμεία και τράπεζες.</p>\n\n<h2>Ποιοι μπορούν να ενταχθούν</h2>\n<p>Ο μηχανισμός απευθύνεται σε φυσικά πρόσωπα και επιχειρήσεις που έχουν συσσωρευμένες ληξιπρόθεσμες οφειλές. Δεν απαιτείται να βρίσκεται κάποιος σε καθεστώς πτώχευσης — αρκεί να μην εξυπηρετεί τις υποχρεώσεις του.</p>\n<p>Συγκεκριμένες προϋποθέσεις:</p>\n<ul><li>Συνολικό ύψος οφειλών άνω των 10.000 ευρώ</li><li>Καθυστέρηση εξυπηρέτησης τουλάχιστον 90 ημερών</li><li>Φορολογικές δηλώσεις των τριών τελευταίων ετών υποβληθείσες</li></ul>\n\n<h2>Πώς λειτουργεί η διαδικασία</h2>\n<p>Όλα γίνονται μέσω της ηλεκτρονικής πλατφόρμας της Ειδικής Γραμματείας Διαχείρισης Ιδιωτικού Χρέους (ΕΓΔΙΧ). Ο οφειλέτης συμπληρώνει αναλυτικά τα οικονομικά του στοιχεία και η πλατφόρμα παράγει αυτόματα πρόταση ρύθμισης.</p>\n<p>Η πρόταση μπορεί να γίνει αποδεκτή ή να αντιπροταθεί. Σε περίπτωση αποδοχής, υπογράφεται σύμβαση και η ρύθμιση τίθεται σε ισχύ.</p>\n\n<h2>Πλεονεκτήματα έναντι παλαιότερων εργαλείων</h2>\n<blockquote>Σε σχέση με τον νόμο Κατσέλη, ο εξωδικαστικός είναι σαφώς πιο γρήγορος, καλύπτει περισσότερους πιστωτές και δεν απαιτεί δικαστική παρέμβαση.</blockquote>\n<ul><li>Ταυτόχρονη ρύθμιση όλων των οφειλών — όχι ξεχωριστά</li><li>Δυνατότητα κούρεμα κεφαλαίου στο Δημόσιο και τράπεζες</li><li>Παγώνουν τα μέτρα αναγκαστικής εκτέλεσης κατά τη διάρκεια της διαπραγμάτευσης</li></ul>\n\n<h2>Σημεία που χρειάζονται προσοχή</h2>\n<p>Παρά τα πλεονεκτήματα, ο μηχανισμός έχει και κάποια αυστηρά σημεία. Η αυτόματη ακύρωση σε περίπτωση καθυστέρησης δόσεων είναι το πιο σημαντικό. Ο οφειλέτης πρέπει να είναι σίγουρος ότι μπορεί να ανταποκριθεί στο πρόγραμμα — αλλιώς η ευκαιρία χάνεται οριστικά.</p>\n",
        ),
        array(
            'title'    => 'Πώς συντάσσεται μια διαθήκη που αντέχει στο δικαστήριο',
            'subtitle' => 'Από τους τύπους διαθήκης μέχρι τα συνηθέστερα λάθη — όλα όσα πρέπει να ξέρετε για να μη χαθεί η τελευταία σας βούληση.',
            'image'    => 'posts/05.jpg',
            'svc'      => 'Οικογενειακό',
            'pull'     => 'Πάνω από το ένα τρίτο των διαθηκών που προσβάλλονται δικαστικά κρίνονται άκυρες — όχι λόγω ψυχικής ανικανότητας, αλλά λόγω τυπικών λαθών.',
            'takeaways'=> "Υπάρχουν τρεις τύποι διαθήκης: ιδιόγραφη, δημόσια και μυστική.\nΗ ιδιόγραφη πρέπει να είναι γραμμένη ολόκληρη με το χέρι, χρονολογημένη και υπογεγραμμένη.\nΗ νόμιμη μοίρα προστατεύει συζύγους, γονείς και τέκνα — δεν μπορεί να αποκλειστεί.\nΗ δημόσια διαθήκη παρέχει τη μεγαλύτερη ασφάλεια και αντέχει καλύτερα σε προσβολή.",
            'content'  => "<p>Η διαθήκη είναι από τα πιο σοβαρά νομικά έγγραφα που μπορεί να συντάξει ένας άνθρωπος. Παρόλα αυτά, πολλοί την προσεγγίζουν με ελαφρότητα — με αποτέλεσμα η τελευταία τους βούληση να μην εκτελείται έτσι όπως την οραματίστηκαν.</p>\n\n<h2>Τύποι διαθήκης</h2>\n<p>Το ελληνικό δίκαιο αναγνωρίζει τρεις τύπους:</p>\n<ul><li><strong>Ιδιόγραφη:</strong> γράφεται ολόκληρη με το χέρι, χρονολογείται και υπογράφεται. Το πιο εύκολο, αλλά και το πιο επικίνδυνο σε προσβολή.</li><li><strong>Δημόσια:</strong> συντάσσεται ενώπιον συμβολαιογράφου και τριών μαρτύρων. Ο πιο ασφαλής τύπος.</li><li><strong>Μυστική:</strong> ο διαθέτης παραδίδει σφραγισμένη τη διαθήκη του στον συμβολαιογράφο. Σπάνια χρησιμοποιείται.</li></ul>\n\n<h2>Νόμιμη μοίρα: τι προστατεύεται οπωσδήποτε</h2>\n<p>Σύζυγος, γονείς και τέκνα του διαθέτη έχουν δικαίωμα στη λεγόμενη «νόμιμη μοίρα» — το μισό από αυτό που θα έπαιρναν χωρίς διαθήκη. Αυτό σημαίνει ότι η διαθήκη μπορεί να διαθέσει ελεύθερα μόνο το άλλο μισό.</p>\n<blockquote>Η προσπάθεια να αποκλειστεί παιδί ή σύζυγος από την κληρονομιά είναι σχεδόν πάντα νομικά αδύνατη — και τέτοιες διαθήκες προσβάλλονται με υψηλή πιθανότητα επιτυχίας.</blockquote>\n\n<h2>Τα 5 πιο συνηθισμένα λάθη</h2>\n<ol><li>Διαθήκη που πληκτρολογήθηκε σε υπολογιστή και απλώς υπογράφηκε (άκυρη)</li><li>Έλλειψη χρονολογίας ή ασαφής χρονολογία</li><li>Παραβίαση νόμιμης μοίρας χωρίς να αναφέρεται κάλυψη με χρηματικό ποσό</li><li>Αλληλοαναιρούμενες διατάξεις</li><li>Διαθήκη που δεν αναφέρει όλα τα περιουσιακά στοιχεία</li></ol>\n\n<h2>Τι πρέπει να γίνει μετά τον θάνατο</h2>\n<p>Η διαθήκη πρέπει να δημοσιευτεί στο αρμόδιο Ειρηνοδικείο εντός εύλογου χρόνου. Στη συνέχεια, οι κληρονόμοι μπορούν να αποδεχθούν ή να αποποιηθούν την κληρονομιά εντός 4 μηνών (12 μηνών αν διαμένουν στο εξωτερικό).</p>\n<p>Η αποδοχή χωρίς προηγούμενη μελέτη της οικονομικής κατάστασης του θανόντος μπορεί να αποδειχθεί ολέθρια — γι' αυτό η συμβουλή δικηγόρου πριν από οποιαδήποτε ενέργεια είναι κρίσιμη.</p>\n",
        ),
        array(
            'title'    => 'Σύσταση εταιρείας στην Ελλάδα: επιλογή νομικής μορφής το 2026',
            'subtitle' => 'Πώς επιλέγετε σωστά μεταξύ ΙΚΕ, ΑΕ, ΕΕ ή ΟΕ — ένας πρακτικός οδηγός με βάση τις πραγματικές ανάγκες της επιχείρησής σας.',
            'image'    => 'posts/06.jpg',
            'svc'      => 'Εμπορικό',
            'pull'     => 'Δεν υπάρχει «καλύτερη» νομική μορφή — υπάρχει η μορφή που ταιριάζει στο μέγεθος, στο πλήθος των εταίρων και στους κινδύνους της δραστηριότητάς σας.',
            'takeaways'=> "Η ΙΚΕ έχει γίνει η πιο δημοφιλής επιλογή για μικρομεσαίες επιχειρήσεις.\nΗ ΑΕ απαιτείται όταν θέλετε να αντλήσετε επενδυτικά κεφάλαια ή να εισαχθείτε σε χρηματιστήριο.\nΗ προσωπική ευθύνη διαφέρει δραματικά μεταξύ ΟΕ/ΕΕ και κεφαλαιουχικών εταιρειών.\nΟι φορολογικές διαφορές μεταξύ νομικών μορφών δεν είναι τόσο μεγάλες όσο πιστεύεται.",
            'content'  => "<p>Η επιλογή της νομικής μορφής μιας επιχείρησης είναι μία από τις πιο σημαντικές αποφάσεις του ιδρυτή. Επηρεάζει την ευθύνη, τη φορολογία, τη δυνατότητα εισδοχής νέων εταίρων και την εικόνα προς τρίτους.</p>\n\n<h2>ΙΚΕ — Η ευέλικτη λύση</h2>\n<p>Η Ιδιωτική Κεφαλαιουχική Εταιρεία (ΙΚΕ) είναι σήμερα η πιο δημοφιλής επιλογή για μικρομεσαίες επιχειρήσεις. Συνδυάζει την ασφάλεια της κεφαλαιουχικής εταιρείας (περιορισμένη ευθύνη) με ευελιξία στη διοίκηση.</p>\n<ul><li>Ελάχιστο κεφάλαιο: 1 ευρώ</li><li>Δυνατότητα εξωκεφαλαιακών εισφορών</li><li>Ευέλικτη διοίκηση από έναν ή περισσότερους διαχειριστές</li></ul>\n\n<h2>ΑΕ — Όταν στοχεύετε ψηλότερα</h2>\n<p>Η Ανώνυμη Εταιρεία (ΑΕ) είναι η ενδεδειγμένη μορφή για μεγαλύτερες επιχειρήσεις, ή για όσες σχεδιάζουν προσέλκυση επενδυτών, εισαγωγή σε χρηματιστήριο ή σύνθετες κεφαλαιακές κινήσεις.</p>\n<p>Απαιτεί ελάχιστο μετοχικό κεφάλαιο 25.000 ευρώ και πιο σύνθετη διοικητική δομή (Διοικητικό Συμβούλιο, Γενική Συνέλευση).</p>\n\n<h2>ΟΕ και ΕΕ — Προσοχή στην προσωπική ευθύνη</h2>\n<p>Η Ομόρρυθμη και Ετερόρρυθμη Εταιρεία είναι οι παραδοσιακές προσωπικές μορφές. Έχουν χαμηλό κόστος ίδρυσης αλλά μεγάλο ρίσκο: στην ΟΕ, οι εταίροι ευθύνονται με όλη τους την περιουσία.</p>\n<blockquote>Δεν συνιστούμε ΟΕ ή ΕΕ σε δραστηριότητες με αυξημένο επιχειρηματικό κίνδυνο — ένα μόνο ατυχές γεγονός μπορεί να καταστρέψει την προσωπική σας περιουσία.</blockquote>\n\n<h2>Ένας πρακτικός οδηγός επιλογής</h2>\n<ul><li><strong>Freelancer / σόλο επιχειρηματίας:</strong> Μονοπρόσωπη ΙΚΕ</li><li><strong>2-5 συνεταίροι, μικρομεσαία:</strong> ΙΚΕ ή ΕΠΕ</li><li><strong>Επιχείρηση με επενδυτές / σχέδια ανάπτυξης:</strong> ΑΕ</li><li><strong>Οικογενειακή επιχείρηση χαμηλού ρίσκου:</strong> ΟΕ μπορεί ακόμη να έχει νόημα</li></ul>\n\n<h2>Φορολογία και έξοδα</h2>\n<p>Πέρα από τη μορφή, μετράει η συνολική εικόνα: φόρος εισοδήματος επί κερδών (22% για όλες τις εταιρείες), παρακράτηση μερισμάτων (5%), εισφορές διοικούντων στον ΕΦΚΑ.</p>\n<p>Σε γενικές γραμμές, η φορολογική επιβάρυνση δεν διαφέρει δραματικά μεταξύ νομικών μορφών — η επιλογή πρέπει να γίνει με κριτήρια ευθύνης, διοίκησης και προοπτικής.</p>\n",
        ),
    );

    // Delete existing posts (only auto-seeded ones).
    $existing = get_posts( array( 'post_type' => 'post', 'numberposts' => -1, 'post_status' => 'any' ) );
    foreach ( $existing as $p ) { wp_delete_post( $p->ID, true ); }

    foreach ( $articles as $i => $a ) {
        $pid = wp_insert_post( array(
            'post_type'    => 'post',
            'post_status'  => 'publish',
            'post_title'   => $a['title'],
            'post_excerpt' => $a['subtitle'],
            'post_content' => $a['content'],
        ) );
        if ( ! $pid ) { continue; }

        // Featured image.
        $aid = mourtzilaki_attach_image( $theme . '/assets/img/' . $a['image'] );
        if ( $aid ) { set_post_thumbnail( $pid, $aid ); }

        // ACF fields.
        update_field( 'article_subtitle', $a['subtitle'], $pid );
        update_field( 'pull_quote',       $a['pull'],     $pid );
        update_field( 'key_takeaways',    $a['takeaways'], $pid );
        update_field( 'is_featured',      $i === 0,        $pid );
        $svc_match = $svc_id( $a['svc'] );
        if ( $svc_match ) { update_field( 'related_service', $svc_match, $pid ); }
    }

    update_option( 'mourtzilaki_articles_v2', '1.0' );
}

add_action( 'init', 'mourtzilaki_seed_faqs', 40 );

add_action( 'init', 'mourtzilaki_seed_more_testimonials', 42 );
function mourtzilaki_seed_more_testimonials() {
    if ( get_option( 'mourtzilaki_more_testimonials' ) === '1.0' ) { return; }
    if ( ! function_exists( 'update_field' ) ) { return; }

    $existing_count = (int) wp_count_posts( 'mz_testimonial' )->publish;
    if ( $existing_count >= 5 ) { update_option( 'mourtzilaki_more_testimonials', '1.0' ); return; }

    $more = array(
        array( 'name' => 'Α. Γεωργίου', 'role' => 'Ιδιώτης · Διαζύγιο &amp; επιμέλεια', 'quote' => 'Σε μια στιγμή που νιώθαμε να χάνουμε το έδαφος, η Έλενα μας έδωσε ένα συγκεκριμένο σχέδιο. Εξήγησε τι μπορεί και τι δεν μπορεί να γίνει, χωρίς ωραιοποιήσεις. Φύγαμε από κάθε συνάντηση πιο ξεκάθαροι.' ),
        array( 'name' => 'Μ. Κωνσταντίνου', 'role' => 'Πρόεδρος ΔΣ τεχνολογικής εταιρείας', 'quote' => 'Δουλεύουμε μαζί 4 χρόνια ως legal retainer. Η ταχύτητα απόκρισης είναι αυτό που με κερδίζει — απαντήσεις σε ώρες, όχι σε ημέρες. Νομικά τεκμηριωμένες, καθαρές, εφαρμόσιμες.' ),
        array( 'name' => 'Ν. Παπαδάκης', 'role' => 'Ιδιώτης · Κτηματολόγιο', 'quote' => 'Είχα μια διόρθωση πρώτης εγγραφής που ήμουν σίγουρος ότι δεν έβγαινε. Η Έλενα μελέτησε τα έγγραφα, βρήκε τη σωστή νομική βάση και η υπόθεση ολοκληρώθηκε σε 8 μήνες. Διαφανές κόστος, καμία έκπληξη.' ),
        array( 'name' => 'Σ. Δημητρίου', 'role' => 'Διευθύνουσα Σύμβουλος Ε.Π.Ε.', 'quote' => 'Χρειαζόμασταν αναδιάρθρωση μετοχικού κεφαλαίου με νέους επενδυτές. Όλα έγιναν ομαλά σε 3 μήνες. Η νομική στήριξη ήταν τέτοια που κάποια στιγμή ξέχασα ότι ασχολούμαι με νομικά — απλώς εμπιστευόμουν τη διαδικασία.' ),
        array( 'name' => 'Ε. Μιχαηλίδης', 'role' => 'Πελάτης ποινικής υπεράσπισης', 'quote' => 'Η αμεσότητα της παρουσίας από την πρώτη στιγμή ήταν αυτό που μου έδωσε ηρεμία. Στην ποινική υπεράσπιση, η ταχύτητα είναι τα πάντα — και εδώ υπήρξε από το πρώτο τηλέφωνο.' ),
    );

    foreach ( $more as $i => $t ) {
        $id = wp_insert_post( array(
            'post_type'   => 'mz_testimonial',
            'post_status' => 'publish',
            'post_title'  => $t['name'],
            'menu_order'  => $existing_count + $i + 1,
        ) );
        if ( $id ) {
            update_field( 'role',  $t['role'],  $id );
            update_field( 'quote', $t['quote'], $id );
        }
    }
    update_option( 'mourtzilaki_more_testimonials', '1.0' );
}

add_action( 'init', 'mourtzilaki_seed_cases', 44 );
function mourtzilaki_seed_cases() {
    if ( get_option( 'mourtzilaki_cases' ) === '1.0' ) { return; }
    if ( ! function_exists( 'update_field' ) ) { return; }
    if ( get_posts( array( 'post_type' => 'mz_case', 'numberposts' => 1, 'fields' => 'ids' ) ) ) {
        update_option( 'mourtzilaki_cases', '1.0' );
        return;
    }

    $find_svc = function ( $needle ) {
        foreach ( get_posts( array( 'post_type' => 'mz_service', 'numberposts' => -1 ) ) as $s ) {
            if ( false !== mb_stripos( get_the_title( $s ), $needle ) ) { return $s->ID; }
        }
        return 0;
    };

    $cases = array(
        array(
            'title'    => 'Διαζύγιο 7 ετών διαφωνίας — συμφωνία σε 4 μήνες',
            'svc'      => 'Οικογενειακό',
            'year'     => '2024',
            'duration' => '4 μήνες',
            'outcome'  => 'Συναινετικό διαζύγιο · συνεπιμέλεια · δίκαιη μοιρασιά',
            'desc'     => 'Επί χρόνια αδιέξοδη υπόθεση με αμοιβαίες αγωγές. Με στρατηγική μεσολάβησης πετύχαμε συναινετικό διαζύγιο, κανονισμένη συνεπιμέλεια και ισόρροπη ρύθμιση των περιουσιακών στοιχείων.',
        ),
        array(
            'title'    => 'Αναδιάρθρωση μετοχικής σύνθεσης ΙΚΕ τεχνολογίας',
            'svc'      => 'Εμπορικό',
            'year'     => '2023',
            'duration' => '3 μήνες',
            'outcome'  => 'Είσοδος δύο νέων επενδυτών · προστασία ιδρυτών',
            'desc'     => 'Σύνταξη shareholder agreement, νέο καταστατικό, drag-along/tag-along ρήτρες, κανόνες αποχώρησης. Διασφάλιση των ιδρυτικών μεριδίων με vesting schedule.',
        ),
        array(
            'title'    => 'Διόρθωση πρώτης εγγραφής σε ακίνητο 320μ²',
            'svc'      => 'Ακίνητα',
            'year'     => '2024',
            'duration' => '8 μήνες',
            'outcome'  => 'Οριστική διόρθωση εγγραφής · αποκατάσταση τίτλου',
            'desc'     => 'Λάθος καταχώριση εμβαδού και ιδιοκτήτη στις πρώτες εγγραφές. Με αγωγή και πλήρη τεκμηρίωση από συμβόλαια 25ετίας, πετύχαμε διόρθωση και νομική κατοχύρωση.',
        ),
        array(
            'title'    => 'Απόλυση εργαζομένου 12ετούς προϋπηρεσίας',
            'svc'      => 'Εργατικό',
            'year'     => '2023',
            'duration' => '6 μήνες',
            'outcome'  => '100% αποζημίωση · επιπλέον αποζημίωση ηθικής βλάβης',
            'desc'     => 'Ομαδική απόλυση χωρίς τήρηση διαδικασίας. Διεκδικήθηκε και επιδικάστηκε πλήρης αποζημίωση συν αποζημίωση ηθικής βλάβης για παράβαση του τύπου της απόλυσης.',
        ),
        array(
            'title'    => 'Τραπεζικό δάνειο €450.000 — εξωδικαστική ρύθμιση',
            'svc'      => 'Τραπεζικό',
            'year'     => '2024',
            'duration' => '5 μήνες',
            'outcome'  => 'Κούρεμα κεφαλαίου 35% · ρύθμιση 180 δόσεων',
            'desc'     => 'Μέσω εξωδικαστικού μηχανισμού, διαπραγμάτευση με τράπεζα και funds. Διασφάλιση κύριας κατοικίας και βιώσιμη ρύθμιση που ο πελάτης μπορεί να εξυπηρετήσει.',
        ),
        array(
            'title'    => 'Κατηγορία υπεξαίρεσης σε στέλεχος επιχείρησης',
            'svc'      => 'Ποινικό',
            'year'     => '2024',
            'duration' => '14 μήνες',
            'outcome'  => 'Πλήρης αθώωση σε όλα τα δικαστήρια',
            'desc'     => 'Σύνθετη υπόθεση με λογιστικά και τραπεζικά στοιχεία. Εξονυχιστική ανάλυση εγγράφων, παρουσίαση οικονομικών εκθέσεων στο δικαστήριο, κατάρρευση κατηγορίας.',
        ),
    );

    foreach ( $cases as $i => $c ) {
        $id = wp_insert_post( array(
            'post_type'   => 'mz_case',
            'post_status' => 'publish',
            'post_title'  => $c['title'],
            'menu_order'  => $i + 1,
        ) );
        if ( $id ) {
            $svc_id = $find_svc( $c['svc'] );
            if ( $svc_id ) { update_field( 'practice_area', $svc_id, $id ); }
            update_field( 'year',     $c['year'],     $id );
            update_field( 'duration', $c['duration'], $id );
            update_field( 'outcome',  $c['outcome'],  $id );
            update_field( 'description', $c['desc'],   $id );
        }
    }
    update_option( 'mourtzilaki_cases', '1.0' );
}
function mourtzilaki_seed_faqs() {
    if ( get_option( 'mourtzilaki_faqs' ) === '1.0' ) { return; }
    if ( ! function_exists( 'update_field' ) ) { return; }
    if ( get_posts( array( 'post_type' => 'mz_faq', 'numberposts' => 1, 'fields' => 'ids' ) ) ) {
        update_option( 'mourtzilaki_faqs', '1.0' );
        return;
    }

    $faqs = array(
        array( 'cat' => 'Γενικά', 'q' => 'Πώς λειτουργεί η πρώτη συνάντηση;',
               'a' => '<p>Στην πρώτη συνάντηση συζητάμε αναλυτικά την υπόθεσή σας, εντοπίζουμε τα κρίσιμα νομικά σημεία και απαντάμε σε όλες τις ερωτήσεις σας. Η συνάντηση μπορεί να πραγματοποιηθεί στο γραφείο μας ή online μέσω βιντεοκλήσης. Στο τέλος, σας ενημερώνουμε με σαφήνεια για τις επιλογές σας και το αναμενόμενο κόστος.</p>' ),
        array( 'cat' => 'Γενικά', 'q' => 'Πόσο κοστίζει η νομική σας υπηρεσία;',
               'a' => '<p>Το κόστος εξαρτάται από τη φύση και την πολυπλοκότητα της υπόθεσης. Πριν αναλάβουμε οποιαδήποτε ενέργεια, σας παρουσιάζουμε γραπτή πρόταση με σαφές κόστος και χρονοδιάγραμμα. Δεν υπάρχουν κρυφές χρεώσεις — γνωρίζετε εξαρχής τι θα πληρώσετε.</p>' ),
        array( 'cat' => 'Γενικά', 'q' => 'Τηρείτε εμπιστευτικότητα;',
               'a' => '<p>Απολύτως. Η δικηγορική εμπιστευτικότητα είναι θεσμοθετημένη υποχρέωσή μας και ταυτόχρονα η νοοτροπία μας. Καμία πληροφορία δεν διαρρέει εκτός γραφείου και τηρούμε αυστηρά πρωτόκολλα ασφαλείας για τα ψηφιακά αρχεία.</p>' ),
        array( 'cat' => 'Διαζύγιο', 'q' => 'Πόσο διαρκεί ένα συναινετικό διαζύγιο;',
               'a' => '<p>Ένα πλήρως συναινετικό διαζύγιο, με συμφωνία σε όλα τα θέματα (επιμέλεια, διατροφή, περιουσιακά), μπορεί να ολοκληρωθεί σε 2 με 4 εβδομάδες. Πραγματοποιείται ενώπιον συμβολαιογράφου, χωρίς ανάγκη δικαστικής παρουσίας.</p>' ),
        array( 'cat' => 'Διαζύγιο', 'q' => 'Τι σημαίνει συνεπιμέλεια;',
               'a' => '<p>Συνεπιμέλεια σημαίνει ότι και οι δύο γονείς λαμβάνουν από κοινού τις σημαντικές αποφάσεις για το παιδί (εκπαίδευση, υγεία, ταξίδια). Δεν σημαίνει υποχρεωτικά 50/50 διανυκτέρευση — η καθημερινή φροντίδα μπορεί να είναι κατά κύριο λόγο σε έναν γονέα.</p>' ),
        array( 'cat' => 'Επιχειρήσεις', 'q' => 'Τι νομική μορφή να επιλέξω για την επιχείρησή μου;',
               'a' => '<p>Εξαρτάται από τον αριθμό των εταίρων, το μέγεθος και τους κινδύνους της δραστηριότητας. Για τις περισσότερες μικρομεσαίες επιχειρήσεις, η ΙΚΕ είναι η πιο ευέλικτη επιλογή. Η ΑΕ ταιριάζει σε μεγαλύτερες δομές και επιχειρήσεις με σχέδια ανάπτυξης.</p>' ),
        array( 'cat' => 'Επιχειρήσεις', 'q' => 'Παρέχετε νομική υποστήριξη σε μηνιαία βάση;',
               'a' => '<p>Ναι. Προσφέρουμε προγράμματα μηνιαίας νομικής υποστήριξης (legal retainer) που καλύπτουν τις συνηθισμένες ανάγκες μιας επιχείρησης: σύνταξη συμβάσεων, εργατικά θέματα, φορολογικές γνωμοδοτήσεις. Το κόστος είναι σαφώς χαμηλότερο από εκπρόσωποι ad-hoc.</p>' ),
        array( 'cat' => 'Ακίνητα', 'q' => 'Τι έλεγχοι χρειάζονται πριν την αγορά ακινήτου;',
               'a' => '<p>Πριν την αγορά ενός ακινήτου, οι ουσιαστικότεροι έλεγχοι είναι: τίτλοι ιδιοκτησίας στο Υποθηκοφυλακείο και Κτηματολόγιο, ύπαρξη εμπραγμάτων βαρών (υποθήκες, προσημειώσεις), πολεοδομικός έλεγχος, βεβαίωση μη οφειλής ΕΝΦΙΑ. Η συμβολαιογραφική πράξη χωρίς αυτούς τους ελέγχους είναι ριψοκίνδυνη.</p>' ),
    );

    foreach ( $faqs as $i => $fq ) {
        $id = wp_insert_post( array(
            'post_type'   => 'mz_faq',
            'post_status' => 'publish',
            'post_title'  => $fq['q'],
            'menu_order'  => $i + 1,
        ) );
        if ( $id ) {
            update_field( 'answer',   $fq['a'],   $id );
            update_field( 'category', $fq['cat'], $id );
        }
    }
    update_option( 'mourtzilaki_faqs', '1.0' );
}
function mourtzilaki_seed_service_content() {
    if ( get_option( 'mourtzilaki_services_content' ) === '1.0' ) { return; }
    if ( ! function_exists( 'update_field' ) ) { return; }

    $content_map = array(
        'Εμπορικό & Εταιρικό Δίκαιο' => array(
            'intro' => 'Παρέχουμε ολοκληρωμένη νομική υποστήριξη σε επιχειρήσεις σε όλα τα στάδια της λειτουργίας τους — από την ίδρυση και την οργάνωση μέχρι τις σύνθετες αναδιαρθρώσεις και τις εμπορικές διαπραγματεύσεις.',
            'list_title' => 'Τι αναλαμβάνουμε',
            'list' => array(
                'Ίδρυση εταιρειών (ΟΕ, ΕΕ, ΕΠΕ, ΙΚΕ, ΑΕ) και επιλογή κατάλληλου εταιρικού τύπου',
                'Σύνταξη και τροποποίηση καταστατικών, μετοχικές συμβάσεις (shareholder agreements)',
                'Εξαγορές, συγχωνεύσεις, μετατροπές και αναδιοργανώσεις επιχειρήσεων',
                'Εταιρική διακυβέρνηση και compliance (συμμόρφωση)',
                'Σύνταξη εμπορικών συμβάσεων: διανομής, αντιπροσωπείας, franchise, παροχής υπηρεσιών',
                'Εκπροσώπηση σε εμπορικές διαφορές ενώπιον δικαστηρίων',
                'Συνεχής νομική υποστήριξη επιχειρήσεων (legal retainer)',
            ),
            'closing' => 'Σε κάθε υπόθεση εξετάζουμε τις νομικές, φορολογικές και επιχειρηματικές πτυχές μαζί, ώστε η λύση να είναι λειτουργική σε όλα τα επίπεδα.',
        ),
        'Αστικό & Ενοχικό Δίκαιο' => array(
            'intro' => 'Καλύπτουμε όλο το φάσμα των αστικών και ενοχικών διαφορών — από τις απλές απαιτήσεις μέχρι τις πιο σύνθετες υποθέσεις αδικοπρακτικής ευθύνης και αποζημίωσης.',
            'list_title' => 'Συνηθέστερα αντικείμενα',
            'list' => array(
                'Συμβάσεις: σύνταξη, ερμηνεία, διαπραγμάτευση και επίλυση διαφορών',
                'Απαιτήσεις από συμβάσεις και αδικοπραξίες',
                'Αποζημιώσεις από τροχαία ατυχήματα, ιατρική αμέλεια, εργατικά ατυχήματα',
                'Διαφορές οροφοκτησίας, γειτονικά δίκαια',
                'Διεκδικήσεις χρηματικών απαιτήσεων (διαταγές πληρωμής, αγωγές)',
                'Δικαστική και εξώδικη επίλυση αστικών διαφορών',
            ),
            'closing' => 'Πριν προτείνουμε δικαστική οδό, εξετάζουμε πάντοτε τη δυνατότητα εξώδικης επίλυσης που εξοικονομεί χρόνο και κόστος.',
        ),
        'Ακίνητα & Κτηματολόγιο' => array(
            'intro' => 'Από τον έλεγχο τίτλων μέχρι τη μεταβίβαση και τη διόρθωση κτηματολογικών εγγραφών, αναλαμβάνουμε όλη την υπόθεση του ακινήτου σας με μεθοδικότητα.',
            'list_title' => 'Τι αναλαμβάνουμε',
            'list' => array(
                'Έλεγχοι τίτλων στο Υποθηκοφυλακείο και στο Κτηματολόγιο',
                'Σύνταξη συμβολαίων μεταβίβασης ακινήτου',
                'Αγωγές διεκδίκησης ακινήτων και αρνητικές αγωγές',
                'Διορθώσεις πρώτων εγγραφών στο Εθνικό Κτηματολόγιο',
                'Συμβάσεις μίσθωσης (κατοικίας, επαγγελματικής, βραχυχρόνιας)',
                'Αποβολές, καταγγελίες μισθώσεων και διεκδικήσεις μισθωμάτων',
                'Πολεοδομικές υποθέσεις και τακτοποιήσεις αυθαιρέτων',
            ),
            'closing' => 'Συνεργαζόμαστε με συμβολαιογράφους, μηχανικούς και τεχνικούς συμβούλους για να καλύπτουμε ολόκληρη τη διαδικασία.',
        ),
        'Οικογενειακό & Κληρονομικό' => array(
            'intro' => 'Στις οικογενειακές και κληρονομικές υποθέσεις χρειάζεται διακριτικότητα, υπομονή και νομική ακρίβεια. Στόχος μας είναι η πιο ομαλή λύση για όλα τα μέρη — και κυρίως για τα παιδιά.',
            'list_title' => 'Αντικείμενα',
            'list' => array(
                'Συναινετικά και κατ’ αντιδικία διαζύγια',
                'Διατροφές συζύγων και τέκνων',
                'Επιμέλεια και επικοινωνία με τα παιδιά (συνεπιμέλεια)',
                'Σύνταξη συμφωνητικών διατροφής και επικοινωνίας',
                'Διαθήκες, νόμιμες μοίρες, αποδοχή/αποποίηση κληρονομίας',
                'Κληρονομικές διαφορές και προσβολή διαθηκών',
                'Δικαστική συμπαράσταση και ασφαλιστικά μέτρα',
            ),
            'closing' => 'Σε κάθε υπόθεση εξετάζουμε προτεραιότητα τη συναινετική λύση, αλλά είμαστε πλήρως προετοιμασμένοι και για δικαστική αντιπαράθεση όταν αυτή είναι αναπόφευκτη.',
        ),
        'Εργατικό Δίκαιο' => array(
            'intro' => 'Εκπροσωπούμε τόσο εργοδότες όσο και εργαζόμενους, με βαθιά γνώση της εργατικής νομοθεσίας και της αντίστοιχης νομολογίας.',
            'list_title' => 'Τι καλύπτουμε',
            'list' => array(
                'Σύνταξη και έλεγχος συμβάσεων εργασίας (ορισμένου, αορίστου, project)',
                'Καταγγελίες, απολύσεις και υπολογισμός αποζημιώσεων',
                'Διαφορές για δεδουλευμένες αποδοχές, υπερωρίες, αργίες',
                'Mobbing, διακρίσεις και παρενόχληση στον χώρο εργασίας',
                'Ομαδικές απολύσεις και μεταβολή εργασιακού καθεστώτος',
                'Εκπροσώπηση ενώπιον ΣΕΠΕ και εργατοδικείων',
            ),
            'closing' => 'Σε εργοδότες παρέχουμε προληπτική νομική υποστήριξη ώστε να αποφεύγονται διαφορές που κοστίζουν χρόνο και αξιοπιστία.',
        ),
        'Ποινικό Δίκαιο' => array(
            'intro' => 'Στο ποινικό δίκαιο, οι αποφάσεις πρέπει να είναι ταχύτατες και η νομική στρατηγική απόλυτα καθαρή. Είμαστε δίπλα σας από την πρώτη στιγμή — προανακριτικά, ανακριτικά, μέχρι το ακροατήριο.',
            'list_title' => 'Πεδία υπεράσπισης & εκπροσώπησης',
            'list' => array(
                'Υπεράσπιση κατηγορουμένων σε όλα τα στάδια της ποινικής διαδικασίας',
                'Υποστήριξη παθόντων (πολιτική αγωγή)',
                'Οικονομικά εγκλήματα, υπεξαίρεση, απάτη',
                'Τροχαία ατυχήματα με ποινικές προεκτάσεις',
                'Παραβάσεις του ΚΟΚ και αυτόφωρα',
                'Ποινικά αδικήματα κατά της προσωπικής ελευθερίας και τιμής',
                'Έκδοση και απελάσεις',
            ),
            'closing' => 'Διατηρούμε απόλυτη εμπιστευτικότητα και διαθεσιμότητα — η ποινική υπόθεση δεν περιμένει.',
        ),
        'Διοικητικό & Φορολογικό' => array(
            'intro' => 'Οι διαφορές με τη Διοίκηση και τη Φορολογική Αρχή απαιτούν εξειδίκευση και προσοχή στις προθεσμίες. Διαχειριζόμαστε όλο το φάσμα προσφυγών και ενδικοφανών διαδικασιών.',
            'list_title' => 'Αντικείμενα',
            'list' => array(
                'Προσφυγές κατά διοικητικών πράξεων (πρόστιμα, ανακλήσεις άδειας κ.ά.)',
                'Φορολογικές διαφορές: φόρος εισοδήματος, ΦΠΑ, ΕΝΦΙΑ',
                'Ενδικοφανείς προσφυγές στη ΔΕΔ',
                'Εκπροσώπηση σε διοικητικά πρωτοδικεία και εφετεία',
                'Δημόσιες συμβάσεις, διαγωνισμοί και ενστάσεις',
                'Πολεοδομικές παραβάσεις και υπαλληλικές υποθέσεις',
            ),
            'closing' => 'Σε κάθε υπόθεση μελετάμε προσεκτικά τις προθεσμίες — οι περισσότερες διοικητικές υποθέσεις χάνονται από αμέλεια χρονικών ορίων.',
        ),
        'Τραπεζικό Δίκαιο' => array(
            'intro' => 'Σε μια εποχή που οι τραπεζικές σχέσεις γίνονται όλο και πιο πολύπλοκες, η νομική υποστήριξη για δάνεια, εγγυήσεις και ρυθμίσεις οφειλών είναι κρίσιμη.',
            'list_title' => 'Τι αναλαμβάνουμε',
            'list' => array(
                'Εξυπηρετούμενα και μη εξυπηρετούμενα δάνεια (NPLs)',
                'Διαπραγμάτευση ρυθμίσεων με τράπεζες και funds',
                'Εξωδικαστικός μηχανισμός ρύθμισης οφειλών',
                'Νόμος Κατσέλη / Νόμος για υπερχρεωμένα φυσικά πρόσωπα',
                'Εγγυήσεις, ενέχυρα, υποθήκες και προσημειώσεις',
                'Πτωχευτικό δίκαιο και εξυγίανση επιχειρήσεων',
                'Διαφορές από τραπεζικές χρεώσεις και προμήθειες',
            ),
            'closing' => 'Η εμπειρία μας στις τραπεζικές διαπραγματεύσεις μας επιτρέπει να επιτυγχάνουμε ρυθμίσεις που είναι πραγματικά βιώσιμες — όχι απλώς θεωρητικά εφικτές.',
        ),
    );

    $services = get_posts( array( 'post_type' => 'mz_service', 'numberposts' => -1 ) );
    foreach ( $services as $svc ) {
        $existing = (string) get_field( 'long_description', $svc->ID );
        if ( $existing !== '' ) { continue; }

        $title = get_the_title( $svc );
        if ( ! isset( $content_map[ $title ] ) ) { continue; }
        $c = $content_map[ $title ];

        $html  = '<p>' . esc_html( $c['intro'] ) . '</p>';
        $html .= '<h3>' . esc_html( $c['list_title'] ) . '</h3>';
        $html .= '<ul>';
        foreach ( $c['list'] as $item ) {
            $html .= '<li>' . esc_html( $item ) . '</li>';
        }
        $html .= '</ul>';
        $html .= '<p>' . esc_html( $c['closing'] ) . '</p>';

        update_field( 'long_description', $html, $svc->ID );
    }

    update_option( 'mourtzilaki_services_content', '1.0' );
    flush_rewrite_rules( false );
}

/**
 * Copy a file from the theme into the WP uploads dir and create an attachment.
 * Returns the attachment ID, or 0 on failure.
 */
function mourtzilaki_attach_image( $src_path ) {
    if ( ! file_exists( $src_path ) ) { return 0; }
    $upload  = wp_upload_dir();
    $name    = wp_unique_filename( $upload['path'], basename( $src_path ) );
    $dest    = trailingslashit( $upload['path'] ) . $name;
    if ( ! @copy( $src_path, $dest ) ) { return 0; }
    $type = wp_check_filetype( $name, null );
    $aid  = wp_insert_attachment( array(
        'guid'           => trailingslashit( $upload['url'] ) . $name,
        'post_mime_type' => $type['type'],
        'post_title'     => preg_replace( '/\.[^.]+$/', '', $name ),
        'post_content'   => '',
        'post_status'    => 'inherit',
    ), $dest );
    if ( is_wp_error( $aid ) || ! $aid ) { return 0; }
    $meta = wp_generate_attachment_metadata( $aid, $dest );
    wp_update_attachment_metadata( $aid, $meta );
    return (int) $aid;
}
