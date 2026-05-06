<?php
/**
 * Single service template.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();

while ( have_posts() ) : the_post();
    $post_id = get_the_ID();
    $title   = get_the_title();
    $short   = function_exists( 'get_field' ) ? (string) get_field( 'description', $post_id ) : '';
    $long    = function_exists( 'get_field' ) ? (string) get_field( 'long_description', $post_id ) : '';

    $related = get_posts( array(
        'post_type'      => 'mz_service',
        'posts_per_page' => 4,
        'post__not_in'   => array( $post_id ),
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
    ) );

    // Articles related to this service (via ACF related_service field).
    $related_articles = get_posts( array(
        'post_type'      => 'post',
        'posts_per_page' => 3,
        'meta_query'     => array(
            array( 'key' => 'related_service', 'value' => $post_id, 'compare' => '=' ),
        ),
    ) );

    // FAQs related: match by category against title keywords.
    $faq_keywords = array(
        'Εμπορικ'  => 'Επιχειρήσεις',
        'Εταιρικ'  => 'Επιχειρήσεις',
        'Αστικ'    => 'Γενικά',
        'Ακινητ'   => 'Ακίνητα',
        'Κτηματ'   => 'Ακίνητα',
        'Οικογεν'  => 'Διαζύγιο',
        'Κληρονομ' => 'Διαζύγιο',
        'Εργατ'    => 'Επιχειρήσεις',
    );
    $faq_cat = '';
    foreach ( $faq_keywords as $needle => $cat_label ) {
        if ( false !== mb_stripos( $title, $needle ) ) { $faq_cat = $cat_label; break; }
    }
    $related_faqs = array();
    if ( $faq_cat ) {
        $faq_query = get_posts( array(
            'post_type'      => 'mz_faq',
            'posts_per_page' => 3,
            'meta_query'     => array(
                array( 'key' => 'category', 'value' => $faq_cat, 'compare' => '=' ),
            ),
        ) );
        $related_faqs = $faq_query;
    }

    $testimonials = mourtzilaki_get_testimonials( 1 );
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
                    <p class="lead mt-4"><?php echo mourtzilaki_field_inline( $short ); ?></p>
                <?php endif; ?>
                <div class="svc-hero-cta mt-4">
                    <a class="btn btn-primary" href="<?php echo esc_url( mourtzilaki_page_url( 'contact' ) ); ?>">Συμβουλευτική συνάντηση <span class="arrow">→</span></a>
                    <a class="btn btn-ghost" href="#svc-content">Διαβάστε αναλυτικά</a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="svc-stats">
    <div class="container">
        <div class="svc-stats-grid reveal reveal-up">
            <div class="svc-stat">
                <div class="svc-stat-num">800+</div>
                <div class="svc-stat-lab">Υποθέσεις διαχειρισμένες</div>
            </div>
            <div class="svc-stat">
                <div class="svc-stat-num">20</div>
                <div class="svc-stat-lab">Έτη εξειδίκευσης</div>
            </div>
            <div class="svc-stat">
                <div class="svc-stat-num">98%</div>
                <div class="svc-stat-lab">Ικανοποίηση πελατών</div>
            </div>
            <div class="svc-stat">
                <div class="svc-stat-num">24h</div>
                <div class="svc-stat-lab">Πρώτη απόκριση</div>
            </div>
        </div>
    </div>
</section>

<section id="svc-content" class="section section-tight">
    <div class="container">
        <div class="svc-layout">
            <article class="svc-content reveal reveal-up">
                <?php
                if ( $long ) {
                    echo $long;
                } else {
                    the_content();
                }
                ?>

                <div class="svc-inline-cta">
                    <div>
                        <strong>Έχετε υπόθεση σε αυτόν τον τομέα;</strong>
                        <p>Συζητήστε την υπόθεσή σας σε εμπιστευτικό ραντεβού. Διαφανές κόστος, ξεκάθαρη στρατηγική.</p>
                    </div>
                    <a class="btn btn-primary" href="<?php echo esc_url( mourtzilaki_page_url( 'contact' ) ); ?>">Κλείστε ραντεβού <span class="arrow">→</span></a>
                </div>
            </article>

            <aside class="svc-aside">
                <div class="svc-aside-card svc-aside-contact reveal reveal-right">
                    <span class="eyebrow" style="color: var(--gold-2);">Άμεση επαφή</span>
                    <h3 class="h-3 mt-2">Πρώτη συνάντηση</h3>
                    <p>Δωρεάν αρχική συζήτηση για να αξιολογήσουμε αν μπορούμε να βοηθήσουμε.</p>
                    <a class="btn btn-primary" href="<?php echo esc_url( mourtzilaki_page_url( 'contact' ) ); ?>">Φόρμα επικοινωνίας <span class="arrow">→</span></a>
                    <?php $c = mourtzilaki_get_contact_info(); ?>
                    <ul class="svc-aside-info">
                        <li>
                            <span class="lab">Τηλέφωνο</span>
                            <a href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $c['phone'] ) ); ?>"><?php echo esc_html( $c['phone'] ); ?></a>
                        </li>
                        <li>
                            <span class="lab">Email</span>
                            <a href="mailto:<?php echo esc_attr( $c['email'] ); ?>"><?php echo esc_html( $c['email'] ); ?></a>
                        </li>
                    </ul>
                </div>

                <?php if ( ! empty( $testimonials ) ) : $t = $testimonials[0]; ?>
                <div class="svc-aside-card svc-aside-quote reveal reveal-right">
                    <span class="quote-mark" aria-hidden="true">“</span>
                    <blockquote><?php echo mourtzilaki_kses_quote( $t['quote'] ); ?></blockquote>
                    <footer>
                        <span class="t-name"><?php echo esc_html( $t['name'] ); ?></span>
                        <?php if ( ! empty( $t['role'] ) ) : ?>
                            <span class="t-role"><?php echo esc_html( $t['role'] ); ?></span>
                        <?php endif; ?>
                    </footer>
                </div>
                <?php endif; ?>
            </aside>
        </div>
    </div>
</section>

<?php if ( ! empty( $related_faqs ) ) : ?>
<section class="section section-soft">
    <div class="container">
        <div class="section-head reveal reveal-up">
            <span class="eyebrow">Σχετικές ερωτήσεις</span>
            <h2 class="h-2 mt-2">Από τις πιο συχνές ερωτήσεις σε αυτόν τον τομέα</h2>
        </div>
        <div class="svc-faq-list">
            <?php foreach ( $related_faqs as $f ) :
                $a = function_exists( 'get_field' ) ? (string) get_field( 'answer', $f->ID ) : '';
            ?>
                <details class="faq-item">
                    <summary>
                        <span class="faq-q"><?php echo esc_html( get_the_title( $f ) ); ?></span>
                        <span class="faq-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
                        </span>
                    </summary>
                    <div class="faq-a"><?php echo wpautop( $a ); ?></div>
                </details>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-6">
            <a class="btn btn-ghost" href="<?php echo esc_url( mourtzilaki_page_url( 'faq' ) ); ?>">Όλες οι συχνές ερωτήσεις <span class="arrow">→</span></a>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ( ! empty( $related_articles ) ) : ?>
<section class="section">
    <div class="container">
        <div class="section-head reveal reveal-up">
            <span class="eyebrow">Άρθρα στον τομέα</span>
            <h2 class="h-2 mt-2">Σχετικές αναλύσεις</h2>
        </div>
        <div class="articles-grid">
            <?php $i = 0; foreach ( $related_articles as $r ) : $i++;
                $r_thumb = mourtzilaki_post_image_url( $r->ID, 'large' );
            ?>
                <a class="mag-card reveal reveal-up reveal-d<?php echo (int) $i; ?>" href="<?php echo esc_url( get_permalink( $r ) ); ?>">
                    <div class="mag-thumb" style="background-image:url(<?php echo esc_url( $r_thumb ); ?>);">
                        <span class="mag-cat"><?php echo esc_html( $title ); ?></span>
                    </div>
                    <div class="mag-body">
                        <div class="mag-meta">
                            <span><?php echo esc_html( get_the_date( 'd.m.Y', $r ) ); ?></span>
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
                    <p class="svc-tile-desc"><?php echo esc_html( wp_trim_words( wp_strip_all_tags( $r_short ), 16, '…' ) ); ?></p>
                    <span class="svc-tile-more">Μάθετε περισσότερα <span class="arrow">→</span></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="cta-band">
    <div class="container row-split">
        <h2 class="h-2 reveal reveal-left">Έτοιμοι να μιλήσουμε;</h2>
        <div class="reveal reveal-right">
            <p>Κλείστε ένα αρχικό, εμπιστευτικό ραντεβού. Αναλύουμε την υπόθεσή σας και προτείνουμε καθαρή στρατηγική.</p>
            <p class="mt-4"><a class="btn btn-primary" href="<?php echo esc_url( mourtzilaki_page_url( 'contact' ) ); ?>">Επικοινωνία <span class="arrow">→</span></a></p>
        </div>
    </div>
</section>

<?php endwhile; get_footer(); ?>
