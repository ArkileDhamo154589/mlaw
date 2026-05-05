<?php
/**
 * Default index template (fallback for archive / blog index).
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header(); ?>

<section class="page-header">
    <div class="container container-narrow">
        <span class="eyebrow">Άρθρα</span>
        <h1 class="h-1 mt-2"><?php
            if      ( is_search() )   { printf( 'Αναζήτηση: %s', esc_html( get_search_query() ) ); }
            elseif  ( is_category() ) { single_cat_title(); }
            elseif  ( is_tag() )      { single_tag_title(); }
            elseif  ( is_author() )   { the_post(); echo esc_html( get_the_author() ); rewind_posts(); }
            else                      { esc_html_e( 'Όλα τα άρθρα', 'mourtzilaki' ); }
        ?></h1>
    </div>
</section>

<section class="section section-tight">
    <div class="container container-narrow">
        <?php if ( have_posts() ) : ?>
            <?php while ( have_posts() ) : the_post(); ?>
                <a class="post-card" href="<?php the_permalink(); ?>">
                    <span class="meta"><?php echo esc_html( get_the_date( 'd.m.Y' ) ); ?></span>
                    <div>
                        <h3 class="h-3"><?php the_title(); ?></h3>
                        <p class="muted mt-2"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 24, '…' ) ); ?></p>
                    </div>
                    <span class="read">Διαβάστε →</span>
                </a>
            <?php endwhile; ?>

            <div class="text-center mt-6">
                <?php the_posts_pagination( array( 'mid_size' => 1, 'prev_text' => '←', 'next_text' => '→' ) ); ?>
            </div>
        <?php else : ?>
            <p class="lead text-center">Δεν βρέθηκαν αποτελέσματα.</p>
        <?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>
