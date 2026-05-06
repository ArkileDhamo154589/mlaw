<?php
/**
 * Header.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#content"><?php esc_html_e( 'Μετάβαση στο περιεχόμενο', 'mourtzilaki' ); ?></a>

<header class="site-header" id="site-header">
    <div class="container inner">
        <?php
        // Logo resolution: Site Settings → Customizer custom_logo → text brand.
        $logo     = mourtzilaki_setting( 'site_logo', null );
        $logo_url = '';
        $logo_alt = mourtzilaki_setting( 'site_logo_alt', get_bloginfo( 'name' ) );
        if ( is_array( $logo ) ) {
            $logo_url = $logo['url'] ?? '';
            if ( '' === trim( $logo_alt ) && ! empty( $logo['alt'] ) ) { $logo_alt = $logo['alt']; }
        } elseif ( has_custom_logo() ) {
            $logo_id  = get_theme_mod( 'custom_logo' );
            $logo_url = $logo_id ? wp_get_attachment_image_url( $logo_id, 'full' ) : '';
        }
        ?>
        <?php if ( $logo_url ) :
            $logo_h = (int) mourtzilaki_setting( 'site_logo_height', 0 );
            $logo_w = (int) mourtzilaki_setting( 'site_logo_width', 0 );
            $logo_style = '';
            if ( $logo_h > 0 ) { $logo_style .= 'max-height:' . $logo_h . 'px;'; }
            if ( $logo_w > 0 ) { $logo_style .= 'max-width:' . $logo_w . 'px;'; }
        ?>
            <a class="brand brand-with-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
                <img class="brand-logo-img" src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( $logo_alt ); ?>"<?php echo $logo_style ? ' style="' . esc_attr( $logo_style ) . '"' : ''; ?>>
            </a>
        <?php else :
            $brand_sub = mourtzilaki_setting( 'brand_sub', 'Δικηγορικό γραφείο' );
        ?>
            <a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
                <span class="brand-mark" aria-hidden="true"></span>
                <span class="brand-name"><?php bloginfo( 'name' ); ?></span>
                <?php if ( $brand_sub ) : ?>
                    <small class="brand-sub"><?php echo esc_html( str_replace( ' ', "\xC2\xA0", $brand_sub ) ); ?></small>
                <?php endif; ?>
            </a>
        <?php endif; ?>

        <nav class="primary-nav-wrap" aria-label="<?php esc_attr_e( 'Κύριο μενού', 'mourtzilaki' ); ?>">
            <?php mourtzilaki_primary_menu(); ?>
        </nav>

        <?php
        $cta_label = mourtzilaki_setting( 'header_cta_label', 'Κλείστε ραντεβού' );
        $cta_url   = mourtzilaki_setting( 'header_cta_url', mourtzilaki_page_url( 'contact' ) );
        ?>
        <div class="header-cta">
            <a class="btn btn-primary" href="<?php echo esc_url( $cta_url ); ?>">
                <?php echo esc_html( $cta_label ); ?> <span class="arrow" aria-hidden="true">→</span>
            </a>
            <button class="menu-toggle" type="button" aria-controls="mobile-menu" aria-expanded="false" aria-label="<?php esc_attr_e( 'Εναλλαγή μενού', 'mourtzilaki' ); ?>">
                <span class="bars" aria-hidden="true"></span>
            </button>
        </div>
    </div>
</header>

<div class="mobile-menu" id="mobile-menu" aria-hidden="true">
    <?php mourtzilaki_mobile_menu(); ?>
    <?php $c = mourtzilaki_get_contact_info(); ?>
    <div class="menu-foot">
        <p><?php echo esc_html( str_replace( "\n", ', ', $c['address'] ) ); ?></p>
        <p>
            <a href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $c['phone'] ) ); ?>"><?php echo esc_html( $c['phone'] ); ?></a><br>
            <a href="mailto:<?php echo esc_attr( $c['email'] ); ?>"><?php echo esc_html( $c['email'] ); ?></a>
        </p>
    </div>
</div>

<main id="content" class="site-main">
