<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed. Use POST.']);
    exit;
}

$payload = json_decode(file_get_contents('php://input') ?: '[]', true);
$message = isset($payload['message']) ? trim((string) $payload['message']) : '';
$pageContext = isset($payload['context']) ? trim((string) $payload['context']) : '';

if ($message === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Message is required.']);
    exit;
}

$serviceContext = <<<PROMPT
You are IskoBot, the helpful AI assistant for Isk⭐Print, a campus print shop located at the Student Services Center, PUP Sto. Tomas Campus.

Services and quick facts:
- Print (documents, PDFs) — from ₱60
- Book Bind (thermal/soft) — from ₱120
- Lamination (IDs, photos, docs) — from ₱40
- Pictures (glossy photo prints) — from ₱25
- Photocopy (B/W) — from ₱10
- Tarpaulin (large-format) — from ₱200

Operating hours: Monday to Saturday, 9:00 AM – 6:00 PM.
Ordering steps: Choose a service, upload files, set print options (paper size, color, duplex, copies, etc.), then submit so the admin can review and confirm.
Contact channels: facebook.com/IskoPrintOfficial, Instagram @iskoprint_official, Telegram t.me/IskoPrintOfficial, email support@iskoprint.com, phone +63 900 123 4567.

Guidelines:
- Mirror the user’s language: if they ask in English, respond fully in English; if they ask in Tagalog, respond fully in Tagalog; if the question mixes both, reply in polished Taglish matching their tone.
- Keep responses concise, grammatically polished, and actionable.
- Only answer questions related to Isk⭐Print, its services, pricing, hours, workflow, or anything found on the website sections described above. Politely decline and redirect if the topic is unrelated.
- Always relate answers to Isk⭐Print services, pricing, hours, and process.
- If unsure or question is outside scope, invite the user to email support@iskoprint.com.
PROMPT;

$pageContextBlock = $pageContext !== '' ? "\n\nPage-specific context:\n{$pageContext}" : '';

$ollamaBody = [
    'model' => 'llama3.2:3b',
    'messages' => [
        ['role' => 'system', 'content' => $serviceContext . $pageContextBlock],
        ['role' => 'user', 'content' => $message],
    ],
    'stream' => false,
    'options' => [
        'num_ctx' => 2048,
        'temperature' => 0.4,
        'top_p' => 0.8,
        'top_k' => 40,
        'repeat_penalty' => 1.1,
        'stop' => ["<|eot_id|>", "<|eom_id|>"],
        'max_tokens' => 256,
    ],
];

$ollamaHosts = [
    'http://127.0.0.1:11434',
    'http://localhost:11434',
];

$lastError = null;
$reply = null;

foreach ($ollamaHosts as $host) {
    $ch = curl_init($host . '/api/chat');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($ollamaBody, JSON_UNESCAPED_UNICODE));
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $response = curl_exec($ch);

    if ($response === false) {
        $lastError = 'cURL error #' . curl_errno($ch) . ': ' . curl_error($ch);
        curl_close($ch);
        continue;
    }

    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($status >= 400) {
        $lastError = 'Ollama returned HTTP ' . $status;
        continue;
    }

    $decoded = json_decode($response, true);
    $reply = $decoded['message']['content'] ?? null;

    if ($reply) {
        break;
    }

    $lastError = 'Unexpected Ollama payload';
}

if (!$reply) {
    http_response_code(502);
    error_log('[IskoBot] Ollama error: ' . ($lastError ?: 'Unknown failure'));
    echo json_encode(['error' => 'Cannot reach the Ollama server. Make sure ollama serve is running.']);
    exit;
}

echo json_encode(['reply' => $reply]);

