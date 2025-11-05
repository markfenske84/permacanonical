=== PermaCanonical ===
Contributors: webforagency
Tags: canonical, seo, yoast, permalink, canonical url
Requires at least: 5.0
Tested up to: 6.8
Requires PHP: 7.2
Stable tag: 1.0.3
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Forces canonical URLs to match WordPress permalinks exactly, overriding any SEO plugins.

== Description ==

PermaCanonical ensures that the canonical URL in your page's head section always matches the actual WordPress permalink, preventing SEO plugins like Yoast SEO from overriding it with custom canonical URLs.

**Why Use This Plugin?**

SEO plugins often allow users to set custom canonical URLs through their advanced settings. While this can be useful in some cases, it can also lead to:

* Canonical URLs that don't match the actual page URL
* Inconsistencies between permalinks and canonical tags
* Potential SEO issues from misconfigured canonical URLs

**Key Features:**

* Automatically removes canonical URLs set by other plugins
* Disables canonical URL filters from major SEO plugins
* Adds clean canonical URLs that match permalinks exactly
* Works with all page types (posts, pages, archives, etc.)
* Full pagination support for all archive types
* No configuration needed - works automatically

**Supported SEO Plugins:**

The plugin overrides canonical URLs from:

* Yoast SEO
* Rank Math
* All in One SEO
* SEOPress
* Any other plugins using standard WordPress hooks

**How It Works:**

The plugin uses multiple strategies to ensure canonical URLs match permalinks:

* Hooks into wp_head with high priority to remove other canonical tags
* Disables canonical URL filters from major SEO plugins
* Uses output buffering to strip any canonical tags that slip through
* Generates clean canonical URLs based on WordPress permalink structure

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/permacanonical` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. That's it! The plugin works automatically with no configuration needed.

== Frequently Asked Questions ==

= Will this create duplicate canonical links? =

No. The plugin actively removes canonical URLs from other plugins before adding its own, ensuring only one canonical tag exists in the page head.

= Does this work with all SEO plugins? =

Yes. The plugin removes canonical URLs using multiple methods including specific filters for major SEO plugins and output buffering to catch any that slip through.

= Do I need to configure anything? =

No. Once activated, the plugin works automatically. There are no settings to configure.

= What page types are supported? =

All WordPress page types including:
* Single posts, pages, and custom post types
* Front page and blog page
* Category, tag, and custom taxonomy archives
* Author archives
* Date archives (year, month, day)
* Custom post type archives
* Search results

= Will this affect my SEO? =

The plugin ensures your canonical URLs match your actual permalinks, which is generally considered best practice for SEO. It prevents potential issues from misconfigured canonical URLs.

= Does this work with paginated pages? =

Yes. The plugin fully supports pagination for all archive types including blog pages, categories, tags, author archives, date archives, custom post types, and search results. When you visit /blog/page/4/, the canonical will correctly show /blog/page/4/.

= What about AJAX pagination? =

The canonical URL is rendered server-side when the page initially loads. If your theme uses AJAX to load paginated content and updates the URL with JavaScript (History API), the canonical in the page source will reflect whatever URL was initially requested from the server.

== Changelog ==

= 1.0.3 =
* Added 'Check for Updates' link in plugin row meta
* Improved update checking user experience
* Minor code improvements

= 1.0.2 =
* Added automatic update functionality via GitHub releases
* Plugin now checks for updates from GitHub repository
* Sites will receive update notifications automatically

= 1.0.1 =
* Added full pagination support for all archive types
* Fixed canonical URLs for paginated blog pages, categories, tags, etc.
* Improved handling of paged query variables

= 1.0.0 =
* Initial release
* Supports all major SEO plugins
* Automatic canonical URL enforcement
* No configuration required

== Upgrade Notice ==

= 1.0.1 =
Added pagination support. Now correctly handles URLs like /blog/page/4/.

= 1.0.0 =
Initial release of PermaCanonical.

