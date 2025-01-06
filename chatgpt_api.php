<?php
// OpenAI API anahtarını ayarlayın
$api_key = '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';

// JSON formatında gelen kullanıcı mesajını alın
$request_body = file_get_contents('php://input');
$data = json_decode($request_body, true);

// OpenAI API isteği için hazırlık
$url = 'https://api.openai.com/v1/chat/completions';
$headers = [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $api_key
];

$payload = [
    'model' => 'gpt-3.5-turbo', // Model adını doğru yazdığınızdan emin olun
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

// Yanıtın tamamını ekrana basarak kontrol edin
// echo '<pre>';
// print_r($response_data);
// echo '</pre>';

// API yanıtında 'choices' array'inin olup olmadığını kontrol et
if (isset($response_data['choices']) && isset($response_data['choices'][0]['message']['content'])) {
    $botReply = $response_data['choices'][0]['message']['content'];
    echo json_encode(['message' => $botReply]);
} else {
    echo json_encode(['error' => 'Beklenmedik yanıt formatı']);
}
?>
