<?php
/**
 * Plugin Updater Class
 * Handles automatic updates from GitHub repository
 */

if (!defined('ABSPATH')) {
    exit;
}

class PermaCanonical_Updater {
    
    private $plugin_slug;
    private $plugin_basename;
    private $github_username;
    private $github_repo;
    private $version;
    private $cache_key;
    private $cache_allowed = true;
    
    /**
     * Constructor
     * 
     * @param string $plugin_basename The plugin basename (plugin-folder/plugin-file.php)
     * @param string $github_username GitHub username or organization
     * @param string $github_repo GitHub repository name
     * @param string $version Current plugin version
     */
    public function __construct($plugin_basename, $github_username, $github_repo, $version) {
        $this->plugin_basename = $plugin_basename;
        $this->plugin_slug = dirname($plugin_basename);
        $this->github_username = $github_username;
        $this->github_repo = $github_repo;
        $this->version = $version;
        $this->cache_key = 'permacanonical_updater_' . md5($this->plugin_basename);
        
        // Hook into WordPress update checks
        add_filter('plugins_api', array($this, 'plugin_info'), 20, 3);
        add_filter('site_transient_update_plugins', array($this, 'check_update'));
        add_filter('upgrader_post_install', array($this, 'after_install'), 10, 3);
        
        // Clear cache when plugin is activated/deactivated
        add_action('upgrader_process_complete', array($this, 'purge_cache'), 10, 2);
    }
    
    /**
     * Get information from GitHub repository
     * 
     * @return object|bool GitHub release information or false on failure
     */
    private function get_repository_info() {
        if (!$this->cache_allowed) {
            return $this->fetch_repository_info();
        }
        
        // Try to get cached data
        $cache_data = get_transient($this->cache_key);
        
        if ($cache_data !== false) {
            return $cache_data;
        }
        
        // Fetch fresh data
        $remote_info = $this->fetch_repository_info();
        
        if ($remote_info) {
            // Cache for 12 hours
            set_transient($this->cache_key, $remote_info, 12 * HOUR_IN_SECONDS);
        }
        
        return $remote_info;
    }
    
    /**
     * Fetch repository information from GitHub API
     * 
     * @return object|bool Repository information or false on failure
     */
    private function fetch_repository_info() {
        $api_url = sprintf(
            'https://api.github.com/repos/%s/%s/releases/latest',
            $this->github_username,
            $this->github_repo
        );
        
        $response = wp_remote_get($api_url, array(
            'timeout' => 10,
            'headers' => array(
                'Accept' => 'application/vnd.github.v3+json',
            ),
        ));
        
        if (is_wp_error($response)) {
            return false;
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body);
        
        if (empty($data)) {
            return false;
        }
        
        return $data;
    }
    
    /**
     * Check for plugin updates
     * 
     * @param object $transient The update_plugins transient
     * @return object Modified transient
     */
    public function check_update($transient) {
        if (empty($transient->checked)) {
            return $transient;
        }
        
        $remote_info = $this->get_repository_info();
        
        if (!$remote_info || !isset($remote_info->tag_name)) {
            return $transient;
        }
        
        // Remove 'v' prefix if present
        $remote_version = ltrim($remote_info->tag_name, 'v');
        
        // Compare versions
        if (version_compare($this->version, $remote_version, '<')) {
            $plugin_data = array(
                'slug' => $this->plugin_slug,
                'plugin' => $this->plugin_basename,
                'new_version' => $remote_version,
                'url' => sprintf(
                    'https://github.com/%s/%s',
                    $this->github_username,
                    $this->github_repo
                ),
                'package' => $this->get_download_url($remote_info),
                'tested' => '6.8',
                'requires_php' => '7.2',
                'compatibility' => new stdClass(),
            );
            
            $transient->response[$this->plugin_basename] = (object) $plugin_data;
        }
        
        return $transient;
    }
    
    /**
     * Get download URL from release info
     * 
     * @param object $remote_info Release information from GitHub
     * @return string Download URL
     */
    private function get_download_url($remote_info) {
        // Check if there's a zipball_url
        if (isset($remote_info->zipball_url)) {
            return $remote_info->zipball_url;
        }
        
        // Fallback to constructed URL
        return sprintf(
            'https://github.com/%s/%s/archive/refs/tags/%s.zip',
            $this->github_username,
            $this->github_repo,
            $remote_info->tag_name
        );
    }
    
    /**
     * Provide plugin information for the "View details" link
     * 
     * @param false|object|array $result The result object or array
     * @param string $action The type of information being requested
     * @param object $args Plugin API arguments
     * @return false|object Modified result
     */
    public function plugin_info($result, $action, $args) {
        if ($action !== 'plugin_information') {
            return $result;
        }
        
        if (empty($args->slug) || $args->slug !== $this->plugin_slug) {
            return $result;
        }
        
        $remote_info = $this->get_repository_info();
        
        if (!$remote_info) {
            return $result;
        }
        
        $remote_version = ltrim($remote_info->tag_name, 'v');
        
        $plugin_info = array(
            'name' => 'PermaCanonical',
            'slug' => $this->plugin_slug,
            'version' => $remote_version,
            'author' => '<a href="https://webfor.com">Webfor Agency</a>',
            'homepage' => sprintf(
                'https://github.com/%s/%s',
                $this->github_username,
                $this->github_repo
            ),
            'requires' => '5.0',
            'tested' => '6.8',
            'requires_php' => '7.2',
            'download_link' => $this->get_download_url($remote_info),
            'sections' => array(
                'description' => 'Forces canonical URLs to match WordPress permalinks exactly, overriding any SEO plugins.',
                'changelog' => $this->parse_changelog($remote_info),
            ),
            'banners' => array(),
        );
        
        return (object) $plugin_info;
    }
    
    /**
     * Parse changelog from release notes
     * 
     * @param object $remote_info Release information
     * @return string Formatted changelog
     */
    private function parse_changelog($remote_info) {
        if (empty($remote_info->body)) {
            return 'No changelog available.';
        }
        
        // Convert markdown to HTML (basic conversion)
        $changelog = $remote_info->body;
        $changelog = wp_kses_post($changelog);
        $changelog = wpautop($changelog);
        
        return $changelog;
    }
    
    /**
     * Perform actions after plugin installation
     * 
     * @param bool $response Installation response
     * @param array $hook_extra Extra arguments passed to hooked filters
     * @param array $result Installation result
     * @return array Modified result
     */
    public function after_install($response, $hook_extra, $result) {
        global $wp_filesystem;
        
        // Make sure we're dealing with our plugin
        if (!isset($hook_extra['plugin']) || $hook_extra['plugin'] !== $this->plugin_basename) {
            return $result;
        }
        
        // Get the installation directory
        $install_directory = plugin_dir_path(WP_PLUGIN_DIR . '/' . $this->plugin_basename);
        
        // GitHub downloads extract to a folder with the repo name and commit hash
        // We need to move the contents to the correct plugin folder
        $wp_filesystem->move($result['destination'], $install_directory);
        $result['destination'] = $install_directory;
        
        // Activate the plugin if it was previously active
        if (is_plugin_active($this->plugin_basename)) {
            activate_plugin($this->plugin_basename);
        }
        
        return $result;
    }
    
    /**
     * Purge update cache
     * 
     * @param object $upgrader_object Upgrader object
     * @param array $options Update options
     */
    public function purge_cache($upgrader_object, $options) {
        if ($options['action'] === 'update' && $options['type'] === 'plugin') {
            delete_transient($this->cache_key);
        }
    }
}

