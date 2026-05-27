=== SEO Autopilot Lite ===
Contributors: seo-autopilot
Tags: seo, meta tags, schema, open graph, twitter cards, focus keyword, ai seo, traveler theme
Requires at least: 6.4
Tested up to: 6.7
Requires PHP: 8.2
Stable tag: 1.0.0
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Lightweight AI-powered WordPress SEO plugin with meta tags, schema, SEO analysis, focus keywords, and Traveler theme support.

== Description ==

**SEO Autopilot Lite** is a lightweight WordPress SEO plugin built like Yoast SEO and Rank Math. It provides essential SEO features without the bloat of a SaaS dashboard.

= Features =

* **SEO Meta Tags**: Set custom SEO title, meta description, and focus keyword for each post/page
* **Google Preview**: See how your page will appear in Google search results
* **SEO Analysis**: Automatic analysis of content quality with actionable recommendations
* **SEO Score**: Get an overall SEO score (0-100) for each piece of content
* **Schema JSON-LD**: Automatic schema markup for Articles, FAQ, LocalBusiness, and more
* **Open Graph Tags**: Optimize how your content appears on Facebook
* **Twitter Cards**: Optimize how your content appears on Twitter
* **Canonical URLs**: Prevent duplicate content issues
* **Focus Keyword**: Track keyword usage and density
* **AI Suggestions** (optional): Generate SEO titles, descriptions, and schema using AI
* **Traveler Theme Support**: Special support for Traveler booking theme CPTs
* **Yoast/Rank Math/AIOSEO Compatibility**: Works alongside or replaces other SEO plugins

= What Makes It Different =

* No SaaS dashboard - everything runs inside WordPress
* No React admin panel - uses native WordPress UI
* No custom database tables - uses WordPress options and post meta
* Lightweight and fast - only loads what you need
* Optional AI - works perfectly without any external API

= SEO Analysis Rules =

The plugin analyzes your content for:

* SEO Title length and keyword usage
* Meta Description length and keyword usage
* Focus keyword presence and density
* Content length (minimum 300 words recommended)
* Heading structure (H1, H2, etc.)
* Image alt text
* Internal linking
* Readability (paragraph length, sentence length)
* Canonical URL validity

= Schema Types Supported =

* Article / BlogPosting
* WebPage
* FAQPage
* BreadcrumbList
* LocalBusiness
* Tour / TouristTrip (Traveler theme)
* TouristAttraction (Traveler theme)
* Hotel (Traveler theme)

= AI Providers Supported =

* OpenAI (GPT-4, GPT-3.5)
* Claude (Anthropic)
* Gemini (Google)
* OpenRouter (multi-provider)

= Traveler Theme Integration =

If you use the Traveler booking theme, SEO Autopilot Lite can:

* Add SEO meta boxes to Traveler CPTs (Tours, Hotels, Activities, etc.)
* Generate Tour/TouristTrip/Hotel schema automatically
* Protect booking fields from accidental modification
* Preserve price and availability data

== Installation ==

1. Upload the `seo-autopilot-lite` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to SEO Autopilot > Settings to configure
4. Edit any post or page to see the SEO meta box

== Frequently Asked Questions ==

= Does this work with Yoast SEO? =

Yes! You can use SEO Autopilot Lite alongside Yoast SEO. The plugin has settings to disable frontend output if another SEO plugin is detected.

= Do I need an API key for AI features? =

No, AI features are optional. The plugin works fully without any AI. If you want AI suggestions, you'll need an API key from OpenAI, Anthropic, Google, or OpenRouter.

= Will this slow down my site? =

No, the plugin is designed to be lightweight. It only loads assets on pages where needed and doesn't run heavy background processes.

= Does it support custom post types? =

Yes! You can select which post types to support in the settings page.

= Is it compatible with Traveler theme? =

Yes, there's special support for Traveler theme CPTs including Tours, Hotels, Activities, Rentals, Cars, and Locations.

== Changelog ==

= 1.0.0 =
* Initial release
* SEO meta tags (title, description, canonical)
* Focus keyword tracking
* SEO analysis with 9 rules
* SEO score calculation
* Schema JSON-LD output
* Open Graph and Twitter Card tags
* Google snippet preview
* AI suggestions (optional)
* Traveler theme support
* Yoast/Rank Math/AIOSEO compatibility

== Upgrade Notice ==

= 1.0.0 =
Initial release of SEO Autopilot Lite.
