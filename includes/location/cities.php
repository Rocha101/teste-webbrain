<?php
header('Content-Type: application/json');

if (!isset($_GET['state']) || empty($_GET['state'])) {
    echo json_encode(['error' => 'Estado não informado']);
    exit;
}

$state = strtoupper($_GET['state']);

// URL da API do IBGE para municípios
$url = "https://servicodados.ibge.gov.br/api/v1/localidades/estados/{$state}/municipios";

// Inicializa o cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

// Executa a requisição
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Verifica se a requisição foi bem sucedida
if ($httpCode === 200) {
    $cities = json_decode($response, true);
    $formattedCities = array_map(function($city) {
        return ['name' => $city['nome']];
    }, $cities);
    echo json_encode($formattedCities);
} else {
    echo json_encode(['error' => 'Erro ao buscar cidades']);
}
?>
