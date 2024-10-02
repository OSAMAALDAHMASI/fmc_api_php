<?php
require 'vendor/autoload.php';

use Google\Client;

function getAccessToken($serviceAccountPath)
{
    $client = new Client();
    $client->setAuthConfig($serviceAccountPath);
    $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
    $client->useApplicationDefaultCredentials();
    $token = $client->fetchAccessTokenWithAssertion();
    return $token['access_token'];
}

function sendMessage($accessToken, $projectId, $message)
{
    $url = 'https://fcm.googleapis.com/v1/projects/' . $projectId . '/messages:send';
    $headers = [
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/json',
    ];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['message' => $message]));
    $response = curl_exec($ch);
    if ($response === false) {
        throw new Exception('Curl error: ' . curl_error($ch));
    }
    curl_close($ch);
    return json_decode($response, true);
}

// إعدادات المستخدم
$serviceAccountPath = 'course-notification2024-07a7c875c923.json'; // قم بتعديل المسار
$projectId = 'course-notification2024'; // قم بتعديل معرف المشروع
$message = [
    // 'token' => 'csxj6sseQS2LYju1yeR0Hk:APA91bF-ANIeMacONdSNtdxdTgRBbiJ5f7nwMRuywxvpB08pALWS5X08_8gaijmj6daHNvypU8OCfImYfTqc8vRe9RuXhgqS4PJnrJ5Gsv-Qlb1vVu4Y1IoD2_T4WSvCYH3WhHhDdnx9', // قم بتعديل رمز الجهاز
    'topic' => 'all',
    'notification' => [
        'title' => 'Hello',
        'body' => 'World',
    ],
    "android" => [
        "notification" => [
            "channel_id" => "channel_id",
            "sound" => "long_notification_sound"
        ]
    ]
];

try {
    $accessToken = getAccessToken($serviceAccountPath);
    $response = sendMessage($accessToken, $projectId, $message);
    echo 'Message sent successfully: ' . print_r($response, true);
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
