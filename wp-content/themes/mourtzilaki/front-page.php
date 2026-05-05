<?php
/**
 * Home / front page — all content read from ACF (CPTs + page fields).
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();

$slides       = mourtzilaki_get_hero_slides();
$service_posts = get_posts( array( 'post_type' => 'mz_service', 'posts_per_page' => -1, 'orderby' => 'menu_order date', 'order' => 'ASC' ) );
$testimonials = mourtzilaki_get_testimonials();
$team         = mourtzilaki_team();
$tpl_uri      = get_template_directory_uri();
$front_id     = (int) get_option( 'page_on_front' );
$f = function ( $field, $fallback = '' ) use ( $front_id ) {
    if ( ! function_exists( 'get_field' ) || ! $front_id ) { return $fallback; }
    $v = (string) get_field( $field, $front_id );
    return $v !== '' ? $v : $fallback;
};

$trust_strip   = mourtzilaki_parse_lines( $f( 'home_trust_strip' ) );
$process_steps = mourtzilaki_parse_lines( $f( 'home_process_steps' ) );
$lawyer_meta   = mourtzilaki_parse_lines( $f( 'home_lawyer_meta' ) );
?>

<!-- 1. Hero carousel -->
<section class="hero-slick" aria-label="Αρχική παρουσίαση">
    <?php foreach ( $slides as $s ) : ?>
        <div class="slide" style="background-image: url('<?php echo esc_url( $s['img'] ); ?>');">
            <div class="container">
                <div class="slide-inner">
                    <?php if ( ! empty( $s['eyebrow'] ) ) : ?><span class="eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></span><?php endif; ?>
                    <h1 class="h-display"><?php echo esc_html( $s['title'] ); ?></h1>
                    <?php if ( ! empty( $s['lead'] ) ) : ?><p class="lead"><?php echo esc_html( $s['lead'] ); ?></p><?php endif; ?>
                    <div class="hero-cta">
                        <a class="btn btn-primary" href="<?php echo esc_url( ! empty( $s['cta_url'] ) ? $s['cta_url'] : mourtzilaki_page_url( 'contact' ) ); ?>">
                            <?php echo esc_html( ! empty( $s['cta_label'] ) ? $s['cta_label'] : 'Κλείστε ραντεβού' ); ?> <span class="arrow">→</span>
                        </a>
                        <a class="btn btn-ghost-light" href="<?php echo esc_url( mourtzilaki_page_url( 'services' ) ); ?>">Τομείς εξειδίκευσης</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</section>

<!-- 2. Trust strip -->
<?php if ( ! empty( $trust_strip ) ) : ?>
<section class="trust-strip">
    <div class="container">
        <div class="ts-grid reveal reveal-up">
            <?php foreach ( $trust_strip as $i => $row ) :
                if ( $i > 0 ) : ?>
                    <div class="ts-sep" aria-hidden="true"></div>
                <?php endif;
                $num = $row[0] ?? '';
                $lab = $row[1] ?? '';
            ?>
                <div class="ts-item">
                    <div class="ts-num"><?php echo esc_html( $num ); ?></div>
                    <div class="ts-lab"><?php echo esc_html( $lab ); ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- 3. Philosophy -->
<section class="section philosophy">
    <span class="deco deco-scales" aria-hidden="true">
        <svg viewBox="0 0 200 200" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M100 30 L100 170"/>
            <path d="M70 170 L130 170"/>
            <path d="M40 50 L160 50"/>
            <circle cx="100" cy="40" r="4" fill="currentColor"/>
            <path d="M40 50 L25 95 L55 95 Z"/>
            <path d="M160 50 L145 95 L175 95 Z"/>
            <path d="M30 95 a15 15 0 0 0 20 0"/>
            <path d="M150 95 a15 15 0 0 0 20 0"/>
        </svg>
    </span>
    <div class="container">
        <div class="philosophy-grid">
            <div class="philosophy-img reveal reveal-left">
                <img src="<?php echo esc_url( $tpl_uri . '/assets/img/about/01.jpg' ); ?>" alt="" loading="lazy">
                <?php $year_caption = $f( 'home_philosophy_year' ); if ( $year_caption ) : ?>
                <div class="ph-caption">
                    <span class="ph-num"><?php echo esc_html( $year_caption ); ?></span>
                    <span class="ph-line"></span>
                    <span class="ph-text">Δικηγορικός Σύλλογος Αθηνών</span>
                </div>
                <?php endif; ?>
            </div>
            <div class="philosophy-text reveal reveal-right">
                <span class="eyebrow"><?php echo esc_html( $f( 'home_about_eyebrow' ) ); ?></span>
                <h2 class="h-1 mt-2"><?php echo esc_html( $f( 'home_about_title' ) ); ?></h2>

                <?php $quote = $f( 'home_philosophy_quote' ); if ( $quote ) : ?>
                    <blockquote class="pull-quote"><?php echo esc_html( $quote ); ?></blockquote>
                <?php endif; ?>

                <?php
                $about_text = $f( 'home_about_text' );
                foreach ( preg_split( "/\n\n+/", $about_text ) as $para ) {
                    if ( '' === trim( $para ) ) { continue; }
                    printf( '<p>%s</p>', esc_html( $para ) );
                }
                ?>

                <div class="philosophy-cta">
                    <a class="btn-link" href="<?php echo esc_url( mourtzilaki_page_url( 'about' ) ); ?>">Διαβάστε τις αξίες μας</a>
                    <a class="btn-link" href="<?php echo esc_url( mourtzilaki_page_url( 'bio' ) ); ?>">Πλήρες βιογραφικό</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 4. Practice areas -->
<section class="section section-soft practice-section">
    <div class="container">
        <div class="section-head row-split" style="align-items: end;">
            <div class="reveal reveal-left">
                <span class="eyebrow"><?php echo esc_html( $f( 'home_svc_eyebrow' ) ); ?></span>
                <h2 class="h-2 mt-2" style="max-width: 18ch;"><?php echo esc_html( $f( 'home_svc_title' ) ); ?></h2>
            </div>
            <div class="reveal reveal-right" style="text-align: right;">
                <a class="btn btn-ghost" href="<?php echo esc_url( mourtzilaki_page_url( 'services' ) ); ?>">Όλοι οι τομείς <span class="arrow">→</span></a>
            </div>
        </div>
        <div class="practice-grid">
            <?php foreach ( $service_posts as $i => $svc ) :
                $short = function_exists( 'get_field' ) ? (string) get_field( 'description', $svc->ID ) : '';
            ?>
                <a class="practice-tile reveal reveal-up reveal-d<?php echo (int) min( ( $i % 4 ) + 1, 6 ); ?>" href="<?php echo esc_url( get_permalink( $svc ) ); ?>">
                    <span class="pt-icon" aria-hidden="true"><?php echo mourtzilaki_service_icon( get_the_title( $svc ) ); ?></span>
                    <h3 class="pt-title"><?php echo esc_html( get_the_title( $svc ) ); ?></h3>
                    <p class="pt-desc"><?php echo esc_html( wp_trim_words( $short, 18, '…' ) ); ?></p>
                    <span class="pt-arrow" aria-hidden="true">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 5. Process -->
<?php if ( ! empty( $process_steps ) ) : ?>
<section class="section process-section">
    <span class="deco deco-columns deco-left" aria-hidden="true">
        <svg viewBox="0 0 80 200" fill="none" stroke="currentColor" stroke-width="1">
            <rect x="6" y="0" width="68" height="6"/>
            <rect x="14" y="6" width="52" height="180"/>
            <line x1="22" y1="6" x2="22" y2="186"/>
            <line x1="30" y1="6" x2="30" y2="186"/>
            <line x1="38" y1="6" x2="38" y2="186"/>
            <line x1="46" y1="6" x2="46" y2="186"/>
            <line x1="54" y1="6" x2="54" y2="186"/>
            <line x1="62" y1="6" x2="62" y2="186"/>
            <rect x="0" y="186" width="80" height="14"/>
        </svg>
    </span>
    <span class="deco deco-columns deco-right" aria-hidden="true">
        <svg viewBox="0 0 80 200" fill="none" stroke="currentColor" stroke-width="1">
            <rect x="6" y="0" width="68" height="6"/>
            <rect x="14" y="6" width="52" height="180"/>
            <line x1="22" y1="6" x2="22" y2="186"/>
            <line x1="30" y1="6" x2="30" y2="186"/>
            <line x1="38" y1="6" x2="38" y2="186"/>
            <line x1="46" y1="6" x2="46" y2="186"/>
            <line x1="54" y1="6" x2="54" y2="186"/>
            <line x1="62" y1="6" x2="62" y2="186"/>
            <rect x="0" y="186" width="80" height="14"/>
        </svg>
    </span>
    <div class="container">
        <div class="section-head reveal reveal-up" style="text-align:center; margin-left:auto; margin-right:auto;">
            <span class="eyebrow" style="justify-content:center;"><?php echo esc_html( $f( 'home_process_eyebrow' ) ); ?></span>
            <h2 class="h-2 mt-2"><?php echo esc_html( $f( 'home_process_title' ) ); ?></h2>
            <p class="lead mt-4"><?php echo esc_html( $f( 'home_process_lead' ) ); ?></p>
        </div>

        <div class="process-strip">
            <?php foreach ( $process_steps as $i => $row ) :
                $num = $row[0] ?? '';
                $title = $row[1] ?? '';
                $desc = $row[2] ?? '';
            ?>
                <div class="process-step reveal reveal-up reveal-d<?php echo (int) min( $i + 1, 6 ); ?>">
                    <span class="ps-num"><?php echo esc_html( $num ); ?></span>
                    <h3><?php echo esc_html( $title ); ?></h3>
                    <p><?php echo esc_html( $desc ); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- 6. Testimonials carousel -->
<?php if ( ! empty( $testimonials ) ) : ?>
<section class="testimonial-section">
    <div class="container container-narrow">
        <div class="testimonials-slick reveal reveal-fade">
            <?php foreach ( $testimonials as $t ) : ?>
                <div class="ts-slide">
                    <span class="quote-mark" aria-hidden="true">“</span>
                    <blockquote><?php echo esc_html( $t['quote'] ); ?></blockquote>
                    <footer class="t-attr">
                        <div>
                            <div class="t-name"><?php echo esc_html( $t['name'] ); ?></div>
                            <?php if ( ! empty( $t['role'] ) ) : ?>
                                <div class="t-role"><?php echo wp_kses( $t['role'], array() ); ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="t-stars" aria-label="5 στα 5">
                            <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
                        </div>
                    </footer>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- 7. Featured lawyer -->
<?php $m = $team[0] ?? null; if ( $m ) : ?>
<section class="section lawyer-feature">
    <div class="container">
        <div class="lf-grid">
            <div class="lf-photo reveal reveal-left">
                <img src="<?php echo esc_url( $m['photo'] ); ?>" alt="<?php echo esc_attr( $m['name'] ); ?>" loading="lazy">
                <span class="lf-tag"><?php echo esc_html( $m['role'] ); ?></span>
            </div>
            <div class="lf-content reveal reveal-right">
                <span class="eyebrow"><?php echo esc_html( $f( 'home_lawyer_eyebrow' ) ); ?></span>
                <h2 class="h-1 mt-2"><?php echo esc_html( $m['name'] ); ?></h2>
                <p class="lead mt-4"><?php echo esc_html( $f( 'home_lawyer_lead', $m['bio'] ) ); ?></p>

                <?php if ( ! empty( $lawyer_meta ) ) : ?>
                <div class="lf-meta">
                    <?php foreach ( $lawyer_meta as $row ) :
                        $lab = $row[0] ?? '';
                        $val = $row[1] ?? '';
                    ?>
                        <div class="lf-meta-it">
                            <div class="lab"><?php echo esc_html( $lab ); ?></div>
                            <div class="val"><?php echo esc_html( $val ); ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <div class="lf-cta">
                    <a class="btn btn-primary" href="<?php echo esc_url( mourtzilaki_page_url( 'bio' ) ); ?>">Πλήρες βιογραφικό <span class="arrow">→</span></a>
                    <a class="btn btn-ghost" href="<?php echo esc_url( mourtzilaki_page_url( 'contact' ) ); ?>">Ραντεβού</a>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- 8. Recent articles -->
<?php
$recent = new WP_Query( array(
    'post_type'      => 'post',
    'posts_per_page' => 3,
    'ignore_sticky_posts' => true,
) );
if ( $recent->have_posts() ) : ?>
<section class="section section-soft articles-section">
    <div class="container">
        <div class="section-head row-split" style="align-items:end;">
            <div class="reveal reveal-left">
                <span class="eyebrow"><?php echo esc_html( $f( 'home_blog_eyebrow' ) ); ?></span>
                <h2 class="h-2 mt-2" style="max-width: 18ch;"><?php echo esc_html( $f( 'home_blog_title' ) ); ?></h2>
            </div>
            <div class="reveal reveal-right" style="text-align:right;">
                <a class="btn btn-ghost" href="<?php echo esc_url( mourtzilaki_page_url( 'blog' ) ); ?>">Όλα τα άρθρα <span class="arrow">→</span></a>
            </div>
        </div>
        <div class="articles-grid">
            <?php $i = 0; while ( $recent->have_posts() ) : $recent->the_post(); $i++;
                $thumb = mourtzilaki_post_image_url( null, 'large' );
                $reading_time = max( 1, ceil( str_word_count( wp_strip_all_tags( get_the_content() ) ) / 200 ) );
            ?>
                <a class="mag-card reveal reveal-up reveal-d<?php echo (int) $i; ?>" href="<?php the_permalink(); ?>">
                    <div class="mag-thumb"<?php if ( $thumb ) echo ' style="background-image:url(' . esc_url( $thumb ) . ');"'; ?>>
                        <span class="mag-cat">
                            <?php $cats = get_the_category(); echo esc_html( ! empty( $cats ) ? $cats[0]->name : 'Άρθρο' ); ?>
                        </span>
                    </div>
                    <div class="mag-body">
                        <div class="mag-meta">
                            <span><?php echo esc_html( get_the_date( 'd.m.Y' ) ); ?></span>
                            <span class="dot">·</span>
                            <span><?php echo esc_html( $reading_time ); ?> λεπτά ανάγνωσης</span>
                        </div>
                        <h3 class="mag-title"><?php the_title(); ?></h3>
                        <p class="mag-excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 22, '…' ) ); ?></p>
                        <span class="mag-more">Διαβάστε το άρθρο <span class="arrow">→</span></span>
                    </div>
                </a>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- 9. Final CTA -->
<section class="cta-final">
    <span class="deco deco-pediment" aria-hidden="true">
        <svg viewBox="0 0 400 200" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
            <path d="M40 200 L200 40 L360 200"/>
            <path d="M20 200 L380 200"/>
            <path d="M60 200 L60 110"/>
            <path d="M120 200 L120 110"/>
            <path d="M280 200 L280 110"/>
            <path d="M340 200 L340 110"/>
            <path d="M60 110 L340 110"/>
            <circle cx="200" cy="120" r="6"/>
            <path d="M180 130 L220 130 M188 130 a12 12 0 0 0 24 0"/>
        </svg>
    </span>
    <span class="deco deco-gavel" aria-hidden="true">
        <svg viewBox="0 0 200 200" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M120 30 L170 80 L130 120 L80 70 Z"/>
            <path d="M70 80 L130 140"/>
            <path d="M30 160 L170 160"/>
            <path d="M50 150 L80 120 L100 140 L70 170 Z"/>
        </svg>
    </span>
    <div class="container">
        <div class="cta-final-inner reveal reveal-fade">
            <span class="eyebrow" style="color:var(--gold);"><?php echo esc_html( $f( 'home_cta_eyebrow' ) ); ?></span>
            <h2 class="h-display mt-2" style="max-width: 22ch;"><?php echo esc_html( $f( 'home_cta_title' ) ); ?></h2>
            <p class="lead mt-4" style="max-width: 60ch;"><?php echo esc_html( $f( 'home_cta_text' ) ); ?></p>
            <div class="cta-final-actions mt-6">
                <a class="btn btn-primary" href="<?php echo esc_url( mourtzilaki_page_url( 'contact' ) ); ?>">Κλείστε ραντεβού <span class="arrow">→</span></a>
                <?php $phone = mourtzilaki_get_contact_info()['phone']; ?>
                <a class="btn btn-ghost-light" href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $phone ) ); ?>">
                    <span class="phone-ic" aria-hidden="true">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72c.13.96.37 1.9.72 2.81a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.91.35 1.85.59 2.81.72A2 2 0 0122 16.92z"/></svg>
                    </span>
                    <?php echo esc_html( $phone ); ?>
                </a>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
