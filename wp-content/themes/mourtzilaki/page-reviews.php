<?php
/**
 * Reviews / Συστάσεις page.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
$h = mourtzilaki_page_hero();
$testimonials = mourtzilaki_get_testimonials();
?>

<section class="page-header">
    <div class="container container-narrow">
        <span class="eyebrow"><?php echo esc_html( ! empty( $h['eyebrow'] ) ? $h['eyebrow'] : 'Συστάσεις' ); ?></span>
        <h1 class="h-1 mt-2"><?php echo esc_html( ! empty( $h['title'] ) ? $h['title'] : 'Τι λένε όσοι μας εμπιστεύτηκαν.' ); ?></h1>
        <p class="lead"><?php echo esc_html( ! empty( $h['lead'] ) ? $h['lead'] : 'Συστάσεις πελατών που μας εμπιστεύθηκαν τις υποθέσεις τους — από ιδιώτες μέχρι μεσαίες και μεγάλες επιχειρήσεις.' ); ?></p>
    </div>
</section>

<section class="section section-tight">
    <div class="container">
        <?php if ( ! empty( $testimonials ) ) : ?>
            <div class="reviews-grid">
                <?php foreach ( $testimonials as $i => $t ) : ?>
                    <article class="review-card reveal reveal-up reveal-d<?php echo (int) min( ( $i % 4 ) + 1, 6 ); ?>">
                        <div class="review-stars" aria-label="5 στα 5">
                            <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
                        </div>
                        <blockquote class="review-quote"><?php echo esc_html( $t['quote'] ); ?></blockquote>
                        <footer class="review-attr">
                            <div class="review-name"><?php echo esc_html( $t['name'] ); ?></div>
                            <?php if ( ! empty( $t['role'] ) ) : ?>
                                <div class="review-role"><?php echo esc_html( $t['role'] ); ?></div>
                            <?php endif; ?>
                        </footer>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <p class="lead muted text-center">Δεν έχουν προστεθεί ακόμη συστάσεις.</p>
        <?php endif; ?>
    </div>
</section>

<section class="section section-soft">
    <div class="container container-narrow text-center">
        <span class="eyebrow" style="justify-content:center">Δικηγορική Δεοντολογία</span>
        <h2 class="h-3 mt-2">Όλες οι συστάσεις είναι αυθεντικές.</h2>
        <p class="lead mt-4">Δημοσιεύονται με ρητή έγγραφη συναίνεση των πελατών. Σύμφωνα με τον Κώδικα Δικηγορικής Δεοντολογίας, αποφεύγουμε διαφημιστικές υπερβολές και δηλώσεις που θα μπορούσαν να εκληφθούν ως υποσχέσεις αποτελέσματος.</p>
    </div>
</section>

<section class="cta-band">
    <div class="container row-split">
        <h2 class="h-2 reveal reveal-left">Έχετε υπόθεση;</h2>
        <div class="reveal reveal-right">
            <p>Κλείστε ένα αρχικό, εμπιστευτικό ραντεβού. Θα μελετήσουμε τα δεδομένα και θα σας προτείνουμε καθαρή στρατηγική.</p>
            <p class="mt-4"><a class="btn btn-primary" href="<?php echo esc_url( mourtzilaki_page_url( 'contact' ) ); ?>">Επικοινωνία <span class="arrow">→</span></a></p>
        </div>
    </div>
</section>

<?php get_footer(); ?>
