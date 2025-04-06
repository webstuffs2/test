<?php
// Get the POST data from the frontend (index.html)
$data = json_decode(file_get_contents('php://input'), true);
$link = $data['link']; // The TikTok link sent from the frontend

// Your RapidAPI Key
$rapidApiKey = "9ff9df4724mshc46ef2cef6b47e4p17c916jsn6fabfd0216bd"; 

// Build the API URL (adjust based on what you're using)
$apiUrl = 'https://tiktok-api23.p.rapidapi.com/api/music/info?musicId=7224128604890990593'; // Example URL, replace with actual dynamic URL if needed

// Initialize cURL
$ch = curl_init();

// Set the cURL options
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'x-rapidapi-host: tiktok-api23.p.rapidapi.com',
    'x-rapidapi-key: ' . $rapidApiKey
]);

// Execute the cURL request and get the response
$response = curl_exec($ch);

// Check for errors
if ($response === false) {
    echo json_encode(['success' => false, 'message' => 'Error fetching data']);
    exit();
}

// Decode the JSON response
$responseData = json_decode($response, true);

// Check if the response contains the necessary data (song title and artist)
if (isset($responseData['music_name']) && isset($responseData['author_name'])) {
    $musicName = $responseData['music_name'];
    $authorName = $responseData['author_name'];
    echo json_encode([
        'success' => true,
        'title' => $musicName,
        'artist' => $authorName
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Song not found']);
}

// Close the cURL session
curl_close($ch);
?>
