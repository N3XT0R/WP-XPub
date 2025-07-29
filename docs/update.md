# 🔄 Automatic Updates

**XPub** supports seamless update delivery through its own plugin update manager, fully integrated into WordPress core
mechanisms.

## ✅ Features

- **GitHub-based Update Integration**  
  The plugin fetches release information directly from GitHub and notifies administrators when a new version is
  available. No need to rely on WordPress.org plugin repository.

- **Manual Update Check**  
  In addition to automatic checks, a **"Check for updates manually"** link appears below the plugin in the admin plugin
  list. This lets administrators trigger an update at any time.

- **Native WordPress Integration**  
  Updates show up just like official WordPress updates. The plugin details panel (`View details`) shows:
    - Current version
    - Required WordPress and PHP versions
    - Download link
    - Formatted changelog

- **Secure & Standards-Based**  
  Version comparison is handled via [`version_compare`](https://www.php.net/manual/en/function.version-compare.php),
  ensuring correct semver behavior.

- **Custom Update Source**  
  Uses a custom class `PluginUpdateManager`, decoupled from WordPress core.  
  You can override or extend this behavior if needed.

## 🛠 Technical Overview

The update system hooks into the following WordPress mechanisms:

| Hook / Action                           | Purpose                           |
|-----------------------------------------|-----------------------------------|
| `pre_set_site_transient_update_plugins` | Inject latest version metadata    |
| `plugins_api`                           | Populate plugin detail modal      |
| `plugin_action_links_{plugin}`          | Add "manual update" link          |
| `admin_init`                            | Trigger update check if requested |
| `admin_notices`                         | Show success message after check  |

> 📦 Updates are fetched from the GitHub releases of:  
> [`https://github.com/N3XT0R/WP-XPub`](https://github.com/N3XT0R/WP-XPub)
