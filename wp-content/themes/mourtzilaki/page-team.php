<?php
/**
 * Team page (Δικηγόροι) — ACF-driven.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
$team = mourtzilaki_team();
$lead = $team[0];
$h    = mourtzilaki_page_hero();
$pid  = get_queried_object_id();
$g = function ( $k, $d = '' ) use ( $pid ) {
    if ( ! function_exists( 'get_field' ) ) { return $d; }
    $v = (string) get_field( $k, $pid );
    return '' !== trim( $v ) ? $v : $d;
};
$meta_rows = mourtzilaki_parse_lines( $g( 'team_lead_meta',
    "Δικηγορικός Σύλλογος | Αθηνών\nΈτη εμπειρίας | 20+\nΓλώσσες | Ελληνικά · Αγγλικά"
) );
?>

<section class="page-header">
    <div class="container container-narrow">
        <span class="eyebrow"><?php echo esc_html( ! empty( $h['eyebrow'] ) ? $h['eyebrow'] : 'Δικηγόροι' ); ?></span>
        <h1 class="h-1 mt-2"><?php echo esc_html( ! empty( $h['title'] ) ? $h['title'] : 'Η ομάδα του γραφείου.' ); ?></h1>
        <p class="lead"><?php echo ! empty( $h['lead'] ) ? mourtzilaki_field_inline( $h['lead'] ) : esc_html( 'Το γραφείο λειτουργεί ως μικρή, εξειδικευμένη μονάδα που εστιάζει στη βαθιά γνώση και στην προσωπική σχέση με τον πελάτη. Κάθε υπόθεση χειρίζεται προσωπικά από τη δικηγόρο, χωρίς να μετατίθεται σε τρίτους.' ); ?></p>
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
                <p class="lead mt-4"><?php echo mourtzilaki_field_inline( $lead['bio'] ); ?></p>
                <p class="mt-2"><?php echo mourtzilaki_field_inline( $g( 'team_lead_p2', 'Παρέχει νομικές υπηρεσίες σε ιδιώτες και επιχειρήσεις, με έμφαση στη σαφή επικοινωνία, τον σεβασμό των προθεσμιών και την υψηλή ποιότητα νομικής τεκμηρίωσης.' ) ); ?></p>
                <?php if ( ! empty( $meta_rows ) ) : ?>
                <div class="featured-meta mt-4">
                    <?php foreach ( $meta_rows as $row ) : $lab = $row[0] ?? ''; $val = $row[1] ?? ''; if ( '' === $lab ) { continue; } ?>
                        <div><span class="lab"><?php echo esc_html( $lab ); ?></span><span class="val"><?php echo esc_html( $val ); ?></span></div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                <?php
                $b1l = $g( 'team_btn1_label', 'Πλήρες βιογραφικό' );
                $b1u = $g( 'team_btn1_url',   mourtzilaki_page_url( 'bio' ) );
                $b2l = $g( 'team_btn2_label', 'Επικοινωνία' );
                $b2u = $g( 'team_btn2_url',   mourtzilaki_page_url( 'contact' ) );
                ?>
                <p class="mt-4">
                    <?php if ( $b1l ) : ?><a class="btn btn-primary" href="<?php echo esc_url( $b1u ); ?>"><?php echo esc_html( $b1l ); ?> <span class="arrow">→</span></a><?php endif; ?>
                    <?php if ( $b2l ) : ?><a class="btn btn-ghost" href="<?php echo esc_url( $b2u ); ?>"><?php echo esc_html( $b2l ); ?></a><?php endif; ?>
                </p>
            </div>
        </div>
    </div>
</section>

<?php
$net_eb = $g( 'team_net_eyebrow', 'Συνεργασίες' );
$net_t  = $g( 'team_net_title',   'Δουλεύουμε με αξιόπιστα δίκτυα.' );
$net_l  = $g( 'team_net_lead',    'Συνεργαζόμαστε με συμβολαιογράφους, λογιστές, οικονομικούς συμβούλους και εξειδικευμένα γραφεία στο εξωτερικό για υποθέσεις που το απαιτούν.' );
?>
<?php if ( $net_t ) : ?>
<section class="section section-soft">
    <div class="container container-narrow text-center">
        <span class="eyebrow" style="justify-content:center"><?php echo esc_html( $net_eb ); ?></span>
        <h2 class="h-2 mt-2"><?php echo esc_html( $net_t ); ?></h2>
        <p class="lead mt-4"><?php echo mourtzilaki_field_inline( $net_l ); ?></p>
    </div>
</section>
<?php endif; ?>

<?php get_footer(); ?>
