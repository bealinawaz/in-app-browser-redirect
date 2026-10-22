# In-App Browser Redirect

Get visitors out of the Facebook, Instagram, Messenger, TikTok, LinkedIn, and Snapchat in-app browsers. On Android the plugin opens the current page in Chrome with an intent link; on iPhone, where websites cannot open Safari, it shows an accessible banner with instructions and a "Copy link" button. In-app browsers break logins, payments, cookies, and tracking, so this keeps social traffic converting.

![Tested up to WordPress 7.1](https://img.shields.io/badge/WordPress-7.1-blue)
![PHP 7.4+](https://img.shields.io/badge/PHP-7.4%2B-777bb4)
![License GPLv2 or later](https://img.shields.io/badge/license-GPLv2%2B-green)

## Features

- User agent detection in the browser (cache and CDN friendly) for Facebook (`FBAN`, `FBAV`, `FB_IAB`), Messenger, Instagram, LinkedIn, TikTok (`musical_ly`, `BytedanceWebview`), Snapchat, LINE, and X (Twitter) where detectable.
- Android: `intent://host/path?query#hash#Intent;scheme=https;package=com.android.chrome;S.browser_fallback_url=…;end`, automatic or behind a button.
- Loop protection: one automatic attempt per page and tab (session storage); without storage it never auto-redirects.
- iOS: dismissible bottom banner with configurable text and a "Copy link" button. Respects `prefers-reduced-motion`.
- Exclusions by post ID or path, plus the `iabr_is_excluded` filter.
- About 5 KB of vanilla JavaScript, deferred, no jQuery. Banner CSS loads only when the banner is shown.
- Test mode: `?iabr_test=ios` or `?iabr_test=android` shows the banner in any browser without redirecting.

## Setup

1. Activate the plugin. Defaults: all apps, automatic Android redirect, iPhone banner on.
2. Adjust everything under **Settings → In-App Browser**.

## Limitations

- iOS cannot be forced to open Safari from an in-app browser.
- Apps change their user agents; detection is best effort.
- X (Twitter) on iOS uses a Safari view that reports itself as Safari.

## Hooks

```php
// Never run on WooCommerce checkout pages.
add_filter( 'iabr_is_excluded', function ( $excluded ) {
	return $excluded || ( function_exists( 'is_checkout' ) && is_checkout() );
} );
```

## Project structure

```
in-app-browser-redirect.php       Plugin header and bootstrap
includes/class-iabr-settings.php  Settings defaults and sanitizing
includes/class-iabr-frontend.php  Enqueues the script with its settings, exclusions
includes/class-iabr-admin.php     Settings → In-App Browser screen and plugin links
assets/redirect.js                Detection, Android redirect, iOS banner
assets/banner.css                 Banner styles (loaded on demand)
uninstall.php                     Removes the settings
```

## Custom development

Need landing pages that convert social traffic, checkout fixes for in-app browsers, or tracking that survives them? [Hire Ali Nawaz for custom WordPress work](https://bealinawaz.com/contact-me/?utm_source=github&utm_medium=readme&utm_campaign=in-app-browser-redirect).

## License

GPLv2 or later. See [LICENSE](LICENSE).
