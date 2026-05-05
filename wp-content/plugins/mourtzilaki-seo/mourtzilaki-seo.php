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
        add_action( 'wp_head',                array( $self, 'print_meta' ), 1 );
        add_action( 'admin_menu',             array( $self, 'add_settings_page' ) );
        add_action( 'admin_init',             array( $self, 'register_settings' ) );
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
        ?>
        <style>
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
