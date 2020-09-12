<?php
ini_set('session.cookie_domain', '.mydomain.com'); // set this to your domain
session_name("mydomain"); // this needs to be set on the target domain as well
session_start();
date_default_timezone_set('Australia/Brisbane'); // set this to your timezone or comment out if not needed

require_once 'director.php';
$dir = new Director();

if(isset($_GET["c"])){
    $shortCode = $dir->removeParams($_GET["c"]);
    $url = $dir->shortToLong($shortCode);
    
    // Optional variable
    $matomoPageTitle = "My Redirector"; // The title of the page
    
    // Load object
    require_once("MatomoTracker.php");
    $ref = $dir->getReferrer();
    // Matomo object
    $matomoTracker = new MatomoTracker((int)$dir->matomoSiteId, $dir->matomoUrl);
    $token = $dir->getToken();
    $matomoTracker->setTokenAuth($token);
    $matomoTracker->setIdSite(1); // set this to the id for your target domain
    $matomoTracker->setUrl($dir->redirectDomain.$shortCode);
    $matomoTracker->setUrlReferrer($ref);
    
    $vid = $matomoTracker->getVisitorId();
    $_SESSION['matreferrer'] = $ref; // see the readme about unsetting these at the target
    $_SESSION['matvisitor'] = $vid; // this needs to be passed to the target domain
    $dir->closeDB(); // close the database connection

    try{
        $matomoTracker->doTrackEvent('shortcode', 'redirect', $shortCode);
        header("HTTP/1.1 301 Moved Permanently");
        // Redirect to the original URL
        header("Location: ".$url);
        exit;
    }catch(Exception $e){
        unset($_SESSION['matvisitor']);
        unset($_SESSION['matreferrer']);
        header("HTTP/1.0 404 Not Found");
        // Display error
        echo $dir->showHtml();
    }
} else {
    $dir->setError("No short code was supplied.");
    echo $dir->showHtml();
}
