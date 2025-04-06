<?php
// Get JSON input
$data = json_decode(file_get_contents('php://input'), true);
$videoUrl = $data['link'] ?? '';

// API credentials and URL
$apiKey = 'ee69272e69msh597ad9b0078f353p1589d5jsn69b9e3013142';
$apiHost = 'tiktok-api23.p.rapidapi.com';

// Extract musicId from TikTok URL (you need to provide logic for this or send it from frontend)
$musicId = ''; // Set this to extract from the TikTok video URL (This part is crucial)

// Check if musicId is available
if (empty($musicId)) {
    echo json_encode(["success" => false, "error" => "Music ID not found in the provided link"]);
    exit;
}

// Setup API endpoint and parameters
$apiUrl = "https://tiktok-api23.p.rapidapi.com/api/music/info?musicId=$musicId";

// Initialize cURL session
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "x-rapidapi-host: $apiHost",
    "x-rapidapi-key: $apiKey"
]);

// Execute cURL and capture the response
$response = curl_exec($ch);
curl_close($ch);

// Check for errors
if ($response === false) {
    echo json_encode(["success" => false, "error" => "API request failed"]);
    exit;
}

// Decode the response from the API
$responseData = json_decode($response, true);

// Check if the API returned valid data
if (isset($responseData['data']) && !empty($responseData['data'])) {
    $musicData = $responseData['data'];
    $title = $musicData['title'] ?? 'Unknown Title';
    $author = $musicData['author'] ?? 'Unknown Artist';
    
    // Respond with the song info
    echo json_encode([
        "success" => true,
        "title" => $title,
        "author" => $author
    ]);
} else {
    echo json_encode(["success" => false, "error" => "Song not found."]);
}
?>
