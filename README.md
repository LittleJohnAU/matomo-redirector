This is for use with the Matomo [PHP tracking client](https://github.com/matomo-org/matomo-php-tracker) and the [Shortcode Tracker plugin](https://github.com/mgazdzik/plugin-ShortcodeTracker) and your short code redirection is on a different subdomain to where Matomo is installed and neither the root domain or redirection subdomain have access to matomo directly.

NB: The subdomain should be one letter to be as short as possible i.e. `https://s.mydomain.com`

![Folder structure](https://res.cloudinary.com/league-of-true-love/image/upload/v1599754872/folders.jpg)

It also has an [HTML template](template.html) for outputting the errors. If you create your own template, just make sure you add `{{error}}` where you want it to go.

## INSTALLING:

**Be sure to add the subdomain you use for redirecting to the website measurables in Matomo** `Administration -> Websites -> Manage -> edit`

![Measurables](https://res.cloudinary.com/league-of-true-love/image/upload/v1599754877/measurable.jpg)

Edit the settings in [director.php](director.php) and [index.php](index.php).

On your target site i.e. `https://mydomain.com` you need to capture the visitor id and referrer by making sure the session name and cookie domain are the same on both.
```php
ini_set('session.cookie_domain', '.mydomain.com');
session_name("mydomain");
```
Rename `htaccess.txt` to `.htaccess`

Check the ShortcodeTracker plugin file -> **ShortcodeTracker/ShortcodeTracker.php** for the following constant:
```php
const REDIRECT_EVENT_CATEGORY
```
If the value is `'shordcode'` change it to `'shortcode'` or the reports will not work. NOTE: If you have been using the Shortcode Tracker plugin prior to this and the category is mispelled, changing it will cause you to lose that data. If this is an issue, then change the spelling in [index.php](index.php) `$matomoTracker->doTrackEvent('shortcode', 'redirect', $shortCode);` to `$matomoTracker->doTrackEvent('shordcode', 'redirect', $shortCode);`

This is the tracking code to use on the tracked domain `https://mydomain.com`, to connect the redirect with the page and track them together. Be sure to set the site ID correctly and enter your API authentication token (found in your personal settings).
```php
const THIS_URL = 'https://mydomain.com';
const PAGE_TITLE = 'My Home Page';
$matomoSiteId = 1;  // Site ID
$matomoUrl = "https://matomo.mydomain.com"; // Your matomo URL
$matomoToken = "";  // Your authentication token

// Load object
require_once("MatomoTracker.php");
$matomoTracker = new MatomoTracker((int)$matomoSiteId, $matomoUrl);
    
// Set authentication token
$matomoTracker->setTokenAuth($matomoToken);
$matomoTracker->setUrl(THIS_URL);
if(isset($_SESSION['matvisitor'])){
    $matomoTracker->setVisitorId($_SESSION['matvisitor']);
    $matomoTracker->setUrlReferrer($_SESSION['matreferrer']);
    unset($_SESSION['matvisitor']);
    unset($_SESSION['matreferrer']);
}
$matomoTracker->doTrackPageView(PAGE_TITLE);
/* If this is a site search page add this. Modify the $_GET variable to your query parameter

$matomoTracker->doTrackSiteSearch(urldecode($_GET['query']));

*/
```
