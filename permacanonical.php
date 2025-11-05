<?php
/**
 * Plugin Name: PermaCanonical
 * Plugin URI: https://webfor.com
 * Description: Forces canonical URL to match the WordPress permalink exactly, overriding any SEO plugins like Yoast SEO. Supports pagination.
 * Version: 1.0.1
 * Author: Webfor Agency
 * Author URI: https://webfor.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: permacanonical
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class PermaCanonical {
    
    /**
     * Initialize the plugin
     */
    public function __construct() {
        // Remove canonical URLs from other plugins with high priority
        add_action('wp_head', array($this, 'remove_other_canonicals'), 1);
        
        // Add our canonical URL with very high priority (after removals)
        add_action('wp_head', array($this, 'add_permalink_canonical'), 99);
        
        // Remove Yoast SEO canonical specifically
        add_filter('wpseo_canonical', '__return_false');
        
        // Remove Rank Math canonical
        add_filter('rank_math/frontend/canonical', '__return_false');
        
        // Remove All in One SEO canonical
        add_filter('aioseop_canonical_url', '__return_false');
        
        // Remove SEOPress canonical
        add_filter('seopress_titles_canonical', '__return_false');
    }
    
    /**
     * Remove canonical tags added by other plugins
     */
    public function remove_other_canonicals() {
        // Remove Yoast SEO frontend
        if (class_exists('WPSEO_Frontend')) {
            remove_action('wpseo_head', array(WPSEO_Frontend::get_instance(), 'canonical'), 20);
        }
        
        // Remove default WordPress rel_canonical
        remove_action('wp_head', 'rel_canonical');
        
        // Remove additional canonical actions that might be added by themes or plugins
        remove_action('wp_head', 'rel_canonical', 10);
        
        // Buffer output to strip any canonical tags that slip through
        ob_start(array($this, 'strip_canonical_tags'));
    }
    
    /**
     * Strip any existing canonical tags from the buffered output
     * 
     * @param string $buffer The buffered HTML content
     * @return string The modified HTML content
     */
    public function strip_canonical_tags($buffer) {
        // Remove any canonical link tags
        $buffer = preg_replace('/<link[^>]+rel=["\']canonical["\'][^>]*>/i', '', $buffer);
        return $buffer;
    }
    
    /**
     * Add canonical URL that matches the permalink exactly
     */
    public function add_permalink_canonical() {
        // End the output buffer if it was started
        if (ob_get_level() > 0) {
            ob_end_flush();
        }
        
        $canonical_url = '';
        
        if (is_singular()) {
            // For single posts, pages, and custom post types
            $canonical_url = get_permalink();
        } elseif (is_front_page()) {
            // For the front page
            $canonical_url = home_url('/');
        } elseif (is_home()) {
            // For the blog page
            $canonical_url = get_permalink(get_option('page_for_posts'));
        } elseif (is_category()) {
            // For category archives
            $canonical_url = get_category_link(get_queried_object_id());
        } elseif (is_tag()) {
            // For tag archives
            $canonical_url = get_tag_link(get_queried_object_id());
        } elseif (is_tax()) {
            // For custom taxonomy archives
            $term = get_queried_object();
            $canonical_url = get_term_link($term);
        } elseif (is_author()) {
            // For author archives
            $canonical_url = get_author_posts_url(get_queried_object_id());
        } elseif (is_date()) {
            // For date archives
            if (is_day()) {
                $canonical_url = get_day_link(get_query_var('year'), get_query_var('monthnum'), get_query_var('day'));
            } elseif (is_month()) {
                $canonical_url = get_month_link(get_query_var('year'), get_query_var('monthnum'));
            } elseif (is_year()) {
                $canonical_url = get_year_link(get_query_var('year'));
            }
        } elseif (is_post_type_archive()) {
            // For custom post type archives
            $canonical_url = get_post_type_archive_link(get_query_var('post_type'));
        } elseif (is_search()) {
            // For search results
            $canonical_url = get_search_link();
        }
        
        // Handle pagination for all archive types (blog, category, tag, author, date, post type, search)
        if (is_paged()) {
            $paged = get_query_var('paged');
            if (!$paged) {
                $paged = get_query_var('page');
            }
            
            if ($paged > 1) {
                // Use get_pagenum_link to get the proper paginated URL
                $canonical_url = get_pagenum_link($paged);
            }
        }
        
        // Remove any query parameters to keep it clean (optional - comment out if you want to keep query params)
        if ($canonical_url && !is_search()) {
            $canonical_url = strtok($canonical_url, '?');
        }
        
        // Ensure it's a valid URL
        if (!empty($canonical_url) && filter_var($canonical_url, FILTER_VALIDATE_URL)) {
            echo '<link rel="canonical" href="' . esc_url($canonical_url) . '" />' . "\n";
        }
    }
}

// Initialize the plugin
new PermaCanonical();


