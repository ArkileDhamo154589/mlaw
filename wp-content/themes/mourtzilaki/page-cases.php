<?php
/**
 * Cases page (Επιλεγμένες υποθέσεις).
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
$h = mourtzilaki_page_hero();

$cases = get_posts( array(
    'post_type'      => 'mz_case',
    'posts_per_page' => -1,
    'orderby'        => 'menu_order date',
    'order'          => 'ASC',
) );

// Build filter list — services that have at least 1 case.
$service_counts = array();
foreach ( $cases as $cs ) {
    $sid = (int) get_field( 'practice_area', $cs->ID );
    if ( $sid ) { $service_counts[ $sid ] = ( $service_counts[ $sid ] ?? 0 ) + 1; }
}
?>

<section class="page-header">
    <div class="container container-narrow">
        <span class="eyebrow"><?php echo esc_html( ! empty( $h['eyebrow'] ) ? $h['eyebrow'] : 'Επιλεγμένες υποθέσεις' ); ?></span>
        <h1 class="h-1 mt-2"><?php echo esc_html( ! empty( $h['title'] ) ? $h['title'] : 'Παραδείγματα από τη δουλειά μας.' ); ?></h1>
        <p class="lead"><?php echo ! empty( $h['lead'] ) ? mourtzilaki_field_inline( $h['lead'] ) : esc_html( 'Επιλεγμένες υποθέσεις που χειριστήκαμε τα τελευταία χρόνια — ανωνυμοποιημένες, ώστε να αναδειχθεί η νομική πρόκληση και το αποτέλεσμα, όχι τα πρόσωπα.' ); ?></p>
    </div>
</section>

<section class="section section-tight">
    <div class="container">
        <?php if ( ! empty( $service_counts ) ) : ?>
        <ul class="cases-filters reveal reveal-up" role="tablist">
            <li><button class="cases-filter is-active" data-filter="*" type="button">Όλες<span class="cf-c"><?php echo (int) count( $cases ); ?></span></button></li>
            <?php foreach ( $service_counts as $sid => $count ) : ?>
                <li><button class="cases-filter" data-filter="svc-<?php echo (int) $sid; ?>" type="button"><?php echo esc_html( get_the_title( $sid ) ); ?><span class="cf-c"><?php echo (int) $count; ?></span></button></li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>

        <?php if ( ! empty( $cases ) ) : ?>
            <div class="cases-grid">
                <?php foreach ( $cases as $i => $cs ) :
                    $svc_id   = (int) get_field( 'practice_area', $cs->ID );
                    $svc_name = $svc_id ? get_the_title( $svc_id ) : '';
                    $year     = (string) get_field( 'year',     $cs->ID );
                    $duration = (string) get_field( 'duration', $cs->ID );
                    $outcome  = (string) get_field( 'outcome',  $cs->ID );
                    $desc     = (string) get_field( 'description', $cs->ID );
                ?>
                    <article class="case-card reveal reveal-up reveal-d<?php echo (int) min( ( $i % 4 ) + 1, 6 ); ?>" data-svc="svc-<?php echo (int) $svc_id; ?>">
                        <header class="case-head">
                            <span class="case-icon" aria-hidden="true"><?php echo $svc_id ? mourtzilaki_service_icon( $svc_name ) : ''; ?></span>
                            <span class="case-meta">
                                <?php if ( $svc_name ) : ?><span class="case-svc"><?php echo esc_html( $svc_name ); ?></span><?php endif; ?>
                                <?php if ( $year ) : ?><span class="case-year"><?php echo esc_html( $year ); ?></span><?php endif; ?>
                            </span>
                        </header>
                        <h3 class="case-title"><?php echo esc_html( get_the_title( $cs ) ); ?></h3>
                        <p class="case-desc"><?php echo mourtzilaki_field_inline( $desc ); ?></p>
                        <div class="case-outcome">
                            <span class="co-lab">Αποτέλεσμα</span>
                            <span class="co-val"><?php echo esc_html( $outcome ); ?></span>
                        </div>
                        <?php if ( $duration ) : ?>
                            <div class="case-foot">
                                <span class="cf-label">Διάρκεια</span>
                                <span class="cf-val"><?php echo esc_html( $duration ); ?></span>
                            </div>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>

            <div class="cases-empty" id="cases-empty" hidden>
                <p>Δεν βρέθηκαν υποθέσεις σε αυτή την κατηγορία.</p>
            </div>

            <div class="cases-disclaimer reveal reveal-up">
                <strong>Σημείωση:</strong> Όλες οι παραπάνω υποθέσεις είναι ανωνυμοποιημένες και παρουσιάζονται μόνο για ενημερωτικούς σκοπούς. Δεν συνιστούν υπόσχεση παρόμοιου αποτελέσματος σε άλλες υποθέσεις. Κάθε νομική υπόθεση κρίνεται με βάση τα δικά της δεδομένα.
            </div>
        <?php else : ?>
            <p class="lead muted text-center">Δεν έχουν προστεθεί ακόμη υποθέσεις.</p>
        <?php endif; ?>
    </div>
</section>

<section class="cta-band">
    <div class="container row-split">
        <h2 class="h-2 reveal reveal-left">Έχετε παρόμοια υπόθεση;</h2>
        <div class="reveal reveal-right">
            <p>Κάθε υπόθεση έχει τα δικά της δεδομένα. Συζητήστε την υπόθεσή σας μαζί μας σε εμπιστευτικό ραντεβού.</p>
            <p class="mt-4"><a class="btn btn-primary" href="<?php echo esc_url( mourtzilaki_page_url( 'contact' ) ); ?>">Επικοινωνία <span class="arrow">→</span></a></p>
        </div>
    </div>
</section>

<?php get_footer(); ?>
