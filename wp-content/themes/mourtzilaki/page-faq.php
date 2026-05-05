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

$grouped    = array();
$cat_counts = array();
foreach ( $faqs as $f ) {
    $cat = function_exists( 'get_field' ) ? trim( (string) get_field( 'category', $f->ID ) ) : '';
    if ( '' === $cat ) { $cat = 'Γενικά'; }
    $grouped[ $cat ][] = $f;
    $cat_counts[ $cat ] = ( isset( $cat_counts[ $cat ] ) ? $cat_counts[ $cat ] : 0 ) + 1;
}

$total_q = count( $faqs );
$total_c = count( $cat_counts );
?>

<section class="page-header">
    <div class="container container-narrow">
        <span class="eyebrow"><?php echo esc_html( ! empty( $h['eyebrow'] ) ? $h['eyebrow'] : 'Συχνές ερωτήσεις' ); ?></span>
        <h1 class="h-1 mt-2"><?php echo esc_html( ! empty( $h['title'] ) ? $h['title'] : 'Απαντήσεις σε όσα μας ρωτούν συχνότερα.' ); ?></h1>
        <p class="lead"><?php echo esc_html( ! empty( $h['lead'] ) ? $h['lead'] : 'Συγκεντρώσαμε τις πιο συχνές ερωτήσεις που λαμβάνουμε από πελάτες. Αν δεν βρείτε την απάντηση που ψάχνετε, επικοινωνήστε μαζί μας — απαντάμε εντός 24 ωρών.' ); ?></p>

        <div class="faq-stats reveal reveal-up">
            <div><span class="num"><?php echo esc_html( $total_q ); ?></span><span class="lab">ερωτήσεις</span></div>
            <div><span class="num"><?php echo esc_html( $total_c ); ?></span><span class="lab">κατηγορίες</span></div>
            <div><span class="num">24h</span><span class="lab">απόκριση</span></div>
        </div>
    </div>
</section>

<section class="section-tight">
    <div class="container container-narrow">
        <div class="faq-toolbar reveal reveal-up">
            <div class="faq-search">
                <span class="faq-search-ic" aria-hidden="true">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
                </span>
                <input type="search" id="faq-search" placeholder="Αναζήτηση στις ερωτήσεις..." aria-label="Αναζήτηση FAQ">
                <button type="button" class="faq-clear" id="faq-clear" aria-label="Καθαρισμός">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>
                </button>
            </div>

            <ul class="faq-pills" role="tablist">
                <li><button class="faq-pill is-active" data-cat="*" type="button">Όλες<span class="pill-c"><?php echo esc_html( $total_q ); ?></span></button></li>
                <?php foreach ( $cat_counts as $cat => $count ) : ?>
                    <li><button class="faq-pill" data-cat="<?php echo esc_attr( $cat ); ?>" type="button"><?php echo esc_html( $cat ); ?><span class="pill-c"><?php echo esc_html( $count ); ?></span></button></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div id="faq-empty" class="faq-empty" hidden>
            <span class="faq-empty-ic" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
            </span>
            <p>Δεν βρήκαμε ερωτήσεις που να ταιριάζουν.</p>
            <a class="btn btn-ghost" href="<?php echo esc_url( mourtzilaki_page_url( 'contact' ) ); ?>">Ρωτήστε μας απευθείας <span class="arrow">→</span></a>
        </div>

        <?php if ( ! empty( $grouped ) ) : ?>
            <div id="faq-content">
                <?php foreach ( $grouped as $cat => $items ) : ?>
                    <div class="faq-block reveal reveal-up" data-cat="<?php echo esc_attr( $cat ); ?>">
                        <h2 class="faq-cat">
                            <?php echo esc_html( $cat ); ?>
                            <span class="faq-cat-count"><?php echo esc_html( count( $items ) ); ?> ερωτήσεις</span>
                        </h2>
                        <div class="faq-list">
                            <?php foreach ( $items as $f ) :
                                $q = get_the_title( $f );
                                $a = function_exists( 'get_field' ) ? (string) get_field( 'answer', $f->ID ) : '';
                            ?>
                                <details class="faq-item" data-q="<?php echo esc_attr( mb_strtolower( $q . ' ' . wp_strip_all_tags( $a ) ) ); ?>">
                                    <summary>
                                        <span class="faq-q"><?php echo esc_html( $q ); ?></span>
                                        <span class="faq-icon" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
                                        </span>
                                    </summary>
                                    <div class="faq-a">
                                        <?php echo wpautop( $a ); ?>
                                    </div>
                                </details>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="section section-soft">
    <div class="container container-narrow">
        <div class="faq-still reveal reveal-up">
            <div>
                <span class="eyebrow">Ακόμη ερωτήματα;</span>
                <h2 class="h-2 mt-2">Δεν βρήκατε αυτό που ψάχνατε;</h2>
                <p class="lead mt-4">Ρωτήστε μας απευθείας. Στις περισσότερες περιπτώσεις, ένα σύντομο email αρκεί για να σας προσανατολίσουμε σωστά.</p>
            </div>
            <div class="faq-still-actions">
                <a class="btn btn-primary" href="<?php echo esc_url( mourtzilaki_page_url( 'contact' ) ); ?>">Επικοινωνία <span class="arrow">→</span></a>
                <a class="btn btn-ghost" href="<?php echo esc_url( mourtzilaki_page_url( 'glossary' ) ); ?>">Νομικό λεξικό</a>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
