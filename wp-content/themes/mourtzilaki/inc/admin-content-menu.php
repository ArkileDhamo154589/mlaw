<?php
/**
 * Top-level "Περιεχόμενο" admin menu that groups all the content CPTs
 * (hero, services, lawyers, cases, testimonials, FAQ) as submenus.
 *
 * Each CPT registers itself with `show_in_menu => 'mz-content'`; this file
 * provides the parent and a small landing page with quick-access cards.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

add_action( 'admin_menu', function () {
    add_menu_page(
        'Περιεχόμενο',
        'Περιεχόμενο',
        'edit_posts',
        'mz-content',
        'mz_content_overview_render',
        'dashicons-archive',
        4
    );
}, 9 );

/* Rename the auto-generated first submenu (which mirrors the parent slug)
 * to "Επισκόπηση" instead of duplicating "Περιεχόμενο". */
add_action( 'admin_menu', function () {
    global $submenu;
    if ( ! empty( $submenu['mz-content'] ) && isset( $submenu['mz-content'][0][0] ) ) {
        $submenu['mz-content'][0][0] = 'Επισκόπηση';
    }
}, 999 );

function mz_content_overview_render() {
    if ( ! current_user_can( 'edit_posts' ) ) { wp_die(); }

    $cards = array(
        array(
            'pt'    => 'mz_hero',
            'label' => 'Hero Slides',
            'desc'  => 'Slides στο μεγάλο εισαγωγικό carousel της αρχικής.',
            'icon'  => 'dashicons-images-alt2',
        ),
        array(
            'pt'    => 'mz_service',
            'label' => 'Τομείς δικαίου',
            'desc'  => 'Οι κατηγορίες υπηρεσιών που εμφανίζονται στις σελίδες υπηρεσιών.',
            'icon'  => 'dashicons-portfolio',
        ),
        array(
            'pt'    => 'mz_member',
            'label' => 'Δικηγόροι',
            'desc'  => 'Μέλη της ομάδας — εμφανίζονται στη σελίδα ομάδας και στους τομείς.',
            'icon'  => 'dashicons-businessperson',
        ),
        array(
            'pt'    => 'mz_case',
            'label' => 'Επιλεγμένες υποθέσεις',
            'desc'  => 'Κρίσιμες υποθέσεις που προβάλλονται στη σελίδα υποθέσεων.',
            'icon'  => 'dashicons-portfolio',
        ),
        array(
            'pt'    => 'mz_testimonial',
            'label' => 'Testimonials',
            'desc'  => 'Μαρτυρίες πελατών στα testimonials sliders.',
            'icon'  => 'dashicons-format-quote',
        ),
        array(
            'pt'    => 'mz_faq',
            'label' => 'Συχνές ερωτήσεις',
            'desc'  => 'Ερωτήσεις που εμφανίζονται στη σελίδα FAQ.',
            'icon'  => 'dashicons-editor-help',
        ),
    );

    /* Per-CPT counts in a single query. */
    global $wpdb;
    $counts_raw = $wpdb->get_results(
        "SELECT post_type, post_status, COUNT(*) AS c FROM {$wpdb->posts}
         WHERE post_type IN ('mz_hero','mz_service','mz_member','mz_case','mz_testimonial','mz_faq')
           AND post_status NOT IN ('auto-draft','trash')
         GROUP BY post_type, post_status",
        ARRAY_A
    );
    $counts = array();
    foreach ( (array) $counts_raw as $r ) {
        $counts[ $r['post_type'] ][ $r['post_status'] ] = (int) $r['c'];
    }

    ?>
    <div class="wrap mz-content-overview">
        <h1 class="wp-heading-inline">Περιεχόμενο</h1>
        <p class="description">Διαχείριση όλων των ενοτήτων που τροφοδοτούν το site από ένα σημείο.</p>

        <div class="mz-content-grid">
            <?php foreach ( $cards as $c ) :
                $obj         = get_post_type_object( $c['pt'] );
                if ( ! $obj ) { continue; }
                $list_url    = admin_url( 'edit.php?post_type=' . $c['pt'] );
                $new_url     = admin_url( 'post-new.php?post_type=' . $c['pt'] );
                $published   = (int) ( $counts[ $c['pt'] ]['publish'] ?? 0 );
                $draft       = (int) ( $counts[ $c['pt'] ]['draft']   ?? 0 );
                $total       = $published + $draft;
            ?>
            <div class="mz-content-card">
                <div class="mz-content-card__head">
                    <span class="mz-content-card__icon dashicons <?php echo esc_attr( $c['icon'] ); ?>" aria-hidden="true"></span>
                    <h2 class="mz-content-card__title"><?php echo esc_html( $c['label'] ); ?></h2>
                    <span class="mz-content-card__count"><?php echo (int) $total; ?></span>
                </div>
                <p class="mz-content-card__desc"><?php echo esc_html( $c['desc'] ); ?></p>
                <div class="mz-content-card__meta">
                    <?php if ( $published ) : ?><span><?php echo (int) $published; ?> δημοσιευμένα</span><?php endif; ?>
                    <?php if ( $draft ) : ?><span class="muted"><?php echo (int) $draft; ?> drafts</span><?php endif; ?>
                </div>
                <div class="mz-content-card__actions">
                    <a class="button button-primary" href="<?php echo esc_url( $list_url ); ?>">Διαχείριση</a>
                    <a class="button button-secondary" href="<?php echo esc_url( $new_url ); ?>">+ Νέο</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <style>
        .mz-content-overview .description { margin: 6px 0 24px; color: #5b6573; font-size: 14px; }
        .mz-content-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 16px;
            max-width: 1200px;
        }
        .mz-content-card {
            background: #fff;
            border: 1px solid #d8dde2;
            border-radius: 8px;
            padding: 18px 20px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03);
            transition: border-color .15s, box-shadow .15s, transform .15s;
        }
        .mz-content-card:hover {
            border-color: #b48a3a;
            box-shadow: 0 4px 14px rgba(31,26,20,0.06);
            transform: translateY(-1px);
        }
        .mz-content-card__head {
            display: flex; align-items: center; gap: 10px; margin-bottom: 2px;
        }
        .mz-content-card__icon {
            color: #b48a3a; font-size: 22px; width: 22px; height: 22px;
        }
        .mz-content-card__title {
            margin: 0; font-size: 15px; font-weight: 600; color: #1f1a14; flex: 1;
        }
        .mz-content-card__count {
            background: #f6f3ec;
            border: 1px solid #e4ddc8;
            color: #6b6048;
            border-radius: 999px;
            padding: 2px 10px;
            font-size: 12px;
            font-weight: 600;
            min-width: 28px;
            text-align: center;
        }
        .mz-content-card__desc {
            margin: 0 0 6px; font-size: 13px; line-height: 1.55; color: #5b6573;
        }
        .mz-content-card__meta {
            display: flex; gap: 12px; font-size: 12px; color: #4b5563; min-height: 16px;
        }
        .mz-content-card__meta .muted { color: #9ca3af; }
        .mz-content-card__actions {
            display: flex; gap: 8px; margin-top: 8px;
        }
    </style>
    <?php
}
