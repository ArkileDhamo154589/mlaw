<?php
/**
 * Team page (Δικηγόροι).
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
$team = mourtzilaki_team();
$lead = $team[0];
$h    = mourtzilaki_page_hero();
?>

<section class="page-header">
    <div class="container container-narrow">
        <span class="eyebrow"><?php echo esc_html( ! empty( $h['eyebrow'] ) ? $h['eyebrow'] : 'Δικηγόροι' ); ?></span>
        <h1 class="h-1 mt-2"><?php echo esc_html( ! empty( $h['title'] ) ? $h['title'] : 'Η ομάδα του γραφείου.' ); ?></h1>
        <p class="lead"><?php echo esc_html( ! empty( $h['lead'] ) ? $h['lead'] : 'Το γραφείο λειτουργεί ως μικρή, εξειδικευμένη μονάδα που εστιάζει στη βαθιά γνώση και στην προσωπική σχέση με τον πελάτη. Κάθε υπόθεση χειρίζεται προσωπικά από τη δικηγόρο, χωρίς να μετατίθεται σε τρίτους.' ); ?></p>
    </div>
</section>

<section class="section section-tight">
    <div class="container">
        <div class="featured-member">
            <div class="featured-photo">
                <img src="<?php echo esc_url( $lead['photo'] ); ?>" alt="<?php echo esc_attr( $lead['name'] ); ?>" loading="lazy">
            </div>
            <div class="featured-info">
                <span class="eyebrow"><?php echo esc_html( $lead['role'] ); ?></span>
                <h2 class="h-1 mt-2"><?php echo esc_html( $lead['name'] ); ?></h2>
                <p class="lead mt-4"><?php echo esc_html( $lead['bio'] ); ?></p>
                <p class="mt-2">Παρέχει νομικές υπηρεσίες σε ιδιώτες και επιχειρήσεις, με έμφαση στη σαφή επικοινωνία, τον σεβασμό των προθεσμιών και την υψηλή ποιότητα νομικής τεκμηρίωσης.</p>
                <div class="featured-meta mt-4">
                    <div><span class="lab">Δικηγορικός Σύλλογος</span><span class="val">Αθηνών</span></div>
                    <div><span class="lab">Έτη εμπειρίας</span><span class="val">20+</span></div>
                    <div><span class="lab">Γλώσσες</span><span class="val">Ελληνικά · Αγγλικά</span></div>
                </div>
                <p class="mt-4">
                    <a class="btn btn-primary" href="<?php echo esc_url( mourtzilaki_page_url( 'bio' ) ); ?>">Πλήρες βιογραφικό <span class="arrow">→</span></a>
                    <a class="btn btn-ghost" href="<?php echo esc_url( mourtzilaki_page_url( 'contact' ) ); ?>">Επικοινωνία</a>
                </p>
            </div>
        </div>
    </div>
</section>

<section class="section section-soft">
    <div class="container container-narrow text-center">
        <span class="eyebrow" style="justify-content:center">Συνεργασίες</span>
        <h2 class="h-2 mt-2">Δουλεύουμε με αξιόπιστα δίκτυα.</h2>
        <p class="lead mt-4">Συνεργαζόμαστε με συμβολαιογράφους, λογιστές, οικονομικούς συμβούλους και εξειδικευμένα γραφεία στο εξωτερικό για υποθέσεις που το απαιτούν.</p>
    </div>
</section>

<?php get_footer(); ?>
