# Mourtzilaki Law

> Δικηγορικό γραφείο — modern WordPress site με custom theme, ACF integration και custom plugins.

**Stack**: WordPress 6.9 · PHP 8.3 · Custom Theme · ACF · Contact Form 7 · DDEV

---

## ✨ Χαρακτηριστικά

- 🎨 **Custom theme** (`mourtzilaki`) — 11 σελίδες, magazine-style article layout, 9-section homepage
- 🧩 **ACF integration** — όλο το content διαχειρίσιμο μέσω WP admin (8 field groups, 7 tabs στην αρχική)
- 🎠 **Hero carousel** με Slick (5 slides από CPT)
- 📂 **5 Custom Post Types** — Hero Slides · Τομείς · Δικηγόροι · Testimonials · FAQ
- 🔌 **3 custom plugins** — SEO, Analytics, Login skin
- 📊 **Self-hosted analytics dashboard** με Chart.js trend graph & top-viewed lists
- 🔍 **Custom SEO plugin** με Open Graph, Twitter Cards, focus keywords
- 🦴 **Mega menu** για τομείς + simple dropdowns για άλλες κατηγορίες
- 📱 **Πλήρως responsive** — fixed mobile header, animated mobile menu
- 🎬 **Scroll-reveal animations** μέσω IntersectionObserver
- 🔎 **FAQ με search + category filters**
- 📚 **Νομικό λεξικό** με sticky αλφαβητική πλοήγηση
- 🎯 **6 πλήρη άρθρα** με rich content (key takeaways, pull quotes, related services)
- ⚖️ **Decorative SVG symbols** (ζυγαριά, κίονες, ναός, σφυρί) σε strategic sections
- 🏷️ **Custom branded login screen**

---

## 📋 Απαιτήσεις

- [DDEV](https://ddev.com) ≥ 1.22
- Docker / Docker Desktop / OrbStack
- Git

Όλες οι υπόλοιπες εξαρτήσεις (PHP, WP-CLI, MariaDB) έρχονται από το DDEV image.

---

## 🚀 Γρήγορη εγκατάσταση

### 1. Clone

```bash
git clone git@github.com:ArkileDhamo154589/mlaw.git mourtzilakilaw
cd mourtzilakilaw
```

### 2. Σήκωμα DDEV

```bash
ddev start
```

Το DDEV θα τρέξει WordPress 6.9 σε PHP 8.3 με MariaDB 11.8. Default URL: `https://mourtzilakilaw.ddev.site`

### 3. Εγκατάσταση WordPress

```bash
ddev wp core install \
  --url=https://mourtzilakilaw.ddev.site \
  --title="Mourtzilaki Law" \
  --admin_user=achilleas \
  --admin_password=CHANGE_ME \
  --admin_email=admin@example.com \
  --skip-email
```

### 4. Ενεργοποίηση theme + plugins

```bash
ddev wp theme activate mourtzilaki
ddev wp plugin activate \
  advanced-custom-fields \
  contact-form-7 \
  mourtzilaki-seo \
  mourtzilaki-analytics
```

### 5. Migration & seeding

```bash
ddev wp eval '
  mourtzilaki_seed_content();
  mourtzilaki_migrate_to_cpts();
  mourtzilaki_seed_home_defaults();
  mourtzilaki_seed_service_content();
  mourtzilaki_seed_articles_v2();
  mourtzilaki_seed_faqs();
  flush_rewrite_rules(false);
  echo "ok\n";
'
```

Αυτό seedάρει αυτόματα:

- 11 pages (Αρχική, Το γραφείο, Τομείς, Δικηγόροι, Βιογραφικό, Συστάσεις, FAQ, Νομικό λεξικό, Καριέρα, Άρθρα, Επικοινωνία)
- 5 hero slides με images
- 8 τομείς δικαίου με long descriptions (Εμπορικό, Αστικό, Ακίνητα, Οικογενειακό, Εργατικό, Ποινικό, Διοικητικό, Τραπεζικό)
- 1 δικηγόρο (Έλενα Μουρτζιλάκη) με photo
- 6 άρθρα με featured images, takeaways, pull quotes
- 8 FAQ entries σε 4 κατηγορίες
- 1 testimonial
- ACF defaults για homepage, about page, contact page

### 6. Δημιουργία CF7 form

```bash
ddev wp eval '
$body = file_get_contents(get_template_directory() . "/inc/cf7-form.txt");
$id = wp_insert_post(["post_type" => "wpcf7_contact_form", "post_title" => "Φόρμα Επικοινωνίας", "post_status" => "publish"]);
update_post_meta($id, "_form", $body);
update_post_meta($id, "_mail", [
  "subject" => "Νέο μήνυμα από [your-name]",
  "sender" => "[your-name] <wordpress@mourtzilakilaw.gr>",
  "recipient" => get_option("admin_email"),
  "body" => "Όνομα: [your-name]\nEmail: [your-email]\nΤηλέφωνο: [your-phone]\nΘέμα: [your-subject]\n\n[your-message]",
  "additional_headers" => "Reply-To: [your-email]",
]);
set_theme_mod("mourtzilaki_cf7_id", $id);
echo "form: $id\n";
'
```

### 7. Logo (προαιρετικά)

Πάνε στο `Appearance → Customize → Site Identity` και ανέβασε logo, ή προγραμματιστικά:

```bash
ddev wp eval 'set_theme_mod("custom_logo", ATTACHMENT_ID);'
```

Το site είναι έτοιμο στο **https://mourtzilakilaw.ddev.site** ✓

---

## 🔑 Default credentials (local)

| Τι | Πού |
|----|-----|
| URL | https://mourtzilakilaw.ddev.site |
| Admin | https://mourtzilakilaw.ddev.site/wp-admin |
| User / Pass | `achilleas` / ορίστηκε στο step 3 |
| Database | user `db`, pass `db`, host `db:3306` |
| MailPit | https://mourtzilakilaw.ddev.site:8026 |
| Analytics | `/wp-admin/admin.php?page=mz-analytics` |
| SEO settings | `/wp-admin/options-general.php?page=mz-seo` |

---

## 📁 Δομή project

```
mourtzilakilaw/
├── .ddev/                                  # DDEV config (committed)
├── wp-content/
│   ├── themes/
│   │   └── mourtzilaki/                    # Custom theme
│   │       ├── style.css                   # Theme header + όλο το CSS
│   │       ├── functions.php               # Setup, CPTs, ACF, helpers, seedings
│   │       ├── header.php / footer.php
│   │       ├── front-page.php              # 9-section homepage
│   │       ├── page-{about,services,team,bio,reviews,faq,glossary,careers,contact,blog}.php
│   │       ├── single.php                  # Magazine-style article
│   │       ├── single-mz_service.php       # Service landing page
│   │       ├── 404.php / page.php / index.php / searchform.php
│   │       └── assets/
│   │           ├── img/{hero,team,about,posts}/   # Local images
│   │           ├── js/main.js                     # Slick init, reveal animations,
│   │           │                                  # mobile menu, FAQ filter, back-to-top
│   │           └── css/                           # (empty — όλα στο style.css)
│   ├── plugins/
│   │   ├── mourtzilaki-seo/                # Custom SEO plugin
│   │   ├── mourtzilaki-analytics/          # Custom analytics plugin
│   │   ├── advanced-custom-fields/         # ACF (free, third-party)
│   │   └── contact-form-7/                 # CF7 (third-party)
│   ├── mu-plugins/
│   │   └── custom-login.php                # Branded login screen
│   └── uploads/                            # Media library (gitignored)
└── ...wp core files
```

---

## 🎨 Theme features

### Σελίδες (auto-seeded)

| Slug | Template | Περιγραφή |
|------|----------|-----------|
| `/` (`arxiki`) | `front-page.php` | 9 sections: hero carousel · trust strip · philosophy · 8 practice tiles · process · testimonial · lawyer · articles · CTA |
| `/about/` | `page-about.php` | Story, mission, 4 values |
| `/services/` | `page-services.php` | 8 service tiles + 4-step process |
| `/team/` | `page-team.php` | Featured member layout |
| `/bio/` | `page-bio.php` | Studies, career, expertise, languages, publications |
| `/reviews/` | `page-reviews.php` | Testimonials grid |
| `/faq/` | `page-faq.php` | Search + category pills + accordion |
| `/glossary/` | `page-glossary.php` | 27 νομικοί όροι, sticky αλφαβητική nav |
| `/careers/` | `page-careers.php` | 3 career tiles |
| `/blog/` | `page-blog.php` | Featured + grid + pagination + newsletter band |
| `/contact/` | `page-contact.php` | 3 channel tiles + CF7 form + sidebar + Google Maps |
| `/tomeas/{slug}/` | `single-mz_service.php` | 8 service landing pages |
| Single posts | `single.php` | Magazine layout: hero, drop cap, key takeaways, pull quote, related service, author card, related articles |

### Custom Post Types

| CPT | Public | Slug | ACF Fields |
|-----|--------|------|------------|
| `mz_hero` | No | — | eyebrow, headline, lead, image, CTA label/url |
| `mz_service` | **Yes** | `/tomeas/` | description, long_description (wysiwyg) |
| `mz_member` | No | — | role, short_bio, photo, email, phone |
| `mz_testimonial` | No | — | quote, role |
| `mz_faq` | No | — | answer (wysiwyg), category |

### ACF Page-level fields

- **Όλες οι pages**: `page_eyebrow`, `page_hero_title`, `page_hero_lead`
- **Front page** (7 tabs): trust strip, philosophy, services intro, process steps, lawyer feature, blog intro, CTA, footer
- **About page**: story, mission, values list
- **Contact page**: address, phone, email, hours
- **Posts** (5 tabs): is_featured, subtitle, key_takeaways, pull_quote, related_service, cta_title/text, disclaimer

---

## 🔌 Custom Plugins

### Mourtzilaki SEO

Per-post & site-wide SEO management.

- Meta box σε κάθε post/page: **SEO Title** · **Meta Description** · **Focus Keywords** · **Social Image (Open Graph)**
- Live character counters (60 / 160) με color-coded warnings
- Media library picker για OG image
- Site-wide defaults στο `Settings → SEO`: default description, default OG image, Twitter handle
- Output: `<meta description>`, `<meta keywords>`, canonical, full **Open Graph** + **Twitter Cards**
- Smart fallback chain: post field → featured image / excerpt → site-wide default → site name/tagline

### Mourtzilaki Analytics

Self-hosted, privacy-respecting analytics. **Δεν** χρησιμοποιεί cookies, **δεν** στέλνει δεδομένα σε τρίτους.

**Tracking**:
- Pageviews per post (skip admins, bots, previews)
- Daily aggregate (rolling 90-day window)
- CF7 submissions log (last 50 entries)

**Dashboard** στο `/wp-admin/admin.php?page=mz-analytics`:
- Dark-mode design με gradient accents
- Hero με live indicator + greeting
- 4 KPI cards (animated counters): Συνολικές προβολές · Σήμερα · Φόρμες · Conversion rate
- Chart.js line graph 30 ημερών με χρυσό gradient fill
- Top 8 most-viewed list με post type badges
- Recent form submissions με avatar + name/email/subject/time
- Empty states + privacy footer

### Mourtzilaki Login (mu-plugin)

Branded login screen.

- Split-screen layout (brand left, form right)
- Custom typography
- Branded colors
- Removes WP logo, replaces with firm name

---

## 🧰 Διαχείριση content (WP Admin)

| Που | Τι κάνεις |
|-----|-----------|
| `Hero Slides` | Επεξεργασία/προσθήκη carousel slides |
| `Τομείς` | Επεξεργασία 8 πρακτικών (πλήρες content + descriptions) |
| `Δικηγόροι` | Διαχείριση team members |
| `Testimonials` | Συστάσεις πελατών |
| `FAQ` | Συχνές ερωτήσεις |
| `Σελίδες → Αρχική` | 7 ACF tabs με όλο το home content |
| `Σελίδες → Επικοινωνία` | Address, phone, email, hours (διαβάζονται από footer/header) |
| `Άρθρα → νέο post` | 5 ACF tabs: Παρουσίαση, Περιεχόμενο, Σύνδεση, CTA, Νομικά |
| `Επαφή → Φόρμες` | CF7 form management |
| `Settings → SEO` | Site-wide SEO defaults |
| `Analytics` | Dashboard με γραφήματα |

---

## 🛠️ Development

### Useful WP-CLI commands

```bash
ddev wp post list --post_type=mz_service              # List services
ddev wp post list --post_type=mz_faq                  # List FAQs
ddev wp option get mz_views_log --format=json         # Daily views log
ddev wp eval 'flush_rewrite_rules(false);'            # After URL changes
ddev wp cache flush                                   # Clear caches
```

### Database snapshots

```bash
ddev snapshot              # Save current state
ddev snapshot --list       # List snapshots
ddev snapshot restore NAME # Restore
```

### SSH into web container

```bash
ddev ssh
```

### Στοιχεία container

- Webserver: nginx-fpm + PHP 8.3
- Database: MariaDB 11.8
- Mailpit (mail catcher): https://mourtzilakilaw.ddev.site:8026

---

## 📝 License

GPL-2.0-or-later (matching WordPress)

---

## 👤 Credits

- **Theme & custom plugins** — Mourtzilaki Law
- **Photos** — [Unsplash](https://unsplash.com) (free license)
- **Icons** — Custom inline SVGs
- **Carousel** — [Slick Carousel](https://kenwheeler.github.io/slick/)
- **Charts** — [Chart.js](https://www.chartjs.org)
- **Fonts** — System font stack (Inter, Segoe UI, BlinkMacSystemFont)
- **Powered by** — [WordPress](https://wordpress.org) · [DDEV](https://ddev.com) · [ACF](https://www.advancedcustomfields.com)
