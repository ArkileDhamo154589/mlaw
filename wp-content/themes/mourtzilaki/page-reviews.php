<?php
/**
 * Reviews / Συστάσεις page.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
$h            = mourtzilaki_page_hero();
$testimonials = mourtzilaki_get_testimonials();
$total        = count( $testimonials );

$sent  = isset( $_GET['review_sent'] ) ? sanitize_text_field( wp_unslash( $_GET['review_sent'] ) ) : '';
$error = isset( $_GET['review_error'] ) ? sanitize_text_field( wp_unslash( $_GET['review_error'] ) ) : '';
?>

<section class="page-header">
    <div class="container container-narrow">
        <span class="eyebrow"><?php echo esc_html( ! empty( $h['eyebrow'] ) ? $h['eyebrow'] : 'Συστάσεις' ); ?></span>
        <h1 class="h-1 mt-2"><?php echo esc_html( ! empty( $h['title'] ) ? $h['title'] : 'Τι λένε όσοι μας εμπιστεύτηκαν.' ); ?></h1>
        <p class="lead"><?php echo esc_html( ! empty( $h['lead'] ) ? $h['lead'] : 'Συστάσεις πελατών που μας εμπιστεύθηκαν τις υποθέσεις τους — από ιδιώτες μέχρι μεσαίες και μεγάλες επιχειρήσεις. Όλες δημοσιεύονται με ρητή έγγραφη συναίνεση.' ); ?></p>

        <div class="reviews-stats reveal reveal-up">
            <div class="rs-item">
                <div class="rs-stars" aria-label="5 στα 5">★★★★★</div>
                <div class="rs-lab">5,0 μέσος όρος</div>
            </div>
            <div class="rs-sep" aria-hidden="true"></div>
            <div class="rs-item">
                <div class="rs-num"><?php echo esc_html( $total ); ?></div>
                <div class="rs-lab">συστάσεις πελατών</div>
            </div>
            <div class="rs-sep" aria-hidden="true"></div>
            <div class="rs-item">
                <div class="rs-num">100%</div>
                <div class="rs-lab">verified αξιολογήσεις</div>
            </div>
        </div>
    </div>
</section>

<?php if ( ! empty( $testimonials ) ) : ?>
<section class="section section-tight">
    <div class="container">
        <div class="reviews-slick reveal reveal-up">
            <?php foreach ( $testimonials as $t ) : ?>
                <div class="rv-slide">
                    <article class="review-card">
                        <div class="review-stars" aria-label="5 στα 5">
                            <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
                        </div>
                        <blockquote class="review-quote"><?php echo esc_html( $t['quote'] ); ?></blockquote>
                        <footer class="review-attr">
                            <div class="review-name"><?php echo esc_html( $t['name'] ); ?></div>
                            <?php if ( ! empty( $t['role'] ) ) : ?>
                                <div class="review-role"><?php echo wp_kses( $t['role'], array() ); ?></div>
                            <?php endif; ?>
                        </footer>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php else : ?>
<section class="section section-tight">
    <div class="container container-narrow">
        <p class="lead muted text-center">Δεν έχουν δημοσιευτεί ακόμη συστάσεις.</p>
    </div>
</section>
<?php endif; ?>

<section id="review-form" class="section section-soft">
    <div class="container container-narrow">
        <div class="review-submit-wrap reveal reveal-up">
            <div class="rsw-head">
                <span class="eyebrow">Στείλτε αξιολόγηση</span>
                <h2 class="h-2 mt-2">Μοιραστείτε την εμπειρία σας</h2>
                <p class="lead mt-4">Αν συνεργαστήκαμε, θα χαρούμε να ακούσουμε τη γνώμη σας. Κάθε αξιολόγηση εξετάζεται προσεκτικά πριν τη δημοσίευση.</p>
            </div>

            <?php if ( '1' === $sent ) : ?>
                <div class="rsw-msg rsw-msg-success">
                    <strong>Ευχαριστούμε.</strong> Λάβαμε την αξιολόγησή σας. Θα δημοσιευτεί μετά την έγκρισή της — συνήθως σε λίγες ώρες.
                </div>
            <?php elseif ( $error === 'missing' ) : ?>
                <div class="rsw-msg rsw-msg-error">
                    Συμπληρώστε όνομα, κείμενο και αποδοχή πολιτικής απορρήτου.
                </div>
            <?php elseif ( $error ) : ?>
                <div class="rsw-msg rsw-msg-error">
                    Παρουσιάστηκε ένα σφάλμα. Δοκιμάστε ξανά ή στείλτε email στο <a href="mailto:<?php echo esc_attr( mourtzilaki_get_contact_info()['email'] ); ?>"><?php echo esc_html( mourtzilaki_get_contact_info()['email'] ); ?></a>.
                </div>
            <?php endif; ?>

            <form class="rsw-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                <input type="hidden" name="action" value="mourtzilaki_submit_review">
                <?php wp_nonce_field( 'mourtzilaki_review', '_review_nonce' ); ?>
                <input type="text" name="website" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px;height:0;width:0;opacity:0;" aria-hidden="true">

                <div class="rsw-row">
                    <div class="rsw-field">
                        <label for="rv-name">Όνομα *</label>
                        <input type="text" id="rv-name" name="reviewer_name" required maxlength="80" placeholder="Πώς να εμφανίζεται η αξιολόγηση">
                    </div>
                    <div class="rsw-field">
                        <label for="rv-role">Ιδιότητα (προαιρετικά)</label>
                        <input type="text" id="rv-role" name="reviewer_role" maxlength="120" placeholder="π.χ. Πελάτης, Διευθύνων Σύμβουλος">
                    </div>
                </div>

                <div class="rsw-field">
                    <label for="rv-email">Email (δεν δημοσιεύεται)</label>
                    <input type="email" id="rv-email" name="reviewer_email" maxlength="120" placeholder="name@example.com">
                </div>

                <div class="rsw-field">
                    <label for="rv-quote">Η εμπειρία σας *</label>
                    <textarea id="rv-quote" name="reviewer_quote" required maxlength="1500" rows="6" placeholder="Περιγράψτε σύντομα τη συνεργασία μαζί μας..."></textarea>
                    <span class="rsw-counter" aria-live="polite">0 / 1500</span>
                </div>

                <label class="rsw-gdpr">
                    <input type="checkbox" name="gdpr" required>
                    <span>Συμφωνώ με την επεξεργασία των στοιχείων μου σύμφωνα με την <a href="<?php echo esc_url( mourtzilaki_page_url( 'privacy' ) ); ?>">Πολιτική Απορρήτου</a> και επιβεβαιώνω ότι η αξιολόγηση είναι αυθεντική.</span>
                </label>

                <div class="rsw-submit">
                    <button class="btn btn-primary" type="submit">Υποβολή αξιολόγησης <span class="arrow">→</span></button>
                </div>

                <p class="rsw-note">Η αξιολόγησή σας θα ελεγχθεί πριν τη δημοσίευση. Σύμφωνα με τον Κώδικα Δικηγορικής Δεοντολογίας, οι αξιολογήσεις πρέπει να είναι αυθεντικές και χωρίς διαφημιστικές υπερβολές.</p>
            </form>
        </div>
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
