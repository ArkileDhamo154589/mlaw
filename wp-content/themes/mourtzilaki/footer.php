<?php
/**
 * Footer.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>
</main>

<?php
$footer_about_text = '';
$footer_legal_right = '';
$front_id = (int) get_option( 'page_on_front' );
if ( $front_id && function_exists( 'get_field' ) ) {
    $footer_about_text  = (string) get_field( 'footer_about_text',  $front_id );
    $footer_legal_right = (string) get_field( 'footer_legal_right', $front_id );
}
if ( '' === $footer_about_text )  { $footer_about_text  = 'Παρέχουμε ολοκληρωμένη νομική υποστήριξη με συνέπεια, διακριτικότητα και βαθιά γνώση του ελληνικού δικαίου.'; }
if ( '' === $footer_legal_right ) { $footer_legal_right = 'Μέλος του Δικηγορικού Συλλόγου Αθηνών'; }
?>
<footer class="site-footer">
    <div class="container">
        <div class="top">
            <div class="col">
                <div class="brand-foot"><?php bloginfo( 'name' ); ?></div>
                <p><?php echo mourtzilaki_field_inline( $footer_about_text ); ?></p>
            </div>
            <div class="col">
                <h4>Πλοήγηση</h4>
                <ul>
                    <li><a href="<?php echo esc_url( mourtzilaki_page_url( 'about' ) ); ?>">Το γραφείο</a></li>
                    <li><a href="<?php echo esc_url( mourtzilaki_page_url( 'services' ) ); ?>">Τομείς εξειδίκευσης</a></li>
                    <li><a href="<?php echo esc_url( mourtzilaki_page_url( 'team' ) ); ?>">Δικηγόροι</a></li>
                    <li><a href="<?php echo esc_url( mourtzilaki_page_url( 'blog' ) ); ?>">Άρθρα</a></li>
                </ul>
            </div>
            <?php $c = mourtzilaki_get_contact_info(); ?>
            <div class="col">
                <h4>Επικοινωνία</h4>
                <ul>
                    <?php foreach ( explode( "\n", $c['address'] ) as $line ) : ?>
                        <li><?php echo esc_html( $line ); ?></li>
                    <?php endforeach; ?>
                    <li><a href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $c['phone'] ) ); ?>"><?php echo esc_html( $c['phone'] ); ?></a></li>
                    <li><a href="mailto:<?php echo esc_attr( $c['email'] ); ?>"><?php echo esc_html( $c['email'] ); ?></a></li>
                </ul>
            </div>
            <div class="col">
                <h4>Ωράριο</h4>
                <ul>
                    <?php foreach ( explode( "\n", $c['hours'] ) as $line ) : ?>
                        <li><?php echo esc_html( $line ); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <div class="legal">
            <span>© <?php echo esc_html( date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. Με την επιφύλαξη παντός δικαιώματος.</span>
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
