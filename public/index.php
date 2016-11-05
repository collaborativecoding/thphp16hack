<?php

require_once __DIR__ . '/../vendor/autoload.php';

$settings = [
    'oauth_access_token' => getenv('TW_ACC_KEY'),
    'oauth_access_token_secret' => getenv('TW_ACC_SEC'),
    'consumer_key' => getenv('TW_APP_KEY'),
    'consumer_secret' => getenv('TW_APP_SEC'),
];

$api_url = 'https://api.twitter.com/1.1/search/tweets.json';
$getfield = http_build_query([
    'q' => '#tnphp OR #tnphp16 OR #truenorthphp'
]);
$requestMethod = 'GET';
$twitter = new TwitterAPIExchange($settings);
$data = $twitter->setGetfield($getfield)
    ->buildOauth($api_url, $requestMethod)
    ->performRequest();

$messages = json_decode($data);

$tweets = [];
foreach ($messages->statuses as $message) {
    $entry = [
        'screen_name' => $message->user->screen_name,
        'name' => $message->user->name,
        'time' => new \DateTime($message->created_at, new \DateTimeZone('UTC')),
        'message' => $message->text,
        'mentions' => [],
    ];
    if (isset ($message->entities->user_mentions)) {
        foreach ($message->entities->user_mentions as $mention) {
            $entry['mentions'][] = [
                'screen_name' => $mention->screen_name,
                'name' => $mention->name,
            ];
        }
    }
    $tweets[] = $entry;
}
header('Content-Type: application/json');
echo json_encode($tweets);