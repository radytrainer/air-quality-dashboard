<?php

$city = 'Phnom Penh';
$url = 'https://api.openaq.org/v3/latest?city=' . urlencode($city) . '&limit=1';

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo "Curl error: " . curl_error($ch);
} else {
    echo $response;
}

curl_close($ch);
