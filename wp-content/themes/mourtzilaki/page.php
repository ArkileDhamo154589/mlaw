<?php
/**
 * Default page template (fallback for any page without a more specific template).
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();

while ( have_posts() ) : the_post(); ?>

<section class="page-header">
    <div class="container container-narrow">
        <h1 class="h-1"><?php the_title(); ?></h1>
    </div>
</section>

<section class="section section-tight">
    <div class="container container-narrow post-content">
        <?php the_content(); ?>
    </div>
</section>

<?php endwhile; get_footer(); ?>
