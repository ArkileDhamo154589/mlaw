<?php
/**
 * About / Philosophy page (Το γραφείο).
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();

$pid     = get_queried_object_id();
$h       = mourtzilaki_page_hero();
$tpl_uri = get_template_directory_uri();
$g = function ( $field, $fallback = '' ) use ( $pid ) {
    if ( ! function_exists( 'get_field' ) ) { return $fallback; }
    $v = (string) get_field( $field, $pid );
    return $v !== '' ? $v : $fallback;
};
$values = mourtzilaki_parse_lines( $g( 'about_values' ) );
$member = mourtzilaki_get_member( 0 );
?>

<!-- 1. Hero -->
<section class="page-header">
    <div class="container container-narrow">
        <span class="eyebrow"><?php echo esc_html( ! empty( $h['eyebrow'] ) ? $h['eyebrow'] : 'Φιλοσοφία' ); ?></span>
        <h1 class="h-1 mt-2"><?php echo esc_html( ! empty( $h['title'] ) ? $h['title'] : 'Δίκαιο που σέβεται τον άνθρωπο και την επιχείρηση.' ); ?></h1>
        <p class="lead"><?php echo esc_html( ! empty( $h['lead'] ) ? $h['lead'] : 'Από το 2005, χτίζουμε ένα γραφείο που συνδυάζει τη νομική ακρίβεια με την προσωπική σχέση εμπιστοσύνης.' ); ?></p>
    </div>
</section>

<!-- 2. Manifesto -->
<section class="about-manifesto">
    <span class="deco deco-scales-mini" aria-hidden="true">
        <svg viewBox="0 0 200 200" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round">
            <path d="M100 30 L100 170M70 170 L130 170M40 50 L160 50"/>
            <circle cx="100" cy="40" r="4" fill="currentColor"/>
            <path d="M40 50 L25 95 L55 95 Z M160 50 L145 95 L175 95 Z"/>
        </svg>
    </span>
    <div class="container container-narrow text-center">
        <span class="eyebrow" style="justify-content:center">Manifesto</span>
        <p class="manifesto-text reveal reveal-fade">
            <span class="manifesto-mark" aria-hidden="true">“</span>
            Πιστεύουμε ότι κάθε νομική υπόθεση κρύβει έναν άνθρωπο. Δεν υπάρχουν «μικρές» υποθέσεις — μόνο υποθέσεις που χρειάζονται την ίδια προσοχή, σαφήνεια και αφοσίωση που θα δίναμε αν μας αφορούσαν προσωπικά.
        </p>
        <span class="manifesto-attr reveal reveal-up">— Έλενα Μουρτζιλάκη, Ιδρύτρια</span>
    </div>
</section>

<!-- 3. Story -->
<section class="section about-story">
    <div class="container">
        <div class="story-grid">
            <div class="story-img reveal reveal-left">
                <img src="<?php echo esc_url( $tpl_uri . '/assets/img/about/01.jpg' ); ?>" alt="" loading="lazy">
                <span class="story-img-tag">
                    <span class="sit-num">Est.</span>
                    <span class="sit-year">2005</span>
                </span>
                <span class="story-img-corner" aria-hidden="true"></span>
            </div>
            <div class="story-text reveal reveal-right">
                <span class="eyebrow"><?php echo esc_html( $g( 'about_story_title', 'Η ιστορία μας' ) ); ?></span>
                <h2 class="h-2 mt-2">Από μια ιδέα, ένα γραφείο.</h2>
                <p class="lead mt-4"><?php echo esc_html( $g( 'about_story_text' ) ); ?></p>
                <p class="mt-4">Η εμπειρία μας στα δικαστήρια, οι σύνθετες υποθέσεις που χειριστήκαμε και — πάνω απ’ όλα — οι σχέσεις εμπιστοσύνης που χτίσαμε με κάθε πελάτη συνθέτουν το γραφείο που είμαστε σήμερα. Δεν είμαστε εδώ απλώς για να γράφουμε δικόγραφα.</p>

                <div class="story-mini-stats">
                    <div><strong>20+</strong><span>χρόνια πορείας</span></div>
                    <div><strong>800+</strong><span>υποθέσεις</span></div>
                    <div><strong>8</strong><span>τομείς εξειδίκευσης</span></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 4. Pillars -->
<?php if ( ! empty( $values ) ) : ?>
<section class="section section-soft about-pillars">
    <div class="container">
        <div class="section-head reveal reveal-up" style="text-align: center; margin-left: auto; margin-right: auto; max-width: 640px;">
            <span class="eyebrow" style="justify-content:center">Πυλώνες</span>
            <h2 class="h-2 mt-2">Οι αρχές που μας οδηγούν</h2>
            <p class="lead mt-4">Τέσσερις αρχές που καθορίζουν τον τρόπο που εργαζόμαστε. Όχι slogan — οδηγός για κάθε απόφαση.</p>
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
<section class="section about-different">
    <div class="container">
        <div class="section-head row-split" style="align-items: end;">
            <div class="reveal reveal-left">
                <span class="eyebrow">Τι μας ξεχωρίζει</span>
                <h2 class="h-2 mt-2" style="max-width: 18ch;">Τρεις διαφορές που έχουν σημασία.</h2>
            </div>
            <div class="reveal reveal-right" style="text-align: right;">
                <a class="btn btn-ghost" href="<?php echo esc_url( mourtzilaki_page_url( 'services' ) ); ?>">Δείτε τους τομείς <span class="arrow">→</span></a>
            </div>
        </div>
        <div class="diff-grid">
            <article class="diff-block reveal reveal-up reveal-d1">
                <span class="diff-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="9"/>
                        <polyline points="12,7 12,12 15,15"/>
                    </svg>
                </span>
                <h3>Άμεση πρόσβαση στη δικηγόρο</h3>
                <p>Δεν περνάτε από βοηθούς. Η δικηγόρος που αναλαμβάνει την υπόθεσή σας είναι αυτή που σας απαντάει στο τηλέφωνο, στο email και σε κάθε εξέλιξη.</p>
            </article>
            <article class="diff-block reveal reveal-up reveal-d2">
                <span class="diff-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 12h2M19 12h2M12 3v2M12 19v2M5.6 5.6l1.4 1.4M17 17l1.4 1.4M5.6 18.4l1.4-1.4M17 7l1.4-1.4"/>
                        <circle cx="12" cy="12" r="4"/>
                    </svg>
                </span>
                <h3>Διαφανές κόστος, χωρίς εκπλήξεις</h3>
                <p>Πριν αναλάβουμε οποιαδήποτε ενέργεια, σας δίνουμε γραπτή πρόταση με σαφές κόστος και χρονοδιάγραμμα. Καμία κρυφή χρέωση, καμία αλλαγή ορόφου χωρίς συνεννόηση.</p>
            </article>
            <article class="diff-block reveal reveal-up reveal-d3">
                <span class="diff-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22,4 12,14.01 9,11.01"/>
                    </svg>
                </span>
                <h3>Ρεαλιστική στρατηγική, πάντα</h3>
                <p>Δεν υποσχόμαστε ό,τι ο πελάτης θέλει να ακούσει. Σας λέμε τι μπορεί ρεαλιστικά να επιτευχθεί, με ποιο κόστος, σε ποιο χρόνο — και ποιοι είναι οι κίνδυνοι.</p>
            </article>
        </div>
    </div>
</section>

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
                <h2 class="h-2 mt-2">Καθαρές αποφάσεις, χωρίς ορολογίες.</h2>
                <p class="lead mt-4"><?php echo esc_html( $g( 'about_mission_text' ) ); ?></p>

                <blockquote class="founder-quote">
                    «Ο πελάτης πρέπει να φεύγει από το γραφείο μας ξέροντας ακριβώς πού στέκεται και τι θα κάνουμε τις επόμενες εβδομάδες. Όχι αόριστες υποσχέσεις.»
                    <cite>— από συζήτηση με νέο πελάτη, 2024</cite>
                </blockquote>
            </div>
        </div>
    </div>
</section>

<!-- 7. CTA -->
<section class="cta-band">
    <div class="container row-split">
        <h2 class="h-2 reveal reveal-left">Γνωρίστε την ομάδα μας.</h2>
        <div class="reveal reveal-right">
            <p>Οι δικηγόροι του γραφείου συνδυάζουν εξειδίκευση και εμπειρία σε διαφορετικούς τομείς δικαίου, με κοινό άξονα την προσωπική σχέση εμπιστοσύνης.</p>
            <p class="mt-4">
                <a class="btn btn-primary" href="<?php echo esc_url( mourtzilaki_page_url( 'team' ) ); ?>">Δείτε την ομάδα <span class="arrow">→</span></a>
                <a class="btn btn-ghost-light" href="<?php echo esc_url( mourtzilaki_page_url( 'contact' ) ); ?>">Επικοινωνία</a>
            </p>
        </div>
    </div>
</section>

<?php get_footer(); ?>
