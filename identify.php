<?php

// Enable error reporting to debug
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Get the POST data (link sent from frontend)
$data = json_decode(file_get_contents('php://input'), true);
$videoLink = $data['link'] ?? '';

// Check if the video link is valid
if (empty($videoLink)) {
    echo json_encode(['success' => false, 'message' => 'Invalid link.']);
    exit;
}

// The RapidAPI URL for TikTok feature summary API
$apiUrl = 'https://tiktok-video-feature-summary.p.rapidapi.com/';
$apiKey = 'ee69272e69msh597ad9b0078f353p1589d5jsn69b9e3013142'; // Your actual API key
$encodedLink = urlencode($videoLink); // Encode the URL to pass as a parameter

// Prepare the headers for the API request
$headers = [
    "x-rapidapi-host: tiktok-video-feature-summary.p.rapidapi.com",
    "x-rapidapi-key: $apiKey"
];

// Make the API request using cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl . "?url=$encodedLink&hd=1");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

// Execute the cURL request
$response = curl_exec($ch);

// Check for errors in the request
if (curl_errno($ch)) {
    echo json_encode(['success' => false, 'message' => 'Error fetching data from API.']);
    exit;
}

curl_close($ch);

// Decode the API response
$responseData = json_decode($response, true);

// Debugging output (optional)
file_put_contents('debug.txt', print_r($responseData, true));  // Save raw response to file for debugging

// Check if the response contains valid data
if (isset($responseData['data']) && isset($responseData['data']['music'])) {
    $music = $responseData['data']['music'];
    $title = $music['title'] ?? 'Unknown title';
    $artist = $music['authorName'] ?? 'Unknown artist';

    // Return the identified music details
    echo json_encode([
        'success' => true,
        'title' => $title,
        'artist' => $artist
    ]);
} else {
    // Return an error if no music data found
    echo json_encode(['success' => false, 'message' => 'Song not found in TikTok video.']);
}
?>



