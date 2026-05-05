<?php
/**
 * FAQ page (Συχνές Ερωτήσεις).
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
$h = mourtzilaki_page_hero();

$faqs = get_posts( array(
    'post_type'      => 'mz_faq',
    'posts_per_page' => -1,
    'orderby'        => 'menu_order date',
    'order'          => 'ASC',
) );

$grouped = array();
foreach ( $faqs as $f ) {
    $cat = function_exists( 'get_field' ) ? (string) get_field( 'category', $f->ID ) : '';
    if ( '' === $cat ) { $cat = 'Γενικά'; }
    $grouped[ $cat ][] = $f;
}
?>

<section class="page-header">
    <div class="container container-narrow">
        <span class="eyebrow"><?php echo esc_html( ! empty( $h['eyebrow'] ) ? $h['eyebrow'] : 'Συχνές ερωτήσεις' ); ?></span>
        <h1 class="h-1 mt-2"><?php echo esc_html( ! empty( $h['title'] ) ? $h['title'] : 'Απαντήσεις σε όσα μας ρωτούν συχνότερα.' ); ?></h1>
        <p class="lead"><?php echo esc_html( ! empty( $h['lead'] ) ? $h['lead'] : 'Συγκεντρώσαμε τις πιο συχνές ερωτήσεις που λαμβάνουμε από πελάτες. Αν δεν βρείτε την απάντηση που ψάχνετε, επικοινωνήστε μαζί μας — απαντάμε εντός 24 ωρών.' ); ?></p>
    </div>
</section>

<section class="section section-tight">
    <div class="container container-narrow">
        <?php if ( ! empty( $grouped ) ) : ?>
            <?php foreach ( $grouped as $cat => $items ) : ?>
                <div class="faq-block reveal reveal-up">
                    <h2 class="h-3 faq-cat"><?php echo esc_html( $cat ); ?></h2>
                    <div class="faq-list">
                        <?php foreach ( $items as $f ) : ?>
                            <details class="faq-item">
                                <summary>
                                    <span class="faq-q"><?php echo esc_html( get_the_title( $f ) ); ?></span>
                                    <span class="faq-icon" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
                                    </span>
                                </summary>
                                <div class="faq-a">
                                    <?php echo function_exists( 'get_field' ) ? wpautop( (string) get_field( 'answer', $f->ID ) ) : ''; ?>
                                </div>
                            </details>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <p class="lead muted text-center">Δεν έχουν προστεθεί ακόμη ερωτήσεις.</p>
        <?php endif; ?>
    </div>
</section>

<section class="cta-band">
    <div class="container row-split">
        <h2 class="h-2 reveal reveal-left">Δεν βρήκατε αυτό που ψάχνατε;</h2>
        <div class="reveal reveal-right">
            <p>Επικοινωνήστε μαζί μας με τη συγκεκριμένη ερώτησή σας. Σας απαντάμε σε 24 ώρες εργάσιμων ημερών.</p>
            <p class="mt-4"><a class="btn btn-primary" href="<?php echo esc_url( mourtzilaki_page_url( 'contact' ) ); ?>">Επικοινωνία <span class="arrow">→</span></a></p>
        </div>
    </div>
</section>

<?php get_footer(); ?>
