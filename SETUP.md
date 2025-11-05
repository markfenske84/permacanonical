# Quick Setup Guide for Auto-Updates

Follow these steps to enable automatic updates for sites with this plugin installed.

## Step 1: Create a GitHub Repository

1. Go to GitHub and create a new repository (or use an existing one)
2. Name it something like `permacanonical` (or your preferred name)
3. Make sure it's set to **Public** (private repos require authentication)

## Step 2: Configure the Plugin

Edit `permacanonical.php` and update these two lines (around line 25-26):

```php
define('PERMACANONICAL_GITHUB_USERNAME', 'your-github-username'); // Your actual GitHub username
define('PERMACANONICAL_GITHUB_REPO', 'permacanonical'); // Your actual repository name
```

Example:
```php
define('PERMACANONICAL_GITHUB_USERNAME', 'webforagency');
define('PERMACANONICAL_GITHUB_REPO', 'permacanonical');
```

## Step 3: Push to GitHub

```bash
cd /path/to/permacanonical
git init
git add .
git commit -m "Initial commit with auto-updater"
git branch -M main
git remote add origin https://github.com/YOUR-USERNAME/YOUR-REPO.git
git push -u origin main
```

## Step 4: Create Your First Release

1. Go to your repository on GitHub
2. Click on "Releases" in the right sidebar
3. Click "Create a new release"
4. Fill in:
   - **Tag**: `v1.0.1` (must match the version in the plugin)
   - **Title**: `Version 1.0.1`
   - **Description**: 
     ```
     Initial release with auto-updater
     
     - Automatic canonical URL enforcement
     - Full pagination support
     - Compatible with all major SEO plugins
     ```
5. Click "Publish release"

## Step 5: Test the Updater

1. Install the plugin on a test WordPress site
2. In WordPress admin, go to Dashboard → Updates
3. Click "Check Again" to force an update check
4. The plugin should show as up-to-date (version 1.0.1)

## Pushing Future Updates

When you want to release a new version:

1. **Update version numbers:**
   - In `permacanonical.php` header: `* Version: 1.0.2`
   - In `permacanonical.php` constant: `define('PERMACANONICAL_VERSION', '1.0.2');`
   - In `readme.txt`: `Stable tag: 1.0.2`

2. **Add changelog** to `readme.txt`:
   ```
   = 1.0.2 =
   * Fixed bug with pagination
   * Added new feature
   ```

3. **Commit and push to GitHub:**
   ```bash
   git add .
   git commit -m "Version 1.0.2 - Bug fixes and improvements"
   git push
   ```

4. **Create a new release on GitHub:**
   - Tag: `v1.0.2`
   - Title: `Version 1.0.2`
   - Description: Copy your changelog

5. **Sites will see the update** within 12 hours (or when they check for updates)

## Troubleshooting

**"No updates available" but I created a release**
- Make sure the tag format is correct: `v1.0.2` (with lowercase 'v')
- Verify the repository is public
- Check the constants in the plugin match your GitHub username/repo
- Try deactivating and reactivating the plugin to clear cache

**Sites can't download the update**
- Ensure your repository is public
- Check that GitHub created the source code zip automatically
- Verify the GitHub API is accessible: visit `https://api.github.com/repos/USERNAME/REPO/releases/latest`

**Rate limit errors**
- Each site caches results for 12 hours to avoid rate limits
- GitHub allows 60 requests/hour for unauthenticated requests
- This should be plenty for normal usage

## Questions?

See the full README.md for more details, or contact Webfor Agency at https://webfor.com

