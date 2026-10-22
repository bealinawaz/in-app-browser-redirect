=== In-App Browser Redirect – Open Social Media Links in Chrome or Safari ===
Contributors: imalinawaz
Donate link: https://www.buymeacoffee.com/bealinawaz
Tags: in-app browser, facebook browser, instagram browser, external browser, redirect
Requires at least: 6.2
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 2.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Open links from Facebook, Instagram, TikTok, and Messenger in the visitor's real browser: Chrome on Android, and a clear Safari hint on iPhone.

== Description ==

When someone taps your link in Facebook, Instagram, Messenger, TikTok, LinkedIn, or Snapchat, the page opens in the app's own **in-app browser**. That built-in browser is where things quietly go wrong: visitors are logged out, social logins and payment pop-ups fail, cookies and carts disappear when the app closes, and analytics and ad tracking undercount conversions.

In-App Browser Redirect helps visitors **open your site in their external browser**. On Android it sends them straight to Chrome. On iPhone, where no website is allowed to open Safari, it shows a short, friendly hint with a "Copy link" button.

**How it works**

* **Facebook in-app browser on Android:** the current page, with its full address, query string, and anchor, opens in Chrome through an Android intent link. Choose between an automatic redirect or a banner with an "Open in Chrome" button.
* **Instagram in-app browser redirect on iPhone:** a small banner at the bottom of the page explains how to open the page in Safari ("tap ••• then Open in browser") and offers a "Copy link" button. The page stays fully usable.
* **No redirect loops:** the automatic redirect runs once per page and tab. If Chrome is not installed, the page simply stays open with the banner.

**Features**

* **Detects the common in-app browsers:** Facebook, Messenger, Instagram, LinkedIn, TikTok, Snapchat, LINE, and X (Twitter) where possible. Choose which ones to act on.
* **Cache friendly:** detection runs in the visitor's browser, not on the server, so it works with page caching and CDNs.
* **Tiny and fast:** one small deferred script with no jQuery. The banner styles load only when the banner is shown.
* **Accessible:** real buttons, a labelled region, keyboard support (Escape closes the banner), and no animation for visitors who prefer reduced motion.
* **Your words:** edit the Android and iPhone banner texts.
* **Exclusions:** skip specific posts and pages by ID or by path, such as a landing page built for in-app traffic.
* **Easy testing:** add `?iabr_test=ios` or `?iabr_test=android` to any page address to preview the banner in a desktop browser.

**Limitations**

* iOS does not let websites open Safari from an in-app browser. On iPhone and iPad the plugin can only show instructions and a "Copy link" button.
* Apps identify their in-app browsers in the user agent, and they change it from time to time. Detection is updated as apps change, but no list is ever complete.
* X (Twitter) on iPhone opens links in a Safari view that identifies itself as Safari, so it cannot be detected there.

**Custom development**

Need landing pages that convert social media traffic, a checkout that works inside in-app browsers, or conversion tracking that survives them? The plugin is built and maintained by Ali Nawaz, a WordPress and WooCommerce developer. [Get in touch for custom work](https://bealinawaz.com/contact-me/?utm_source=wordpress.org&utm_medium=plugin-readme&utm_campaign=in-app-browser-redirect).

== Installation ==

1. Install and activate the plugin. It works right away with the default settings.
2. Go to **Settings → In-App Browser** to choose the apps, the Android behavior, and the banner texts.
3. Open `https://your-site.com/?iabr_test=ios` to preview the iPhone banner.
4. For a real test, send yourself a link in a Facebook or Instagram message and open it on your phone.

== Frequently Asked Questions ==

= Why should I take visitors out of the Facebook in-app browser? =

In-app browsers keep their own cookies and storage, block some pop-ups, and close when the visitor goes back to the app. That breaks logins, PayPal and other payment windows, saved carts, and conversion tracking. A real browser keeps the visitor signed in and remembers their cart.

= Can the plugin open Safari automatically on iPhone? =

No. Apple does not allow websites to open Safari from inside an app. The plugin shows a short hint and a "Copy link" button instead, and the page stays usable.

= What happens on Android if Chrome is not installed? =

The intent link includes a fallback address, so the page reloads in the in-app browser. The plugin remembers that it already tried and shows the banner instead of redirecting again.

= Does it work with caching plugins and CDNs? =

Yes. The server sends the same page to everyone and the detection runs in the browser.

= Will it affect search engines or normal visitors? =

No. Search engine crawlers and regular browsers such as Chrome, Safari, Firefox, and Edge are not matched, so they never see the banner or a redirect.

= How do I stop the redirect on one page? =

Add the page ID or its path under Exclusions in **Settings → In-App Browser**. Developers can also use the `iabr_is_excluded` filter.

= Does it collect any data? =

No. Nothing is stored or sent anywhere. The only browser storage used is session storage, to remember that the banner was closed and that a redirect was already tried.

== Changelog ==

= 2.0.0 =
* Complete rewrite, renamed from Facebook Link Redirect to In-App Browser Redirect.
* New: detects Facebook, Messenger, Instagram, LinkedIn, TikTok, Snapchat, LINE, and X (Twitter) in-app browsers, each with its own setting.
* New: Android redirect keeps HTTPS and the full page address, including archive pages, query strings, and anchors, and includes a fallback address.
* New: protection against redirect loops.
* New: accessible iPhone banner with instructions and a "Copy link" button.
* New: settings page with Android behavior, banner texts, and exclusions by ID or path, plus a test mode.
* Changed: detection runs in an enqueued, deferred script instead of inline code, so it works with page caching.
* Fixed: links always opened over HTTP and used the wrong address on archive pages.
* Removed: global constants that could clash with other plugins.

= 1.0 =
* Initial release.

== Upgrade Notice ==

= 2.0.0 =
Complete rewrite. Now covers Instagram, TikTok, and more apps, keeps HTTPS, avoids redirect loops, and shows a Safari hint on iPhone.
