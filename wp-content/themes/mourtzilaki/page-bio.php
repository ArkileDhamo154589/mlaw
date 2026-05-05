<?php
/**
 * Biography page (Βιογραφικό) — cinematic redesign.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
$h       = mourtzilaki_page_hero();
$team    = mourtzilaki_team();
$lead    = $team[0] ?? null;
$tpl_uri = get_template_directory_uri();

// Combined timeline (education + career).
$timeline = array(
    array( 'year' => '1998', 'type' => 'edu',  'title' => 'Έναρξη σπουδών Νομικής', 'org' => 'Νομική Σχολή ΕΚΠΑ' ),
    array( 'year' => '2003', 'type' => 'edu',  'title' => 'Πτυχίο Νομικής', 'org' => 'ΕΚΠΑ', 'note' => 'Διάκριση «Άριστα»' ),
    array( 'year' => '2005', 'type' => 'edu',  'title' => 'Μεταπτυχιακό (LL.M.)', 'org' => 'Αστικό & Εμπορικό Δίκαιο · ΕΚΠΑ' ),
    array( 'year' => '2005', 'type' => 'work', 'title' => 'Έναρξη επαγγέλματος', 'org' => 'Ασκούμενη δικηγόρος' ),
    array( 'year' => '2008', 'type' => 'work', 'title' => 'Συνεργάτιδα', 'org' => 'Δικηγορικό γραφείο Αθηνών' ),
    array( 'year' => '2010', 'type' => 'edu',  'title' => 'Διεθνές Εμπορικό Δίκαιο', 'org' => "King's College London", 'note' => 'Συνοπτικός κύκλος' ),
    array( 'year' => '2012', 'type' => 'work', 'title' => 'Ίδρυση δικηγορικού γραφείου', 'org' => 'Mourtzilaki Law', 'highlight' => true ),
    array( 'year' => '2018', 'type' => 'cert', 'title' => 'Διαπιστευμένη Διαμεσολαβήτρια', 'org' => 'Αστικές & εμπορικές διαφορές' ),
    array( 'year' => '2024', 'type' => 'cert', 'title' => 'Δικηγόρος παρ’ Αρείω Πάγω', 'org' => 'Ανώτατος βαθμός δικηγορίας' ),
);

$expertise_primary = array( 'Εμπορικό &amp; Εταιρικό', 'Αστικό &amp; Ενοχικό', 'Ακίνητα &amp; Κτηματολόγιο' );
$expertise_secondary = array( 'Οικογενειακό', 'Εργατικό', 'Τραπεζικό' );
$expertise_other = array( 'Διοικητικό', 'Φορολογικό', 'Ποινικό', 'Διαμεσολάβηση' );

$languages = array(
    array( 'lang' => 'Ελληνικά', 'level' => 'Μητρική',         'pct' => 100, 'cefr' => '' ),
    array( 'lang' => 'Αγγλικά',  'level' => 'Άριστη γνώση',    'pct' => 95,  'cefr' => 'C2' ),
    array( 'lang' => 'Γαλλικά',  'level' => 'Πολύ καλή γνώση', 'pct' => 75,  'cefr' => 'C1' ),
);

$memberships = array(
    array( 'title' => 'Δικηγορικός Σύλλογος Αθηνών',  'note' => 'Ενεργό μέλος από 2005', 'icon' => 'building' ),
    array( 'title' => 'Παρ’ Αρείω Πάγω',              'note' => 'Ανώτατος βαθμός δικηγορίας', 'icon' => 'scale' ),
    array( 'title' => 'Ένωση Ελλήνων Εμπορικολόγων',  'note' => 'Επιστημονική ένωση',     'icon' => 'badge' ),
    array( 'title' => 'Διαπιστευμένη Διαμεσολαβήτρια','note' => 'Υπουργείο Δικαιοσύνης',   'icon' => 'handshake' ),
);

$publications = array(
    array( 'year' => '2022', 'type' => 'Άρθρο',   'title' => 'Η εξέλιξη της νομολογίας στις τραπεζικές διαφορές', 'venue' => 'Νομικό Βήμα' ),
    array( 'year' => '2019', 'type' => 'Ομιλία',  'title' => 'Σύγχρονες προκλήσεις στο εργατικό δίκαιο',          'venue' => 'Συνέδριο ΔΣΑ' ),
    array( 'year' => '2017', 'type' => 'Συμμετοχή', 'title' => 'Δίκαιο των Συμβάσεων — συλλογικός τόμος',         'venue' => 'Νομική Βιβλιοθήκη' ),
    array( 'year' => '2015', 'type' => 'Άρθρο',   'title' => 'Διορθώσεις πρώτων εγγραφών στο Κτηματολόγιο',     'venue' => 'Δίκαιο &amp; Ακίνητα' ),
);

$mship_icons = array(
    'building'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21V8l5-3 5 3v13"/><path d="M13 21V11l5-3 5 3v10"/><path d="M3 21h18"/></svg>',
    'scale'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v18M3 7h18M7 7l-3 7c0 2 1.5 3 3 3s3-1 3-3l-3-7zM17 7l-3 7c0 2 1.5 3 3 3s3-1 3-3l-3-7zM7 21h10"/></svg>',
    'badge'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="9" r="6"/><polyline points="8.21,13.89 7,22 12,19 17,22 15.79,13.88"/></svg>',
    'handshake' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 17l-3-3m6 0l3 3M5 11l3-3 4 4 4-4 3 3M3 11l8 8 8-8M3 11h2m14 0h2"/></svg>',
);

$photo = $lead['photo'] ?? '';
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
                    <img src="<?php echo esc_url( $photo ); ?>" alt="<?php echo esc_attr( $lead['name'] ); ?>">
                </div>
                <div class="bp-corners" aria-hidden="true">
                    <span class="c c-tl"></span><span class="c c-tr"></span>
                    <span class="c c-bl"></span><span class="c c-br"></span>
                </div>
                <span class="bp-badge">
                    <span class="bb-num">20<sup>+</sup></span>
                    <span class="bb-lab">χρόνια εμπειρίας</span>
                </span>
            </div>
            <?php endif; ?>

            <div class="bio-intro reveal reveal-right">
                <span class="eyebrow"><?php echo esc_html( ! empty( $h['eyebrow'] ) ? $h['eyebrow'] : 'Βιογραφικό' ); ?></span>
                <h1 class="bio-name"><?php echo esc_html( ! empty( $h['title'] ) ? $h['title'] : ( $lead['name'] ?? '' ) ); ?></h1>
                <p class="bio-role"><?php echo esc_html( $lead['role'] ?? '' ); ?></p>

                <div class="bio-badges">
                    <span class="bio-badge"><span class="bbg-dot"></span> Δ.Σ.Α.</span>
                    <span class="bio-badge"><span class="bbg-dot"></span> Παρ’ Αρείω Πάγω</span>
                    <span class="bio-badge"><span class="bbg-dot"></span> LL.M.</span>
                    <span class="bio-badge"><span class="bbg-dot"></span> Διαμεσολαβήτρια</span>
                </div>

                <p class="bio-tagline"><?php echo esc_html( ! empty( $h['lead'] ) ? $h['lead'] : ( $lead['bio'] ?? '' ) ); ?></p>

                <div class="bio-cta">
                    <a class="btn btn-primary" href="<?php echo esc_url( mourtzilaki_page_url( 'contact' ) ); ?>">Κλείστε ραντεβού <span class="arrow">→</span></a>
                    <a class="btn btn-ghost" href="#bio-timeline">Δείτε την πορεία</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 2. QUICK STATS -->
<section class="bio-stats">
    <div class="container">
        <div class="bs-grid reveal reveal-up">
            <div class="bs-item"><div class="bs-num">20<sup>+</sup></div><div class="bs-lab">Έτη επαγγέλματος</div></div>
            <div class="bs-sep"></div>
            <div class="bs-item"><div class="bs-num">800<sup>+</sup></div><div class="bs-lab">Υποθέσεις</div></div>
            <div class="bs-sep"></div>
            <div class="bs-item"><div class="bs-num">8</div><div class="bs-lab">Τομείς εξειδίκευσης</div></div>
            <div class="bs-sep"></div>
            <div class="bs-item"><div class="bs-num">3</div><div class="bs-lab">Γλώσσες</div></div>
            <div class="bs-sep"></div>
            <div class="bs-item"><div class="bs-num">5,0<span class="bs-star">★</span></div><div class="bs-lab">Αξιολογήσεις</div></div>
        </div>
    </div>
</section>

<!-- 3. PHILOSOPHY QUOTE -->
<section class="section bio-philosophy">
    <div class="container container-narrow text-center">
        <span class="eyebrow" style="justify-content:center">Φιλοσοφία</span>
        <p class="bio-quote reveal reveal-fade">
            <span class="bq-mark" aria-hidden="true">“</span>
            Δεν είμαι εδώ για να σας πω τι θέλετε να ακούσετε. Είμαι εδώ για να σας πω τι μπορεί ρεαλιστικά να γίνει — και να κάνω καθετί δυνατό για να γίνει.
        </p>
        <span class="bq-attr">— <?php echo esc_html( $lead['name'] ?? '' ); ?></span>
    </div>
</section>

<!-- 4. TIMELINE -->
<section id="bio-timeline" class="section section-soft bio-timeline-section">
    <div class="container">
        <div class="section-head reveal reveal-up" style="text-align: center; max-width: 640px; margin-left: auto; margin-right: auto;">
            <span class="eyebrow" style="justify-content:center">Πορεία</span>
            <h2 class="h-2 mt-2">Σπουδές &amp; επαγγελματική διαδρομή</h2>
            <p class="lead mt-4">Από το πρώτο εξάμηνο της Νομικής μέχρι τον ανώτατο βαθμό δικηγορίας — μια διαδρομή 25+ ετών.</p>
        </div>

        <div class="timeline">
            <span class="timeline-line" aria-hidden="true"></span>
            <?php foreach ( $timeline as $i => $item ) :
                $type_label = array( 'edu' => 'Σπουδές', 'work' => 'Εργασία', 'cert' => 'Πιστοποίηση' );
                $type = $item['type'];
                $is_hl = ! empty( $item['highlight'] );
            ?>
                <div class="tl-item reveal reveal-up<?php echo $is_hl ? ' tl-item-highlight' : ''; ?>">
                    <div class="tl-year">
                        <span class="tly-num"><?php echo esc_html( $item['year'] ); ?></span>
                        <span class="tly-dot" aria-hidden="true"></span>
                    </div>
                    <div class="tl-card">
                        <span class="tl-type tl-type-<?php echo esc_attr( $type ); ?>"><?php echo esc_html( $type_label[ $type ] ?? '' ); ?></span>
                        <h3 class="tl-title"><?php echo esc_html( $item['title'] ); ?></h3>
                        <?php if ( ! empty( $item['org'] ) ) : ?>
                            <p class="tl-org"><?php echo esc_html( $item['org'] ); ?></p>
                        <?php endif; ?>
                        <?php if ( ! empty( $item['note'] ) ) : ?>
                            <p class="tl-note"><?php echo esc_html( $item['note'] ); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 5. EXPERTISE -->
<section class="section bio-expertise">
    <div class="container">
        <div class="section-head reveal reveal-up">
            <span class="eyebrow">Εξειδίκευση</span>
            <h2 class="h-2 mt-2" style="max-width: 22ch;">Τομείς δικαίου που χειρίζομαι.</h2>
        </div>
        <div class="exp-groups">
            <div class="exp-group reveal reveal-up">
                <h3 class="exp-h">Πρωτεύοντες</h3>
                <ul class="exp-tags exp-primary">
                    <?php foreach ( $expertise_primary as $tag ) : ?>
                        <li><?php echo wp_kses( $tag, array() ); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="exp-group reveal reveal-up">
                <h3 class="exp-h">Δευτερεύοντες</h3>
                <ul class="exp-tags exp-secondary">
                    <?php foreach ( $expertise_secondary as $tag ) : ?>
                        <li><?php echo wp_kses( $tag, array() ); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="exp-group reveal reveal-up">
                <h3 class="exp-h">Πρόσθετοι τομείς</h3>
                <ul class="exp-tags exp-tertiary">
                    <?php foreach ( $expertise_other as $tag ) : ?>
                        <li><?php echo wp_kses( $tag, array() ); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- 6. MEMBERSHIPS -->
<section class="section section-soft bio-memberships">
    <div class="container">
        <div class="section-head reveal reveal-up">
            <span class="eyebrow">Ιδιότητες &amp; συμμετοχές</span>
            <h2 class="h-2 mt-2">Πιστοποιήσεις &amp; επιστημονικοί σύλλογοι.</h2>
        </div>
        <div class="mem-grid">
            <?php foreach ( $memberships as $i => $m ) : ?>
                <article class="mem-card reveal reveal-up reveal-d<?php echo (int) min( $i + 1, 6 ); ?>">
                    <span class="mem-icon" aria-hidden="true"><?php echo $mship_icons[ $m['icon'] ] ?? ''; ?></span>
                    <h3 class="mem-title"><?php echo esc_html( $m['title'] ); ?></h3>
                    <p class="mem-note"><?php echo esc_html( $m['note'] ); ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 7. LANGUAGES -->
<section class="section bio-languages">
    <div class="container container-narrow">
        <div class="section-head reveal reveal-up">
            <span class="eyebrow">Γλώσσες</span>
            <h2 class="h-2 mt-2">Επικοινωνία σε τρεις γλώσσες.</h2>
        </div>
        <div class="lang-list">
            <?php foreach ( $languages as $i => $l ) : ?>
                <article class="lang-row reveal reveal-up reveal-d<?php echo (int) min( $i + 1, 6 ); ?>">
                    <div class="lr-name">
                        <span class="lr-lang"><?php echo esc_html( $l['lang'] ); ?></span>
                        <?php if ( ! empty( $l['cefr'] ) ) : ?>
                            <span class="lr-cefr"><?php echo esc_html( $l['cefr'] ); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="lr-bar" aria-hidden="true">
                        <span class="lr-fill" style="--pct: <?php echo (int) $l['pct']; ?>%;"></span>
                    </div>
                    <div class="lr-level"><?php echo esc_html( $l['level'] ); ?></div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 8. PUBLICATIONS -->
<section class="section section-soft bio-publications">
    <div class="container">
        <div class="section-head reveal reveal-up">
            <span class="eyebrow">Δημοσιεύσεις &amp; ομιλίες</span>
            <h2 class="h-2 mt-2">Επιλεγμένες συμβολές στη νομική κοινότητα.</h2>
        </div>
        <div class="pub-grid">
            <?php foreach ( $publications as $i => $p ) : ?>
                <article class="pub-card reveal reveal-up reveal-d<?php echo (int) min( $i + 1, 6 ); ?>">
                    <div class="pub-meta">
                        <span class="pub-year"><?php echo esc_html( $p['year'] ); ?></span>
                        <span class="pub-type"><?php echo esc_html( $p['type'] ); ?></span>
                    </div>
                    <h3 class="pub-title"><?php echo esc_html( $p['title'] ); ?></h3>
                    <p class="pub-venue"><?php echo wp_kses( $p['venue'], array() ); ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

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
            <span class="eyebrow" style="color: var(--gold)">Ραντεβού</span>
            <h2 class="h-display mt-2" style="max-width: 22ch;">Επικοινωνήστε απευθείας με την <?php echo esc_html( $lead['name'] ?? 'δικηγόρο' ); ?>.</h2>
            <p class="lead mt-4" style="max-width: 60ch;">Πρώτη συνάντηση διάρκειας 30 λεπτών για να αξιολογήσουμε αν μπορώ να βοηθήσω. Διακριτική, εμπιστευτική, χωρίς χρέωση.</p>
            <div class="bio-cta-actions mt-6">
                <a class="btn btn-primary" href="<?php echo esc_url( mourtzilaki_page_url( 'contact' ) ); ?>">Φόρμα επικοινωνίας <span class="arrow">→</span></a>
                <?php $cinfo = mourtzilaki_get_contact_info(); ?>
                <a class="btn btn-ghost-light" href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $cinfo['phone'] ) ); ?>"><?php echo esc_html( $cinfo['phone'] ); ?></a>
                <a class="btn btn-ghost-light" href="mailto:<?php echo esc_attr( $cinfo['email'] ); ?>"><?php echo esc_html( $cinfo['email'] ); ?></a>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
