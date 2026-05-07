<?php
/**
 * Biography page (Βιογραφικό) — fully ACF-driven.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();

$h       = mourtzilaki_page_hero();
$team    = mourtzilaki_team();
$lead    = $team[0] ?? null;
$page_id = get_queried_object_id();

$f = function ( $name, $fallback = '' ) use ( $page_id ) {
    if ( ! function_exists( 'get_field' ) ) { return $fallback; }
    $v = get_field( $name, $page_id );
    if ( is_array( $v ) ) { return $v; }
    $v = (string) $v;
    return '' !== trim( $v ) ? $v : $fallback;
};

$photo     = $lead['photo'] ?? '';
$lead_name = $lead['name'] ?? 'δικηγόρο';
$lead_role = $f( 'bio_role_override', $lead['role'] ?? '' );

// Hero badges
$hero_badges_raw = $f( 'bio_hero_badges', "Δ.Σ.Α.\nΠαρ' Αρείω Πάγω\nLL.M.\nΔιαμεσολαβήτρια" );
$hero_badges     = array_filter( array_map( 'trim', preg_split( "/\r\n|\r|\n/", $hero_badges_raw ) ) );

$years_num = $f( 'bio_years_badge_num',   '20+' );
$years_lab = $f( 'bio_years_badge_label', 'χρόνια εμπειρίας' );

$cta1_label = $f( 'bio_cta_primary_label', 'Κλείστε ραντεβού' );
$cta1_url   = $f( 'bio_cta_primary_url',   mourtzilaki_page_url( 'contact' ) );
$cta2_label = $f( 'bio_cta_secondary_label', 'Δείτε την πορεία' );
$cta2_url   = $f( 'bio_cta_secondary_anchor', '#bio-timeline' );

// Stats
$stats_default = "20+ | Έτη επαγγέλματος\n800+ | Υποθέσεις\n8 | Τομείς εξειδίκευσης\n3 | Γλώσσες\n5,0★ | Αξιολογήσεις";
$stats         = mourtzilaki_parse_lines( $f( 'bio_stats', $stats_default ) );

// Philosophy
$phil_eb    = $f( 'bio_philosophy_eyebrow', 'Φιλοσοφία' );
$phil_quote = $f( 'bio_philosophy_quote', '<p>Δεν είμαι εδώ για να σας πω τι θέλετε να ακούσετε. Είμαι εδώ για να σας πω τι μπορεί ρεαλιστικά να γίνει — και να κάνω καθετί δυνατό για να γίνει.</p>' );
$phil_attr  = $f( 'bio_philosophy_attr', $lead_name );

// Timeline
$tl_eb    = $f( 'bio_timeline_eyebrow', 'Πορεία' );
$tl_title = $f( 'bio_timeline_title',   'Σπουδές & επαγγελματική διαδρομή' );
$tl_lead  = $f( 'bio_timeline_lead',    'Από το πρώτο εξάμηνο της Νομικής μέχρι τον ανώτατο βαθμό δικηγορίας — μια διαδρομή 25+ ετών.' );
$tl_default = "1998 | edu | Έναρξη σπουδών Νομικής | Νομική Σχολή ΕΚΠΑ | |\n" .
              "2003 | edu | Πτυχίο Νομικής | ΕΚΠΑ | Διάκριση «Άριστα» |\n" .
              "2005 | edu | Μεταπτυχιακό (LL.M.) | Αστικό & Εμπορικό Δίκαιο · ΕΚΠΑ | |\n" .
              "2005 | work | Έναρξη επαγγέλματος | Ασκούμενη δικηγόρος | |\n" .
              "2008 | work | Συνεργάτιδα | Δικηγορικό γραφείο Αθηνών | |\n" .
              "2010 | edu | Διεθνές Εμπορικό Δίκαιο | King's College London | Συνοπτικός κύκλος |\n" .
              "2012 | work | Ίδρυση δικηγορικού γραφείου | Mourtzilaki Law | | *\n" .
              "2018 | cert | Διαπιστευμένη Διαμεσολαβήτρια | Αστικές & εμπορικές διαφορές | |\n" .
              "2024 | cert | Δικηγόρος παρ' Αρείω Πάγω | Ανώτατος βαθμός δικηγορίας | |";
$timeline_rows = mourtzilaki_parse_lines( $f( 'bio_timeline', $tl_default ) );

// Expertise
$exp_eb    = $f( 'bio_expertise_eyebrow', 'Εξειδίκευση' );
$exp_title = $f( 'bio_expertise_title',   'Τομείς δικαίου που χειρίζομαι.' );
$exp_groups = array(
    array(
        'title' => $f( 'bio_expertise_t1_title', 'Πρωτεύοντες' ),
        'class' => 'exp-primary',
        'tags'  => array_filter( array_map( 'trim', preg_split( "/\r\n|\r|\n/", $f( 'bio_expertise_t1_tags', "Εμπορικό & Εταιρικό\nΑστικό & Ενοχικό\nΑκίνητα & Κτηματολόγιο" ) ) ) ),
    ),
    array(
        'title' => $f( 'bio_expertise_t2_title', 'Δευτερεύοντες' ),
        'class' => 'exp-secondary',
        'tags'  => array_filter( array_map( 'trim', preg_split( "/\r\n|\r|\n/", $f( 'bio_expertise_t2_tags', "Οικογενειακό\nΕργατικό\nΤραπεζικό" ) ) ) ),
    ),
    array(
        'title' => $f( 'bio_expertise_t3_title', 'Πρόσθετοι τομείς' ),
        'class' => 'exp-tertiary',
        'tags'  => array_filter( array_map( 'trim', preg_split( "/\r\n|\r|\n/", $f( 'bio_expertise_t3_tags', "Διοικητικό\nΦορολογικό\nΠοινικό\nΔιαμεσολάβηση" ) ) ) ),
    ),
);

// Memberships
$mem_eb    = $f( 'bio_memberships_eyebrow', 'Ιδιότητες & συμμετοχές' );
$mem_title = $f( 'bio_memberships_title',   'Πιστοποιήσεις & επιστημονικοί σύλλογοι.' );
$mem_default = "Δικηγορικός Σύλλογος Αθηνών | Ενεργό μέλος από 2005 | building\n" .
               "Παρ' Αρείω Πάγω | Ανώτατος βαθμός δικηγορίας | scale\n" .
               "Ένωση Ελλήνων Εμπορικολόγων | Επιστημονική ένωση | badge\n" .
               "Διαπιστευμένη Διαμεσολαβήτρια | Υπουργείο Δικαιοσύνης | handshake";
$memberships = mourtzilaki_parse_lines( $f( 'bio_memberships', $mem_default ) );

// Languages
$lang_eb    = $f( 'bio_languages_eyebrow', 'Γλώσσες' );
$lang_title = $f( 'bio_languages_title',   'Επικοινωνία σε τρεις γλώσσες.' );
$lang_default = "Ελληνικά | Μητρική | 100 |\n" .
                "Αγγλικά | Άριστη γνώση | 95 | C2\n" .
                "Γαλλικά | Πολύ καλή γνώση | 75 | C1";
$languages = mourtzilaki_parse_lines( $f( 'bio_languages', $lang_default ) );

// Publications
$pub_eb    = $f( 'bio_publications_eyebrow', 'Δημοσιεύσεις & ομιλίες' );
$pub_title = $f( 'bio_publications_title',   'Επιλεγμένες συμβολές στη νομική κοινότητα.' );
$pub_default = "2022 | Άρθρο | Η εξέλιξη της νομολογίας στις τραπεζικές διαφορές | Νομικό Βήμα\n" .
               "2019 | Ομιλία | Σύγχρονες προκλήσεις στο εργατικό δίκαιο | Συνέδριο ΔΣΑ\n" .
               "2017 | Συμμετοχή | Δίκαιο των Συμβάσεων — συλλογικός τόμος | Νομική Βιβλιοθήκη\n" .
               "2015 | Άρθρο | Διορθώσεις πρώτων εγγραφών στο Κτηματολόγιο | Δίκαιο & Ακίνητα";
$publications = mourtzilaki_parse_lines( $f( 'bio_publications', $pub_default ) );

// CTA
$cta_eb        = $f( 'bio_cta_eyebrow', 'Ραντεβού' );
$cta_title_raw = $f( 'bio_cta_title',   'Επικοινωνήστε απευθείας με την {name}.' );
$cta_title     = str_replace( '{name}', $lead_name, $cta_title_raw );
$cta_lead      = $f( 'bio_cta_lead',    'Πρώτη συνάντηση διάρκειας 30 λεπτών για να αξιολογήσουμε αν μπορώ να βοηθήσω. Διακριτική, εμπιστευτική, χωρίς χρέωση.' );
$cta_form_lab  = $f( 'bio_cta_form_label', 'Φόρμα επικοινωνίας' );
$cta_form_url  = $f( 'bio_cta_form_url',   mourtzilaki_page_url( 'contact' ) );

// Membership icons (kept in template, not editable)
$mship_icons = array(
    'building'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21V8l5-3 5 3v13"/><path d="M13 21V11l5-3 5 3v10"/><path d="M3 21h18"/></svg>',
    'scale'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v18M3 7h18M7 7l-3 7c0 2 1.5 3 3 3s3-1 3-3l-3-7zM17 7l-3 7c0 2 1.5 3 3 3s3-1 3-3l-3-7zM7 21h10"/></svg>',
    'badge'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="9" r="6"/><polyline points="8.21,13.89 7,22 12,19 17,22 15.79,13.88"/></svg>',
    'handshake' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 17l-3-3m6 0l3 3M5 11l3-3 4 4 4-4 3 3M3 11l8 8 8-8M3 11h2m14 0h2"/></svg>',
);

$type_label = array( 'edu' => 'Σπουδές', 'work' => 'Εργασία', 'cert' => 'Πιστοποίηση' );
?>

<!-- 1. CINEMATIC HERO -->
<section class="bio-hero">
    <span class="deco deco-bio-scales" aria-hidden="true">
        <svg viewBox="0 0 200 200" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M100 30 L100 170M70 170 L130 170M40 50 L160 50"/>
            <circle cx="100" cy="40" r="4" fill="currentColor"/>
            <path d="M40 50 L25 95 L55 95 Z M160 50 L145 95 L175 95 Z"/>
        </svg>
    </span>
    <div class="container">
        <div class="bio-hero-grid">
            <?php if ( $photo ) : ?>
            <div class="bio-portrait reveal reveal-left">
                <div class="bp-frame">
                    <img src="<?php echo esc_url( $photo ); ?>" alt="<?php echo esc_attr( $lead_name ); ?>">
                </div>
                <div class="bp-corners" aria-hidden="true">
                    <span class="c c-tl"></span><span class="c c-tr"></span>
                    <span class="c c-bl"></span><span class="c c-br"></span>
                </div>
                <span class="bp-badge">
                    <span class="bb-num"><?php echo wp_kses( str_replace( '+', '<sup>+</sup>', esc_html( $years_num ) ), array( 'sup' => array() ) ); ?></span>
                    <span class="bb-lab"><?php echo esc_html( $years_lab ); ?></span>
                </span>
            </div>
            <?php endif; ?>

            <div class="bio-intro reveal reveal-right">
                <span class="eyebrow"><?php echo esc_html( ! empty( $h['eyebrow'] ) ? $h['eyebrow'] : 'Βιογραφικό' ); ?></span>
                <h1 class="bio-name"><?php echo esc_html( ! empty( $h['title'] ) ? $h['title'] : $lead_name ); ?></h1>
                <?php if ( $lead_role ) : ?>
                    <p class="bio-role"><?php echo esc_html( $lead_role ); ?></p>
                <?php endif; ?>

                <?php if ( ! empty( $hero_badges ) ) : ?>
                    <div class="bio-badges">
                        <?php foreach ( $hero_badges as $b ) : ?>
                            <span class="bio-badge"><span class="bbg-dot"></span> <?php echo esc_html( $b ); ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <p class="bio-tagline"><?php echo mourtzilaki_field_inline( ! empty( $h['lead'] ) ? $h['lead'] : ( $lead['bio'] ?? '' ) ); ?></p>

                <div class="bio-cta">
                    <a class="btn btn-primary" href="<?php echo esc_url( $cta1_url ); ?>"><?php echo esc_html( $cta1_label ); ?> <span class="arrow">→</span></a>
                    <a class="btn btn-ghost" href="<?php echo esc_url( $cta2_url ); ?>"><?php echo esc_html( $cta2_label ); ?></a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 2. QUICK STATS -->
<?php if ( ! empty( $stats ) ) : ?>
<section class="bio-stats">
    <div class="container">
        <div class="bs-grid reveal reveal-up">
            <?php foreach ( $stats as $row ) :
                $num = $row[0] ?? '';
                $lab = $row[1] ?? '';
                if ( '' === $num && '' === $lab ) { continue; }
                ?>
                <div class="bs-item">
                    <div class="bs-num"><?php
                        $num_html = esc_html( $num );
                        $num_html = preg_replace( '/\+/', '<sup>+</sup>', $num_html );
                        $num_html = preg_replace( '/★/', '<span class="bs-star">★</span>', $num_html );
                        echo $num_html; // sup/span only
                    ?></div>
                    <div class="bs-lab"><?php echo esc_html( $lab ); ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- 3. PHILOSOPHY QUOTE -->
<?php if ( $phil_quote ) : ?>
<section class="section bio-philosophy">
    <div class="container container-narrow text-center">
        <span class="eyebrow" style="justify-content:center"><?php echo esc_html( $phil_eb ); ?></span>
        <p class="bio-quote reveal reveal-fade">
            <span class="bq-mark" aria-hidden="true">“</span>
            <?php echo mourtzilaki_field_inline( $phil_quote ); ?>
        </p>
        <?php if ( $phil_attr ) : ?>
            <span class="bq-attr">— <?php echo esc_html( $phil_attr ); ?></span>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<!-- 4. TIMELINE -->
<?php if ( ! empty( $timeline_rows ) ) : ?>
<section id="bio-timeline" class="section section-soft bio-timeline-section">
    <div class="container">
        <div class="section-head reveal reveal-up" style="text-align: center; max-width: 640px; margin-left: auto; margin-right: auto;">
            <span class="eyebrow" style="justify-content:center"><?php echo esc_html( $tl_eb ); ?></span>
            <h2 class="h-2 mt-2"><?php echo esc_html( $tl_title ); ?></h2>
            <p class="lead mt-4"><?php echo mourtzilaki_field_inline( $tl_lead ); ?></p>
        </div>

        <div class="timeline">
            <span class="timeline-line" aria-hidden="true"></span>
            <?php foreach ( $timeline_rows as $row ) :
                $year     = $row[0] ?? '';
                $type     = $row[1] ?? 'work';
                $title    = $row[2] ?? '';
                $org      = $row[3] ?? '';
                $note     = $row[4] ?? '';
                $is_hl    = ! empty( $row[5] ) && '*' === trim( $row[5] );
                if ( '' === $title && '' === $year ) { continue; }
            ?>
                <div class="tl-item reveal reveal-up<?php echo $is_hl ? ' tl-item-highlight' : ''; ?>">
                    <div class="tl-year">
                        <span class="tly-num"><?php echo esc_html( $year ); ?></span>
                        <span class="tly-dot" aria-hidden="true"></span>
                    </div>
                    <div class="tl-card">
                        <span class="tl-type tl-type-<?php echo esc_attr( $type ); ?>"><?php echo esc_html( $type_label[ $type ] ?? $type ); ?></span>
                        <h3 class="tl-title"><?php echo esc_html( $title ); ?></h3>
                        <?php if ( '' !== $org ) : ?>
                            <p class="tl-org"><?php echo esc_html( $org ); ?></p>
                        <?php endif; ?>
                        <?php if ( '' !== $note ) : ?>
                            <p class="tl-note"><?php echo esc_html( $note ); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- 5. EXPERTISE -->
<?php
$has_any_exp = false;
foreach ( $exp_groups as $eg ) { if ( ! empty( $eg['tags'] ) ) { $has_any_exp = true; break; } }
?>
<?php if ( $has_any_exp ) : ?>
<section class="section bio-expertise">
    <div class="container">
        <div class="section-head reveal reveal-up">
            <span class="eyebrow"><?php echo esc_html( $exp_eb ); ?></span>
            <h2 class="h-2 mt-2" style="max-width: 22ch;"><?php echo esc_html( $exp_title ); ?></h2>
        </div>
        <div class="exp-groups">
            <?php foreach ( $exp_groups as $eg ) : if ( empty( $eg['tags'] ) ) { continue; } ?>
                <div class="exp-group reveal reveal-up">
                    <h3 class="exp-h"><?php echo esc_html( $eg['title'] ); ?></h3>
                    <ul class="exp-tags <?php echo esc_attr( $eg['class'] ); ?>">
                        <?php foreach ( $eg['tags'] as $tag ) : ?>
                            <li><?php echo esc_html( $tag ); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- 6. MEMBERSHIPS -->
<?php if ( ! empty( $memberships ) ) : ?>
<section class="section section-soft bio-memberships">
    <div class="container">
        <div class="section-head reveal reveal-up">
            <span class="eyebrow"><?php echo esc_html( $mem_eb ); ?></span>
            <h2 class="h-2 mt-2"><?php echo esc_html( $mem_title ); ?></h2>
        </div>
        <div class="mem-grid">
            <?php foreach ( $memberships as $i => $row ) :
                $title = $row[0] ?? '';
                $note  = $row[1] ?? '';
                $icon  = $row[2] ?? 'building';
                if ( '' === $title ) { continue; }
            ?>
                <article class="mem-card reveal reveal-up reveal-d<?php echo (int) min( $i + 1, 6 ); ?>">
                    <span class="mem-icon" aria-hidden="true"><?php echo $mship_icons[ $icon ] ?? $mship_icons['building']; ?></span>
                    <h3 class="mem-title"><?php echo esc_html( $title ); ?></h3>
                    <?php if ( '' !== $note ) : ?>
                        <p class="mem-note"><?php echo esc_html( $note ); ?></p>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- 7. LANGUAGES -->
<?php if ( ! empty( $languages ) ) : ?>
<section class="section bio-languages">
    <div class="container container-narrow">
        <div class="section-head reveal reveal-up">
            <span class="eyebrow"><?php echo esc_html( $lang_eb ); ?></span>
            <h2 class="h-2 mt-2"><?php echo esc_html( $lang_title ); ?></h2>
        </div>
        <div class="lang-list">
            <?php foreach ( $languages as $i => $row ) :
                $lang  = $row[0] ?? '';
                $level = $row[1] ?? '';
                $pct   = (int) ( $row[2] ?? 0 );
                $cefr  = $row[3] ?? '';
                if ( '' === $lang ) { continue; }
            ?>
                <article class="lang-row reveal reveal-up reveal-d<?php echo (int) min( $i + 1, 6 ); ?>">
                    <div class="lr-name">
                        <span class="lr-lang"><?php echo esc_html( $lang ); ?></span>
                        <?php if ( '' !== $cefr ) : ?>
                            <span class="lr-cefr"><?php echo esc_html( $cefr ); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="lr-bar" aria-hidden="true">
                        <span class="lr-fill" style="--pct: <?php echo (int) $pct; ?>%;"></span>
                    </div>
                    <div class="lr-level"><?php echo esc_html( $level ); ?></div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- 8. PUBLICATIONS -->
<?php if ( ! empty( $publications ) ) : ?>
<section class="section section-soft bio-publications">
    <div class="container">
        <div class="section-head reveal reveal-up">
            <span class="eyebrow"><?php echo esc_html( $pub_eb ); ?></span>
            <h2 class="h-2 mt-2"><?php echo esc_html( $pub_title ); ?></h2>
        </div>
        <div class="pub-grid">
            <?php foreach ( $publications as $i => $row ) :
                $year  = $row[0] ?? '';
                $type  = $row[1] ?? '';
                $title = $row[2] ?? '';
                $venue = $row[3] ?? '';
                if ( '' === $title ) { continue; }
            ?>
                <article class="pub-card reveal reveal-up reveal-d<?php echo (int) min( $i + 1, 6 ); ?>">
                    <div class="pub-meta">
                        <span class="pub-year"><?php echo esc_html( $year ); ?></span>
                        <?php if ( '' !== $type ) : ?>
                            <span class="pub-type"><?php echo esc_html( $type ); ?></span>
                        <?php endif; ?>
                    </div>
                    <h3 class="pub-title"><?php echo esc_html( $title ); ?></h3>
                    <?php if ( '' !== $venue ) : ?>
                        <p class="pub-venue"><?php echo esc_html( $venue ); ?></p>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- 9. CONTACT CTA -->
<section class="bio-cta-section">
    <span class="deco deco-bio-pediment" aria-hidden="true">
        <svg viewBox="0 0 400 200" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
            <path d="M40 200 L200 40 L360 200"/>
            <path d="M20 200 L380 200"/>
            <path d="M60 200 L60 110M120 200 L120 110M280 200 L280 110M340 200 L340 110"/>
            <path d="M60 110 L340 110"/>
        </svg>
    </span>
    <div class="container">
        <div class="bio-cta-inner reveal reveal-fade">
            <span class="eyebrow" style="color: var(--gold)"><?php echo esc_html( $cta_eb ); ?></span>
            <h2 class="h-display mt-2" style="max-width: 22ch;"><?php echo esc_html( $cta_title ); ?></h2>
            <p class="lead mt-4" style="max-width: 60ch;"><?php echo mourtzilaki_field_inline( $cta_lead ); ?></p>
            <div class="bio-cta-actions mt-6">
                <a class="btn btn-primary" href="<?php echo esc_url( $cta_form_url ); ?>"><?php echo esc_html( $cta_form_lab ); ?> <span class="arrow">→</span></a>
                <?php $cinfo = mourtzilaki_get_contact_info(); ?>
                <a class="btn btn-ghost-light" href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $cinfo['phone'] ) ); ?>"><?php echo esc_html( $cinfo['phone'] ); ?></a>
                <a class="btn btn-ghost-light" href="mailto:<?php echo esc_attr( $cinfo['email'] ); ?>"><?php echo esc_html( $cinfo['email'] ); ?></a>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
