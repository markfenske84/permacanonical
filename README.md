# PermaCanonical

Forces canonical URLs to match WordPress permalinks exactly, overriding any SEO plugins like Yoast SEO. Supports pagination.

## Features

- Automatically removes canonical URLs set by other plugins
- Disables canonical URL filters from major SEO plugins
- Adds clean canonical URLs that match permalinks exactly
- Works with all page types (posts, pages, archives, etc.)
- Full pagination support for all archive types
- No configuration needed - works automatically

## Supported SEO Plugins

The plugin overrides canonical URLs from:
- Yoast SEO
- Rank Math
- All in One SEO
- SEOPress
- Any other plugins using standard WordPress hooks

## Installation

1. Upload the plugin files to `/wp-content/plugins/permacanonical/`
2. Activate the plugin through the 'Plugins' screen in WordPress
3. That's it! The plugin works automatically with no configuration needed

## Auto-Update Functionality

This plugin includes a built-in auto-updater that checks for new versions from this GitHub repository.

### How It Works

The plugin checks this GitHub repository for new releases and notifies WordPress sites when updates are available. Sites with the plugin installed will see update notifications in their WordPress admin dashboard, just like plugins from the WordPress.org repository.

### Setting Up Auto-Updates

1. **Configure GitHub Repository Info**

   Edit `permacanonical.php` and update these constants with your GitHub information:

   ```php
   define('PERMACANONICAL_GITHUB_USERNAME', 'your-github-username'); // Your GitHub username or organization
   define('PERMACANONICAL_GITHUB_REPO', 'your-repo-name'); // Your repository name
   ```

2. **Create a New Release**

   When you want to push an update:

   a. Update the version number in `permacanonical.php`:
   ```php
   * Version: 1.0.2
   ```
   
   b. Update the version constant:
   ```php
   define('PERMACANONICAL_VERSION', '1.0.2');
   ```
   
   c. Update `readme.txt`:
   ```
   Stable tag: 1.0.2
   ```
   
   d. Add changelog entries to `readme.txt`:
   ```
   = 1.0.2 =
   * Your changes here
   ```

   e. Commit and push your changes to GitHub

   f. Create a new release on GitHub:
      - Go to your repository on GitHub
      - Click "Releases" → "Create a new release"
      - Tag version: `v1.0.2` (must match version in plugin file)
      - Release title: `Version 1.0.2` (or any title)
      - Description: List your changes (this becomes the changelog)
      - Click "Publish release"

3. **Sites Will Auto-Detect Updates**

   Within 12 hours (or when WordPress checks for updates), sites with the plugin installed will see the update notification in their WordPress admin.

### Update Check Process

- WordPress checks for updates on admin pages and via wp-cron
- The plugin queries the GitHub API for the latest release
- Results are cached for 12 hours to reduce API calls
- If a newer version is found, WordPress shows an update notification
- Admins can click "Update Now" to install the new version

### GitHub API Rate Limits

The GitHub API allows 60 requests per hour for unauthenticated requests. Since the plugin caches results for 12 hours, this shouldn't be an issue for most sites. If you need higher limits, you can modify the updater to use a GitHub personal access token.

### Troubleshooting

**Updates not showing?**
- Verify the GitHub username and repo name in the plugin constants
- Make sure you've created a release (not just a tag) on GitHub
- Check that the release tag starts with 'v' (e.g., v1.0.2)
- Clear the update cache by deactivating and reactivating the plugin
- Check your site can access GitHub API: `https://api.github.com/repos/USERNAME/REPO/releases/latest`

**Download fails?**
- Ensure your GitHub repository is public
- Verify the release includes a source code zip file

## Development

### File Structure

```
permacanonical/
├── permacanonical.php    # Main plugin file
├── updater.php           # Auto-updater class
├── readme.txt            # WordPress.org style readme
└── README.md             # GitHub readme (this file)
```

### Releasing a New Version

1. Update version numbers in:
   - `permacanonical.php` (header and constant)
   - `readme.txt` (stable tag and changelog)

2. Test the plugin thoroughly

3. Commit and push to GitHub

4. Create a new release on GitHub with tag format: `vX.Y.Z`

5. Sites will automatically detect the update within 12 hours

## License

GPL v2 or later - https://www.gnu.org/licenses/gpl-2.0.html

## Support

For issues and feature requests, please use the GitHub issue tracker or contact Webfor Agency at https://webfor.com

## Changelog

### 1.0.5
- Version bump for stable release
- Ensured all version references are synchronized

### 1.0.4
- Replaced custom updater with plugin-update-checker library
- Improved compatibility and reliability of automatic updates
- Better integration with WordPress plugin update system

### 1.0.3
- Added 'Check for Updates' link in plugin row meta
- Improved update checking user experience
- Minor code improvements

### 1.0.2
- Added automatic update functionality via GitHub releases
- Plugin now checks for updates from GitHub repository
- Sites will receive update notifications automatically

### 1.0.1
- Added full pagination support for all archive types
- Fixed canonical URLs for paginated blog pages, categories, tags, etc.
- Improved handling of paged query variables

### 1.0.0
- Initial release
- Supports all major SEO plugins
- Automatic canonical URL enforcement
- No configuration required

