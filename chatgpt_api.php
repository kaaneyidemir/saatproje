<?php
$api_key = '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';

if (!$api_key) {
    echo json_encode(['error' => 'API anahtarı bulunamadı.']);
    exit;
}

// JSON formatında gelen kullanıcı mesajını al
$request_body = file_get_contents('php://input');
$data = json_decode($request_body, true);

// Eğer 'message' verisi yoksa hata döndür
if (!isset($data['message'])) {
    echo json_encode(['error' => 'Mesaj parametresi eksik.']);
    exit;
}

// OpenAI API isteği için hazırlık
$url = 'https://api.openai.com/v1/chat/completions';
$headers = [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $api_key
];

$payload = [
    'model' => 'GPT-4o.', // Model adı doğru
    'messages' => [
        ['role' => 'system', 'content' => 'Kullanıcıya yardımcı olan bir asistan gibi davran.'],
        ['role' => 'user', 'content' => $data['message']]
    ]
];

// Curl isteği oluştur
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

$response = curl_exec($ch);

// Hata kontrolü
if (curl_errno($ch)) {
    echo json_encode(['error' => curl_error($ch)]);
    exit;
}

curl_close($ch);

// API yanıtını çözümle
$response_data = json_decode($response, true);

// Yanıtı ekrana basarak kontrol edin (hata ayıklama için)
if (isset($response_data['choices']) && isset($response_data['choices'][0]['message']['content'])) {
    $botReply = $response_data['choices'][0]['message']['content'];
    echo json_encode(['message' => $botReply]);
} else {
    // API beklenmedik formatta yanıt verirse burada yakalanır
    echo json_encode(['error' => 'Beklenmedik yanıt formatı', 'response' => $response_data]);
}
?>
