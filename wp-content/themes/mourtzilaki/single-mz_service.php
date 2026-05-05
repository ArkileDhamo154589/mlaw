<?php
/**
 * Single service template (Τομέας Δικαίου).
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();

while ( have_posts() ) : the_post();
    $title       = get_the_title();
    $short       = function_exists( 'get_field' ) ? (string) get_field( 'description', get_the_ID() ) : '';
    $long        = function_exists( 'get_field' ) ? (string) get_field( 'long_description', get_the_ID() ) : '';

    $related = get_posts( array(
        'post_type'      => 'mz_service',
        'posts_per_page' => 4,
        'post__not_in'   => array( get_the_ID() ),
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
    ) );
?>

<section class="single-svc-hero">
    <div class="container">
        <nav class="crumbs reveal reveal-up" aria-label="Breadcrumbs">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Αρχική</a>
            <span aria-hidden="true">/</span>
            <a href="<?php echo esc_url( mourtzilaki_page_url( 'services' ) ); ?>">Τομείς εξειδίκευσης</a>
            <span aria-hidden="true">/</span>
            <span><?php echo esc_html( $title ); ?></span>
        </nav>
        <div class="svc-hero-grid">
            <div class="svc-hero-icon reveal reveal-left" aria-hidden="true">
                <?php echo mourtzilaki_service_icon( $title ); ?>
            </div>
            <div class="svc-hero-text reveal reveal-right">
                <span class="eyebrow">Τομέας δικαίου</span>
                <h1 class="h-1 mt-2"><?php echo esc_html( $title ); ?></h1>
                <?php if ( $short ) : ?>
                    <p class="lead mt-4"><?php echo esc_html( $short ); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<section class="section section-tight">
    <div class="container container-narrow svc-content">
        <?php
        if ( $long ) {
            echo $long; // wysiwyg ACF — already escaped on save
        } else {
            the_content();
        }
        ?>
    </div>
</section>

<?php if ( ! empty( $related ) ) : ?>
<section class="section section-soft">
    <div class="container">
        <div class="section-head reveal reveal-up">
            <span class="eyebrow">Σχετικοί τομείς</span>
            <h2 class="h-2 mt-2">Δείτε επίσης</h2>
        </div>
        <div class="services-tiles">
            <?php foreach ( $related as $r ) :
                $r_short = function_exists( 'get_field' ) ? (string) get_field( 'description', $r->ID ) : '';
            ?>
                <a class="svc-tile reveal reveal-up" href="<?php echo esc_url( get_permalink( $r ) ); ?>">
                    <span class="svc-tile-icon" aria-hidden="true"><?php echo mourtzilaki_service_icon( get_the_title( $r ) ); ?></span>
                    <h3 class="svc-tile-title"><?php echo esc_html( get_the_title( $r ) ); ?></h3>
                    <p class="svc-tile-desc"><?php echo esc_html( wp_trim_words( $r_short, 16, '…' ) ); ?></p>
                    <span class="svc-tile-more">Μάθετε περισσότερα <span class="arrow">→</span></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="cta-band">
    <div class="container row-split">
        <h2 class="h-2 reveal reveal-left">Έχετε υπόθεση σε αυτόν τον τομέα;</h2>
        <div class="reveal reveal-right">
            <p>Κλείστε ένα αρχικό, εμπιστευτικό ραντεβού. Θα μελετήσουμε τα δεδομένα και θα προτείνουμε καθαρή στρατηγική.</p>
            <p class="mt-4"><a class="btn btn-primary" href="<?php echo esc_url( mourtzilaki_page_url( 'contact' ) ); ?>">Επικοινωνία <span class="arrow">→</span></a></p>
        </div>
    </div>
</section>

<?php endwhile; get_footer(); ?>
