<?php
header('Content-Type: application/json');

// Optional: Basic IP-based rate limiting
$ip = $_SERVER['REMOTE_ADDR'];
$limitFile = sys_get_temp_dir() . "/ttmusicid_" . md5($ip);
$cooldownSeconds = 3600; // 1 hour

if (file_exists($limitFile)) {
    $lastTime = file_get_contents($limitFile);
    if (time() - (int)$lastTime < $cooldownSeconds) {
        echo json_encode([
            'success' => false,
            'error' => 'Limit reached. Please wait before trying again.'
        ]);
        exit;
    }
}

$data = json_decode(file_get_contents('php://input'), true);
$link = trim($data['link'] ?? '');

if (!$link || !filter_var($link, FILTER_VALIDATE_URL)) {
    echo json_encode(['success' => false, 'error' => 'Invalid URL.']);
    exit;
}

function isTikTokLink($url) {
    return strpos($url, 'tiktok.com') !== false;
}

function extractTikTokAudio($videoUrl) {
    // Replace this with your actual TikTok downloader API
    // For example: call a TikTok API that returns the audio URL
    // This is a placeholder for now
    return 'https://example.com/audiofile.mp3';
}

function identifySongWithAudD($audioUrl) {
    $apiKey = 'YOUR_AUDD_API_KEY'; // Replace with your real AudD key
    $apiUrl = 'https://api.audd.io/';

    $response = file_get_contents($apiUrl . '?' . http_build_query([
        'api_token' => $apiKey,
        'url' => $audioUrl,
        'return' => 'apple_music,spotify',
    ]));

    $json = json_decode($response, true);

    if (!empty($json['result'])) {
        return [
            'success' => true,
            'title' => $json['result']['title'],
            'artist' => $json['result']['artist'],
        ];
    }

    return ['success' => false, 'error' => 'No match found.'];
}

if (isTikTokLink($link)) {
    $audioUrl = extractTikTokAudio($link);

    // Optional: check if audio URL was successfully retrieved
    if (!$audioUrl) {
        echo json_encode(['success' => false, 'error' => 'Failed to get audio from TikTok.']);
        exit;
    }

    $result = identifySongWithAudD($audioUrl);

    if ($result['success']) {
        file_put_contents($limitFile, time()); // Save timestamp
    }

    echo json_encode($result);
    exit;
} else {
    echo json_encode(['success' => false, 'error' => 'Only TikTok links supported.']);
    exit;
}
?>
