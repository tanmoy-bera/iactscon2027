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

function sendFcmToUser($mycms, $userId, $title, $body, $dataPayload = []) {

    // 1) user-এর সব active device token fetch করো
    $tsql = [];
    $tsql['QUERY'] = "SELECT `fcm_token` FROM `rcg_user_devices` WHERE `user_id`=?";
    $tsql['PARAM'] = [['FILD' => 'user_id', 'DATA' => $userId, 'TYP' => 'i']];
    $tokens = $mycms->sql_select($tsql);

    if (empty($tokens)) {
        return "No FCM tokens found for user_id=$userId";
    }

    // FCM data payload-এর সব value string হতে হবে
    $stringData = [];
    foreach ($dataPayload as $k => $v) {
        $stringData[$k] = (string)$v;
    }

    $results = [];

    foreach ($tokens as $row) {
        $token = $row['fcm_token'];

        $message = [
            "token" => $token,
            "notification" => [
                "title" => $title,
                "body"  => $body
            ],
            "data" => $stringData
        ];

        $response = sendFCM($message);
        $results[] = $response;

        // invalid/unregistered token হলে log রাখা যেতে পারে (optional cleanup)
        $decoded = json_decode($response, true);
        if (isset($decoded['error']['status']) &&
            in_array($decoded['error']['status'], ['UNREGISTERED', 'INVALID_ARGUMENT'])) {
            // চাইলে এখানে token delete করে দিতে পারো rcg_user_devices থেকে
            // $mycms->sql_delete(['QUERY' => "DELETE FROM rcg_user_devices WHERE fcm_token=?", 'PARAM' => [['FILD'=>'t','DATA'=>$token,'TYP'=>'s']]]);
        }
    }

    return $results;
}