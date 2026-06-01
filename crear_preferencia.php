<?php
// MANTENER SIN ESPACIOS PREVIOS
header('Content-Type: application/json');
ini_set('display_errors', 1); 
error_reporting(E_ALL);

$input = file_get_contents('php://input');
$data = json_decode($input, true);

$total = isset($data['total']) ? floatval($data['total']) : 0;

if ($total <= 0) {
    echo json_encode(['error' => 'El monto total enviado no es válido o está en ceros.']);
    exit;
}

// Tu Token de acceso privado asignado
$access_token = "APP_USR-5495614520913809-052022-4b22daca22a234124065e414b3da24bc-3417406578"; 

try {
    $url = "https://api.mercadopago.com/checkout/preferences";
    
    $headers = [
        "Authorization: Bearer " . trim($access_token),
        "Content-Type: application/json"
    ];
    
    // Al no enviar 'auto_return', la API aprueba la estructura sin restricciones complejas de letras
    $body = json_encode([
        "items" => [
            [
                "title" => "Compra en Agrolity - Productos del Campo",
                "quantity" => 1,
                "unit_price" => $total,
                "currency_id" => "COP"
            ]
        ],
        "back_urls" => [
            "success" => "http://localhost/Agrolity/respuesta_pago.php",
            "failure" => "http://localhost/Agrolity/checkout.php",
            "pending" => "http://localhost/Agrolity/checkout.php"
        ]
    ]);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $res_data = json_decode($response, true);
    
    if ($http_code === 200 || $http_code === 201) {
        if (isset($res_data['init_point'])) {
            echo json_encode(['init_point' => $res_data['init_point']]);
            exit;
        }
    }
    
    $detalle_error = "Error desconocido de la API.";
    if (isset($res_data['message'])) {
        $detalle_error = $res_data['message'];
    } elseif (isset($res_data['cause'][0]['description'])) {
        $detalle_error = $res_data['cause'][0]['description'];
    }
    
    echo json_encode([
        'error' => 'Mercado Pago rechazó la petición: ' . $detalle_error
    ]);
    exit;

} catch (Exception $e) {
    echo json_encode([
        'error' => $e->getMessage()
    ]);
    exit;
}
?>
