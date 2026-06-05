<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/autoload.php';

date_default_timezone_set('Europe/Paris');

$date = $_GET['date'] ?? '';
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    http_response_code(400);
    echo json_encode(['error' => 'Date invalide']);
    exit;
}

$clubs = [
    'forest-hill-nanterre-la-defense' => 'Forest Hill (Nanterre)',
    'sportfield-courbevoie-la-defense' => 'Sportfield (Courbevoie)',
];

$apiUrl = 'https://www.anybuddyapp.com/api/v1/availabilities';
$userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36';

function callApi(string $url, string $userAgent): ?array
{
    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
        ]);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($status === 200 && $response !== false) {
            $data = json_decode($response, true);
            return is_array($data) ? $data : null;
        }
    }

    // Fallback file_get_contents if cURL is unavailable
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => "User-Agent: $userAgent\r\n",
            'timeout' => 10,
        ],
        'ssl' => [
            'verify_peer' => true,
            'verify_peer_name' => true,
        ],
    ]);

    $response = @file_get_contents($url, false, $context);
    if ($response === false) {
        $fallbackContext = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => "User-Agent: $userAgent\r\nAccept: application/json\r\n",
                'timeout' => 10,
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ]);
        $response = @file_get_contents($url, false, $fallbackContext);
        if ($response === false) {
            return null;
        }
    }

    $data = json_decode($response, true);
    return is_array($data) ? $data : null;
}

function formatSlot(array $slot): ?array
{
    $fullDate = $slot['startDateTime'] ?? '';
    if (!is_string($fullDate) || strpos($fullDate, 'T') === false) {
        return null;
    }

    $timeStr = substr(explode('T', $fullDate)[1], 0, 5);
    $timeParts = explode(':', $timeStr);
    if (count($timeParts) !== 2) {
        return null;
    }

    $hours = (int)$timeParts[0];
    $minutes = (int)$timeParts[1];
    $totalMinutes = $hours * 60 + $minutes;

    $valid = (
        ($totalMinutes >= 7 * 60 && $totalMinutes < 9 * 60) ||
        ($totalMinutes >= 12 * 60 && $totalMinutes < 14 * 60) ||
        ($totalMinutes >= 18 * 60 && $totalMinutes < 22 * 60)
    );

    if (!$valid) {
        return null;
    }

    $services = $slot['services'] ?? [];
    if (!is_array($services)) {
        return null;
    }

    $results = [];
    $seen = [];
    foreach ($services as $service) {
        if (!is_array($service)) {
            continue;
        }

        $duration = isset($service['duration']) ? (int)$service['duration'] : 60;
        $price = isset($service['price']) ? ((float)$service['price'] / 100) : 0.0;
        $key = $duration . '_' . $price;
        if (isset($seen[$key])) {
            continue;
        }
        $seen[$key] = true;

        switch ($duration) {
            case 60:
                $durationText = '1h';
                break;
            case 90:
                $durationText = '1h30';
                break;
            case 120:
                $durationText = '2h';
                break;
            default:
                $durationText = $duration . 'min';
                break;
        }

        $results[] = [
            'heure' => $timeStr,
            'duree' => $durationText,
            'prix' => $price,
        ];
    }

    return $results ? $results : null;
}

$output = [];
foreach ($clubs as $slug => $clubName) {
    $params = http_build_query([
        'clubSlug' => $slug,
        'dateFrom' => $date,
        'dateTo' => $date . 'T23:59',
        'activity' => 'padel',
    ]);

    $url = $apiUrl . '?' . $params;
    $apiResult = callApi($url, $userAgent);

    if (!is_array($apiResult) || !isset($apiResult['data']) || !is_array($apiResult['data'])) {
        $output[$clubName] = 'erreur';
        continue;
    }

    $slots = [];
    foreach ($apiResult['data'] as $slot) {
        $formatted = formatSlot($slot);
        if (!$formatted) {
            continue;
        }

        foreach ($formatted as $slotData) {
            $slots[] = $slotData;
        }
    }

    $output[$clubName] = $slots;
}

$hasSlots = false;
$hasError = false;
foreach ($output as $slots) {
    if (is_array($slots) && count($slots) > 0) {
        $hasSlots = true;
        break;
    }
    if (!is_array($slots) && $slots === 'erreur') {
        $hasError = true;
    }
}

if (!$hasSlots && !$hasError) {
    $notificationController = new NotificationController();
    $message = sprintf(
        "Aucun créneau disponible trouvé pour le %s via l'API de terrains. Vérifiez ou modifiez les créneaux concernés dans l'espace admin.",
        $date
    );

    $check = Database::getInstance()->getConnection()->prepare(
        "SELECT COUNT(*) FROM notification WHERE message = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 6 HOUR)"
    );
    $check->execute([$message]);

    if ((int)$check->fetchColumn() === 0) {
        $notificationId = $notificationController->create($message, null);
        $notificationController->hideFromAllNonAdmins($notificationId);
    }
}

echo json_encode($output);
