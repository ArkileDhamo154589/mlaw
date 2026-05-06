<?php
/**
 * Footer.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>
</main>

<?php
// Resolution order for these fields: Site Settings → legacy front-page ACF → default.
$front_id = (int) get_option( 'page_on_front' );
$legacy_about = $legacy_legal = '';
if ( $front_id && function_exists( 'get_field' ) ) {
    $legacy_about = (string) get_field( 'footer_about_text',  $front_id );
    $legacy_legal = (string) get_field( 'footer_legal_right', $front_id );
}

$footer_about_text  = mourtzilaki_setting( 'footer_about_text', $legacy_about ?: 'Παρέχουμε ολοκληρωμένη νομική υποστήριξη με συνέπεια, διακριτικότητα και βαθιά γνώση του ελληνικού δικαίου.' );
$footer_legal_right = mourtzilaki_setting( 'footer_legal_right', $legacy_legal ?: 'Μέλος του Δικηγορικού Συλλόγου Αθηνών' );
$footer_brand       = mourtzilaki_setting( 'footer_brand', '' ) ?: get_bloginfo( 'name' );
$footer_copyright   = mourtzilaki_setting( 'footer_copyright', '' );
$col_nav_title      = mourtzilaki_setting( 'footer_col_nav_title',     'Πλοήγηση' );
$col_contact_title  = mourtzilaki_setting( 'footer_col_contact_title', 'Επικοινωνία' );
$col_hours_title    = mourtzilaki_setting( 'footer_col_hours_title',   'Ωράριο' );
?>
<footer class="site-footer">
    <div class="container">
        <div class="top">
            <div class="col">
                <?php
                $logo_obj = mourtzilaki_setting( 'site_logo', null );
                $logo_alt_f = mourtzilaki_setting( 'site_logo_alt', $footer_brand );
                if ( is_array( $logo_obj ) && ! empty( $logo_obj['url'] ) ) : ?>
                    <a class="brand-foot brand-foot-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( $footer_brand ); ?>">
                        <img src="<?php echo esc_url( $logo_obj['url'] ); ?>" alt="<?php echo esc_attr( $logo_alt_f ); ?>">
                    </a>
                <?php else : ?>
                    <div class="brand-foot"><?php echo esc_html( $footer_brand ); ?></div>
                <?php endif; ?>
                <p><?php echo mourtzilaki_field_inline( $footer_about_text ); ?></p>
            </div>
            <div class="col">
                <h4><?php echo esc_html( $col_nav_title ); ?></h4>
                <?php mourtzilaki_footer_menu(); ?>
            </div>
            <?php $c = mourtzilaki_get_contact_info(); ?>
            <div class="col">
                <h4><?php echo esc_html( $col_contact_title ); ?></h4>
                <ul>
                    <?php foreach ( explode( "\n", $c['address'] ) as $line ) : if ( '' === trim( $line ) ) { continue; } ?>
                        <li><?php echo esc_html( $line ); ?></li>
                    <?php endforeach; ?>
                    <li><a href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $c['phone'] ) ); ?>"><?php echo esc_html( $c['phone'] ); ?></a></li>
                    <li><a href="mailto:<?php echo esc_attr( $c['email'] ); ?>"><?php echo esc_html( $c['email'] ); ?></a></li>
                </ul>
            </div>
            <div class="col">
                <h4><?php echo esc_html( $col_hours_title ); ?></h4>
                <ul>
                    <?php foreach ( explode( "\n", $c['hours'] ) as $line ) : if ( '' === trim( $line ) ) { continue; } ?>
                        <li><?php echo esc_html( $line ); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <div class="legal">
            <?php if ( $footer_copyright ) : ?>
                <span><?php echo esc_html( str_replace( '[Year]', date( 'Y' ), $footer_copyright ) ); ?></span>
            <?php else : ?>
                <span>© <?php echo esc_html( date( 'Y' ) ); ?> <?php echo esc_html( $footer_brand ); ?>. Με την επιφύλαξη παντός δικαιώματος.</span>
            <?php endif; ?>
            <span><?php echo esc_html( $footer_legal_right ); ?></span>
        </div>
    </div>
</footer>

<button id="back-to-top" class="back-to-top" type="button" aria-label="Επιστροφή στην κορυφή">
    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 19V5M5 12l7-7 7 7"/></svg>
</button>

<?php wp_footer(); ?>
</body>
</html>
