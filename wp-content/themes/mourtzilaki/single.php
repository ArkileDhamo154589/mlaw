<?php
/**
 * Single article — magazine layout.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();

while ( have_posts() ) : the_post();
    $post_id      = get_the_ID();
    $thumb        = mourtzilaki_post_image_url( $post_id, 'full' );
    $reading_time = max( 1, ceil( str_word_count( wp_strip_all_tags( get_the_content() ) ) / 200 ) );
    $cats         = get_the_category();
    $cat          = ! empty( $cats ) ? $cats[0] : null;
    $author       = get_the_author();
    $author_id    = (int) get_the_author_meta( 'ID' );
    $member       = mourtzilaki_get_member( 0 );

    // ACF fields
    $f_subt    = function_exists( 'get_field' ) ? (string) get_field( 'article_subtitle', $post_id ) : '';
    $f_pull    = function_exists( 'get_field' ) ? (string) get_field( 'pull_quote',       $post_id ) : '';
    $f_takes   = function_exists( 'get_field' ) ? (string) get_field( 'key_takeaways',    $post_id ) : '';
    $f_svc_id  = function_exists( 'get_field' ) ? (int) get_field( 'related_service',     $post_id ) : 0;
    $f_cta_t   = function_exists( 'get_field' ) ? (string) get_field( 'cta_title',        $post_id ) : '';
    $f_cta_x   = function_exists( 'get_field' ) ? (string) get_field( 'cta_text',         $post_id ) : '';
    $f_disc    = function_exists( 'get_field' ) ? (string) get_field( 'disclaimer',       $post_id ) : '';
    $takeaways = mourtzilaki_parse_lines( $f_takes );

    $related = get_posts( array(
        'post_type'      => 'post',
        'posts_per_page' => 3,
        'post__not_in'   => array( $post_id ),
        'orderby'        => 'date',
        'order'          => 'DESC',
    ) );

    $share_url   = urlencode( get_permalink() );
    $share_title = urlencode( get_the_title() );
?>

<!-- Article hero -->
<section class="article-hero">
    <div class="container container-narrow">
        <nav class="crumbs reveal reveal-up" aria-label="Breadcrumbs">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Αρχική</a>
            <span aria-hidden="true">/</span>
            <a href="<?php echo esc_url( mourtzilaki_page_url( 'blog' ) ); ?>">Άρθρα</a>
            <?php if ( $cat ) : ?>
                <span aria-hidden="true">/</span>
                <a href="<?php echo esc_url( get_category_link( $cat ) ); ?>"><?php echo esc_html( $cat->name ); ?></a>
            <?php endif; ?>
        </nav>

        <div class="art-hero-meta reveal reveal-up">
            <?php if ( $cat ) : ?>
                <span class="art-cat"><?php echo esc_html( $cat->name ); ?></span>
            <?php endif; ?>
            <span class="art-date"><?php echo esc_html( get_the_date( 'd.m.Y' ) ); ?></span>
            <span class="art-dot">·</span>
            <span class="art-read"><?php echo esc_html( $reading_time ); ?> λεπτά ανάγνωσης</span>
        </div>

        <h1 class="art-title reveal reveal-up"><?php the_title(); ?></h1>

        <?php
        $lead_text = ! empty( $f_subt ) ? $f_subt : get_the_excerpt();
        if ( $lead_text ) : ?>
            <p class="art-lead reveal reveal-up"><?php echo esc_html( $lead_text ); ?></p>
        <?php endif; ?>

        <div class="art-byline reveal reveal-up">
            <div class="art-author">
                <?php if ( ! empty( $member['photo'] ) ) : ?>
                    <img class="art-author-photo" src="<?php echo esc_url( $member['photo'] ); ?>" alt="<?php echo esc_attr( $author ); ?>" loading="lazy">
                <?php endif; ?>
                <div>
                    <div class="art-author-label">Συντάκτης</div>
                    <div class="art-author-name"><?php echo esc_html( ! empty( $member['name'] ) ? $member['name'] : $author ); ?></div>
                </div>
            </div>
            <div class="art-share" aria-label="Κοινοποίηση">
                <span class="art-share-label">Κοινοποίηση</span>
                <a class="art-share-btn" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $share_url; ?>" target="_blank" rel="noopener" aria-label="Facebook">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M22 12c0-5.5-4.5-10-10-10S2 6.5 2 12c0 5 3.7 9.1 8.4 9.9v-7H7.9V12h2.5V9.8c0-2.5 1.5-3.9 3.8-3.9 1.1 0 2.2.2 2.2.2v2.5h-1.3c-1.2 0-1.6.8-1.6 1.6V12h2.8l-.4 2.9h-2.3v7C18.3 21.1 22 17 22 12z"/></svg>
                </a>
                <a class="art-share-btn" href="https://twitter.com/intent/tweet?url=<?php echo $share_url; ?>&text=<?php echo $share_title; ?>" target="_blank" rel="noopener" aria-label="X">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231L18.244 2.25zm-1.161 17.52h1.833L7.084 4.126H5.117L17.083 19.77z"/></svg>
                </a>
                <a class="art-share-btn" href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo $share_url; ?>" target="_blank" rel="noopener" aria-label="LinkedIn">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M20.5 2h-17A1.5 1.5 0 002 3.5v17A1.5 1.5 0 003.5 22h17a1.5 1.5 0 001.5-1.5v-17A1.5 1.5 0 0020.5 2zM8 19H5V8h3v11zM6.5 6.7c-1 0-1.7-.7-1.7-1.6S5.5 3.5 6.5 3.5s1.7.7 1.7 1.6S7.5 6.7 6.5 6.7zM19 19h-3v-5.6c0-1.4-.5-2.3-1.7-2.3-.9 0-1.5.6-1.7 1.2-.1.2-.1.5-.1.8V19h-3V8h3v1.3c.4-.6 1.1-1.5 2.7-1.5 2 0 3.5 1.3 3.5 4.1V19z"/></svg>
                </a>
                <a class="art-share-btn" href="mailto:?subject=<?php echo $share_title; ?>&body=<?php echo $share_url; ?>" aria-label="Email">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                </a>
            </div>
        </div>
    </div>
</section>

<?php if ( $thumb ) : ?>
<section class="article-feature reveal reveal-fade">
    <div class="container container-wide">
        <div class="art-feature-img" style="background-image:url('<?php echo esc_url( $thumb ); ?>');" role="img" aria-label="<?php the_title_attribute(); ?>"></div>
    </div>
</section>
<?php endif; ?>

<section class="section section-tight">
    <div class="container container-narrow">

        <?php if ( ! empty( $takeaways ) ) : ?>
            <aside class="art-takeaways reveal reveal-up" aria-label="Κύρια συμπεράσματα">
                <h2 class="att-title">Κύρια συμπεράσματα</h2>
                <ul>
                    <?php foreach ( $takeaways as $row ) :
                        $line = $row[0] ?? ''; if ( '' === $line ) { continue; }
                    ?>
                        <li><?php echo esc_html( $line ); ?></li>
                    <?php endforeach; ?>
                </ul>
            </aside>
        <?php endif; ?>

        <article class="art-content">
            <?php
            $content = apply_filters( 'the_content', get_the_content() );
            if ( ! empty( $f_pull ) ) {
                // Inject pull quote roughly in the middle.
                $paragraphs = explode( '</p>', $content );
                $mid = (int) floor( count( $paragraphs ) / 2 );
                $pull_html = '<aside class="art-pullquote" aria-hidden="true">' . mourtzilaki_field_inline( $f_pull ) . '</aside>';
                array_splice( $paragraphs, $mid, 0, array( $pull_html ) );
                echo implode( '</p>', $paragraphs );
            } else {
                echo $content;
            }
            ?>
        </article>

        <?php if ( $f_svc_id ) : $svc_post = get_post( $f_svc_id ); if ( $svc_post ) : ?>
            <aside class="art-related-svc reveal reveal-up">
                <span class="ars-label">Σχετικός τομέας</span>
                <a class="ars-card" href="<?php echo esc_url( get_permalink( $svc_post ) ); ?>">
                    <span class="ars-icon" aria-hidden="true"><?php echo mourtzilaki_service_icon( get_the_title( $svc_post ) ); ?></span>
                    <span class="ars-text">
                        <span class="ars-title"><?php echo esc_html( get_the_title( $svc_post ) ); ?></span>
                        <span class="ars-more">Δείτε τον τομέα <span class="arrow">→</span></span>
                    </span>
                </a>
            </aside>
        <?php endif; endif; ?>

        <?php if ( $f_disc ) : ?>
            <p class="art-disclaimer"><strong>Σημείωση:</strong> <?php echo mourtzilaki_field_inline( $f_disc ); ?></p>
        <?php endif; ?>

        <?php $tags = get_the_tags(); if ( $tags ) : ?>
            <div class="art-tags">
                <span class="art-tags-label">Ετικέτες</span>
                <?php foreach ( $tags as $tag ) : ?>
                    <a href="<?php echo esc_url( get_tag_link( $tag ) ); ?>"># <?php echo esc_html( $tag->name ); ?></a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php if ( $member && ! empty( $member['photo'] ) ) : ?>
<section class="section-tight">
    <div class="container container-narrow">
        <div class="art-author-card reveal reveal-up">
            <img class="art-author-card-photo" src="<?php echo esc_url( $member['photo'] ); ?>" alt="<?php echo esc_attr( $member['name'] ); ?>" loading="lazy">
            <div class="art-author-card-body">
                <span class="eyebrow">Συντάκτης</span>
                <h3 class="h-3 mt-2"><?php echo esc_html( $member['name'] ); ?></h3>
                <p class="muted mt-2"><?php echo esc_html( $member['role'] ); ?></p>
                <p class="mt-4"><?php echo mourtzilaki_field_inline( $member['short_bio'] ); ?></p>
                <p class="mt-4">
                    <a class="btn btn-ghost" href="<?php echo esc_url( mourtzilaki_page_url( 'bio' ) ); ?>">Πλήρες βιογραφικό <span class="arrow">→</span></a>
                </p>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ( ! empty( $related ) ) : ?>
<section class="section section-soft">
    <div class="container">
        <div class="section-head reveal reveal-up">
            <span class="eyebrow">Συνέχεια</span>
            <h2 class="h-2 mt-2">Άλλα άρθρα που μπορεί να σας ενδιαφέρουν</h2>
        </div>
        <div class="articles-grid">
            <?php $i = 0; foreach ( $related as $r ) : $i++;
                $r_thumb = mourtzilaki_post_image_url( $r->ID, 'large' );
                $r_rt    = max( 1, ceil( str_word_count( wp_strip_all_tags( $r->post_content ) ) / 200 ) );
            ?>
                <a class="mag-card reveal reveal-up reveal-d<?php echo (int) $i; ?>" href="<?php echo esc_url( get_permalink( $r ) ); ?>">
                    <div class="mag-thumb" style="background-image:url(<?php echo esc_url( $r_thumb ); ?>);">
                        <?php $r_cats = get_the_category( $r->ID ); ?>
                        <span class="mag-cat"><?php echo esc_html( ! empty( $r_cats ) ? $r_cats[0]->name : 'Άρθρο' ); ?></span>
                    </div>
                    <div class="mag-body">
                        <div class="mag-meta">
                            <span><?php echo esc_html( get_the_date( 'd.m.Y', $r ) ); ?></span>
                            <span class="dot">·</span>
                            <span><?php echo esc_html( $r_rt ); ?> λεπτά</span>
                        </div>
                        <h3 class="mag-title"><?php echo esc_html( get_the_title( $r ) ); ?></h3>
                        <p class="mag-excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt( $r ), 18, '…' ) ); ?></p>
                        <span class="mag-more">Διαβάστε <span class="arrow">→</span></span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="cta-band">
    <div class="container row-split">
        <h2 class="h-2 reveal reveal-left"><?php echo esc_html( $f_cta_t !== '' ? $f_cta_t : 'Έχετε υπόθεση σχετική με αυτό;' ); ?></h2>
        <div class="reveal reveal-right">
            <p><?php echo $f_cta_x !== '' ? mourtzilaki_field_inline( $f_cta_x ) : esc_html( 'Ας τη συζητήσουμε στο πλαίσιο ενός εμπιστευτικού ραντεβού. Σας απαντάμε σε 24 ώρες εργάσιμων ημερών.' ); ?></p>
            <p class="mt-4"><a class="btn btn-primary" href="<?php echo esc_url( mourtzilaki_page_url( 'contact' ) ); ?>">Επικοινωνία <span class="arrow">→</span></a></p>
        </div>
    </div>
</section>

<?php endwhile; get_footer(); ?>
