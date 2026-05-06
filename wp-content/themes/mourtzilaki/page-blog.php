<?php
/**
 * Blog page (Άρθρα).
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();

$paged = max( 1, get_query_var( 'paged' ) ? get_query_var( 'paged' ) : ( get_query_var( 'page' ) ?: 1 ) );
$h     = mourtzilaki_page_hero();

// Featured post: latest published post (only on first page).
$featured_id = 0;
if ( 1 === (int) $paged ) {
    $featured = get_posts( array( 'post_type' => 'post', 'posts_per_page' => 1, 'fields' => 'ids' ) );
    if ( ! empty( $featured ) ) { $featured_id = (int) $featured[0]; }
}

$exclude = $featured_id ? array( $featured_id ) : array();
$query = new WP_Query( array(
    'post_type'           => 'post',
    'posts_per_page'      => 6,
    'paged'               => $paged,
    'post__not_in'        => $exclude,
    'ignore_sticky_posts' => true,
) );

$categories = get_categories( array( 'hide_empty' => true ) );
?>

<section class="page-header">
    <div class="container container-narrow">
        <span class="eyebrow"><?php echo esc_html( ! empty( $h['eyebrow'] ) ? $h['eyebrow'] : 'Άρθρα' ); ?></span>
        <h1 class="h-1 mt-2"><?php echo esc_html( ! empty( $h['title'] ) ? $h['title'] : 'Νομικές αναλύσεις & ενημερώσεις.' ); ?></h1>
        <p class="lead"><?php echo ! empty( $h['lead'] ) ? mourtzilaki_field_inline( $h['lead'] ) : esc_html( 'Παρακολουθούμε τις εξελίξεις σε νομοθεσία και νομολογία και μοιραζόμαστε αναλύσεις σε κατανοητή γλώσσα, χωρίς περιττή ορολογία.' ); ?></p>
    </div>
</section>

<?php if ( ! empty( $categories ) && count( $categories ) > 1 ) : ?>
<section class="section-tight">
    <div class="container">
        <ul class="cat-bar reveal reveal-up">
            <li><a class="<?php echo ! is_category() ? 'is-active' : ''; ?>" href="<?php echo esc_url( mourtzilaki_page_url( 'blog' ) ); ?>">Όλα</a></li>
            <?php foreach ( $categories as $cat ) : ?>
                <li><a class="<?php echo ( is_category() && get_queried_object_id() === $cat->term_id ) ? 'is-active' : ''; ?>" href="<?php echo esc_url( get_category_link( $cat ) ); ?>"><?php echo esc_html( $cat->name ); ?></a></li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>
<?php endif; ?>

<?php if ( $featured_id ) :
    $fp = get_post( $featured_id );
    setup_postdata( $fp );
    $thumb = mourtzilaki_post_image_url( $fp->ID, 'large' );
    ?>
    <section class="section-tight">
        <div class="container">
            <a class="blog-featured reveal reveal-up" href="<?php echo esc_url( get_permalink( $fp ) ); ?>">
                <div class="blog-featured-media<?php echo $thumb ? '' : ' no-image'; ?>" <?php echo $thumb ? 'style="background-image:url(' . esc_url( $thumb ) . ');"' : ''; ?>>
                    <?php if ( ! $thumb ) : ?>
                        <span class="ml-init">M·L</span>
                    <?php endif; ?>
                </div>
                <div class="blog-featured-body">
                    <span class="badge">Επιλεγμένο</span>
                    <span class="meta">
                        <?php echo esc_html( get_the_date( 'd.m.Y', $fp ) ); ?>
                        <?php
                        $cats = get_the_category( $fp->ID );
                        if ( ! empty( $cats ) ) {
                            echo ' · ' . esc_html( $cats[0]->name );
                        }
                        ?>
                    </span>
                    <h2 class="h-2 mt-2"><?php echo esc_html( get_the_title( $fp ) ); ?></h2>
                    <p class="lead mt-4"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 38, '…' ) ); ?></p>
                    <span class="more">Διαβάστε ολόκληρο το άρθρο →</span>
                </div>
            </a>
        </div>
    </section>
    <?php wp_reset_postdata(); endif; ?>

<section class="section section-soft">
    <div class="container">
        <div class="section-head reveal reveal-up">
            <span class="eyebrow">Όλα τα άρθρα</span>
            <h2 class="h-2 mt-2">Πρόσφατες δημοσιεύσεις.</h2>
        </div>

        <?php if ( $query->have_posts() ) : ?>
            <div class="grid grid-3">
                <?php $i = 0; while ( $query->have_posts() ) : $query->the_post(); $i++;
                    $thumb = mourtzilaki_post_image_url( null, 'medium_large' );
                ?>
                    <a class="article-card article-card-rich reveal reveal-up reveal-d<?php echo (int) min( $i, 6 ); ?>" href="<?php the_permalink(); ?>">
                        <div class="article-thumb" style="background-image:url(<?php echo esc_url( $thumb ); ?>);"></div>
                        <div class="article-body">
                            <span class="meta"><?php echo esc_html( get_the_date( 'd.m.Y' ) ); ?></span>
                            <h3><?php the_title(); ?></h3>
                            <p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 22, '…' ) ); ?></p>
                            <span class="more">Διαβάστε →</span>
                        </div>
                    </a>
                <?php endwhile; ?>
            </div>

            <?php
            $links = paginate_links( array(
                'total'     => $query->max_num_pages,
                'current'   => $paged,
                'mid_size'  => 1,
                'prev_text' => '←',
                'next_text' => '→',
                'type'      => 'array',
            ) );
            if ( $links ) : ?>
                <nav class="pagination mt-6" aria-label="Σελίδες">
                    <?php foreach ( $links as $link ) : ?>
                        <span class="page-link"><?php echo wp_kses_post( $link ); ?></span>
                    <?php endforeach; ?>
                </nav>
            <?php endif; ?>
        <?php else : ?>
            <p class="lead text-center muted">Δεν έχουν δημοσιευτεί ακόμη άρθρα.</p>
        <?php endif; wp_reset_postdata(); ?>
    </div>
</section>

<section class="section">
    <div class="container container-narrow">
        <div class="newsletter-band reveal reveal-up">
            <div class="nb-text">
                <span class="eyebrow">Newsletter</span>
                <h2 class="h-3 mt-2">Λάβετε τις σημαντικές νομικές εξελίξεις στο email σας.</h2>
                <p class="muted mt-2">Μία φορά τον μήνα. Επιλεγμένες αναλύσεις. Καμία διαφήμιση.</p>
            </div>
            <form class="nb-form" onsubmit="return false;" aria-label="Εγγραφή newsletter">
                <input type="email" placeholder="Το email σας" aria-label="Email" required>
                <button class="btn btn-primary" type="submit">Εγγραφή <span class="arrow">→</span></button>
            </form>
        </div>
    </div>
</section>

<section class="cta-band">
    <div class="container row-split">
        <h2 class="h-2 reveal reveal-left">Έχετε νομικό ερώτημα που απαντά κάποιο άρθρο;</h2>
        <div class="reveal reveal-right">
            <p>Ας το συζητήσουμε στο πλαίσιο ενός εμπιστευτικού ραντεβού. Σας απαντάμε σε 24 ώρες εργάσιμων ημερών.</p>
            <p class="mt-4"><a class="btn btn-primary" href="<?php echo esc_url( mourtzilaki_page_url( 'contact' ) ); ?>">Επικοινωνία <span class="arrow">→</span></a></p>
        </div>
    </div>
</section>

<?php get_footer(); ?>
