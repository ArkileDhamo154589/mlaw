<?php
/**
 * Biography page (Βιογραφικό).
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
$team = mourtzilaki_team();
$lead = $team[0];
$h    = mourtzilaki_page_hero();
?>

<section class="page-header">
    <div class="container container-narrow">
        <span class="eyebrow"><?php echo esc_html( ! empty( $h['eyebrow'] ) ? $h['eyebrow'] : 'Βιογραφικό' ); ?></span>
        <h1 class="h-1 mt-2"><?php echo esc_html( ! empty( $h['title'] ) ? $h['title'] : $lead['name'] ); ?></h1>
        <p class="lead"><?php echo esc_html( ! empty( $h['lead'] ) ? $h['lead'] : $lead['role'] . ' · Δικηγορικός Σύλλογος Αθηνών' ); ?></p>
    </div>
</section>

<section class="section section-tight">
    <div class="container">
        <div class="bio-top">
            <div class="bio-photo">
                <img src="<?php echo esc_url( $lead['photo'] ); ?>" alt="<?php echo esc_attr( $lead['name'] ); ?>" loading="lazy">
            </div>
            <div class="bio-intro">
                <p class="lead"><?php echo esc_html( $lead['bio'] ); ?></p>
                <p class="mt-4">Η Έλενα Μουρτζιλάκη ασκεί τη δικηγορία από το 2005, με συνεπή παρουσία στα ελληνικά δικαστήρια και ενεργή συμμετοχή σε σύνθετες υποθέσεις αστικού, εμπορικού και ποινικού δικαίου. Η μεθοδικότητα, η νομική ακρίβεια και η προσωπική σχέση με κάθε πελάτη αποτελούν τη βάση της εργασίας της.</p>
            </div>
        </div>
    </div>
</section>

<section class="section section-tight">
    <div class="container container-narrow">
        <div class="bio-block">
            <h2 class="h-3">Σπουδές</h2>
            <ul class="bio-list">
                <li>
                    <span class="when">2003 — 2005</span>
                    <span class="what"><strong>Μεταπτυχιακό Δίπλωμα Ειδίκευσης (LL.M.)</strong> στο Αστικό &amp; Εμπορικό Δίκαιο &middot; Νομική Σχολή ΕΚΠΑ</span>
                </li>
                <li>
                    <span class="when">1998 — 2003</span>
                    <span class="what"><strong>Πτυχίο Νομικής</strong> &middot; Νομική Σχολή ΕΚΠΑ &middot; Διάκριση «Άριστα»</span>
                </li>
                <li>
                    <span class="when">2010</span>
                    <span class="what"><strong>Επιμόρφωση στο Διεθνές Εμπορικό Δίκαιο</strong> &middot; King&rsquo;s College London (συνοπτικός κύκλος)</span>
                </li>
            </ul>
        </div>

        <div class="bio-block">
            <h2 class="h-3">Επαγγελματική πορεία</h2>
            <ul class="bio-list">
                <li>
                    <span class="when">2012 — σήμερα</span>
                    <span class="what"><strong>Ιδρύτρια &amp; Δικηγόρος</strong> &middot; Δικηγορικό Γραφείο Έλενας Μουρτζιλάκη</span>
                </li>
                <li>
                    <span class="when">2008 — 2012</span>
                    <span class="what"><strong>Συνεργάτιδα</strong> σε καταξιωμένο δικηγορικό γραφείο της Αθήνας &middot; Εμπορικό &amp; Εργατικό Δίκαιο</span>
                </li>
                <li>
                    <span class="when">2005 — 2008</span>
                    <span class="what"><strong>Ασκούμενη &amp; Δικηγόρος</strong> &middot; Πρακτική σε αστικές, εμπορικές και ακινήτων υποθέσεις</span>
                </li>
            </ul>
        </div>

        <div class="bio-block">
            <h2 class="h-3">Τομείς εξειδίκευσης</h2>
            <ul class="bio-tags">
                <li>Αστικό &amp; Ενοχικό Δίκαιο</li>
                <li>Εμπορικό &amp; Εταιρικό Δίκαιο</li>
                <li>Ακίνητα &amp; Κτηματολόγιο</li>
                <li>Οικογενειακό &amp; Κληρονομικό</li>
                <li>Εργατικό Δίκαιο</li>
                <li>Ποινικό Δίκαιο</li>
                <li>Διοικητικό &amp; Φορολογικό</li>
                <li>Τραπεζικό Δίκαιο</li>
            </ul>
        </div>

        <div class="bio-block">
            <h2 class="h-3">Συμμετοχές &amp; ιδιότητες</h2>
            <ul class="bio-list compact">
                <li><span class="what">Μέλος του <strong>Δικηγορικού Συλλόγου Αθηνών</strong> (ΔΣΑ)</span></li>
                <li><span class="what">Δικηγόρος <strong>παρ&rsquo; Αρείω Πάγω</strong></span></li>
                <li><span class="what">Μέλος <strong>Ένωσης Ελλήνων Εμπορικολόγων</strong></span></li>
                <li><span class="what">Διαπιστευμένη Διαμεσολαβήτρια αστικών &amp; εμπορικών διαφορών</span></li>
            </ul>
        </div>

        <div class="bio-block">
            <h2 class="h-3">Γλώσσες</h2>
            <ul class="bio-tags">
                <li>Ελληνικά &mdash; Μητρική</li>
                <li>Αγγλικά &mdash; Άριστη γνώση (C2)</li>
                <li>Γαλλικά &mdash; Πολύ καλή γνώση (C1)</li>
            </ul>
        </div>

        <div class="bio-block">
            <h2 class="h-3">Δημοσιεύσεις &amp; ομιλίες</h2>
            <ul class="bio-list">
                <li>
                    <span class="when">2022</span>
                    <span class="what">Άρθρο: «Η εξέλιξη της νομολογίας στις τραπεζικές διαφορές» &middot; Νομικό Βήμα</span>
                </li>
                <li>
                    <span class="when">2019</span>
                    <span class="what">Ομιλία: «Σύγχρονες προκλήσεις στο εργατικό δίκαιο» &middot; Συνέδριο ΔΣΑ</span>
                </li>
                <li>
                    <span class="when">2017</span>
                    <span class="what">Συμμετοχή σε συλλογικό τόμο για το Δίκαιο των Συμβάσεων</span>
                </li>
            </ul>
        </div>
    </div>
</section>

<section class="cta-band">
    <div class="container row-split">
        <h2 class="h-2">Θέλετε να συζητήσουμε την υπόθεσή σας;</h2>
        <div>
            <p>Κλείστε ένα αρχικό, εμπιστευτικό ραντεβού. Θα μελετήσουμε τα δεδομένα και θα σας προτείνουμε καθαρή στρατηγική.</p>
            <p class="mt-4"><a class="btn btn-primary" href="<?php echo esc_url( mourtzilaki_page_url( 'contact' ) ); ?>">Επικοινωνία <span class="arrow">→</span></a></p>
        </div>
    </div>
</section>

<?php get_footer(); ?>
