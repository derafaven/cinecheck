<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$query = trim($_GET['q'] ?? '');
if (!$query) { echo json_encode(['error' => 'Ingresa el nombre de una película o serie']); exit; }

$cfg    = json_decode(file_get_contents(__DIR__ . '/config/sitios.json'), true);
$sitios = array_filter($cfg['sitios'] ?? [], fn($s) => !empty($s['activo']));
if (!$sitios) { echo json_encode(['error' => 'No hay sitios activos configurados']); exit; }

require_once __DIR__ . '/adaptadores/DooPlayAdaptador.php';
require_once __DIR__ . '/adaptadores/ScraperAdaptador.php';

function getAdaptador(array $s) {
    return match($s['tipo']) {
        'dooplay_api'   => new DooPlayAdaptador($s),
        'scraping_html' => new ScraperAdaptador($s),
        default         => null,
    };
}

$resultados = [];
foreach ($sitios as $s) {
    $a = getAdaptador($s);
    if ($a) $resultados[] = $a->buscar($query);
}

$con = count(array_filter($resultados, fn($r) => $r['encontrada']));
echo json_encode([
    'query'                => $query,
    'sitios_consultados'   => count($resultados),
    'sitios_con_resultado' => $con,
    'resultados'           => $resultados,
]);
