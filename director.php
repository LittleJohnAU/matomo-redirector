<?php
/**
 *
 * For installation and information, see: https://github.com/LittleJohnAU/matomo-redirector
 *
 * @license released under GPL-3.0 License http://www.opensource.org/licenses/GPL-3.0
 * Copyright (C) 2020 The League Of True Love
 * @link https://github.com/matomo-org/matomo-php-tracker
 * @link https://github.com/mgazdzik/plugin-ShortcodeTracker
 *
 */
class Director
{
    protected static $chars = "abcdfghjkmnpqrstvwxyz|ABCDFGHJKLMNPQRSTVWXYZ|0123456789";
    protected $pdo;
    public $error;
    // change these settings
    protected $dbUsername = "db_username";
    protected $dbPassword = "db_password";
    protected $dbName     = "db_name";
    public $matomoSiteId = 1;  // Site ID
    public $matomoUrl = "https://matomo.mydomain.com"; // Your matomo URL
    private $matomoToken = "";     // your api token
    public $redirectDomain = "https://short.mydomain.com/";   // with trailing slash  
    private $query = "SELECT `id`, `url` FROM matomo_shortcode WHERE code = :short_code LIMIT 1";

    public function __construct(){
        $this->pdo = new PDO("mysql:host=localhost;dbname=".$this->dbName, $this->dbUsername, $this->dbPassword);
        $this->timestamp = date("Y-m-d H:i:s");
    }
    
    public function setError($err){
        $this->error = $err;
    }

    public function showHtml(){
        $html = file_get_contents("template.html");
        return str_replace("{{error}}",$this->error,$html);
    }

    public function getReferrer()
    {
        if (!empty($_SERVER['HTTP_REFERER'])) {
            return $_SERVER['HTTP_REFERER'];
        }
        return false;
    }

    public function getToken(){
        return $this->matomoToken;
    }

    public function removeParams($code){
        if(stristr($code,'?')){
            $sc = explode("?",$code);
            $code = $sc[0];
        }
        return $code;
    }
    
    public function shortToLong($code){
        if(empty($code)) {
            $this->error = "No short code was supplied";
            throw new Exception("No short code was supplied");
        }

        if($this->validateShortCode($code) == false){
            $this->error = "Short code is not valid";
            throw new Exception("Short code is not valid");
        }

        $urlRow = $this->getUrlFromDB($code);
        if(empty($urlRow)){
            $this->error = "Short code does not appear to exist.";
            throw new Exception("Short code does not appear to exist.");
        }

        return $urlRow["url"];
    }

    protected function validateShortCode($code){
        $rawChars = str_replace('|', '', self::$chars);
        return preg_match("|[".$rawChars."]+|", $code);
    }

    protected function getUrlFromDB($code){
        $stmt = $this->pdo->prepare($this->query);
        $params=array(
            "short_code" => $code
        );
        $stmt->execute($params);

        $result = $stmt->fetch();
        return (empty($result)) ? false : $result;
    }

    public function closeDB(){
        if($this->pdo){
            $this->pdo = null;
            return true;
        }else{
            $this->error = "Error: Connection to database lost.";
            throw new Exception("Connection to database lost.");
            return false;
        }
    }
}
