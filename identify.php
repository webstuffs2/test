<?php

// RapidAPI Key (replace with your actual key)
$apiKey = 'ee69272e69msh597ad9b0078f353p1589d5jsn69b9e3013142'; // Replace with your RapidAPI key

// Function to fetch song info from TikTok using RapidAPI
function extractTikTokAudio($videoUrl) {
    $apiHost = 'tiktok-video-feature-summary.p.rapidapi.com';
    $encodedUrl = urlencode($videoUrl); // Encode the TikTok URL for use in the API query
    $apiUrl = "https://$apiHost/?url=$encodedUrl&hd=1"; // API endpoint for TikTok music details

    // Set up cURL to make the API request
    $headers = [
        "x-rapidapi-host: $apiHost",
        "x-rapidapi-key: $apiKey"
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl); // Set the API URL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // Return response instead of printing it
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // Set the necessary headers

    $response = curl_exec($ch); // Execute the cURL request

    if(curl_errno($ch)) {
        return ['success' => false, 'message' => 'Error: ' . curl_error($ch)];
    }

    curl_close($ch); // Close cURL session

    // Parse the response from the API
    $data = json_decode($response, true);
    
    // Check if the response contains the necessary music details
    if (isset($data['music'])) {
        return [
            'success' => true,
            'title' => $data['music']['title'],  // Song Title
            'artist' => $data['music']['author'], // Artist Name
            'audio_url' => $data['audio_url']    // Audio URL for direct access
        ];
    } else {
        return ['success' => false, 'message' => 'Audio URL not found for this TikTok video.'];
    }
}

// Handle the POST request from the frontend
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the TikTok URL from the POST request body
    $input = json_decode(file_get_contents('php://input'), true);
    $videoUrl = $input['link'] ?? ''; // Extract TikTok URL from input JSON

    // Check if a valid URL is provided
    if (empty($videoUrl)) {
        echo json_encode(['success' => false, 'message' => 'No TikTok URL provided.']);
        exit;
    }

    // Call the function to extract song info
    $result = extractTikTokAudio($videoUrl);

    // Return the result as a JSON response to the frontend
    echo json_encode($result);
    exit;
}
?>

