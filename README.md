# matomo-redirector
This is for use with the Matomo [PHP tracking client](https://github.com/matomo-org/matomo-php-tracker) and the [Shortcode Tracker plugin](https://github.com/mgazdzik/plugin-ShortcodeTracker) and your short code redirection is on a different subdomain to where Matomo is installed and redirecting your short urls to the main domain.
i.e. Matomo is installed on **matomo.mydomain.com**, this redirector is installed on **s.mydomain.com** and the short code target is **mydomain.com**

NB: It will capture external links (i.e. to google) as events in Matomo. I use this just for short urls to my primary domain.

It also has an HTML template for outputting the errors. If you create your own template, just make sure you add `{{error}}` where you want it to go.

Edit the settings in **director.php**. On your target site i.e. **mydomain.com** you need to capture the visitor id and referrer by making sure the session name and cookie domain are the same and include at the top of every target page.
```
ini_set('session.cookie_domain', '.mydomain.com');
session_name("mydomain");
```
This is the tracking code I use on the primary domain, to connect the redirect with the page and track it Matomo.
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
    unset($_SESSION['pinvisitor']);
    unset($_SESSION['pinreferrer']);
}
$matomoTracker->doTrackPageView(PAGE_TITLE);
// if this is a site search add this. Modify the if statement and $_GET variable to your needs
if($sitesearch !== false){
   $matomoTracker->doTrackSiteSearch(urldecode($_GET['q']));
}
```
