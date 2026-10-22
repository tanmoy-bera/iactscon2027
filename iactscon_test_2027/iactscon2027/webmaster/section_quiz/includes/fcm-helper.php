<?php
function base64UrlEncode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function getAccessToken() {
    $key = json_decode(file_get_contents(__DIR__ . '/firebase-key.json'), true);

    $header = base64UrlEncode(json_encode([
        "alg" => "RS256",
        "typ" => "JWT"
    ]));

    $now = time();

    $payload = base64UrlEncode(json_encode([
        "iss" => $key['client_email'],
        "scope" => "https://www.googleapis.com/auth/firebase.messaging",
        "aud" => "https://oauth2.googleapis.com/token",
        "iat" => $now,
        "exp" => $now + 3600
    ]));

    $signatureInput = $header . "." . $payload;

    openssl_sign($signatureInput, $signature, $key['private_key'], 'SHA256');

    $jwt = $signatureInput . "." . base64UrlEncode($signature);

    // Exchange JWT for access token
    $ch = curl_init("https://oauth2.googleapis.com/token");

    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        "grant_type" => "urn:ietf:params:oauth:grant-type:jwt-bearer",
        "assertion" => $jwt
    ]));

    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);

    return $data['access_token'] ?? null;
}

function sendFCM($message) {

    $accessToken = getAccessToken();

    if (!$accessToken) {
        return "Failed to get access token";
    }

    $projectId = "concloud-708c9"; // from firebase

    $url = "https://fcm.googleapis.com/v1/projects/$projectId/messages:send";

    $payload = json_encode([
        "message" => $message
    ]);

    $headers = [
        "Authorization: Bearer " . $accessToken,
        "Content-Type: application/json"
    ];

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}