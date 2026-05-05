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
        <?php if ( has_custom_logo() ) :
            $logo_id  = get_theme_mod( 'custom_logo' );
            $logo_url = $logo_id ? wp_get_attachment_image_url( $logo_id, 'full' ) : '';
        ?>
            <a class="brand brand-with-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php bloginfo( 'name' ); ?>">
                <img class="brand-logo-img" src="<?php echo esc_url( $logo_url ); ?>" alt="<?php bloginfo( 'name' ); ?>">
            </a>
        <?php else : ?>
            <a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php bloginfo( 'name' ); ?>">
                <span class="brand-mark" aria-hidden="true"></span>
                <span class="brand-name"><?php bloginfo( 'name' ); ?></span>
                <small class="brand-sub">Δικηγορικό&nbsp;γραφείο</small>
            </a>
        <?php endif; ?>

        <nav class="primary-nav-wrap" aria-label="<?php esc_attr_e( 'Κύριο μενού', 'mourtzilaki' ); ?>">
            <?php mourtzilaki_primary_menu(); ?>
        </nav>

        <div class="header-cta">
            <a class="btn btn-primary" href="<?php echo esc_url( mourtzilaki_page_url( 'contact' ) ); ?>">
                Κλείστε ραντεβού <span class="arrow" aria-hidden="true">→</span>
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
