<?php
/**
 * Services page (Τομείς εξειδίκευσης) — ACF-driven sections.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();

$services = get_posts( array(
    'post_type'      => 'mz_service',
    'posts_per_page' => -1,
    'orderby'        => 'menu_order date',
    'order'          => 'ASC',
) );
$h   = mourtzilaki_page_hero();
$pid = get_queried_object_id();
$g = function ( $k, $d = '' ) use ( $pid ) {
    if ( ! function_exists( 'get_field' ) ) { return $d; }
    $v = (string) get_field( $k, $pid );
    return '' !== trim( $v ) ? $v : $d;
};

$proc_default = "01 | Αρχική συνάντηση | Συζητάμε αναλυτικά την υπόθεση, εντοπίζουμε κρίσιμα σημεία και απαντάμε σε ό,τι σας απασχολεί.\n" .
                "02 | Νομική ανάλυση | Μελετάμε τη νομοθεσία, τη νομολογία και τα συγκεκριμένα δεδομένα της υπόθεσης πριν προτείνουμε στρατηγική.\n" .
                "03 | Σχέδιο ενεργειών | Σας παρουσιάζουμε καθαρά τις διαθέσιμες επιλογές, με τα πλεονεκτήματα, τους κινδύνους και το αναμενόμενο κόστος καθεμίας.\n" .
                "04 | Εκτέλεση & ενημέρωση | Αναλαμβάνουμε εξ ολοκλήρου τη διαχείριση και σας ενημερώνουμε σε κάθε σημαντική εξέλιξη.";
$steps = mourtzilaki_parse_lines( $g( 'services_proc_steps', $proc_default ) );
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

<?php if ( ! empty( $steps ) ) : ?>
<section class="section section-soft">
    <div class="container row-split">
        <div class="reveal reveal-left">
            <span class="eyebrow"><?php echo esc_html( $g( 'services_proc_eyebrow', 'Διαδικασία' ) ); ?></span>
            <h2 class="h-2 mt-2"><?php echo esc_html( $g( 'services_proc_title', 'Πώς δουλεύουμε σε κάθε υπόθεση.' ) ); ?></h2>
        </div>
        <div class="stack stack-xl reveal reveal-right">
            <?php foreach ( $steps as $row ) :
                $num = $row[0] ?? '';
                $tt  = $row[1] ?? '';
                $tx  = $row[2] ?? '';
                if ( '' === $tt ) { continue; }
            ?>
                <div>
                    <span class="eyebrow"><?php echo esc_html( $num ); ?></span>
                    <h3 class="h-3 mt-2"><?php echo esc_html( $tt ); ?></h3>
                    <p class="mt-2"><?php echo esc_html( $tx ); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php
$cta_t  = $g( 'services_cta_title', 'Δεν είστε σίγουρος αν η υπόθεσή σας εμπίπτει εδώ;' );
$cta_l  = $g( 'services_cta_lead',  'Επικοινωνήστε μαζί μας για να αξιολογήσουμε την υπόθεση και να σας κατευθύνουμε σωστά.' );
$cta_bl = $g( 'services_cta_btn_label', 'Επικοινωνία' );
$cta_bu = $g( 'services_cta_btn_url',   mourtzilaki_page_url( 'contact' ) );
?>
<section class="cta-band">
    <div class="container row-split">
        <h2 class="h-2 reveal reveal-left"><?php echo esc_html( $cta_t ); ?></h2>
        <div class="reveal reveal-right">
            <p><?php echo mourtzilaki_field_inline( $cta_l ); ?></p>
            <?php if ( $cta_bl ) : ?>
                <p class="mt-4"><a class="btn btn-primary" href="<?php echo esc_url( $cta_bu ); ?>"><?php echo esc_html( $cta_bl ); ?> <span class="arrow">→</span></a></p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php get_footer(); ?>
