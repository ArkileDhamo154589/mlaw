<?php
/**
 * Plugin Name: Mourtzilaki Law - Custom Login
 * Description: Minimal, modern login screen for the law firm.
 * Version: 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'login_enqueue_scripts', function () {
    ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600&family=Inter:wght@400;450;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --ink:    #1f1a14;
            --ink-2:  #4a3f31;
            --muted:  #8a7c68;
            --line:   #e6dfd2;
            --bg:     #f6f1e7;
            --bg-2:   #efe7d6;
            --gold:   #b08a3e;
            --gold-2: #8e6e2a;
        }

        html, body.login {
            height: 100%;
            margin: 0;
        }

        body.login {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            color: var(--ink);
            background: var(--bg);
            display: grid;
            grid-template-columns: 1.1fr 1fr;
            min-height: 100vh;
        }

        /* LEFT brand panel */
        body.login::before {
            content: "";
            grid-column: 1;
            background:
                radial-gradient(120% 80% at 0% 0%, rgba(176,138,62,0.08), transparent 60%),
                radial-gradient(80% 60% at 100% 100%, rgba(31,26,20,0.06), transparent 60%),
                linear-gradient(160deg, var(--bg-2) 0%, var(--bg) 100%);
            border-right: 1px solid var(--line);
        }

        /* Brand text laid over the left panel */
        body.login::after {
            content: "Mourtzilaki\00a0\00a0Law";
            position: fixed;
            top: 50%;
            left: calc((100vw * 1.1 / 2.1) / 2);
            transform: translate(-50%, -50%);
            font-family: 'Fraunces', 'Cormorant Garamond', Georgia, serif;
            font-weight: 500;
            font-size: clamp(40px, 5.2vw, 76px);
            line-height: 1;
            letter-spacing: -0.02em;
            color: var(--ink);
            white-space: nowrap;
            pointer-events: none;
        }

        /* Tiny corner labels via real DOM injection */
        #brand-tag, #brand-foot {
            position: fixed;
            font-size: 11px;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: var(--muted);
        }
        #brand-tag {
            top: 36px;
            left: 40px;
        }
        #brand-tag .dot {
            display: inline-block;
            width: 6px; height: 6px;
            border-radius: 50%;
            background: var(--gold);
            margin-right: 10px;
            vertical-align: middle;
            transform: translateY(-1px);
        }
        #brand-foot {
            bottom: 36px;
            left: 40px;
            color: var(--ink-2);
            font-style: italic;
            text-transform: none;
            letter-spacing: 0;
            font-size: 13px;
            font-family: 'Fraunces', serif;
            max-width: 360px;
        }

        /* RIGHT form panel takes the second grid column */
        #login {
            grid-column: 2;
            align-self: center;
            justify-self: center;
            width: min(380px, 86vw);
            padding: 40px 0;
        }

        /* Replace WP logo with a clean wordmark */
        #login h1 {
            margin-bottom: 32px;
        }
        #login h1 a {
            background-image: none !important;
            width: auto !important;
            height: auto !important;
            margin: 0 !important;
            padding: 0 !important;
            display: block;
            text-indent: 0;
            font-family: 'Inter', sans-serif;
            font-weight: 500;
            font-size: 13px;
            letter-spacing: 0.22em;
            text-transform: uppercase;
            color: var(--ink-2) !important;
            text-align: left;
            text-shadow: none;
        }
        #login h1 a::before {
            content: "";
            display: inline-block;
            width: 28px;
            height: 1px;
            background: var(--gold);
            vertical-align: middle;
            margin-right: 12px;
            transform: translateY(-2px);
        }

        /* Heading above the form */
        #login::before {
            content: "Καλώς ήρθατε";
            display: block;
            font-family: 'Fraunces', serif;
            font-weight: 500;
            font-size: 36px;
            line-height: 1.15;
            letter-spacing: -0.01em;
            color: var(--ink);
            margin-bottom: 6px;
        }
        #login::after {
            content: "Συνδεθείτε στον λογαριασμό σας για να συνεχίσετε.";
            display: block;
            font-size: 14px;
            color: var(--muted);
            margin-bottom: 28px;
        }

        /* Form: no card. Just clean fields. */
        .login form {
            margin: 0;
            padding: 0;
            background: transparent;
            border: 0;
            box-shadow: none;
        }

        .login label {
            font-size: 12px;
            font-weight: 500;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: var(--ink-2);
        }

        .login form .input,
        .login input[type="text"],
        .login input[type="password"],
        .login input[type="email"] {
            background: transparent;
            border: 0;
            border-bottom: 1px solid var(--line);
            border-radius: 0;
            color: var(--ink);
            font-size: 15px;
            font-family: inherit;
            padding: 10px 0;
            margin: 4px 0 18px;
            box-shadow: none;
            transition: border-color .2s ease;
        }

        .login form .input:focus,
        .login input[type="text"]:focus,
        .login input[type="password"]:focus,
        .login input[type="email"]:focus {
            border-bottom-color: var(--ink);
            outline: none;
            box-shadow: none;
        }

        .login .forgetmenot {
            font-size: 13px;
            color: var(--ink-2);
            margin: 4px 0 22px;
        }
        .login .forgetmenot label {
            text-transform: none;
            letter-spacing: 0;
            font-weight: 400;
            color: var(--ink-2);
        }
        .login input[type="checkbox"] {
            border: 1px solid var(--muted);
            background: transparent;
            box-shadow: none;
        }
        .login input[type="checkbox"]:checked {
            background: var(--ink);
            border-color: var(--ink);
        }
        .login input[type="checkbox"]:checked::before {
            color: var(--bg);
            margin: -2px 0 0 -3px;
        }

        /* Submit button: solid ink, gold underline accent on hover */
        .wp-core-ui .button-primary,
        .login .button-primary {
            display: block;
            width: 100%;
            background: var(--ink) !important;
            border: 0 !important;
            color: var(--bg) !important;
            font-family: 'Inter', sans-serif !important;
            font-weight: 500 !important;
            font-size: 14px !important;
            letter-spacing: 0.06em !important;
            padding: 14px 18px !important;
            height: auto !important;
            border-radius: 2px !important;
            box-shadow: none !important;
            text-shadow: none !important;
            text-transform: none;
            cursor: pointer;
            transition: background .2s ease, transform .15s ease;
            position: relative;
        }
        .wp-core-ui .button-primary:hover,
        .login .button-primary:hover {
            background: var(--gold-2) !important;
        }
        .wp-core-ui .button-primary:active,
        .login .button-primary:active {
            transform: translateY(1px);
        }

        /* Footer links */
        #login #nav,
        #login #backtoblog {
            text-align: left;
            padding: 0;
            margin-top: 16px;
            font-size: 13px;
        }
        #login #nav a,
        #login #backtoblog a {
            color: var(--ink-2) !important;
            text-decoration: none;
            border-bottom: 1px solid transparent;
            transition: color .15s, border-color .15s;
        }
        #login #nav a:hover,
        #login #backtoblog a:hover {
            color: var(--ink) !important;
            border-bottom-color: var(--gold);
        }
        #login #nav { margin-top: 24px; }

        /* Notices / errors */
        .login .message,
        .login .notice,
        .login #login_error {
            background: #fff;
            border: 1px solid var(--line);
            border-left: 3px solid var(--gold);
            color: var(--ink-2);
            border-radius: 2px;
            box-shadow: 0 8px 24px rgba(31,26,20,0.06);
            font-size: 13px;
        }
        .login #login_error {
            border-left-color: #b3261e;
        }

        /* Language switcher */
        .login .language-switcher {
            margin-top: 28px;
            text-align: left;
            font-size: 12px;
        }
        .login .language-switcher select {
            background: transparent;
            border: 0;
            border-bottom: 1px solid var(--line);
            color: var(--ink-2);
            border-radius: 0;
            padding: 4px 0;
        }

        /* Mobile: collapse to a single column */
        @media (max-width: 860px) {
            body.login {
                grid-template-columns: 1fr;
            }
            body.login::before {
                display: none;
            }
            body.login::after {
                position: static;
                transform: none;
                display: block;
                font-size: 36px;
                text-align: left;
                padding: 80px 24px 0;
                white-space: normal;
            }
            #login {
                grid-column: 1;
                width: min(380px, 86vw);
                padding: 32px 24px 60px;
                justify-self: start;
            }
            #brand-tag { top: 24px; left: 24px; }
            #brand-foot { display: none; }
        }
    </style>
    <?php
} );

// Inject small DOM elements for the corner labels (top tag + bottom quote).
add_action( 'login_header', function () {
    echo '<div id="brand-tag"><span class="dot"></span>Δικηγορικό γραφείο</div>';
    echo '<div id="brand-foot">Justitia, virtutum regina.</div>';
} );

// Logo link / title
add_filter( 'login_headerurl', function () {
    return home_url( '/' );
} );
add_filter( 'login_headertext', function () {
    return 'Πίνακας διαχείρισης';
} );
add_filter( 'login_title', function () {
    return get_bloginfo( 'name' ) . ' — Σύνδεση';
} );
