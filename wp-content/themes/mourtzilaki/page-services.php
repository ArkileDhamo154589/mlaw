<?php
/**
 * Services page (Τομείς εξειδίκευσης).
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();

$services = get_posts( array(
    'post_type'      => 'mz_service',
    'posts_per_page' => -1,
    'orderby'        => 'menu_order date',
    'order'          => 'ASC',
) );
$h = mourtzilaki_page_hero();
?>

<section class="page-header">
    <div class="container container-narrow">
        <span class="eyebrow"><?php echo esc_html( ! empty( $h['eyebrow'] ) ? $h['eyebrow'] : 'Τομείς εξειδίκευσης' ); ?></span>
        <h1 class="h-1 mt-2"><?php echo esc_html( ! empty( $h['title'] ) ? $h['title'] : 'Εξειδίκευση εκεί που πραγματικά μετράει.' ); ?></h1>
        <p class="lead"><?php echo ! empty( $h['lead'] ) ? mourtzilaki_field_inline( $h['lead'] ) : esc_html( 'Καλύπτουμε τους τομείς δικαίου που συναντούν συχνότερα ιδιώτες και επιχειρήσεις στην Ελλάδα. Σε κάθε υπόθεση, ορίζουμε καθαρή στρατηγική, ρεαλιστικά χρονοδιαγράμματα και διαφανές κόστος.' ); ?></p>
    </div>
</section>

<section class="section section-tight">
    <div class="container">
        <div class="services-tiles">
            <?php foreach ( $services as $i => $svc ) :
                $short = function_exists( 'get_field' ) ? (string) get_field( 'description', $svc->ID ) : '';
            ?>
                <a class="svc-tile reveal reveal-up reveal-d<?php echo (int) min( ( $i % 4 ) + 1, 6 ); ?>" href="<?php echo esc_url( get_permalink( $svc ) ); ?>">
                    <span class="svc-tile-icon" aria-hidden="true"><?php echo mourtzilaki_service_icon( get_the_title( $svc ) ); ?></span>
                    <h3 class="svc-tile-title"><?php echo esc_html( get_the_title( $svc ) ); ?></h3>
                    <p class="svc-tile-desc"><?php echo esc_html( wp_trim_words( wp_strip_all_tags( $short ), 22, '…' ) ); ?></p>
                    <span class="svc-tile-more">Μάθετε περισσότερα <span class="arrow">→</span></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section-soft">
    <div class="container row-split">
        <div class="reveal reveal-left">
            <span class="eyebrow">Διαδικασία</span>
            <h2 class="h-2 mt-2">Πώς δουλεύουμε σε κάθε υπόθεση.</h2>
        </div>
        <div class="stack stack-xl reveal reveal-right">
            <div>
                <span class="eyebrow">01</span>
                <h3 class="h-3 mt-2">Αρχική συνάντηση</h3>
                <p class="mt-2">Συζητάμε αναλυτικά την υπόθεση, εντοπίζουμε κρίσιμα σημεία και απαντάμε σε ό,τι σας απασχολεί.</p>
            </div>
            <div>
                <span class="eyebrow">02</span>
                <h3 class="h-3 mt-2">Νομική ανάλυση</h3>
                <p class="mt-2">Μελετάμε τη νομοθεσία, τη νομολογία και τα συγκεκριμένα δεδομένα της υπόθεσης πριν προτείνουμε στρατηγική.</p>
            </div>
            <div>
                <span class="eyebrow">03</span>
                <h3 class="h-3 mt-2">Σχέδιο ενεργειών</h3>
                <p class="mt-2">Σας παρουσιάζουμε καθαρά τις διαθέσιμες επιλογές, με τα πλεονεκτήματα, τους κινδύνους και το αναμενόμενο κόστος καθεμίας.</p>
            </div>
            <div>
                <span class="eyebrow">04</span>
                <h3 class="h-3 mt-2">Εκτέλεση & ενημέρωση</h3>
                <p class="mt-2">Αναλαμβάνουμε εξ ολοκλήρου τη διαχείριση και σας ενημερώνουμε σε κάθε σημαντική εξέλιξη.</p>
            </div>
        </div>
    </div>
</section>

<section class="cta-band">
    <div class="container row-split">
        <h2 class="h-2 reveal reveal-left">Δεν είστε σίγουρος αν η υπόθεσή σας εμπίπτει εδώ;</h2>
        <div class="reveal reveal-right">
            <p>Επικοινωνήστε μαζί μας για να αξιολογήσουμε την υπόθεση και να σας κατευθύνουμε σωστά.</p>
            <p class="mt-4"><a class="btn btn-primary" href="<?php echo esc_url( mourtzilaki_page_url( 'contact' ) ); ?>">Επικοινωνία <span class="arrow">→</span></a></p>
        </div>
    </div>
</section>

<?php get_footer(); ?>
