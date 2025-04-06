<?php

// API key for TikTok-related API
$apiKey = 'YOUR_RAPIDAPI_KEY'; // Replace with your actual RapidAPI key

// Function to fetch song info from TikTok
function extractTikTokAudio($videoUrl) {
    $apiHost = 'tiktok-video-feature-summary.p.rapidapi.com';
    $encodedUrl = urlencode($videoUrl);
    $apiUrl = "https://$apiHost/?url=$encodedUrl&hd=1";

    // Set up cURL to make the API request
    $headers = [
        "x-rapidapi-host: $apiHost",
        "x-rapidapi-key: $apiKey"
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $response = curl_exec($ch);

    if(curl_errno($ch)) {
        return ['success' => false, 'message' => 'Error: ' . curl_error($ch)];
    }

    curl_close($ch);

    // Parse the response to extract song details
    $data = json_decode($response, true);
    
    // Check if the audio URL exists in the response
    if (isset($data['audio_url'])) {
        return [
            'success' => true,
            'title' => $data['music']['title'],  // Song Title
            'artist' => $data['music']['author'], // Artist Name
            'audio_url' => $data['audio_url']    // Audio URL
        ];
    } else {
        return ['success' => false, 'message' => 'Audio URL not found for this TikTok video.'];
    }
}

// Handle the POST request from the frontend
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the TikTok URL from the POST request
    $input = json_decode(file_get_contents('php://input'), true);
    $videoUrl = $input['link'] ?? '';

    // If no URL provided, return an error message
    if (empty($videoUrl)) {
        echo json_encode(['success' => false, 'message' => 'No TikTok URL provided.']);
        exit;
    }

    // Call the function to extract song info
    $result = extractTikTokAudio($videoUrl);

    // Return the result as a JSON response
    echo json_encode($result);
    exit;
}
?>
