<?php

// Get POST data
$data = json_decode(file_get_contents("php://input"), true);
$videoId = $data['videoId']; // Extract videoId

if (!$videoId) {
    echo json_encode(['success' => false, 'error' => 'No video ID provided.']);
    exit;
}

// Prepare the URL for the API request
$apiUrl = "https://tiktok-api23.p.rapidapi.com/api/music/info?musicId=" . $videoId;

// Initialize cURL
$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_URL => $apiUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "x-rapidapi-host: tiktok-api23.p.rapidapi.com",
        "x-rapidapi-key: ee69272e69msh597ad9b0078f353p1589d5jsn69b9e3013142"
    ]
]);

// Execute cURL request and capture the response
$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

// Check for errors in the cURL request
if ($err) {
    echo json_encode(['success' => false, 'error' => 'cURL Error: ' . $err]);
    exit;
}

// Decode the JSON response from the API
$responseData = json_decode($response, true);

// Check if response contains music info
if (isset($responseData['data']) && isset($responseData['data']['music'])) {
    $music = $responseData['data']['music'];
    $result = [
        'success' => true,
        'title' => $music['title'] ?? 'Unknown Title',
        'artist' => $music['authorName'] ?? 'Unknown Artist',
    ];
    echo json_encode($result);
} else {
    echo json_encode(['success' => false, 'error' => 'Song not found.']);
}

?>

