<?php
/**
 * Admin UI polish for the mz_site (Site Settings) edit screen.
 * - Hides default WP noise (title, slug, status, etc.) on the singleton.
 * - Adds a header bar with "View site" button.
 * - Styles ACF tabs and fields for breathing room and clearer hierarchy.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/* Better menu icon for the Site Settings entry. */
add_action( 'admin_menu', function () {
    global $menu;
    if ( ! is_array( $menu ) ) { return; }
    foreach ( $menu as $i => $entry ) {
        if ( ! empty( $entry[2] ) && false !== strpos( $entry[2], 'post.php?post=' ) && 'Ρυθμίσεις site' === ( $entry[0] ?? '' ) ) {
            $menu[ $i ][6] = 'dashicons-admin-settings';
        }
    }
}, 100 );

/* Remove unused meta boxes on the settings screen. */
add_action( 'add_meta_boxes', function () {
    $screen = get_current_screen();
    if ( ! $screen || 'mz_site' !== $screen->post_type ) { return; }
    foreach ( array(
        'commentstatusdiv','commentsdiv','trackbacksdiv','authordiv',
        'postcustom','slugdiv','revisionsdiv','postimagediv',
    ) as $id ) {
        remove_meta_box( $id, 'mz_site', 'normal' );
        remove_meta_box( $id, 'mz_site', 'side' );
        remove_meta_box( $id, 'mz_site', 'advanced' );
    }
}, 50 );

/* Header bar above the content: "View site" + last-modified chip. */
add_action( 'edit_form_top', function ( $post ) {
    if ( ! $post || 'mz_site' !== $post->post_type ) { return; }
    $home_url   = home_url( '/' );
    $modified   = get_post_modified_time( 'j M Y, H:i', false, $post );
    $brand      = get_bloginfo( 'name' );
    ?>
    <div class="mz-settings-bar">
        <div class="mz-settings-bar__intro">
            <span class="mz-settings-bar__eyebrow">Ρυθμίσεις site</span>
            <strong class="mz-settings-bar__brand"><?php echo esc_html( $brand ); ?></strong>
            <span class="mz-settings-bar__hint">Διαχειρίσου από εδώ logo, footer, social links και στοιχεία επικοινωνίας. Οι αλλαγές εμφανίζονται σε όλες τις σελίδες.</span>
        </div>
        <div class="mz-settings-bar__meta">
            <?php if ( $modified ) : ?>
                <span class="mz-settings-bar__chip" title="Τελευταία ενημέρωση">
                    <span class="dashicons dashicons-clock" aria-hidden="true"></span>
                    <?php echo esc_html( $modified ); ?>
                </span>
            <?php endif; ?>
            <a class="button button-secondary" href="<?php echo esc_url( $home_url ); ?>" target="_blank" rel="noopener">
                <span class="dashicons dashicons-external" aria-hidden="true"></span>
                Δες το site
            </a>
        </div>
    </div>
    <?php
} );

/* CSS only on the mz_site edit screen. */
add_action( 'admin_print_styles-post.php', function () {
    $screen = get_current_screen();
    if ( ! $screen || 'mz_site' !== $screen->post_type ) { return; }
    ?>
    <style id="mz-settings-ui">
        /* Hide WP noise that does not apply to the singleton settings post */
        body.post-type-mz_site #titlediv,
        body.post-type-mz_site #screen-meta-links,
        body.post-type-mz_site #screen-meta,
        body.post-type-mz_site #edit-slug-box,
        body.post-type-mz_site #post-preview,
        body.post-type-mz_site .page-title-action,
        body.post-type-mz_site #minor-publishing-actions,
        body.post-type-mz_site #misc-publishing-actions .misc-pub-post-status,
        body.post-type-mz_site #misc-publishing-actions .misc-pub-visibility,
        body.post-type-mz_site #misc-publishing-actions .misc-pub-curtime,
        body.post-type-mz_site #delete-action {
            display: none !important;
        }

        /* Tighter chrome */
        body.post-type-mz_site #wpbody-content { padding-bottom: 32px; }
        body.post-type-mz_site .wrap > h1.wp-heading-inline { margin: 16px 0 4px; font-size: 22px; }

        /* Header bar */
        .mz-settings-bar {
            display: flex;
            gap: 16px;
            justify-content: space-between;
            align-items: flex-start;
            background: #fff;
            border: 1px solid #d8dde2;
            border-left: 4px solid #b48a3a;
            border-radius: 8px;
            padding: 18px 22px;
            margin: 8px 0 20px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03);
        }
        .mz-settings-bar__intro { display: flex; flex-direction: column; gap: 4px; max-width: 720px; }
        .mz-settings-bar__eyebrow {
            font-size: 11px; text-transform: uppercase; letter-spacing: .14em;
            color: #6b7280; font-weight: 600;
        }
        .mz-settings-bar__brand { font-size: 20px; line-height: 1.2; color: #1f1a14; }
        .mz-settings-bar__hint { color: #5b6573; font-size: 13px; line-height: 1.5; margin-top: 4px; }
        .mz-settings-bar__meta { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }
        .mz-settings-bar__chip {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 6px 10px; background: #f6f3ec; border: 1px solid #e4ddc8;
            border-radius: 999px; font-size: 12px; color: #6b6048;
        }
        .mz-settings-bar__chip .dashicons { font-size: 14px; width: 14px; height: 14px; }
        .mz-settings-bar__meta .button .dashicons {
            font-size: 16px; width: 16px; height: 16px; line-height: 1; margin-right: 4px; vertical-align: text-bottom;
        }

        /* The ACF settings group: cleaner card */
        body.post-type-mz_site #postbox-container-2 .postbox#acf-group_mz_site_settings {
            border: 1px solid #d8dde2;
            border-radius: 8px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03);
        }
        body.post-type-mz_site #acf-group_mz_site_settings > .postbox-header {
            border-bottom: 1px solid #e6e9ec;
            padding: 14px 20px;
        }
        body.post-type-mz_site #acf-group_mz_site_settings > .postbox-header h2 {
            font-size: 14px; margin: 0; color: #1f1a14; font-weight: 600;
        }
        body.post-type-mz_site #acf-group_mz_site_settings > .postbox-header .handle-actions { display: none; }
        body.post-type-mz_site #acf-group_mz_site_settings .inside { padding: 0; margin: 0; }

        /* ACF tabs row */
        body.post-type-mz_site .acf-tab-wrap {
            background: #fafbfc;
            border-bottom: 1px solid #e6e9ec;
            position: sticky;
            top: 32px;
            z-index: 10;
        }
        body.post-type-mz_site .acf-tab-group {
            padding: 4px 16px 0;
            display: flex;
            gap: 4px;
            border: 0;
        }
        body.post-type-mz_site .acf-tab-group li a {
            padding: 12px 16px;
            font-weight: 500;
            color: #5b6573;
            border: 0 !important;
            border-bottom: 2px solid transparent !important;
            background: transparent !important;
            box-shadow: none !important;
            border-radius: 0 !important;
            margin-bottom: -1px;
        }
        body.post-type-mz_site .acf-tab-group li a:focus { box-shadow: none !important; outline: none; }
        body.post-type-mz_site .acf-tab-group li a:hover { color: #1f1a14; }
        body.post-type-mz_site .acf-tab-group li.active a {
            color: #1f1a14;
            border-bottom-color: #b48a3a !important;
            background: transparent !important;
        }

        /* Field rows */
        body.post-type-mz_site .acf-fields > .acf-field {
            padding: 18px 24px;
            border-top: 1px solid #f0f1f3;
        }
        body.post-type-mz_site .acf-fields > .acf-field:first-of-type { border-top: 0; }
        body.post-type-mz_site .acf-fields > .acf-field > .acf-label label {
            font-size: 13px; color: #1f1a14; font-weight: 600; margin-bottom: 6px;
        }
        body.post-type-mz_site .acf-fields > .acf-field > .acf-label .description,
        body.post-type-mz_site .acf-fields > .acf-field p.description {
            color: #6b7280; font-size: 12.5px; line-height: 1.55;
        }
        body.post-type-mz_site .acf-input input[type="text"],
        body.post-type-mz_site .acf-input input[type="email"],
        body.post-type-mz_site .acf-input input[type="url"],
        body.post-type-mz_site .acf-input input[type="number"],
        body.post-type-mz_site .acf-input textarea {
            border: 1px solid #d8dde2; border-radius: 6px;
            padding: 8px 12px;
            font-size: 14px;
            box-shadow: none;
            transition: border-color .15s, box-shadow .15s;
        }
        body.post-type-mz_site .acf-input input[type="text"]:focus,
        body.post-type-mz_site .acf-input input[type="email"]:focus,
        body.post-type-mz_site .acf-input input[type="url"]:focus,
        body.post-type-mz_site .acf-input input[type="number"]:focus,
        body.post-type-mz_site .acf-input textarea:focus {
            border-color: #b48a3a; box-shadow: 0 0 0 1px #b48a3a; outline: none;
        }
        body.post-type-mz_site .acf-input textarea { min-height: 96px; font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; }
        body.post-type-mz_site .acf-image-uploader { background: #fafbfc; border-radius: 6px; padding: 12px; }

        /* Publish meta box → looks like a save card */
        body.post-type-mz_site #submitdiv {
            border: 1px solid #d8dde2;
            border-radius: 8px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03);
        }
        body.post-type-mz_site #submitdiv .postbox-header { padding: 12px 16px; border-bottom: 1px solid #e6e9ec; }
        body.post-type-mz_site #submitdiv .postbox-header h2 { font-size: 13px; margin: 0; color: #1f1a14; }
        body.post-type-mz_site #submitdiv .inside { padding: 16px; margin: 0; }
        body.post-type-mz_site #publishing-action #publish {
            background: #b48a3a; border-color: #9a7530; color: #fff;
            text-shadow: none; box-shadow: 0 1px 0 rgba(0,0,0,0.05);
        }
        body.post-type-mz_site #publishing-action #publish:hover { background: #9a7530; border-color: #825f23; }
        body.post-type-mz_site #publishing-action .spinner { float: none; vertical-align: middle; }

        /* Side column tweaks */
        body.post-type-mz_site #post-body.columns-2 #postbox-container-1 .postbox { border: 1px solid #d8dde2; border-radius: 8px; }

        @media (max-width: 782px) {
            .mz-settings-bar { flex-direction: column; }
            .mz-settings-bar__meta { width: 100%; flex-wrap: wrap; }
            body.post-type-mz_site .acf-tab-wrap { position: static; }
        }
    </style>
    <?php
} );

/* Fix the publish button label: avoid "Update" feeling stale on a settings page. */
add_filter( 'gettext_with_context', function ( $translation, $text, $context, $domain ) {
    if ( 'default' !== $domain || 'post action/button label' !== $context ) { return $translation; }
    $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
    if ( $screen && 'mz_site' === $screen->post_type && 'Update' === $text ) {
        return 'Αποθήκευση αλλαγών';
    }
    return $translation;
}, 10, 4 );
