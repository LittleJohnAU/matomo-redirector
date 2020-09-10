# matomo-redirector
This is for use with the Matomo [PHP tracking client](https://github.com/matomo-org/matomo-php-tracker) and the [Shortcode Tracker plugin](https://github.com/mgazdzik/plugin-ShortcodeTracker) and your short code redirection is on a different subdomain to where Matomo is installed and redirecting your short urls to the main domain.
i.e. Matomo is installed on **matomo.mydomain.com**, this redirector is installed on **s.mydomain.com** and the short code target is **mydomain.com**

NB: It will capture external links (i.e. someotherdomain.com) as events in Matomo. I use this just for short urls to my primary domain.

It also has an HTML template for outputting the errors. If you create your own template, just make sure you add `{{error}}` where you want it to go.

Edit the settings in **director.php** and **index.php**. On your target site i.e. **mydomain.com** you need to capture the visitor id and referrer by making sure the session name and cookie domain are the same and include at the top of every target page.
```
ini_set('session.cookie_domain', '.mydomain.com');
session_name("mydomain");
```
This is what the .htaccess file looks like:
```
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^([a-zA-Z0-9]+)/?$ index.php?c=$1 [L] 
</IfModule>
```
Check the ShortcodeTracker plugin file -> **ShortcodeTracker/ShortcodeTracker.php** for the following constant
```
const REDIRECT_EVENT_CATEGORY
```
If the value is `'shordcode'` change it to `'shortcode'` or the reports will not work

This is the tracking code I use on the primary domain, to connect the redirect with the page and track it in Matomo.
```
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
// if this is a site search add this. Modify the if statement and $_GET variable to your needs
if($sitesearch !== false){
   $matomoTracker->doTrackSiteSearch(urldecode($_GET['query']));
}
```

See it in action with one of my blog posts (The Beast Of The Flesh): https://p.inall.love/ab1d4f
