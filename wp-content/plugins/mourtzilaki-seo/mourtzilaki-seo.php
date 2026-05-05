<?php
/**
 * Plugin Name:       Mourtzilaki SEO
 * Plugin URI:        https://mourtzilakilaw.gr
 * Description:       Lightweight SEO meta tags, Open Graph and Twitter Cards. Per-post fields for title, description, keywords and social image. Site-wide defaults under Settings → SEO.
 * Version:           1.0.0
 * Requires PHP:      8.0
 * Requires at least: 6.0
 * Author:            Mourtzilaki Law
 * Text Domain:       mourtzilaki-seo
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class Mourtzilaki_SEO {

    const META_TITLE    = '_mz_seo_title';
    const META_DESC     = '_mz_seo_desc';
    const META_KW       = '_mz_seo_keywords';
    const META_OG_IMG   = '_mz_seo_og_image_id';
    const NONCE         = 'mz_seo_save';
    const OPT_GROUP     = 'mz_seo_options';
    const OPT_NAME      = 'mz_seo_settings';

    public static function init() {
        $self = new self();
        add_action( 'add_meta_boxes',         array( $self, 'add_meta_box' ) );
        add_action( 'save_post',              array( $self, 'save_meta' ), 10, 2 );
        add_action( 'admin_enqueue_scripts',  array( $self, 'admin_assets' ) );
        add_filter( 'document_title_parts',   array( $self, 'filter_title' ), 20 );
        add_action( 'wp_head',                array( $self, 'print_resource_hints' ), 0 );
        add_action( 'wp_head',                array( $self, 'print_meta' ), 1 );
        add_action( 'wp_head',                array( $self, 'print_robots' ), 2 );
        add_action( 'wp_head',                array( $self, 'print_jsonld' ), 5 );
        add_action( 'admin_menu',             array( $self, 'add_settings_page' ) );
        add_action( 'admin_init',             array( $self, 'register_settings' ) );
    }

    /* -----------------------------------------------------------
     *  Performance hints (preconnect / dns-prefetch)
     * --------------------------------------------------------- */
    public function print_resource_hints() {
        if ( is_admin() ) { return; }
        echo "\n<!-- Mourtzilaki SEO · resource hints -->\n";
        echo '<link rel="dns-prefetch" href="//cdn.jsdelivr.net">' . "\n";
        echo '<link rel="dns-prefetch" href="//images.unsplash.com">' . "\n";
        echo '<link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>' . "\n";
    }

    /* -----------------------------------------------------------
     *  Robots meta
     * --------------------------------------------------------- */
    public function print_robots() {
        if ( is_admin() ) { return; }
        if ( is_search() || is_404() || is_preview() ) {
            echo '<meta name="robots" content="noindex,follow">' . "\n";
            return;
        }
        echo '<meta name="robots" content="index,follow,max-snippet:-1,max-image-preview:large,max-video-preview:-1">' . "\n";
    }

    /* -----------------------------------------------------------
     *  Meta box on post / page edit screens
     * --------------------------------------------------------- */
    public function add_meta_box() {
        $types = get_post_types( array( 'public' => true ), 'names' );
        unset( $types['attachment'] );
        foreach ( $types as $t ) {
            add_meta_box(
                'mz_seo_box',
                __( 'SEO & Social', 'mourtzilaki-seo' ),
                array( $this, 'render_meta_box' ),
                $t,
                'normal',
                'high'
            );
        }
    }

    public function render_meta_box( $post ) {
        wp_nonce_field( self::NONCE, self::NONCE );

        $title   = (string) get_post_meta( $post->ID, self::META_TITLE,  true );
        $desc    = (string) get_post_meta( $post->ID, self::META_DESC,   true );
        $keys    = (string) get_post_meta( $post->ID, self::META_KW,     true );
        $img_id  = (int)    get_post_meta( $post->ID, self::META_OG_IMG, true );
        $img_url = $img_id ? wp_get_attachment_image_url( $img_id, 'medium' ) : '';

        $fallback_title = wp_strip_all_tags( get_the_title( $post ) );
        $fallback_desc  = wp_strip_all_tags( $post->post_excerpt ? $post->post_excerpt : wp_trim_words( $post->post_content, 28, '…' ) );

        $score = $this->calc_seo_score( $post, array(
            'title'    => $title ?: $fallback_title,
            'desc'     => $desc  ?: $fallback_desc,
            'keys'     => $keys,
            'img_id'   => $img_id ?: (int) get_post_thumbnail_id( $post ),
        ) );
        ?>
        <div class="mz-seo-score">
            <div class="mz-seo-score-head">
                <span class="mz-seo-score-lab">SEO score</span>
                <span class="mz-seo-score-grade mz-seo-grade-<?php echo esc_attr( $score['grade'] ); ?>"><?php echo esc_html( $score['grade'] ); ?></span>
            </div>
            <div class="mz-seo-score-bar">
                <span class="mz-seo-score-fill" style="width:<?php echo (int) $score['percent']; ?>%; background:<?php echo esc_attr( $score['color'] ); ?>"></span>
            </div>
            <div class="mz-seo-score-num"><?php echo (int) $score['percent']; ?> / 100</div>
            <ul class="mz-seo-score-list">
                <?php foreach ( $score['checks'] as $c ) : ?>
                    <li class="mz-seo-check mz-seo-check-<?php echo esc_attr( $c['ok'] ? 'ok' : ( $c['warn'] ? 'warn' : 'fail' ) ); ?>">
                        <span class="ic" aria-hidden="true"><?php echo $c['ok'] ? '✓' : ( $c['warn'] ? '!' : '✕' ); ?></span>
                        <?php echo esc_html( $c['label'] ); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <hr style="margin:18px 0; border:0; border-top:1px solid #dcdcde;">
        <style>
            .mz-seo-score { padding: 14px 16px; background: #f6f1e7; border: 1px solid #e6dfd2; border-radius: 4px; margin-bottom: 4px; }
            .mz-seo-score-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; }
            .mz-seo-score-lab { font-weight: 600; font-size: 12px; letter-spacing: 0.1em; text-transform: uppercase; color: #4a3f31; }
            .mz-seo-score-grade { font-size: 22px; font-weight: 700; padding: 2px 12px; border-radius: 4px; color: #fff; line-height: 1.2; }
            .mz-seo-grade-A { background: #0a7c3e; }
            .mz-seo-grade-B { background: #6b8e1c; }
            .mz-seo-grade-C { background: #b08a3e; }
            .mz-seo-grade-D { background: #b3261e; }
            .mz-seo-score-bar { height: 6px; background: #e6dfd2; border-radius: 999px; overflow: hidden; margin-bottom: 6px; }
            .mz-seo-score-fill { display: block; height: 100%; transition: width .4s ease; }
            .mz-seo-score-num { font-size: 12px; color: #8a7c68; margin-bottom: 12px; font-variant-numeric: tabular-nums; }
            .mz-seo-score-list { margin: 0; padding: 0; list-style: none; display: grid; gap: 4px; }
            .mz-seo-check { display: flex; align-items: center; gap: 8px; font-size: 12.5px; padding: 4px 0; }
            .mz-seo-check .ic { width: 18px; height: 18px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; color: #fff; font-weight: 600; font-size: 11px; }
            .mz-seo-check-ok   .ic { background: #0a7c3e; }
            .mz-seo-check-warn .ic { background: #b08a3e; }
            .mz-seo-check-fail .ic { background: #b3261e; }
            .mz-seo-fld { margin: 14px 0; }
            .mz-seo-fld label { display:block; font-weight:600; margin-bottom:6px; font-size:13px; }
            .mz-seo-fld input[type="text"], .mz-seo-fld textarea { width:100%; }
            .mz-seo-fld textarea { min-height: 70px; }
            .mz-seo-count { float:right; color:#646970; font-weight:400; font-size:11px; }
            .mz-seo-help  { color:#646970; font-size:12px; margin-top:4px; }
            .mz-seo-help.warn { color:#b3261e; }
            .mz-seo-img-row { display:flex; gap:14px; align-items:flex-start; }
            .mz-seo-img-preview {
                width: 160px; height: 84px;
                background: #f0f0f1 center/cover no-repeat; border:1px solid #dcdcde; border-radius:3px;
                display: flex; align-items: center; justify-content: center; color:#8c8f94; font-size: 12px;
            }
        </style>
        <div class="mz-seo-fld">
            <label for="mz_seo_title">
                <?php esc_html_e( 'SEO Title', 'mourtzilaki-seo' ); ?>
                <span class="mz-seo-count" data-target="mz_seo_title" data-rec="60">0 / 60</span>
            </label>
            <input type="text" id="mz_seo_title" name="<?php echo esc_attr( self::META_TITLE ); ?>" value="<?php echo esc_attr( $title ); ?>" placeholder="<?php echo esc_attr( $fallback_title ); ?>" maxlength="120">
            <p class="mz-seo-help"><?php esc_html_e( 'Εμφανίζεται στις σελίδες αποτελεσμάτων αναζήτησης και στην καρτέλα του browser. Ιδανικά 50–60 χαρακτήρες.', 'mourtzilaki-seo' ); ?></p>
        </div>

        <div class="mz-seo-fld">
            <label for="mz_seo_desc">
                <?php esc_html_e( 'Meta Description', 'mourtzilaki-seo' ); ?>
                <span class="mz-seo-count" data-target="mz_seo_desc" data-rec="160">0 / 160</span>
            </label>
            <textarea id="mz_seo_desc" name="<?php echo esc_attr( self::META_DESC ); ?>" maxlength="320" placeholder="<?php echo esc_attr( $fallback_desc ); ?>"><?php echo esc_textarea( $desc ); ?></textarea>
            <p class="mz-seo-help"><?php esc_html_e( 'Σύντομη περιγραφή. Ιδανικά 140–160 χαρακτήρες.', 'mourtzilaki-seo' ); ?></p>
        </div>

        <div class="mz-seo-fld">
            <label for="mz_seo_kw"><?php esc_html_e( 'Focus Keywords', 'mourtzilaki-seo' ); ?></label>
            <input type="text" id="mz_seo_kw" name="<?php echo esc_attr( self::META_KW ); ?>" value="<?php echo esc_attr( $keys ); ?>" placeholder="δικηγορικό, αθήνα, κληρονομικό">
            <p class="mz-seo-help"><?php esc_html_e( 'Λέξεις-κλειδιά χωρισμένες με κόμματα. Δεν επηρεάζουν Google direct, αλλά βοηθούν στην επιμέλεια του περιεχομένου.', 'mourtzilaki-seo' ); ?></p>
        </div>

        <div class="mz-seo-fld">
            <label><?php esc_html_e( 'Social Image (Open Graph)', 'mourtzilaki-seo' ); ?></label>
            <div class="mz-seo-img-row">
                <div id="mz_seo_img_preview" class="mz-seo-img-preview" style="<?php echo $img_url ? 'background-image:url(' . esc_url( $img_url ) . ');' : ''; ?>">
                    <?php if ( ! $img_url ) : ?><?php esc_html_e( 'Καμία εικόνα', 'mourtzilaki-seo' ); ?><?php endif; ?>
                </div>
                <div>
                    <input type="hidden" id="mz_seo_img_id" name="<?php echo esc_attr( self::META_OG_IMG ); ?>" value="<?php echo esc_attr( $img_id ); ?>">
                    <p>
                        <button type="button" class="button" id="mz_seo_img_select"><?php esc_html_e( 'Επιλογή εικόνας', 'mourtzilaki-seo' ); ?></button>
                        <button type="button" class="button-link" id="mz_seo_img_clear" <?php echo $img_id ? '' : 'style="display:none;"'; ?>><?php esc_html_e( 'Αφαίρεση', 'mourtzilaki-seo' ); ?></button>
                    </p>
                    <p class="mz-seo-help"><?php esc_html_e( 'Συνιστώμενη διάσταση 1200×630. Αν αφεθεί κενό, χρησιμοποιείται το featured image ή η site-wide εικόνα.', 'mourtzilaki-seo' ); ?></p>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * SEO score calculation — heuristic checks against best practices.
     */
    public function calc_seo_score( $post, $values ) {
        $title = (string) $values['title'];
        $desc  = (string) $values['desc'];
        $keys  = (string) $values['keys'];
        $img   = (int) $values['img_id'];

        $word_count = str_word_count( wp_strip_all_tags( $post->post_content ) );
        $first_kw   = '';
        if ( $keys ) {
            $parts = array_filter( array_map( 'trim', explode( ',', $keys ) ) );
            if ( ! empty( $parts ) ) { $first_kw = mb_strtolower( $parts[0] ); }
        }
        $title_l   = mb_strtolower( $title );
        $desc_l    = mb_strtolower( $desc );
        $content_l = mb_strtolower( wp_strip_all_tags( $post->post_content ) );
        $first_para = mb_strtolower( wp_strip_all_tags( substr( $post->post_content, 0, 600 ) ) );

        $title_len = mb_strlen( $title );
        $desc_len  = mb_strlen( $desc );
        $checks = array();

        // 1. Title length
        if ( $title_len === 0 ) {
            $checks[] = array( 'label' => 'SEO Title δεν έχει οριστεί', 'ok' => false, 'warn' => false );
        } elseif ( $title_len > 60 ) {
            $checks[] = array( 'label' => 'Title πάνω από 60 χαρακτήρες ('.$title_len.')', 'ok' => false, 'warn' => true );
        } elseif ( $title_len < 30 ) {
            $checks[] = array( 'label' => 'Title σύντομος ('.$title_len.' χαρ.) — προτείνετε 30-60', 'ok' => false, 'warn' => true );
        } else {
            $checks[] = array( 'label' => 'Title σε ιδανικό μήκος ('.$title_len.')', 'ok' => true, 'warn' => false );
        }

        // 2. Meta description length
        if ( $desc_len === 0 ) {
            $checks[] = array( 'label' => 'Meta description δεν έχει οριστεί', 'ok' => false, 'warn' => false );
        } elseif ( $desc_len > 160 ) {
            $checks[] = array( 'label' => 'Description πάνω από 160 ('.$desc_len.')', 'ok' => false, 'warn' => true );
        } elseif ( $desc_len < 120 ) {
            $checks[] = array( 'label' => 'Description σύντομη ('.$desc_len.' χαρ.) — ιδανικά 140-160', 'ok' => false, 'warn' => true );
        } else {
            $checks[] = array( 'label' => 'Description σε ιδανικό μήκος ('.$desc_len.')', 'ok' => true, 'warn' => false );
        }

        // 3. Focus keyword set
        if ( '' === $first_kw ) {
            $checks[] = array( 'label' => 'Focus keyword δεν έχει οριστεί', 'ok' => false, 'warn' => true );
        } else {
            $checks[] = array( 'label' => 'Focus keyword: ' . $first_kw, 'ok' => true, 'warn' => false );

            // 4. Keyword in title
            if ( $title_l && false !== mb_strpos( $title_l, $first_kw ) ) {
                $checks[] = array( 'label' => 'Keyword στον τίτλο', 'ok' => true, 'warn' => false );
            } else {
                $checks[] = array( 'label' => 'Keyword δεν υπάρχει στον τίτλο', 'ok' => false, 'warn' => false );
            }

            // 5. Keyword in description
            if ( $desc_l && false !== mb_strpos( $desc_l, $first_kw ) ) {
                $checks[] = array( 'label' => 'Keyword στο description', 'ok' => true, 'warn' => false );
            } else {
                $checks[] = array( 'label' => 'Keyword δεν υπάρχει στο description', 'ok' => false, 'warn' => false );
            }

            // 6. Keyword in first paragraph
            if ( false !== mb_strpos( $first_para, $first_kw ) ) {
                $checks[] = array( 'label' => 'Keyword στις πρώτες παραγράφους', 'ok' => true, 'warn' => false );
            } else {
                $checks[] = array( 'label' => 'Keyword δεν εμφανίζεται νωρίς στο κείμενο', 'ok' => false, 'warn' => true );
            }

            // 7. Keyword density (1-3% ideal)
            if ( $word_count > 0 ) {
                $occ = mb_substr_count( $content_l, $first_kw );
                $density = $occ / max( 1, $word_count ) * 100;
                if ( $density < 0.5 ) {
                    $checks[] = array( 'label' => 'Πυκνότητα keyword χαμηλή (' . number_format( $density, 1 ) . '%)', 'ok' => false, 'warn' => true );
                } elseif ( $density > 3.5 ) {
                    $checks[] = array( 'label' => 'Πυκνότητα keyword υψηλή (' . number_format( $density, 1 ) . '%)', 'ok' => false, 'warn' => true );
                } else {
                    $checks[] = array( 'label' => 'Πυκνότητα keyword OK (' . number_format( $density, 1 ) . '%)', 'ok' => true, 'warn' => false );
                }
            }
        }

        // 8. Word count
        if ( $word_count === 0 ) {
            $checks[] = array( 'label' => 'Δεν υπάρχει περιεχόμενο', 'ok' => false, 'warn' => false );
        } elseif ( $word_count < 300 ) {
            $checks[] = array( 'label' => 'Περιεχόμενο ' . $word_count . ' λέξεις (κάτω από 300)', 'ok' => false, 'warn' => true );
        } else {
            $checks[] = array( 'label' => 'Περιεχόμενο ' . $word_count . ' λέξεις', 'ok' => true, 'warn' => false );
        }

        // 9. Featured / OG image
        if ( $img > 0 ) {
            $checks[] = array( 'label' => 'Εικόνα social έχει οριστεί', 'ok' => true, 'warn' => false );
        } else {
            $checks[] = array( 'label' => 'Εικόνα social δεν έχει οριστεί', 'ok' => false, 'warn' => true );
        }

        // 10. Has H2/H3 in content
        if ( preg_match( '/<h[2-3][^>]*>/i', $post->post_content ) ) {
            $checks[] = array( 'label' => 'Περιεχόμενο έχει υποτίτλους (H2/H3)', 'ok' => true, 'warn' => false );
        } else {
            $checks[] = array( 'label' => 'Δεν υπάρχουν υπότιτλοι (H2/H3)', 'ok' => false, 'warn' => true );
        }

        // 11. Permalink quality
        if ( $post->post_name && strlen( $post->post_name ) <= 75 ) {
            $checks[] = array( 'label' => 'Permalink slug OK', 'ok' => true, 'warn' => false );
        } else {
            $checks[] = array( 'label' => 'Permalink slug πολύ μακρύς ή κενός', 'ok' => false, 'warn' => true );
        }

        // Calculate score: ok=1, warn=0.5, fail=0
        $total = 0; $max = count( $checks );
        foreach ( $checks as $c ) {
            $total += $c['ok'] ? 1.0 : ( $c['warn'] ? 0.5 : 0.0 );
        }
        $percent = $max > 0 ? round( ( $total / $max ) * 100 ) : 0;

        $grade = 'D';
        $color = '#b3261e';
        if ( $percent >= 85 )      { $grade = 'A'; $color = '#0a7c3e'; }
        elseif ( $percent >= 70 )  { $grade = 'B'; $color = '#6b8e1c'; }
        elseif ( $percent >= 50 )  { $grade = 'C'; $color = '#b08a3e'; }

        return array(
            'percent' => $percent,
            'grade'   => $grade,
            'color'   => $color,
            'checks'  => $checks,
        );
    }

    public function admin_assets( $hook ) {
        if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) { return; }
        wp_enqueue_media();
        wp_add_inline_script( 'media-views', $this->admin_js() );
    }

    private function admin_js() {
        return <<<'JS'
(function () {
    function init () {
        var btn   = document.getElementById('mz_seo_img_select');
        var clr   = document.getElementById('mz_seo_img_clear');
        var input = document.getElementById('mz_seo_img_id');
        var prev  = document.getElementById('mz_seo_img_preview');
        if (!btn || !input || !prev) { return; }

        var frame;
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            if (frame) { frame.open(); return; }
            frame = wp.media({ title: 'Επιλέξτε εικόνα Social', button: { text: 'Χρήση εικόνας' }, multiple: false, library: { type: 'image' } });
            frame.on('select', function () {
                var att = frame.state().get('selection').first().toJSON();
                input.value = att.id;
                prev.style.backgroundImage = 'url(' + att.url + ')';
                prev.textContent = '';
                clr.style.display = '';
            });
            frame.open();
        });
        clr.addEventListener('click', function (e) {
            e.preventDefault();
            input.value = '';
            prev.style.backgroundImage = '';
            prev.textContent = 'Καμία εικόνα';
            clr.style.display = 'none';
        });

        // Live char counters.
        document.querySelectorAll('.mz-seo-count').forEach(function (el) {
            var t = document.getElementById(el.dataset.target);
            if (!t) { return; }
            var rec = parseInt(el.dataset.rec, 10);
            var update = function () {
                var n = t.value.length;
                el.textContent = n + ' / ' + rec;
                el.style.color = n > rec ? '#b3261e' : (n >= rec * 0.6 ? '#0a7c3e' : '#646970');
            };
            t.addEventListener('input', update);
            update();
        });
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
JS;
    }

    public function save_meta( $post_id, $post ) {
        if ( ! isset( $_POST[ self::NONCE ] ) ) { return; }
        if ( ! wp_verify_nonce( wp_unslash( $_POST[ self::NONCE ] ), self::NONCE ) ) { return; }
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return; }
        if ( ! current_user_can( 'edit_post', $post_id ) ) { return; }
        if ( wp_is_post_revision( $post_id ) ) { return; }

        $map = array(
            self::META_TITLE  => 'sanitize_text_field',
            self::META_DESC   => 'sanitize_textarea_field',
            self::META_KW     => 'sanitize_text_field',
            self::META_OG_IMG => 'absint',
        );
        foreach ( $map as $key => $fn ) {
            if ( ! isset( $_POST[ $key ] ) ) { continue; }
            $val = call_user_func( $fn, wp_unslash( $_POST[ $key ] ) );
            if ( '' === $val || 0 === $val ) {
                delete_post_meta( $post_id, $key );
            } else {
                update_post_meta( $post_id, $key, $val );
            }
        }
    }

    /* -----------------------------------------------------------
     *  Output
     * --------------------------------------------------------- */
    public function filter_title( $parts ) {
        if ( is_admin() || ! is_singular() ) { return $parts; }
        $custom = get_post_meta( get_queried_object_id(), self::META_TITLE, true );
        if ( ! empty( $custom ) ) {
            $parts['title'] = $custom;
            unset( $parts['site'], $parts['tagline'] );
        }
        return $parts;
    }

    public function print_meta() {
        $opts = (array) get_option( self::OPT_NAME, array() );

        $title       = '';
        $desc        = '';
        $url         = '';
        $img_id      = 0;
        $obj_type    = 'website';

        if ( is_singular() ) {
            $post     = get_queried_object();
            $obj_type = ( 'post' === $post->post_type ) ? 'article' : 'website';
            $url      = get_permalink( $post );
            $title    = get_post_meta( $post->ID, self::META_TITLE, true );
            $desc     = get_post_meta( $post->ID, self::META_DESC,  true );
            $img_id   = (int) get_post_meta( $post->ID, self::META_OG_IMG, true );

            if ( '' === $title ) { $title = wp_strip_all_tags( get_the_title( $post ) ); }
            if ( '' === $desc )  {
                $desc = wp_strip_all_tags( $post->post_excerpt ? $post->post_excerpt : wp_trim_words( $post->post_content, 28, '…' ) );
            }
            if ( ! $img_id && has_post_thumbnail( $post ) ) {
                $img_id = (int) get_post_thumbnail_id( $post );
            }
        } elseif ( is_home() || is_front_page() ) {
            $title = get_bloginfo( 'name' );
            $desc  = get_bloginfo( 'description' );
            $url   = home_url( '/' );
        } else {
            return;
        }

        if ( ! $img_id && ! empty( $opts['default_og_image'] ) ) {
            $img_id = (int) $opts['default_og_image'];
        }
        if ( '' === $desc && ! empty( $opts['default_desc'] ) ) {
            $desc = (string) $opts['default_desc'];
        }

        $img_url = $img_id ? wp_get_attachment_image_url( $img_id, 'full' ) : '';
        $kw      = is_singular() ? get_post_meta( get_queried_object_id(), self::META_KW, true ) : '';
        $site    = get_bloginfo( 'name' );
        $locale  = get_locale();
        $twitter = ! empty( $opts['twitter_handle'] ) ? '@' . ltrim( $opts['twitter_handle'], '@' ) : '';

        echo "\n<!-- Mourtzilaki SEO -->\n";
        if ( $desc ) {
            printf( '<meta name="description" content="%s">' . "\n", esc_attr( $desc ) );
        }
        if ( $kw ) {
            printf( '<meta name="keywords" content="%s">' . "\n", esc_attr( $kw ) );
        }
        if ( $url ) {
            printf( '<link rel="canonical" href="%s">' . "\n", esc_url( $url ) );
        }

        // Open Graph.
        printf( '<meta property="og:locale" content="%s">' . "\n", esc_attr( str_replace( '-', '_', $locale ) ) );
        printf( '<meta property="og:type" content="%s">'   . "\n", esc_attr( $obj_type ) );
        printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( $site ) );
        if ( $title ) printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $title ) );
        if ( $desc )  printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $desc ) );
        if ( $url )   printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $url ) );
        if ( $img_url ) {
            printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $img_url ) );
            $meta = wp_get_attachment_metadata( $img_id );
            if ( ! empty( $meta['width'] ) )  printf( '<meta property="og:image:width" content="%d">' . "\n",  (int) $meta['width'] );
            if ( ! empty( $meta['height'] ) ) printf( '<meta property="og:image:height" content="%d">' . "\n", (int) $meta['height'] );
        }

        // Twitter.
        printf( '<meta name="twitter:card" content="%s">' . "\n", $img_url ? 'summary_large_image' : 'summary' );
        if ( $title ) printf( '<meta name="twitter:title" content="%s">' . "\n", esc_attr( $title ) );
        if ( $desc )  printf( '<meta name="twitter:description" content="%s">' . "\n", esc_attr( $desc ) );
        if ( $img_url ) printf( '<meta name="twitter:image" content="%s">' . "\n", esc_url( $img_url ) );
        if ( $twitter ) {
            printf( '<meta name="twitter:site" content="%s">' . "\n",    esc_attr( $twitter ) );
            printf( '<meta name="twitter:creator" content="%s">' . "\n", esc_attr( $twitter ) );
        }
        echo "<!-- /Mourtzilaki SEO -->\n";
    }

    /* -----------------------------------------------------------
     *  JSON-LD structured data (Schema.org)
     * --------------------------------------------------------- */
    public function print_jsonld() {
        if ( is_admin() ) { return; }
        $opts    = (array) get_option( self::OPT_NAME, array() );
        $site    = get_bloginfo( 'name' );
        $tagline = get_bloginfo( 'description' );
        $home    = home_url( '/' );
        $logo_id = (int) get_theme_mod( 'custom_logo' );
        $logo    = $logo_id ? wp_get_attachment_image_url( $logo_id, 'full' ) : '';

        // Contact info from contact page (mirrors theme helper).
        $contact_page = get_page_by_path( 'contact' );
        $address      = "Λεωφ. Παράδειγμα 12\n10678, Αθήνα";
        $phone        = '+30 210 000 0000';
        $email        = 'info@mourtzilakilaw.gr';
        if ( $contact_page && function_exists( 'get_field' ) ) {
            $a = trim( (string) get_field( 'contact_address', $contact_page->ID ) ); if ( $a ) { $address = $a; }
            $p = trim( (string) get_field( 'contact_phone',   $contact_page->ID ) ); if ( $p ) { $phone   = $p; }
            $e = trim( (string) get_field( 'contact_email',   $contact_page->ID ) ); if ( $e ) { $email   = $e; }
        }

        // Address components.
        $street = '';
        $locality = 'Αθήνα';
        $postal = '';
        $addr_lines = preg_split( "/\r\n|\r|\n/", $address );
        if ( ! empty( $addr_lines[0] ) ) { $street = trim( $addr_lines[0] ); }
        if ( ! empty( $addr_lines[1] ) ) {
            $second = trim( $addr_lines[1] );
            if ( preg_match( '/^(\d{4,5})[, ]+(.+)$/u', $second, $m ) ) {
                $postal   = $m[1];
                $locality = $m[2];
            } else {
                $locality = $second;
            }
        }

        // Organization (LegalService is a more specific subtype).
        $organization = array(
            '@type'        => 'LegalService',
            '@id'          => $home . '#organization',
            'name'         => $site,
            'description'  => $tagline,
            'url'          => $home,
            'telephone'    => $phone,
            'email'        => $email,
            'priceRange'   => '€€',
            'address'      => array(
                '@type'           => 'PostalAddress',
                'streetAddress'   => $street,
                'addressLocality' => $locality,
                'postalCode'      => $postal,
                'addressCountry'  => 'GR',
            ),
            'areaServed'   => array( '@type' => 'Country', 'name' => 'Ελλάδα' ),
            'openingHoursSpecification' => array(
                array(
                    '@type'     => 'OpeningHoursSpecification',
                    'dayOfWeek' => array( 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday' ),
                    'opens'     => '09:00',
                    'closes'    => '19:00',
                ),
            ),
        );
        if ( $logo ) {
            $organization['logo'] = $logo;
            $organization['image'] = $logo;
        }
        if ( ! empty( $opts['twitter_handle'] ) ) {
            $organization['sameAs'] = array( 'https://twitter.com/' . ltrim( $opts['twitter_handle'], '@' ) );
        }

        // Aggregate rating from testimonials.
        $rev_count = (int) wp_count_posts( 'mz_testimonial' )->publish;
        if ( $rev_count > 0 ) {
            $organization['aggregateRating'] = array(
                '@type'       => 'AggregateRating',
                'ratingValue' => '5.0',
                'reviewCount' => $rev_count,
                'bestRating'  => '5',
                'worstRating' => '1',
            );
        }

        // Founder (Person).
        $founder = null;
        if ( post_type_exists( 'mz_member' ) ) {
            $members = get_posts( array( 'post_type' => 'mz_member', 'numberposts' => 1, 'orderby' => 'menu_order date', 'order' => 'ASC' ) );
            if ( ! empty( $members ) ) {
                $m  = $members[0];
                $mp = function_exists( 'get_field' ) ? get_field( 'photo', $m->ID ) : null;
                $mp_url = is_array( $mp ) ? $mp['url'] : '';
                $founder = array(
                    '@type'    => 'Person',
                    '@id'      => $home . '#founder',
                    'name'     => get_the_title( $m ),
                    'jobTitle' => function_exists( 'get_field' ) ? (string) get_field( 'role', $m->ID ) : 'Δικηγόρος',
                    'worksFor' => array( '@id' => $home . '#organization' ),
                );
                if ( $mp_url ) { $founder['image'] = $mp_url; }
                $organization['founder'] = array( '@id' => $home . '#founder' );
                $organization['employee'] = array( array( '@id' => $home . '#founder' ) );
            }
        }

        $graph = array( $organization );
        if ( $founder ) { $graph[] = $founder; }

        // WebSite + SearchAction.
        $graph[] = array(
            '@type'           => 'WebSite',
            '@id'             => $home . '#website',
            'url'             => $home,
            'name'            => $site,
            'description'     => $tagline,
            'publisher'       => array( '@id' => $home . '#organization' ),
            'inLanguage'      => 'el-GR',
            'potentialAction' => array(
                '@type'       => 'SearchAction',
                'target'      => array(
                    '@type'       => 'EntryPoint',
                    'urlTemplate' => $home . '?s={search_term_string}',
                ),
                'query-input' => 'required name=search_term_string',
            ),
        );

        // Page-specific structured data.
        if ( is_singular( 'post' ) ) {
            $post = get_queried_object();
            $img_id = (int) get_post_thumbnail_id( $post );
            $img    = $img_id ? wp_get_attachment_image_url( $img_id, 'full' ) : '';
            $cats   = get_the_category( $post->ID );
            $tags   = get_the_tags( $post->ID );
            $excerpt = wp_strip_all_tags( $post->post_excerpt ? $post->post_excerpt : wp_trim_words( $post->post_content, 28, '…' ) );

            $article = array(
                '@type'            => 'Article',
                '@id'              => get_permalink( $post ) . '#article',
                'headline'         => get_the_title( $post ),
                'description'      => $excerpt,
                'datePublished'    => get_the_date( 'c', $post ),
                'dateModified'     => get_the_modified_date( 'c', $post ),
                'mainEntityOfPage' => array( '@type' => 'WebPage', '@id' => get_permalink( $post ) ),
                'inLanguage'       => 'el-GR',
                'publisher'        => array( '@id' => $home . '#organization' ),
                'author'           => $founder ? array( '@id' => $home . '#founder' ) : array( '@type' => 'Person', 'name' => get_the_author_meta( 'display_name', $post->post_author ) ),
            );
            if ( $img ) { $article['image'] = $img; }
            if ( ! empty( $cats ) ) { $article['articleSection'] = $cats[0]->name; }
            if ( ! empty( $tags ) )  { $article['keywords'] = implode( ', ', wp_list_pluck( $tags, 'name' ) ); }
            $graph[] = $article;
        }

        if ( is_singular( 'mz_service' ) ) {
            $svc = get_queried_object();
            $desc = function_exists( 'get_field' ) ? (string) get_field( 'description', $svc->ID ) : '';
            $graph[] = array(
                '@type'           => 'Service',
                '@id'             => get_permalink( $svc ) . '#service',
                'serviceType'     => get_the_title( $svc ),
                'name'            => get_the_title( $svc ),
                'description'     => $desc ? $desc : wp_trim_words( $svc->post_content, 28, '…' ),
                'provider'        => array( '@id' => $home . '#organization' ),
                'areaServed'      => array( '@type' => 'Country', 'name' => 'Ελλάδα' ),
                'mainEntityOfPage' => array( '@type' => 'WebPage', '@id' => get_permalink( $svc ) ),
            );
        }

        if ( is_page( 'faq' ) && post_type_exists( 'mz_faq' ) ) {
            $faqs = get_posts( array( 'post_type' => 'mz_faq', 'numberposts' => -1, 'orderby' => 'menu_order date', 'order' => 'ASC' ) );
            $main = array();
            foreach ( $faqs as $f ) {
                $a = function_exists( 'get_field' ) ? wp_strip_all_tags( (string) get_field( 'answer', $f->ID ) ) : '';
                $main[] = array(
                    '@type' => 'Question',
                    'name'  => get_the_title( $f ),
                    'acceptedAnswer' => array(
                        '@type' => 'Answer',
                        'text'  => $a,
                    ),
                );
            }
            if ( ! empty( $main ) ) {
                $graph[] = array(
                    '@type'      => 'FAQPage',
                    '@id'        => get_permalink() . '#faq',
                    'mainEntity' => $main,
                );
            }
        }

        if ( is_page( 'reviews' ) && post_type_exists( 'mz_testimonial' ) ) {
            $tposts = get_posts( array( 'post_type' => 'mz_testimonial', 'numberposts' => -1, 'orderby' => 'menu_order date', 'order' => 'ASC' ) );
            $reviews = array();
            foreach ( $tposts as $tp ) {
                $q = function_exists( 'get_field' ) ? (string) get_field( 'quote', $tp->ID ) : '';
                $r = function_exists( 'get_field' ) ? (string) get_field( 'role',  $tp->ID ) : '';
                $reviews[] = array(
                    '@type'        => 'Review',
                    'author'       => array( '@type' => 'Person', 'name' => get_the_title( $tp ) ),
                    'datePublished' => get_the_date( 'c', $tp ),
                    'reviewBody'   => $q,
                    'reviewRating' => array(
                        '@type'       => 'Rating',
                        'ratingValue' => '5',
                        'bestRating'  => '5',
                    ),
                );
            }
            if ( ! empty( $reviews ) ) {
                $graph[] = array(
                    '@type'  => 'ItemList',
                    '@id'    => get_permalink() . '#reviews',
                    'itemListElement' => array_map( function ( $r, $i ) {
                        return array( '@type' => 'ListItem', 'position' => $i + 1, 'item' => $r );
                    }, $reviews, array_keys( $reviews ) ),
                );
            }
        }

        // Breadcrumbs (built from current URL).
        $crumbs = $this->build_breadcrumbs();
        if ( count( $crumbs ) > 1 ) {
            $graph[] = array(
                '@type'           => 'BreadcrumbList',
                'itemListElement' => array_map( function ( $c, $i ) {
                    return array(
                        '@type'    => 'ListItem',
                        'position' => $i + 1,
                        'name'     => $c['name'],
                        'item'     => $c['url'],
                    );
                }, $crumbs, array_keys( $crumbs ) ),
            );
        }

        $payload = array(
            '@context' => 'https://schema.org',
            '@graph'   => $graph,
        );

        echo '<script type="application/ld+json">' . wp_json_encode( $payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n";

        // Article-specific OG meta supplements.
        if ( is_singular( 'post' ) ) {
            $post = get_queried_object();
            printf( '<meta property="article:published_time" content="%s">' . "\n", esc_attr( get_the_date( 'c', $post ) ) );
            printf( '<meta property="article:modified_time" content="%s">' . "\n",  esc_attr( get_the_modified_date( 'c', $post ) ) );
            $cats = get_the_category( $post->ID );
            if ( ! empty( $cats ) ) {
                printf( '<meta property="article:section" content="%s">' . "\n", esc_attr( $cats[0]->name ) );
            }
            $tags = get_the_tags( $post->ID );
            if ( ! empty( $tags ) ) {
                foreach ( $tags as $tag ) {
                    printf( '<meta property="article:tag" content="%s">' . "\n", esc_attr( $tag->name ) );
                }
            }
            printf( '<meta name="author" content="%s">' . "\n", esc_attr( get_the_author_meta( 'display_name', $post->post_author ) ) );
        }
    }

    private function build_breadcrumbs() {
        $home = home_url( '/' );
        $crumbs = array( array( 'name' => 'Αρχική', 'url' => $home ) );

        if ( is_singular( 'post' ) ) {
            $post = get_queried_object();
            $blog_page = get_page_by_path( 'blog' );
            if ( $blog_page ) {
                $crumbs[] = array( 'name' => get_the_title( $blog_page ), 'url' => get_permalink( $blog_page ) );
            }
            $crumbs[] = array( 'name' => get_the_title( $post ), 'url' => get_permalink( $post ) );
        } elseif ( is_singular( 'mz_service' ) ) {
            $svcs_page = get_page_by_path( 'services' );
            if ( $svcs_page ) {
                $crumbs[] = array( 'name' => get_the_title( $svcs_page ), 'url' => get_permalink( $svcs_page ) );
            }
            $svc = get_queried_object();
            $crumbs[] = array( 'name' => get_the_title( $svc ), 'url' => get_permalink( $svc ) );
        } elseif ( is_page() ) {
            $page = get_queried_object();
            $crumbs[] = array( 'name' => get_the_title( $page ), 'url' => get_permalink( $page ) );
        }

        return $crumbs;
    }

    /* -----------------------------------------------------------
     *  Settings page (Settings → SEO)
     * --------------------------------------------------------- */
    public function add_settings_page() {
        add_options_page(
            __( 'SEO', 'mourtzilaki-seo' ),
            __( 'SEO', 'mourtzilaki-seo' ),
            'manage_options',
            'mz-seo',
            array( $this, 'render_settings_page' )
        );
    }

    public function register_settings() {
        register_setting( self::OPT_GROUP, self::OPT_NAME, array( $this, 'sanitize_options' ) );
    }

    public function sanitize_options( $in ) {
        $out = array();
        $out['default_desc']     = isset( $in['default_desc'] )     ? sanitize_textarea_field( $in['default_desc'] ) : '';
        $out['default_og_image'] = isset( $in['default_og_image'] ) ? absint( $in['default_og_image'] )              : 0;
        $out['twitter_handle']   = isset( $in['twitter_handle'] )   ? sanitize_text_field( $in['twitter_handle'] )   : '';
        return $out;
    }

    public function render_settings_page() {
        if ( ! current_user_can( 'manage_options' ) ) { return; }
        $opts = (array) get_option( self::OPT_NAME, array() );
        $img_id  = isset( $opts['default_og_image'] ) ? (int) $opts['default_og_image'] : 0;
        $img_url = $img_id ? wp_get_attachment_image_url( $img_id, 'medium' ) : '';

        wp_enqueue_media();
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Ρυθμίσεις SEO', 'mourtzilaki-seo' ); ?></h1>
            <form method="post" action="options.php">
                <?php settings_fields( self::OPT_GROUP ); ?>
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><label for="mz_default_desc"><?php esc_html_e( 'Προεπιλεγμένη περιγραφή', 'mourtzilaki-seo' ); ?></label></th>
                        <td>
                            <textarea id="mz_default_desc" class="large-text" rows="3" name="<?php echo esc_attr( self::OPT_NAME ); ?>[default_desc]"><?php echo esc_textarea( $opts['default_desc'] ?? '' ); ?></textarea>
                            <p class="description"><?php esc_html_e( 'Χρησιμοποιείται όταν δεν υπάρχει συγκεκριμένη περιγραφή στη σελίδα.', 'mourtzilaki-seo' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Προεπιλεγμένη εικόνα Social', 'mourtzilaki-seo' ); ?></th>
                        <td>
                            <div style="display:flex; gap:14px; align-items:flex-start;">
                                <div id="mz_seo_default_img_preview" style="width:200px;height:105px;background:#f0f0f1 center/cover no-repeat;border:1px solid #dcdcde;border-radius:3px;<?php echo $img_url ? 'background-image:url(' . esc_url( $img_url ) . ');' : ''; ?>"></div>
                                <div>
                                    <input type="hidden" id="mz_seo_default_img_id" name="<?php echo esc_attr( self::OPT_NAME ); ?>[default_og_image]" value="<?php echo esc_attr( $img_id ); ?>">
                                    <p>
                                        <button type="button" class="button" id="mz_seo_default_img_select"><?php esc_html_e( 'Επιλογή εικόνας', 'mourtzilaki-seo' ); ?></button>
                                        <button type="button" class="button-link" id="mz_seo_default_img_clear" <?php echo $img_id ? '' : 'style="display:none;"'; ?>><?php esc_html_e( 'Αφαίρεση', 'mourtzilaki-seo' ); ?></button>
                                    </p>
                                    <p class="description"><?php esc_html_e( 'Συνιστώμενη διάσταση 1200×630.', 'mourtzilaki-seo' ); ?></p>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="mz_twitter_handle"><?php esc_html_e( 'Twitter / X handle', 'mourtzilaki-seo' ); ?></label></th>
                        <td>
                            <input type="text" id="mz_twitter_handle" class="regular-text" name="<?php echo esc_attr( self::OPT_NAME ); ?>[twitter_handle]" value="<?php echo esc_attr( $opts['twitter_handle'] ?? '' ); ?>" placeholder="mourtzilakilaw">
                            <p class="description"><?php esc_html_e( 'Χωρίς το @. Π.χ. mourtzilakilaw', 'mourtzilaki-seo' ); ?></p>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <script>
        (function(){
            var btn = document.getElementById('mz_seo_default_img_select');
            var clr = document.getElementById('mz_seo_default_img_clear');
            var input = document.getElementById('mz_seo_default_img_id');
            var prev  = document.getElementById('mz_seo_default_img_preview');
            var frame;
            btn.addEventListener('click', function(e){
                e.preventDefault();
                if (frame) { frame.open(); return; }
                frame = wp.media({ title: 'Επιλέξτε εικόνα', button: { text: 'Χρήση εικόνας' }, multiple:false, library:{ type:'image' }});
                frame.on('select', function(){
                    var a = frame.state().get('selection').first().toJSON();
                    input.value = a.id;
                    prev.style.backgroundImage = 'url(' + a.url + ')';
                    clr.style.display = '';
                });
                frame.open();
            });
            clr.addEventListener('click', function(e){
                e.preventDefault();
                input.value = '';
                prev.style.backgroundImage = '';
                clr.style.display = 'none';
            });
        })();
        </script>
        <?php
    }
}

Mourtzilaki_SEO::init();
