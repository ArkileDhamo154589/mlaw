<?php
/**
 * About page (Το γραφείο).
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
$h = mourtzilaki_page_hero();

$pid = get_queried_object_id();
$g = function ( $field, $fallback = '' ) use ( $pid ) {
    if ( ! function_exists( 'get_field' ) ) { return $fallback; }
    $v = (string) get_field( $field, $pid );
    return $v !== '' ? $v : $fallback;
};
$values = mourtzilaki_parse_lines( $g( 'about_values' ) );
?>

<section class="page-header">
    <div class="container container-narrow">
        <span class="eyebrow"><?php echo esc_html( ! empty( $h['eyebrow'] ) ? $h['eyebrow'] : 'Το γραφείο' ); ?></span>
        <h1 class="h-1 mt-2"><?php echo esc_html( ! empty( $h['title'] ) ? $h['title'] : 'Δίκαιο που σέβεται τον άνθρωπο και την επιχείρηση.' ); ?></h1>
        <p class="lead"><?php echo esc_html( ! empty( $h['lead'] ) ? $h['lead'] : 'Από το 2005, χτίζουμε ένα γραφείο που συνδυάζει τη νομική ακρίβεια με την προσωπική σχέση εμπιστοσύνης.' ); ?></p>
    </div>
</section>

<section class="section section-tight">
    <div class="container container-narrow stack stack-xl">
        <div class="reveal reveal-up">
            <h2 class="h-3"><?php echo esc_html( $g( 'about_story_title', 'Η ιστορία μας' ) ); ?></h2>
            <p class="mt-2"><?php echo esc_html( $g( 'about_story_text' ) ); ?></p>
        </div>

        <div class="reveal reveal-up">
            <h2 class="h-3"><?php echo esc_html( $g( 'about_mission_title', 'Η αποστολή μας' ) ); ?></h2>
            <p class="mt-2"><?php echo esc_html( $g( 'about_mission_text' ) ); ?></p>
        </div>

        <?php if ( ! empty( $values ) ) : ?>
            <hr class="divider">
            <div class="grid grid-2">
                <?php foreach ( $values as $row ) :
                    $title = $row[0] ?? '';
                    $text  = $row[1] ?? '';
                    if ( '' === $title ) continue;
                ?>
                    <div class="reveal reveal-up">
                        <span class="eyebrow">Αξίες</span>
                        <h3 class="h-3 mt-2"><?php echo esc_html( $title ); ?></h3>
                        <p class="mt-2"><?php echo esc_html( $text ); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="cta-band">
    <div class="container row-split">
        <h2 class="h-2 reveal reveal-left">Γνωρίστε την ομάδα μας.</h2>
        <div class="reveal reveal-right">
            <p>Οι δικηγόροι του γραφείου συνδυάζουν εξειδίκευση και εμπειρία σε διαφορετικούς τομείς δικαίου.</p>
            <p class="mt-4"><a class="btn btn-primary" href="<?php echo esc_url( mourtzilaki_page_url( 'team' ) ); ?>">Δείτε τους δικηγόρους <span class="arrow">→</span></a></p>
        </div>
    </div>
</section>

<?php get_footer(); ?>
