<?php
/**
 * Contact page (Επικοινωνία).
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
$h = mourtzilaki_page_hero();
$c = mourtzilaki_get_contact_info();
$cf7_id = (int) get_theme_mod( 'mourtzilaki_cf7_id', 0 );
?>

<section class="page-header">
    <div class="container container-narrow">
        <span class="eyebrow"><?php echo esc_html( ! empty( $h['eyebrow'] ) ? $h['eyebrow'] : 'Επικοινωνία' ); ?></span>
        <h1 class="h-1 mt-2"><?php echo esc_html( ! empty( $h['title'] ) ? $h['title'] : 'Ας μιλήσουμε για την υπόθεσή σας.' ); ?></h1>
        <p class="lead"><?php echo ! empty( $h['lead'] ) ? mourtzilaki_field_inline( $h['lead'] ) : esc_html( 'Συμπληρώστε τη φόρμα ή επικοινωνήστε απευθείας. Απαντάμε σε κάθε αίτημα εντός 24 ωρών εργάσιμων ημερών — με απόλυτη διακριτικότητα.' ); ?></p>
    </div>
</section>

<section class="section section-tight">
    <div class="container">
        <div class="contact-channels reveal reveal-up">
            <a class="cc-tile" href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $c['phone'] ) ); ?>">
                <span class="cc-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72c.13.96.37 1.9.72 2.81a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.91.35 1.85.59 2.81.72A2 2 0 0122 16.92z"/></svg>
                </span>
                <span class="cc-label">Τηλέφωνο</span>
                <span class="cc-value"><?php echo esc_html( $c['phone'] ); ?></span>
                <span class="cc-hint">Δευτ — Παρ, 09:00 — 19:00</span>
            </a>
            <a class="cc-tile" href="mailto:<?php echo esc_attr( $c['email'] ); ?>">
                <span class="cc-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                </span>
                <span class="cc-label">Email</span>
                <span class="cc-value"><?php echo esc_html( $c['email'] ); ?></span>
                <span class="cc-hint">Απάντηση σε 24 ώρες</span>
            </a>
            <a class="cc-tile" href="https://www.google.com/maps/search/?api=1&query=<?php echo urlencode( str_replace( "\n", ', ', $c['address'] ) ); ?>" target="_blank" rel="noopener">
                <span class="cc-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                </span>
                <span class="cc-label">Διεύθυνση</span>
                <span class="cc-value"><?php echo nl2br( esc_html( $c['address'] ) ); ?></span>
                <span class="cc-hint">Δείτε στον χάρτη →</span>
            </a>
        </div>
    </div>
</section>

<section class="section section-tight">
    <div class="container">
        <div class="contact-grid">
            <div class="contact-form-wrap reveal reveal-left">
                <span class="eyebrow">Φόρμα επικοινωνίας</span>
                <h2 class="h-2 mt-2">Στείλτε μας μήνυμα</h2>
                <p class="muted mt-2">Απαντάμε εντός 24 ωρών. Όλες οι επικοινωνίες είναι αυστηρά εμπιστευτικές.</p>

                <div class="contact-form mt-6">
                    <?php if ( $cf7_id && function_exists( 'do_shortcode' ) ) :
                        echo do_shortcode( '[contact-form-7 id="' . (int) $cf7_id . '" title="Φόρμα Επικοινωνίας"]' );
                    else : ?>
                        <p class="muted">Η φόρμα δεν είναι διαθέσιμη. Στείλτε email στο <a href="mailto:<?php echo esc_attr( $c['email'] ); ?>" style="border-bottom: 1px solid var(--gold);"><?php echo esc_html( $c['email'] ); ?></a>.</p>
                    <?php endif; ?>
                </div>
            </div>

            <aside class="contact-side reveal reveal-right">
                <div class="cs-block">
                    <h3 class="h-4">Ωράριο γραφείου</h3>
                    <ul class="cs-hours">
                        <?php foreach ( explode( "\n", $c['hours'] ) as $line ) : ?>
                            <li><?php echo esc_html( $line ); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="cs-block">
                    <h3 class="h-4">Επείγουσες υποθέσεις</h3>
                    <p class="muted">Σε αυτόφωρη διαδικασία ή κρίσιμο ζήτημα εκτός ωραρίου, καλέστε απευθείας στο τηλέφωνο. Διαθέτουμε εφημερία για επείγοντα ποινικά &amp; ασφαλιστικά μέτρα.</p>
                </div>

                <div class="cs-block">
                    <h3 class="h-4">Online συνάντηση</h3>
                    <p class="muted">Πραγματοποιούμε συναντήσεις και μέσω Zoom ή Google Meet για πελάτες εκτός Αθήνας ή εξωτερικού. Στη φόρμα αναφέρετε «online» στο θέμα.</p>
                </div>

                <div class="cs-block cs-block-tight">
                    <h3 class="h-4">Κοινωνικά</h3>
                    <div class="cs-social">
                        <a href="#" aria-label="LinkedIn" target="_blank" rel="noopener">
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M20.5 2h-17A1.5 1.5 0 002 3.5v17A1.5 1.5 0 003.5 22h17a1.5 1.5 0 001.5-1.5v-17A1.5 1.5 0 0020.5 2zM8 19H5V8h3v11zM6.5 6.7c-1 0-1.7-.7-1.7-1.6S5.5 3.5 6.5 3.5s1.7.7 1.7 1.6S7.5 6.7 6.5 6.7zM19 19h-3v-5.6c0-1.4-.5-2.3-1.7-2.3-.9 0-1.5.6-1.7 1.2-.1.2-.1.5-.1.8V19h-3V8h3v1.3c.4-.6 1.1-1.5 2.7-1.5 2 0 3.5 1.3 3.5 4.1V19z"/></svg>
                        </a>
                        <a href="#" aria-label="Facebook" target="_blank" rel="noopener">
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M22 12c0-5.5-4.5-10-10-10S2 6.5 2 12c0 5 3.7 9.1 8.4 9.9v-7H7.9V12h2.5V9.8c0-2.5 1.5-3.9 3.8-3.9 1.1 0 2.2.2 2.2.2v2.5h-1.3c-1.2 0-1.6.8-1.6 1.6V12h2.8l-.4 2.9h-2.3v7C18.3 21.1 22 17 22 12z"/></svg>
                        </a>
                        <a href="#" aria-label="Instagram" target="_blank" rel="noopener">
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="0.5" fill="currentColor"/></svg>
                        </a>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>

<section class="section-tight">
    <div class="container">
        <div class="map-wrap reveal reveal-up">
            <iframe
                src="https://maps.google.com/maps?q=<?php echo urlencode( str_replace( "\n", ', ', $c['address'] ) ); ?>&t=&z=15&ie=UTF8&iwloc=&output=embed"
                width="100%"
                height="420"
                style="border:0;"
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                aria-label="Χάρτης τοποθεσίας γραφείου"></iframe>
        </div>
    </div>
</section>

<section class="section section-soft">
    <div class="container container-narrow text-center">
        <span class="eyebrow" style="justify-content:center">Διακριτικότητα</span>
        <h2 class="h-3 mt-2">Όλες οι επικοινωνίες είναι εμπιστευτικές</h2>
        <p class="lead mt-4">Η δικηγορική εμπιστευτικότητα είναι θεσμοθετημένη υποχρέωση. Καμία πληροφορία δεν διαρρέει εκτός γραφείου, ακόμη κι αν δεν ανατεθεί τελικά η υπόθεση.</p>
    </div>
</section>

<?php get_footer(); ?>
