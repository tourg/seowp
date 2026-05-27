Based on your uploaded SEO Autopilot blueprint, the current system is a Next.js SaaS, not a WordPress plugin, so this prompt tells the AI agent to rebuild the same feature set as a **native WordPress plugin**. 

````markdown
# AI AGENT PROMPT — Build Full WordPress SEO Autopilot Plugin

You are a senior WordPress plugin engineer, PHP 8.2+ developer, WordPress REST API expert, SEO engineer, and security-focused SaaS architect.

Your task is to create a complete production-ready WordPress plugin that recreates the full feature set of the existing SEO Autopilot SaaS blueprint as a native WordPress plugin.

The original system was a Next.js SaaS dashboard that connects remotely to WordPress via REST API. For this task, rebuild it as a WordPress plugin installed directly inside WordPress.

Do not build a Next.js SaaS app.
Do not require an external dashboard.
Do not depend on XAMPP, Prisma, BullMQ, Redis, NextAuth, Stripe SaaS billing, or external SaaS infrastructure.
Everything must run inside WordPress as a plugin, while still allowing external AI providers and Google Search Console integration.

---

## 0. Main Goal

Create a WordPress plugin named:

**SEO Autopilot for WordPress**

Plugin slug:

`seo-autopilot-wp`

The plugin should help WordPress site owners:

- Audit SEO for posts, pages, and custom post types
- Optimize meta titles and meta descriptions
- Generate AI SEO suggestions
- Generate AI articles
- Generate FAQ sections
- Generate schema JSON-LD
- Optimize Traveler theme content
- Support Yoast SEO, Rank Math, and AIOSEO
- Sync and analyze Traveler theme custom post types
- Detect SEO issues
- Create before/after diffs
- Require manual approval before publishing AI changes
- Backup content before publishing
- Rollback published changes
- Track SEO score over time
- Integrate with Google Search Console
- Run weekly SEO improvement cycles
- Generate reports
- Add automation rules
- Provide a full WordPress admin dashboard

---

## 1. Required Plugin Architecture

Build a clean modular WordPress plugin using best practices.

Recommended structure:

```text
seo-autopilot-wp/
├── seo-autopilot-wp.php
├── readme.txt
├── uninstall.php
├── composer.json
├── package.json
├── vite.config.js
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
├── build/
├── languages/
├── templates/
├── includes/
│   ├── Core/
│   │   ├── Plugin.php
│   │   ├── Activator.php
│   │   ├── Deactivator.php
│   │   ├── Installer.php
│   │   ├── Uninstaller.php
│   │   ├── Capabilities.php
│   │   ├── Cron.php
│   │   ├── Logger.php
│   │   └── Container.php
│   ├── Admin/
│   │   ├── AdminMenu.php
│   │   ├── DashboardPage.php
│   │   ├── AuditPage.php
│   │   ├── OptimizerPage.php
│   │   ├── ApprovalsPage.php
│   │   ├── GeneratorPage.php
│   │   ├── KeywordsPage.php
│   │   ├── ReportsPage.php
│   │   ├── AutomationPage.php
│   │   ├── SettingsPage.php
│   │   └── Assets.php
│   ├── Rest/
│   │   ├── RestController.php
│   │   ├── DashboardController.php
│   │   ├── AuditController.php
│   │   ├── OptimizerController.php
│   │   ├── ApprovalsController.php
│   │   ├── GeneratorController.php
│   │   ├── KeywordsController.php
│   │   ├── SchemaController.php
│   │   ├── ReportsController.php
│   │   ├── AutomationController.php
│   │   ├── TravelerController.php
│   │   ├── GscController.php
│   │   └── SettingsController.php
│   ├── SEO/
│   │   ├── SeoScanner.php
│   │   ├── SeoScore.php
│   │   ├── Rules/
│   │   │   ├── MetaTitleRule.php
│   │   │   ├── MetaDescriptionRule.php
│   │   │   ├── CanonicalRule.php
│   │   │   ├── ImageAltRule.php
│   │   │   ├── ContentQualityRule.php
│   │   │   ├── DuplicateMetaRule.php
│   │   │   ├── ReadabilityRule.php
│   │   │   ├── LocalSeoRule.php
│   │   │   ├── InternalLinksRule.php
│   │   │   ├── SitemapRule.php
│   │   │   └── RobotsTxtRule.php
│   │   ├── InternalLinkAnalyzer.php
│   │   ├── OpportunityDetector.php
│   │   ├── TrendTracker.php
│   │   ├── WeeklyCycle.php
│   │   └── ContentFreshness.php
│   ├── AI/
│   │   ├── AiProviderInterface.php
│   │   ├── OpenAiProvider.php
│   │   ├── ClaudeProvider.php
│   │   ├── GeminiProvider.php
│   │   ├── OpenRouterProvider.php
│   │   ├── AiClient.php
│   │   ├── PromptBuilder.php
│   │   ├── SeoPrompts.php
│   │   ├── TravelerPrompts.php
│   │   └── TokenUsageTracker.php
│   ├── Optimizer/
│   │   ├── ContentOptimizer.php
│   │   ├── DiffGenerator.php
│   │   ├── ApprovalService.php
│   │   ├── Publisher.php
│   │   ├── BackupService.php
│   │   ├── RollbackService.php
│   │   ├── YoastAdapter.php
│   │   ├── RankMathAdapter.php
│   │   ├── AioseoAdapter.php
│   │   └── SeoPluginDetector.php
│   ├── Traveler/
│   │   ├── TravelerDetector.php
│   │   ├── TravelerContentSync.php
│   │   ├── TravelerFieldDiscovery.php
│   │   ├── TravelerFieldClassifier.php
│   │   ├── TravelerPublisher.php
│   │   ├── TravelerSchema.php
│   │   ├── TravelerAudit.php
│   │   └── TravelerContentAdapter.php
│   ├── Schema/
│   │   ├── SchemaGenerator.php
│   │   ├── ArticleSchema.php
│   │   ├── FaqSchema.php
│   │   ├── BreadcrumbSchema.php
│   │   ├── LocalBusinessSchema.php
│   │   ├── TourSchema.php
│   │   ├── ActivitySchema.php
│   │   ├── HotelSchema.php
│   │   └── SchemaInjector.php
│   ├── Keywords/
│   │   ├── KeywordDiscovery.php
│   │   ├── KeywordClusterer.php
│   │   ├── KeywordGapAnalyzer.php
│   │   └── KeywordRepository.php
│   ├── GSC/
│   │   ├── GoogleSearchConsoleClient.php
│   │   ├── OAuthHandler.php
│   │   ├── MetricsSync.php
│   │   └── OpportunityFromGsc.php
│   ├── Reports/
│   │   ├── ReportGenerator.php
│   │   ├── PdfReport.php
│   │   ├── HtmlReport.php
│   │   └── ShareableReport.php
│   ├── Automation/
│   │   ├── AutomationRule.php
│   │   ├── AutomationRunner.php
│   │   ├── ScheduledTask.php
│   │   └── WeeklyAutomation.php
│   ├── Database/
│   │   ├── Tables.php
│   │   ├── Migrations.php
│   │   ├── Repositories/
│   │   │   ├── AuditRepository.php
│   │   │   ├── IssueRepository.php
│   │   │   ├── DraftRepository.php
│   │   │   ├── ApprovalRepository.php
│   │   │   ├── BackupRepository.php
│   │   │   ├── KeywordRepository.php
│   │   │   ├── ReportRepository.php
│   │   │   ├── AutomationRepository.php
│   │   │   └── UsageRepository.php
│   ├── Security/
│   │   ├── Nonce.php
│   │   ├── Permission.php
│   │   ├── Sanitizer.php
│   │   ├── Encryption.php
│   │   ├── RateLimiter.php
│   │   ├── UrlSafety.php
│   │   └── HtmlSanitizer.php
│   └── Utils/
│       ├── Http.php
│       ├── ArrayHelper.php
│       ├── StringHelper.php
│       ├── DateHelper.php
│       └── Response.php
└── tests/
    ├── unit/
    ├── integration/
    └── e2e/
````

---

## 2. Plugin Bootstrap Requirements

Create main plugin file:

`seo-autopilot-wp.php`

It must include:

* Plugin header
* ABSPATH protection
* PHP version check
* WordPress version check
* Composer autoload support
* Activation hook
* Deactivation hook
* Uninstall handling
* Main plugin singleton/bootstrap
* Text domain loading
* Admin menu loading
* REST route registration
* Cron schedule registration

Required plugin header:

```php
/**
 * Plugin Name: SEO Autopilot for WordPress
 * Plugin URI: https://example.com/seo-autopilot-wp
 * Description: AI-powered SEO automation, audit, optimization, Traveler theme SEO, schema generation, approvals, backups, rollback, reports, and Google Search Console insights.
 * Version: 1.0.0
 * Author: SEO Autopilot
 * Text Domain: seo-autopilot-wp
 * Domain Path: /languages
 * Requires at least: 6.4
 * Requires PHP: 8.2
 */
```

---

## 3. WordPress Admin Dashboard

Create a modern WordPress admin dashboard.

Main menu:

**SEO Autopilot**

Submenus:

* Dashboard
* SEO Audit
* Content Optimizer
* AI Generator
* Approvals
* Traveler SEO
* Keywords
* Schema
* Google Search Console
* Reports
* Automation
* Settings
* Logs

Use React or vanilla JS admin app.

Preferred:

* React admin app bundled with Vite
* WordPress REST API for backend communication
* wp_enqueue_script
* wp_localize_script or wp_add_inline_script for nonce/config
* Tailwind or WordPress admin-compatible CSS
* Clean cards, tables, filters, tabs, modals, status badges

Dashboard must show:

* Overall SEO score
* Number of scanned posts/pages/CPTs
* Open SEO issues
* Critical issues
* AI drafts pending approval
* Published optimizations
* Rollbacks available
* Keyword opportunities
* GSC clicks/impressions if connected
* Recent activity
* Weekly trend chart
* Issue distribution chart

---

## 4. Database Tables

Create custom WordPress database tables using dbDelta.

Use `$wpdb->prefix`.

Required tables:

```text
wp_seo_autopilot_audits
wp_seo_autopilot_issues
wp_seo_autopilot_drafts
wp_seo_autopilot_approvals
wp_seo_autopilot_backups
wp_seo_autopilot_publish_history
wp_seo_autopilot_keywords
wp_seo_autopilot_keyword_clusters
wp_seo_autopilot_reports
wp_seo_autopilot_automation_rules
wp_seo_autopilot_jobs
wp_seo_autopilot_logs
wp_seo_autopilot_token_usage
wp_seo_autopilot_gsc_connections
wp_seo_autopilot_gsc_metrics
wp_seo_autopilot_traveler_fields
wp_seo_autopilot_traveler_audits
wp_seo_autopilot_settings
```

Each table must include:

* id BIGINT unsigned auto increment primary key
* created_at datetime
* updated_at datetime where needed
* proper indexes
* site/content/post references where needed
* JSON columns as LONGTEXT with validation in PHP
* status columns where needed

Do not store sensitive data in plain text.

---

## 5. Security Requirements

The plugin must follow WordPress security best practices.

Implement:

* Capability checks for every admin page
* REST permission callbacks
* Nonce validation for admin requests
* Sanitization for all inputs
* Escaping for all outputs
* Prepared SQL statements
* No raw unsanitized SQL
* No direct file access
* No unsafe eval
* No untrusted HTML injection
* Rate limiting for AI calls and expensive scans
* Encrypted storage for API keys
* Secure deletion of secrets on uninstall if user chooses full cleanup

Required capabilities:

```text
manage_seo_autopilot
seo_autopilot_view_dashboard
seo_autopilot_run_audits
seo_autopilot_generate_ai
seo_autopilot_approve_changes
seo_autopilot_publish_changes
seo_autopilot_manage_settings
seo_autopilot_view_reports
```

Admin users get all capabilities on activation.

REST API must use:

* `current_user_can()`
* `check_ajax_referer()` or REST nonce verification
* Strict permission callbacks
* Structured JSON error responses

---

## 6. Settings Page

Create settings sections:

### General Settings

* Enable plugin
* Default post types to scan
* Include pages
* Include custom post types
* Scan frequency
* Auto-detect SEO plugins
* Enable logs
* Data retention days

### AI Provider Settings

Support:

* OpenAI
* Claude
* Gemini
* OpenRouter

Each provider should have:

* Enable/disable
* API key
* Model name
* Max tokens
* Temperature
* Monthly token limit
* Test connection button

API keys must be encrypted before storage.

### SEO Settings

* Target language
* Default country/region
* Default brand name
* Default business name
* Default destination/location
* Minimum word count
* Meta title length range
* Meta description length range
* Enable local SEO rules
* Enable readability scoring
* Enable internal link suggestions
* Enable duplicate meta detection

### Publishing Settings

* Require manual approval before publish: default true
* Always create backup before publish: default true
* Allow auto-publish: default false
* Allow rollback: default true
* Rollback retention days
* Preserve shortcodes
* Preserve blocks
* Preserve booking fields
* Preserve custom fields

### Traveler Settings

* Enable Traveler integration
* Auto-detect Traveler theme
* Scan Traveler CPTs
* Protect booking fields
* Protect price fields
* Protect availability fields
* Enable Traveler schema
* Enable destination SEO
* Enable seasonal content detection

### Google Search Console Settings

* Connect Google account
* OAuth client ID
* OAuth client secret
* Select property
* Sync frequency
* Enable GSC opportunity detection

---

## 7. REST API Routes

Register all routes under:

`/wp-json/seo-autopilot/v1/`

Required routes:

### Dashboard

```text
GET /dashboard
GET /dashboard/trends
GET /dashboard/issues
GET /dashboard/activity
```

### Audit

```text
POST /audit/run
GET /audit/results
GET /audit/results/{post_id}
GET /audit/issues
POST /audit/bulk
```

### Optimizer

```text
POST /optimizer/analyze/{post_id}
POST /optimizer/generate/{post_id}
GET /optimizer/suggestions/{post_id}
POST /optimizer/diff/{draft_id}
```

### Approvals

```text
GET /approvals
GET /approvals/{id}
POST /approvals/{id}/approve
POST /approvals/{id}/reject
POST /approvals/{id}/edit
POST /approvals/{id}/publish
```

### Publishing

```text
POST /publish/{draft_id}
POST /publish/{draft_id}/rollback
GET /publish/history
GET /publish/backups/{post_id}
```

### Generator

```text
POST /generator/article
POST /generator/meta
POST /generator/faq
POST /generator/schema
GET /generator/history
```

### Keywords

```text
POST /keywords/discover
GET /keywords
POST /keywords/cluster
GET /keywords/clusters
POST /keywords/gaps
```

### Schema

```text
POST /schema/generate/{post_id}
GET /schema/{post_id}
POST /schema/inject/{post_id}
DELETE /schema/{post_id}
```

### Traveler

```text
GET /traveler/detect
GET /traveler/content
POST /traveler/sync
GET /traveler/fields
POST /traveler/audit/{post_id}
POST /traveler/optimize/{post_id}
POST /traveler/schema/{post_id}
```

### Google Search Console

```text
GET /gsc/auth-url
POST /gsc/callback
GET /gsc/properties
POST /gsc/sync
GET /gsc/metrics
GET /gsc/opportunities
```

### Reports

```text
GET /reports
POST /reports/generate
GET /reports/{id}
GET /reports/{id}/download
DELETE /reports/{id}
```

### Automation

```text
GET /automation/rules
POST /automation/rules
PUT /automation/rules/{id}
DELETE /automation/rules/{id}
POST /automation/run/{id}
GET /automation/jobs
```

### Settings

```text
GET /settings
POST /settings
POST /settings/test-ai
POST /settings/test-gsc
```

---

## 8. SEO Audit Engine

Build a complete SEO audit engine.

It must scan:

* Posts
* Pages
* Custom post types
* Traveler CPTs
* Titles
* Meta titles
* Meta descriptions
* Headings
* Content length
* Keyword usage
* Images and alt text
* Internal links
* External links
* Canonical URLs
* Schema presence
* FAQ presence
* Duplicate titles
* Duplicate descriptions
* Readability
* Local SEO signals
* Sitemap availability
* robots.txt
* Indexability
* Slug quality
* Content freshness

SEO score must be from 0 to 100.

Score categories:

```text
technical_score
onpage_score
content_score
links_score
freshness_score
schema_score
local_seo_score
```

Issue severity:

```text
critical
high
medium
low
info
```

Issue status:

```text
open
fixed
ignored
```

Each issue must include:

* post_id
* post_type
* issue_key
* title
* description
* severity
* recommendation
* current_value
* suggested_value
* created_at

---

## 9. SEO Rules Required

Implement the following rule classes:

### Meta Title Rule

Checks:

* Missing SEO title
* Too short
* Too long
* Missing focus keyword
* Missing destination/location
* Duplicate title
* Brand placement

### Meta Description Rule

Checks:

* Missing description
* Too short
* Too long
* Missing focus keyword
* Missing call to action
* Duplicate description

### Canonical Rule

Checks:

* Missing canonical
* Invalid canonical
* Self-referencing canonical
* Duplicate canonical

### Image Alt Rule

Checks:

* Missing alt text
* Generic alt text
* Keyword stuffing in alt text
* Featured image missing alt

### Content Quality Rule

Checks:

* Thin content
* Missing headings
* Bad heading hierarchy
* Missing FAQ
* Weak introduction
* Weak call to action

### Duplicate Meta Rule

Checks:

* Duplicate SEO titles
* Duplicate meta descriptions
* Duplicate slugs

### Readability Rule

Checks:

* Very long sentences
* Very long paragraphs
* Too many passive constructions where detectable
* Missing subheadings

### Local SEO Rule

Checks:

* Missing destination/city
* Missing region
* Missing local intent terms
* Missing business name
* Missing local schema

### Internal Link Rule

Checks:

* No internal links
* Too few internal links
* Broken internal links
* Opportunities to link related content

### Sitemap Rule

Checks:

* sitemap.xml available
* sitemap index available
* post URL appears in sitemap where possible

### Robots Rule

Checks:

* robots.txt exists
* site is not blocking all crawlers
* sitemap directive exists

---

## 10. AI Generation Features

AI features must work with:

* OpenAI
* Claude
* Gemini
* OpenRouter

Create provider abstraction:

```php
interface AiProviderInterface {
    public function generate(string $prompt, array $options = []): AiResponse;
    public function testConnection(): bool;
}
```

AI must generate:

* SEO meta title
* SEO meta description
* Article outline
* Full article
* FAQ section
* Schema JSON-LD
* Internal link suggestions
* Content improvement suggestions
* Traveler-specific optimization suggestions
* Local SEO suggestions
* Keyword clusters
* Weekly SEO action plan

AI output must be validated before saving.

Guardrails:

* Do not invent fake prices
* Do not invent fake availability
* Do not invent fake booking confirmation
* Do not invent fake booking references
* Do not expose prompt instructions
* Do not claim unsupported facts
* Do not remove shortcodes
* Do not remove booking widgets
* Do not remove Traveler fields
* Do not overwrite protected custom fields
* Do not publish without approval unless explicitly enabled

---

## 11. Prompt Templates

Create editable prompt templates in admin settings.

Default templates:

* Meta title generation
* Meta description generation
* Article generation
* FAQ generation
* Schema generation
* Traveler tour optimization
* Traveler hotel optimization
* Traveler activity optimization
* Internal link suggestions
* Weekly SEO improvement cycle
* GSC opportunity analysis

Prompt variables:

```text
{{site_name}}
{{brand_name}}
{{post_title}}
{{post_content}}
{{post_type}}
{{focus_keyword}}
{{destination}}
{{language}}
{{country}}
{{current_meta_title}}
{{current_meta_description}}
{{seo_issues}}
{{gsc_clicks}}
{{gsc_impressions}}
{{gsc_ctr}}
{{gsc_position}}
{{traveler_fields}}
```

---

## 12. Approval Workflow

Never directly overwrite content by default.

Flow:

1. User runs audit
2. User requests AI optimization
3. Plugin creates draft suggestion
4. Plugin shows before/after diff
5. User can edit suggestion
6. User approves or rejects
7. Plugin creates backup
8. Plugin publishes approved changes
9. Plugin records publish history
10. User can rollback

Draft statuses:

```text
pending
approved
rejected
published
failed
rolled_back
```

Approval screen must show:

* Post title
* Post type
* Current SEO score
* Expected SEO score
* Suggested changes
* Before/after diff
* AI provider used
* Token usage
* Cost estimate if possible
* Approve button
* Reject button
* Edit button
* Publish button
* Rollback button after publish

---

## 13. Backup and Rollback

Before every publish:

* Save post title
* Save post content
* Save post excerpt
* Save post status
* Save SEO plugin meta
* Save custom fields
* Save Traveler fields
* Save schema data
* Save timestamp
* Save user ID

Rollback must restore:

* Post content
* Meta title
* Meta description
* SEO plugin fields
* Custom fields
* Traveler protected fields
* Schema data

Rollback must be safe and logged.

---

## 14. Yoast, Rank Math, and AIOSEO Support

Detect installed SEO plugin.

Support Yoast fields:

```text
_yoast_wpseo_title
_yoast_wpseo_metadesc
_yoast_wpseo_focuskw
_yoast_wpseo_canonical
```

Support Rank Math fields:

```text
rank_math_title
rank_math_description
rank_math_focus_keyword
rank_math_canonical_url
```

Support AIOSEO fields where available.

Create adapter pattern:

```php
interface SeoPluginAdapterInterface {
    public function isActive(): bool;
    public function getMetaTitle(int $postId): ?string;
    public function updateMetaTitle(int $postId, string $title): void;
    public function getMetaDescription(int $postId): ?string;
    public function updateMetaDescription(int $postId, string $description): void;
    public function getFocusKeyword(int $postId): ?string;
    public function updateFocusKeyword(int $postId, string $keyword): void;
    public function getCanonical(int $postId): ?string;
    public function updateCanonical(int $postId, string $url): void;
}
```

If no SEO plugin exists, store SEO data in plugin meta and inject tags in frontend head.

---

## 15. Schema JSON-LD

Generate and inject schema.

Supported schema:

* Article
* FAQPage
* BreadcrumbList
* LocalBusiness
* Tour
* TouristTrip
* TouristAttraction
* Hotel
* Product where applicable
* Service where applicable

Schema injection:

* Use `wp_head`
* Avoid duplicate schema
* Respect Yoast/Rank Math schema where possible
* Allow user to preview schema
* Validate JSON before save

Traveler schema:

* Tour schema
* Activity schema
* Hotel schema
* Destination schema
* FAQ schema

---

## 16. Traveler Theme Integration

Traveler is a major requirement.

Detect Traveler theme by:

* Active theme name
* Parent theme name
* Known Traveler constants
* Known Traveler CPTs
* Known Traveler meta fields

Support common Traveler CPTs:

```text
st_tours
st_activity
st_hotel
st_rental
st_cars
st_location
```

Map display names:

```text
st_tours => Tours
st_activity => Activities
st_hotel => Hotels
st_rental => Rentals
st_cars => Cars
st_location => Locations
```

Traveler features:

* Detect Traveler content
* Sync Traveler content
* Audit Traveler content
* Optimize Traveler content
* Generate Traveler schema
* Preserve booking fields
* Preserve pricing fields
* Preserve availability fields
* Preserve calendar fields
* Preserve location fields
* Preserve gallery fields
* Preserve review fields

Protected fields should never be overwritten unless explicitly allowed.

Field classifier categories:

```text
seo_safe
content_safe
booking_protected
price_protected
availability_protected
system_protected
unknown_review_required
```

Traveler admin page must show:

* Traveler detected yes/no
* Found CPTs
* Found fields
* Protected fields
* Content count
* Audit status
* Optimization status

---

## 17. Keyword System

Implement keyword discovery and clustering.

Features:

* Manual keyword input
* AI keyword suggestions
* Extract keywords from content
* Extract destinations/locations
* Find missing focus keywords
* Keyword gap analysis
* Keyword clustering
* Keyword difficulty placeholder
* Search intent classification

Keyword intent types:

```text
informational
commercial
transactional
local
navigational
```

Keyword record fields:

* keyword
* intent
* cluster
* post_id
* source
* volume if available
* difficulty if available
* priority
* status

---

## 18. Google Search Console Integration

Implement Google Search Console OAuth.

Features:

* Connect Google account
* Select property
* Store refresh token encrypted
* Sync clicks
* Sync impressions
* Sync CTR
* Sync average position
* Sync pages
* Sync queries
* Detect pages losing traffic
* Detect pages with high impressions and low CTR
* Detect keywords near page 1
* Suggest optimizations based on GSC data

GSC opportunity types:

```text
high_impressions_low_ctr
ranking_distance
traffic_drop
content_refresh
keyword_expansion
meta_rewrite
```

---

## 19. Reports

Create report generation.

Report types:

* SEO audit report
* Weekly improvement report
* Traveler SEO report
* GSC opportunity report
* Published changes report
* Keyword report

Report formats:

* HTML inside WordPress admin
* Downloadable PDF if library available
* CSV export for issues and keywords

Reports must include:

* Overall score
* Score by category
* Issues by severity
* Fixed issues
* Pending issues
* Published optimizations
* Rollbacks
* Keyword opportunities
* GSC performance
* Recommendations

---

## 20. Automation

Use WordPress cron.

Automation rules:

* Weekly SEO audit
* Weekly GSC sync
* Weekly opportunity detection
* Monthly content freshness scan
* Auto-generate suggestions
* Auto-create drafts
* Notify admin for approval
* Never auto-publish unless setting enabled

Automation rule fields:

* name
* type
* frequency
* post types
* status
* last_run_at
* next_run_at
* settings JSON

Job statuses:

```text
queued
running
completed
failed
cancelled
```

Create job logs.

---

## 21. Logging

Create logs page.

Log types:

* audit_started
* audit_completed
* ai_generation_started
* ai_generation_completed
* draft_created
* draft_approved
* draft_rejected
* publish_started
* publish_completed
* publish_failed
* rollback_completed
* gsc_sync_started
* gsc_sync_completed
* automation_started
* automation_completed
* error

Each log:

* level
* message
* context JSON
* user_id
* post_id if available
* created_at

---

## 22. Frontend SEO Output

The plugin must optionally output SEO data on the frontend if no SEO plugin handles it.

Inject:

* `<title>` filter
* meta description
* canonical URL
* Open Graph title
* Open Graph description
* JSON-LD schema

Avoid conflicts with Yoast, Rank Math, and AIOSEO.

If SEO plugin active, update their fields instead of duplicating output.

---

## 23. Content Safety

When optimizing content:

Preserve:

* Gutenberg blocks
* Shortcodes
* HTML structure where possible
* Forms
* Booking widgets
* Traveler widgets
* WooCommerce shortcodes
* Affiliate links
* Existing internal links unless replacing safely
* Protected custom fields

Do not:

* Remove booking buttons
* Remove prices
* Invent prices
* Invent availability
* Invent reviews
* Invent locations
* Invent guarantees
* Break shortcodes
* Break block comments

---

## 24. Performance Requirements

The plugin must be efficient.

Implement:

* Batch scanning
* Pagination
* Background jobs via WP-Cron
* Avoid long admin requests
* Store audit results
* Cache dashboard stats with transients
* Clear cache on content update
* Avoid scanning all posts on every page load
* Use indexes in custom tables
* Use async REST actions for long jobs
* Allow scan limits per run

Large sites must not timeout.

---

## 25. Compatibility Requirements

Support:

* WordPress 6.4+
* PHP 8.2+
* Classic Editor
* Gutenberg Block Editor
* Custom post types
* Yoast SEO
* Rank Math
* AIOSEO
* Traveler theme
* WordPress multisite basic detection

Do not fully enable multisite network mode in v1 unless implemented safely.

---

## 26. Testing Requirements

Create tests for:

* Plugin activation
* Database table creation
* Settings save/load
* API key encryption/decryption
* REST permission checks
* SEO audit rules
* Meta title rule
* Meta description rule
* Image alt rule
* Content quality rule
* Schema generation
* AI provider abstraction with mock provider
* Draft creation
* Approval workflow
* Backup creation
* Rollback restore
* Traveler field protection
* Yoast adapter
* Rank Math adapter
* GSC client mock
* Automation cron scheduling

Use:

* PHPUnit for PHP
* WP test suite where possible
* Playwright/Cypress optional for admin UI

---

## 27. Build and Quality Gates

Before final delivery, run or document:

```bash
composer install
composer dump-autoload
npm install
npm run build
vendor/bin/phpcs
vendor/bin/phpunit
```

Use WordPress Coding Standards.

No fatal errors.
No direct output during plugin load.
No missing nonce checks.
No missing permission callbacks.
No unsafe SQL.
No plain-text API keys.
No admin page accessible without capability.

---

## 28. MVP Build Phases

### Phase 1 — Plugin Foundation

* [ ] Create plugin structure
* [ ] Add bootstrap file
* [ ] Add autoloading
* [ ] Add activation/deactivation
* [ ] Add database installer
* [ ] Add capabilities
* [ ] Add admin menu
* [ ] Add REST base controller
* [ ] Add settings storage
* [ ] Add logging service

### Phase 2 — Admin Dashboard

* [ ] Create React or JS admin app
* [ ] Add dashboard page
* [ ] Add KPI cards
* [ ] Add issue charts
* [ ] Add recent activity
* [ ] Add settings page
* [ ] Add logs page

### Phase 3 — SEO Audit Engine

* [ ] Add scanner
* [ ] Add scoring system
* [ ] Add meta title rule
* [ ] Add meta description rule
* [ ] Add canonical rule
* [ ] Add image alt rule
* [ ] Add content quality rule
* [ ] Add readability rule
* [ ] Add internal link rule
* [ ] Add sitemap rule
* [ ] Add robots rule
* [ ] Save audit results to DB

### Phase 4 — SEO Plugin Adapters

* [ ] Detect Yoast
* [ ] Detect Rank Math
* [ ] Detect AIOSEO
* [ ] Read/write meta titles
* [ ] Read/write meta descriptions
* [ ] Read/write focus keywords
* [ ] Read/write canonical URLs
* [ ] Fallback to plugin-owned meta

### Phase 5 — AI Provider System

* [ ] Add provider interface
* [ ] Add OpenAI provider
* [ ] Add Claude provider
* [ ] Add Gemini provider
* [ ] Add OpenRouter provider
* [ ] Add encrypted API key storage
* [ ] Add test connection
* [ ] Add prompt builder
* [ ] Add token usage tracking
* [ ] Add mock provider for tests

### Phase 6 — Generator and Optimizer

* [ ] Generate meta title
* [ ] Generate meta description
* [ ] Generate article
* [ ] Generate FAQ
* [ ] Generate schema
* [ ] Generate internal link suggestions
* [ ] Create draft suggestions
* [ ] Create before/after diff

### Phase 7 — Approval, Publish, Backup, Rollback

* [ ] Add approvals table
* [ ] Add approvals admin page
* [ ] Add approve/reject/edit actions
* [ ] Add backup service
* [ ] Add publisher service
* [ ] Add rollback service
* [ ] Add publish history

### Phase 8 — Traveler Integration

* [ ] Detect Traveler theme
* [ ] Detect Traveler CPTs
* [ ] Discover Traveler fields
* [ ] Classify protected fields
* [ ] Audit Traveler content
* [ ] Optimize Traveler content
* [ ] Generate Traveler schema
* [ ] Protect booking/price/availability fields
* [ ] Add Traveler admin page

### Phase 9 — Schema System

* [ ] Add schema generator
* [ ] Add Article schema
* [ ] Add FAQ schema
* [ ] Add Breadcrumb schema
* [ ] Add LocalBusiness schema
* [ ] Add Tour schema
* [ ] Add Activity schema
* [ ] Add Hotel schema
* [ ] Add schema preview
* [ ] Add schema injection

### Phase 10 — Keywords

* [ ] Add keyword extraction
* [ ] Add keyword discovery
* [ ] Add AI keyword suggestions
* [ ] Add keyword clustering
* [ ] Add keyword gap analyzer
* [ ] Add keyword admin page

### Phase 11 — Google Search Console

* [ ] Add OAuth flow
* [ ] Store encrypted refresh token
* [ ] Select property
* [ ] Sync metrics
* [ ] Show clicks/impressions/CTR/position
* [ ] Detect opportunities
* [ ] Use GSC data in AI prompts

### Phase 12 — Reports

* [ ] Add report generator
* [ ] Add audit report
* [ ] Add weekly report
* [ ] Add Traveler report
* [ ] Add GSC report
* [ ] Add HTML report view
* [ ] Add PDF export if available
* [ ] Add CSV export

### Phase 13 — Automation

* [ ] Add WP-Cron schedules
* [ ] Add automation rules
* [ ] Add weekly audit
* [ ] Add weekly GSC sync
* [ ] Add weekly AI suggestion generation
* [ ] Add monthly content freshness scan
* [ ] Add job history
* [ ] Add failure logs

### Phase 14 — Hardening

* [ ] Add rate limiting
* [ ] Add full nonce verification
* [ ] Add full capability checks
* [ ] Add HTML sanitizer
* [ ] Add URL safety validation
* [ ] Add CSP-friendly admin assets
* [ ] Add uninstall cleanup options
* [ ] Add import/export settings
* [ ] Add documentation

### Phase 15 — Testing and Release

* [ ] Add PHPUnit tests
* [ ] Add integration tests
* [ ] Add admin UI smoke tests
* [ ] Run PHPCS
* [ ] Run PHPUnit
* [ ] Build admin assets
* [ ] Create plugin zip
* [ ] Test install on fresh WordPress
* [ ] Test with Yoast
* [ ] Test with Rank Math
* [ ] Test with Traveler
* [ ] Test rollback
* [ ] Test AI provider failures
* [ ] Test large site scan

---

## 29. Final Acceptance Criteria

The plugin is complete only when:

* [ ] It installs without fatal errors
* [ ] It creates all required DB tables
* [ ] It adds SEO Autopilot admin menu
* [ ] Settings can be saved securely
* [ ] AI keys are encrypted
* [ ] SEO audit works for posts and pages
* [ ] SEO score is generated
* [ ] Issues are saved and displayed
* [ ] AI suggestions can be generated
* [ ] Suggestions are saved as drafts
* [ ] User can approve/reject/edit drafts
* [ ] Plugin creates backup before publish
* [ ] Plugin can publish approved changes
* [ ] Plugin can rollback published changes
* [ ] Yoast fields are supported
* [ ] Rank Math fields are supported
* [ ] AIOSEO fallback is handled
* [ ] Schema can be generated and injected
* [ ] Traveler theme is detected
* [ ] Traveler fields are protected
* [ ] Google Search Console can connect
* [ ] Reports can be generated
* [ ] Automation can run with WP-Cron
* [ ] Logs are visible
* [ ] No REST route lacks permission checks
* [ ] No unsafe SQL exists
* [ ] No API key is stored in plain text
* [ ] PHPCS passes
* [ ] PHPUnit tests pass
* [ ] Plugin zip is ready for deployment

---

## 30. Important Implementation Rules

Follow these rules strictly:

1. Build this as a native WordPress plugin, not a SaaS app.
2. Use PHP 8.2+ clean OOP architecture.
3. Use WordPress Coding Standards.
4. Never publish AI changes without approval by default.
5. Always create backup before publishing.
6. Always allow rollback.
7. Never overwrite Traveler booking, price, availability, or system fields.
8. Never store API keys in plain text.
9. Every REST route must have a permission callback.
10. Every admin action must verify nonce and capability.
11. All output must be escaped.
12. All input must be sanitized.
13. Use prepared SQL only.
14. Avoid long blocking requests.
15. Use WP-Cron/background jobs for heavy tasks.
16. Make the admin UI clean and easy to use.
17. Add enough tests to verify critical safety logic.
18. Keep the code modular and extendable.
19. Add clear documentation.
20. Deliver a working plugin zip.

---

## 31. Deliverables

Create and provide:

* Complete plugin source code
* Admin UI source and built assets
* Database installer/migrations
* REST API controllers
* SEO audit engine
* AI provider system
* Optimizer system
* Approval workflow
* Backup and rollback system
* Traveler integration
* Schema system
* GSC integration
* Reports system
* Automation system
* Tests
* README
* Installation guide
* Developer documentation
* Final plugin zip

End goal:

A production-ready WordPress plugin that brings the SEO Autopilot SaaS features directly into WordPress admin, with strong safety, approval, backup, rollback, AI SEO generation, Traveler support, and Google Search Console insights.

```
```
