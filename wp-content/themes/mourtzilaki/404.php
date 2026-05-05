<?php
/**
 * 404.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header(); ?>

<section class="section">
    <div class="container container-narrow text-center stack stack-xl">
        <span class="eyebrow" style="justify-content:center">Σφάλμα 404</span>
        <h1 class="h-1">Η σελίδα δεν βρέθηκε.</h1>
        <p class="lead">Η διεύθυνση που ζητήσατε δεν υπάρχει ή έχει μεταφερθεί.</p>
        <p>
            <a class="btn btn-primary" href="<?php echo esc_url( home_url( '/' ) ); ?>">Επιστροφή στην αρχική <span class="arrow">→</span></a>
        </p>
    </div>
</section>

<?php get_footer(); ?>
