<?php
// yolink-proxy.php
// Place this file on your web server alongside index.html.
// The browser calls this script; this script calls the YoLink API.

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

// -------------------------------------------------------
// Fill in your credentials here (server-side only, safe)
// YoLink app → Menu → Settings → Account
//           → Advanced Settings → User Access Credentials
// -------------------------------------------------------
$UAID       = "YOUR_UAID_HERE";
$SECRET_KEY = "YOUR_SECRET_KEY_HERE";
// -------------------------------------------------------

$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action === 'token') {
    // Step 1: get OAuth2 access token
    $ch = curl_init("https://api.yosmart.com/open/yolink/token");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials&client_id=$UAID&client_secret=$SECRET_KEY");
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/x-www-form-urlencoded"]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
    echo $result;

} elseif ($action === 'api') {
    // Step 2+3: proxy any JSON body to the YoLink v2 API
    $token = isset($_GET['token']) ? $_GET['token'] : '';
    $body  = file_get_contents("php://input");

    $ch = curl_init("https://api.yosmart.com/open/yolink/v2/api");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer $token"
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
    echo $result;

} else {
    echo json_encode(["error" => "Unknown action"]);
}
?>
