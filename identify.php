<?php
// Get the raw POST data from the request
$data = json_decode(file_get_contents("php://input"), true);

// Ensure the 'link' key is present in the data
if (isset($data['link'])) {
    // Extract the TikTok video URL
    $videoLink = $data['link'];
    
    // Extract the video ID from the URL
    preg_match('/(?:https?:\/\/(?:www\.)?tiktok\.com\/(?:@[\w.-]+\/)?video\/(\d+))/', $videoLink, $matches);
    
    if (isset($matches[1])) {
        // Get the TikTok video ID
        $videoID = $matches[1];
        
        // API URL with the new endpoint to fetch music info based on the video ID
        $apiUrl = 'https://tiktok-video-feature-summary.p.rapidapi.com/music/info?url=' . urlencode('https://www.tiktok.com/@spider_slack/video/' . $videoID);
        
        // Initialize cURL session
        $ch = curl_init();
        
        // Set the cURL options
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "x-rapidapi-host: tiktok-video-feature-summary.p.rapidapi.com",
            "x-rapidapi-key: ee69272e69msh597ad9b0078f353p1589d5jsn69b9e3013142" // Replace with your actual RapidAPI key
        ]);
        
        // Execute the cURL request
        $response = curl_exec($ch);
        
        // Check for errors in the request
        if (curl_errno($ch)) {
            // If there is an error, return a failure response
            $responseData = ['success' => false, 'error' => 'Error connecting to API'];
        } else {
            // Parse the API response
            $data = json_decode($response, true);
            
            // Check if music information is available
            if (isset($data['music']['title']) && isset($data['music']['author'])) {
                // Return the song info
                $responseData = [
                    'success' => true,
                    'title' => $data['music']['title'],
                    'author' => $data['music']['author']
                ];
            } else {
                // If no music information is found, return a failure message
                $responseData = ['success' => false, 'error' => 'Song information not found.'];
            }
        }
        
        // Close the cURL session
        curl_close($ch);
        
    } else {
        // If video ID cannot be extracted, return an error
        $responseData = ['success' => false, 'error' => 'Invalid TikTok URL.'];
    }
} else {
    // If no link was provided, return an error
    $responseData = ['success' => false, 'error' => 'No link provided.'];
}

// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($responseData);
?>







