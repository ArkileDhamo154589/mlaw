<?php
/**
 * Καριέρα (Careers).
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
$h = mourtzilaki_page_hero();
?>

<section class="page-header">
    <div class="container container-narrow">
        <span class="eyebrow"><?php echo esc_html( ! empty( $h['eyebrow'] ) ? $h['eyebrow'] : 'Καριέρα' ); ?></span>
        <h1 class="h-1 mt-2"><?php echo esc_html( ! empty( $h['title'] ) ? $h['title'] : 'Συνεργαστείτε μαζί μας.' ); ?></h1>
        <p class="lead"><?php echo esc_html( ! empty( $h['lead'] ) ? $h['lead'] : 'Αναζητούμε δικηγόρους και ασκούμενους που μοιράζονται την αφοσίωσή μας στη νομική ακρίβεια και τη σχέση εμπιστοσύνης με τον πελάτη.' ); ?></p>
    </div>
</section>

<section class="section section-tight">
    <div class="container container-narrow stack stack-xl">
        <div class="reveal reveal-up">
            <span class="eyebrow">Φιλοσοφία εργασίας</span>
            <h2 class="h-3 mt-2">Τι σημαίνει να εργάζεσαι εδώ</h2>
            <p class="mt-2">Λειτουργούμε ως μικρή, εξειδικευμένη ομάδα που εστιάζει στην ποιότητα της δουλειάς, όχι στον όγκο των υποθέσεων. Κάθε μέλος έχει χώρο να εμβαθύνει στους τομείς που τον/την ενδιαφέρουν, με ουσιαστική νομική επίβλεψη και άμεση εμπλοκή σε υποθέσεις από την πρώτη μέρα.</p>
        </div>

        <div class="careers-grid reveal reveal-up">
            <article class="career-tile">
                <span class="career-tag">Πλήρης απασχόληση</span>
                <h3 class="h-3">Δικηγόρος Συνεργάτης</h3>
                <p class="muted">Ελάχιστη εμπειρία 3 έτη · Αθήνα</p>
                <p>Αναζητούμε δικηγόρο με εμπειρία στο αστικό και εμπορικό δίκαιο, που θα συμμετέχει σε σύνθετες υποθέσεις και θα αναπτύξει δικό του χαρτοφυλάκιο πελατών.</p>
                <ul class="career-skills">
                    <li>Άριστη γνώση αστικού &amp; εμπορικού δικαίου</li>
                    <li>Εμπειρία σε δικαστηριακή πρακτική</li>
                    <li>Καλή γνώση αγγλικών (γραπτός λόγος)</li>
                    <li>Ικανότητα ανάληψης πρωτοβουλίας</li>
                </ul>
            </article>
            <article class="career-tile">
                <span class="career-tag">Άσκηση</span>
                <h3 class="h-3">Ασκούμενος/η δικηγόρος</h3>
                <p class="muted">Έναρξη: άμεσα · Αθήνα</p>
                <p>Δίνουμε χώρο σε νέους νομικούς να μάθουν την πραγματική δικηγορία — με συμμετοχή σε όλα τα στάδια υποθέσεων, από τη μελέτη φακέλου μέχρι το ακροατήριο.</p>
                <ul class="career-skills">
                    <li>Πτυχίο Νομικής (ΑΕΙ)</li>
                    <li>Όρεξη για νομική έρευνα</li>
                    <li>Καλή γραπτή έκφραση</li>
                    <li>Αξιοπιστία και διακριτικότητα</li>
                </ul>
            </article>
            <article class="career-tile">
                <span class="career-tag">Συνεργασία</span>
                <h3 class="h-3">Εξωτερικοί συνεργάτες</h3>
                <p class="muted">Ad-hoc βάση · Όλη η Ελλάδα</p>
                <p>Συνεργαζόμαστε με δικηγόρους εκτός Αθήνας για υποθέσεις σε επαρχιακά δικαστήρια. Επίσης με συμβολαιογράφους, μηχανικούς και λογιστές για ολοκληρωμένη εξυπηρέτηση πελατών.</p>
                <ul class="career-skills">
                    <li>Άδεια άσκησης σε αρμόδιο σύλλογο</li>
                    <li>Διαθεσιμότητα για ταχείες ενέργειες</li>
                    <li>Σαφής τιμολόγηση</li>
                </ul>
            </article>
        </div>

        <div class="reveal reveal-up">
            <h2 class="h-3">Πώς να υποβάλετε αίτηση</h2>
            <p class="mt-2">Στείλτε βιογραφικό και σύντομη συνοδευτική επιστολή στο <a href="mailto:careers@mourtzilakilaw.gr" style="border-bottom: 1px solid var(--gold);">careers@mourtzilakilaw.gr</a>. Στην επιστολή αναφέρετε τη θέση που σας ενδιαφέρει και δύο-τρεις λόγους γιατί θέλετε να συνεργαστείτε με το γραφείο μας.</p>
            <p class="mt-2">Απαντάμε σε κάθε αίτηση εντός 15 εργάσιμων ημερών, ακόμη και σε αρνητική περίπτωση.</p>
        </div>
    </div>
</section>

<section class="cta-band">
    <div class="container row-split">
        <h2 class="h-2 reveal reveal-left">Δεν βλέπετε ταιριαστή θέση;</h2>
        <div class="reveal reveal-right">
            <p>Στείλτε μας βιογραφικό ούτως ή άλλως. Συχνά ανοίγουμε θέσεις χωρίς να τις δημοσιεύουμε.</p>
            <p class="mt-4"><a class="btn btn-primary" href="mailto:careers@mourtzilakilaw.gr">Στείλτε βιογραφικό <span class="arrow">→</span></a></p>
        </div>
    </div>
</section>

<?php get_footer(); ?>
