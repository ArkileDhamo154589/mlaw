<?php
/**
 * About / Philosophy page (Το γραφείο) — fully ACF-driven.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();

$pid     = get_queried_object_id();
$h       = mourtzilaki_page_hero();
$tpl_uri = get_template_directory_uri();
$g = function ( $field, $fallback = '' ) use ( $pid ) {
    if ( ! function_exists( 'get_field' ) ) { return $fallback; }
    $v = (string) get_field( $field, $pid );
    return '' !== trim( $v ) ? $v : $fallback;
};
$values    = mourtzilaki_parse_lines( $g( 'about_values' ) );
$mini      = mourtzilaki_parse_lines( $g( 'about_story_mini', "20+ | χρόνια πορείας\n800+ | υποθέσεις\n8 | τομείς εξειδίκευσης" ) );
$diff      = mourtzilaki_parse_lines( $g( 'about_diff_items',
    "clock | Άμεση πρόσβαση στη δικηγόρο | Δεν περνάτε από βοηθούς. Η δικηγόρος που αναλαμβάνει την υπόθεσή σας είναι αυτή που σας απαντάει στο τηλέφωνο, στο email και σε κάθε εξέλιξη.\n" .
    "sun | Διαφανές κόστος, χωρίς εκπλήξεις | Πριν αναλάβουμε οποιαδήποτε ενέργεια, σας δίνουμε γραπτή πρόταση με σαφές κόστος και χρονοδιάγραμμα. Καμία κρυφή χρέωση, καμία αλλαγή ορόφου χωρίς συνεννόηση.\n" .
    "check | Ρεαλιστική στρατηγική, πάντα | Δεν υποσχόμαστε ό,τι ο πελάτης θέλει να ακούσει. Σας λέμε τι μπορεί ρεαλιστικά να επιτευχθεί, με ποιο κόστος, σε ποιο χρόνο — και ποιοι είναι οι κίνδυνοι."
) );
$member = mourtzilaki_get_member( 0 );

$diff_icons = array(
    'clock' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><polyline points="12,7 12,12 15,15"/></svg>',
    'sun'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12h2M19 12h2M12 3v2M12 19v2M5.6 5.6l1.4 1.4M17 17l1.4 1.4M5.6 18.4l1.4-1.4M17 7l1.4-1.4"/><circle cx="12" cy="12" r="4"/></svg>',
    'check' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22,4 12,14.01 9,11.01"/></svg>',
);
?>

<!-- 1. Hero -->
<section class="page-header">
    <div class="container container-narrow">
        <span class="eyebrow"><?php echo esc_html( ! empty( $h['eyebrow'] ) ? $h['eyebrow'] : 'Φιλοσοφία' ); ?></span>
        <h1 class="h-1 mt-2"><?php echo esc_html( ! empty( $h['title'] ) ? $h['title'] : 'Δίκαιο που σέβεται τον άνθρωπο και την επιχείρηση.' ); ?></h1>
        <p class="lead"><?php echo ! empty( $h['lead'] ) ? mourtzilaki_field_inline( $h['lead'] ) : esc_html( 'Από το 2005, χτίζουμε ένα γραφείο που συνδυάζει τη νομική ακρίβεια με την προσωπική σχέση εμπιστοσύνης.' ); ?></p>
    </div>
</section>

<!-- 2. Manifesto -->
<?php
$man_eb   = $g( 'about_manifesto_eyebrow', 'Manifesto' );
$man_text = $g( 'about_manifesto_text',    'Πιστεύουμε ότι κάθε νομική υπόθεση κρύβει έναν άνθρωπο. Δεν υπάρχουν «μικρές» υποθέσεις — μόνο υποθέσεις που χρειάζονται την ίδια προσοχή, σαφήνεια και αφοσίωση που θα δίναμε αν μας αφορούσαν προσωπικά.' );
$man_attr = $g( 'about_manifesto_attr',    '— Έλενα Μουρτζιλάκη, Ιδρύτρια' );
?>
<?php if ( $man_text ) : ?>
<section class="about-manifesto">
    <span class="deco deco-scales-mini" aria-hidden="true">
        <svg viewBox="0 0 200 200" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round">
            <path d="M100 30 L100 170M70 170 L130 170M40 50 L160 50"/>
            <circle cx="100" cy="40" r="4" fill="currentColor"/>
            <path d="M40 50 L25 95 L55 95 Z M160 50 L145 95 L175 95 Z"/>
        </svg>
    </span>
    <div class="container container-narrow text-center">
        <span class="eyebrow" style="justify-content:center"><?php echo esc_html( $man_eb ); ?></span>
        <p class="manifesto-text reveal reveal-fade">
            <span class="manifesto-mark" aria-hidden="true">“</span>
            <?php echo mourtzilaki_field_inline( $man_text ); ?>
        </p>
        <?php if ( $man_attr ) : ?>
            <span class="manifesto-attr reveal reveal-up"><?php echo esc_html( $man_attr ); ?></span>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<!-- 3. Story -->
<section class="section about-story">
    <div class="container">
        <div class="story-grid">
            <div class="story-img reveal reveal-left">
                <img src="<?php echo esc_url( $tpl_uri . '/assets/img/about/01.jpg' ); ?>" alt="" loading="lazy">
                <span class="story-img-tag">
                    <span class="sit-num">Est.</span>
                    <span class="sit-year"><?php echo esc_html( $g( 'about_story_year', '2005' ) ); ?></span>
                </span>
                <span class="story-img-corner" aria-hidden="true"></span>
            </div>
            <div class="story-text reveal reveal-right">
                <span class="eyebrow"><?php echo esc_html( $g( 'about_story_title', 'Η ιστορία μας' ) ); ?></span>
                <h2 class="h-2 mt-2"><?php echo esc_html( $g( 'about_story_heading', 'Από μια ιδέα, ένα γραφείο.' ) ); ?></h2>
                <p class="lead mt-4"><?php echo mourtzilaki_field_inline( $g( 'about_story_text' ) ); ?></p>
                <p class="mt-4"><?php echo mourtzilaki_field_inline( $g( 'about_story_p2', 'Η εμπειρία μας στα δικαστήρια, οι σύνθετες υποθέσεις που χειριστήκαμε και — πάνω απ\' όλα — οι σχέσεις εμπιστοσύνης που χτίσαμε με κάθε πελάτη συνθέτουν το γραφείο που είμαστε σήμερα. Δεν είμαστε εδώ απλώς για να γράφουμε δικόγραφα.' ) ); ?></p>

                <?php if ( ! empty( $mini ) ) : ?>
                <div class="story-mini-stats">
                    <?php foreach ( $mini as $row ) : $n = $row[0] ?? ''; $l = $row[1] ?? ''; if ( '' === $n ) { continue; } ?>
                        <div><strong><?php echo esc_html( $n ); ?></strong><span><?php echo esc_html( $l ); ?></span></div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- 4. Pillars -->
<?php if ( ! empty( $values ) ) : ?>
<section class="section section-soft about-pillars">
    <div class="container">
        <div class="section-head reveal reveal-up" style="text-align: center; margin-left: auto; margin-right: auto; max-width: 640px;">
            <span class="eyebrow" style="justify-content:center"><?php echo esc_html( $g( 'about_pillars_eyebrow', 'Πυλώνες' ) ); ?></span>
            <h2 class="h-2 mt-2"><?php echo esc_html( $g( 'about_pillars_heading', 'Οι αρχές που μας οδηγούν' ) ); ?></h2>
            <p class="lead mt-4"><?php echo mourtzilaki_field_inline( $g( 'about_pillars_lead', 'Τέσσερις αρχές που καθορίζουν τον τρόπο που εργαζόμαστε. Όχι slogan — οδηγός για κάθε απόφαση.' ) ); ?></p>
        </div>
        <div class="pillars-grid">
            <?php foreach ( $values as $i => $row ) :
                $title = $row[0] ?? '';
                $text  = $row[1] ?? '';
                if ( '' === $title ) { continue; }
            ?>
                <article class="pillar-card reveal reveal-up reveal-d<?php echo (int) min( $i + 1, 6 ); ?>">
                    <span class="pillar-num"><?php echo esc_html( str_pad( $i + 1, 2, '0', STR_PAD_LEFT ) ); ?></span>
                    <span class="pillar-line" aria-hidden="true"></span>
                    <h3 class="pillar-title"><?php echo esc_html( $title ); ?></h3>
                    <p class="pillar-text"><?php echo esc_html( $text ); ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- 5. What sets us apart -->
<?php if ( ! empty( $diff ) ) :
    $diff_btn_l = $g( 'about_diff_btn_label', 'Δείτε τους τομείς' );
    $diff_btn_u = $g( 'about_diff_btn_url',   mourtzilaki_page_url( 'services' ) );
?>
<section class="section about-different">
    <div class="container">
        <div class="section-head row-split" style="align-items: end;">
            <div class="reveal reveal-left">
                <span class="eyebrow"><?php echo esc_html( $g( 'about_diff_eyebrow', 'Τι μας ξεχωρίζει' ) ); ?></span>
                <h2 class="h-2 mt-2" style="max-width: 18ch;"><?php echo esc_html( $g( 'about_diff_heading', 'Τρεις διαφορές που έχουν σημασία.' ) ); ?></h2>
            </div>
            <?php if ( $diff_btn_l ) : ?>
            <div class="reveal reveal-right" style="text-align: right;">
                <a class="btn btn-ghost" href="<?php echo esc_url( $diff_btn_u ); ?>"><?php echo esc_html( $diff_btn_l ); ?> <span class="arrow">→</span></a>
            </div>
            <?php endif; ?>
        </div>
        <div class="diff-grid">
            <?php foreach ( $diff as $i => $row ) :
                $icon  = $row[0] ?? 'clock';
                $title = $row[1] ?? '';
                $text  = $row[2] ?? '';
                if ( '' === $title ) { continue; }
            ?>
                <article class="diff-block reveal reveal-up reveal-d<?php echo (int) min( $i + 1, 6 ); ?>">
                    <span class="diff-icon" aria-hidden="true"><?php echo $diff_icons[ $icon ] ?? $diff_icons['clock']; ?></span>
                    <h3><?php echo esc_html( $title ); ?></h3>
                    <p><?php echo esc_html( $text ); ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- 6. Mission + Founder spotlight -->
<section class="section section-soft about-mission">
    <div class="container">
        <div class="mission-grid">
            <?php if ( $member && ! empty( $member['photo'] ) ) : ?>
            <div class="mission-photo reveal reveal-left">
                <img src="<?php echo esc_url( $member['photo'] ); ?>" alt="<?php echo esc_attr( $member['name'] ); ?>" loading="lazy">
                <div class="mission-photo-meta">
                    <div class="mp-name"><?php echo esc_html( $member['name'] ); ?></div>
                    <div class="mp-role"><?php echo esc_html( $member['role'] ); ?></div>
                </div>
                <a class="mission-photo-link" href="<?php echo esc_url( mourtzilaki_page_url( 'bio' ) ); ?>">Πλήρες βιογραφικό →</a>
            </div>
            <?php endif; ?>
            <div class="mission-text reveal reveal-right">
                <span class="eyebrow"><?php echo esc_html( $g( 'about_mission_title', 'Η αποστολή μας' ) ); ?></span>
                <h2 class="h-2 mt-2"><?php echo esc_html( $g( 'about_mission_heading', 'Καθαρές αποφάσεις, χωρίς ορολογίες.' ) ); ?></h2>
                <p class="lead mt-4"><?php echo mourtzilaki_field_inline( $g( 'about_mission_text' ) ); ?></p>

                <?php
                $mq  = $g( 'about_mission_quote' );
                $mqc = $g( 'about_mission_quote_cite', '— από συζήτηση με νέο πελάτη, 2024' );
                if ( $mq ) :
                ?>
                <blockquote class="founder-quote">
                    «<?php echo esc_html( $mq ); ?>»
                    <?php if ( $mqc ) : ?><cite><?php echo esc_html( $mqc ); ?></cite><?php endif; ?>
                </blockquote>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- 7. CTA -->
<?php
$cta_h    = $g( 'about_cta_heading', 'Γνωρίστε την ομάδα μας.' );
$cta_l    = $g( 'about_cta_lead',    'Οι δικηγόροι του γραφείου συνδυάζουν εξειδίκευση και εμπειρία σε διαφορετικούς τομείς δικαίου, με κοινό άξονα την προσωπική σχέση εμπιστοσύνης.' );
$cta_b1l  = $g( 'about_cta_btn1_label', 'Δείτε την ομάδα' );
$cta_b1u  = $g( 'about_cta_btn1_url',   mourtzilaki_page_url( 'team' ) );
$cta_b2l  = $g( 'about_cta_btn2_label', 'Επικοινωνία' );
$cta_b2u  = $g( 'about_cta_btn2_url',   mourtzilaki_page_url( 'contact' ) );
?>
<section class="cta-band">
    <div class="container row-split">
        <h2 class="h-2 reveal reveal-left"><?php echo esc_html( $cta_h ); ?></h2>
        <div class="reveal reveal-right">
            <p><?php echo mourtzilaki_field_inline( $cta_l ); ?></p>
            <p class="mt-4">
                <?php if ( $cta_b1l ) : ?>
                    <a class="btn btn-primary" href="<?php echo esc_url( $cta_b1u ); ?>"><?php echo esc_html( $cta_b1l ); ?> <span class="arrow">→</span></a>
                <?php endif; ?>
                <?php if ( $cta_b2l ) : ?>
                    <a class="btn btn-ghost-light" href="<?php echo esc_url( $cta_b2u ); ?>"><?php echo esc_html( $cta_b2l ); ?></a>
                <?php endif; ?>
            </p>
        </div>
    </div>
</section>

<?php get_footer(); ?>
