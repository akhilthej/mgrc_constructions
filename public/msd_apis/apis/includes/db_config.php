<?php
// ---- Base URLs ----
$local_baseurl     = "http://localhost/";
$local_secureurl   = "*";

$invoice_baseurl     = "https://invoice.cyberspacedigital.in/";
$invoice_secureurl   = "*";


// Detect current domain
$currentURL = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http")
              . "://$_SERVER[HTTP_HOST]/";

// ---- Default NULL values ----
$servername = null;
$username   = null;
$password   = null;
$dbname     = null;
$baseurl    = null;
$SecureURL  = null;

// ---- Domain: Live ----
if ($currentURL === $invoice_baseurl) {

    $servername = "localhost";
    $username   = "test";
    $password   = "ilovemotherA1!";
    $dbname     = "test";
    
    $baseurl    = $invoice_baseurl;
    $SecureURL  = $invoice_secureurl;
}
// ---- Domain: local.host ----
elseif ($currentURL === $local_baseurl) {

$servername = "localhost";
    $username   = "root";
    $password   = "";
    $dbname     = "thewinnerindiamk";
   
    

    $baseurl    = $local_baseurl;
    $SecureURL  = $local_secureurl;
}

// ---- If domain matches any of the above, connect ----
if ($servername !== null) {

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    define("BASE_URL", $baseurl);
    define("SECURE_URL", $SecureURL);

} else {
    die("Invalid domain access. No database assigned.");
}
?>
