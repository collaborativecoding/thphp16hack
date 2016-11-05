<?php

ini_set('display_errors', getenv('APP_ERROR'));

require_once __DIR__ . '/../vendor/autoload.php';

$settings = [
    'oauth_access_token' => getenv('TW_ACC_KEY'),
    'oauth_access_token_secret' => getenv('TW_ACC_SEC'),
    'consumer_key' => getenv('TW_APP_KEY'),
    'consumer_secret' => getenv('TW_APP_SEC'),
];

$api_url = 'https://api.twitter.com/1.1/search/tweets.json';
$getfield = http_build_query([
    'q' => '#tnphp OR #tnphp16 OR #truenorthphp',
    'count' => 100,
//    'result_type' => 'popular',
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
        'id' => $message->id,
        'author' => [
            'handle' => $message->user->screen_name,
            'name' => $message->user->name,
            'avatar' => $message->user->profile_image_url_https,
        ],
        'time' => new \DateTime($message->created_at, new \DateTimeZone('UTC')),
        'tweet' => $message->text,
    ];
    if (isset ($message->entities->user_mentions)) {
        $speakers = [];
        foreach ($message->entities->user_mentions as $mention) {
            $speakers[] = [
                'handle' => $mention->screen_name,
                'name' => $mention->name,
            ];
        }
        if ([] !== $speakers) {
            $entry['speaker'] = $speakers[0];
        }
    }
    if (isset ($message->entities->media)) {
        $entry['image'] = $message->entities->media[0]->media_url_https;
    }
    $tweets[] = $entry;
}
header('Content-Type: application/json');
echo json_encode($tweets);