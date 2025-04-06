<?php

// Get the URL from the request body
$data = json_decode(file_get_contents('php://input'), true);
$link = $data['link'];  // The TikTok link sent from the frontend

// Your NEW RapidAPI Key (replace this with the one you provided)
$rapidApiKey = "9ff9df4724mshc46ef2cef6b47e4p17c916jsn6fabfd0216bd"; 

// Extract the TikTok video ID from the link
preg_match('/\/video\/(\d+)/', $link, $matches);
$videoId = $matches[1];  // Get the video ID from the TikTok URL

// If no video ID was found, return an error
if (!$videoId) {
    echo json_encode(['success' => false, 'message' => 'Invalid TikTok URL']);
    exit();
}

// Build the API URL dynamically using the videoId
$apiUrl = "https://tiktok-api23.p.rapidapi.com/api/music/info?musicId=" . $videoId;

// Initialize cURL
$curl = curl_init();

curl_setopt_array($curl, [
	CURLOPT_URL => $apiUrl,
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_ENCODING => "",
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 30,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => "GET",
	CURLOPT_HTTPHEADER => [
		"x-rapidapi-host: tiktok-api23.p.rapidapi.com",
		"x-rapidapi-key: " . $rapidApiKey
	],
]);

// Execute the cURL request and get the response
$response = curl_exec($curl);
$err = curl_error($curl);

// Close the cURL session
curl_close($curl);

// Handle any errors
if ($err) {
    echo json_encode(['success' => false, 'message' => "cURL Error: " . $err]);
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
?>


