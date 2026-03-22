<?php
require_once __DIR__ . '/../helpers/HttpHelper.php';
require_once __DIR__ . '/BaseAdaptador.php';

class DooPlayAdaptador extends BaseAdaptador {
    public function buscar(string $query): array {
        $urlBase    = rtrim($this->sitio['url_base'], '/');
        $cfg        = $this->sitio['config'];
        $cookieFile = sys_get_temp_dir() . '/cc_' . $this->sitio['id'] . '.txt';
        $nonce      = '';

        if (!empty($cfg['necesita_nonce'])) {
            $res = HttpHelper::get($urlBase . '/', [
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            ], $cookieFile);
            if ($res['code'] !== 200 || !$res['body'])
                return $this->resultado(false, [], 'No se pudo obtener el nonce. HTTP ' . $res['code']);
            if (preg_match('/"nonce"\s*:\s*"([a-f0-9]+)"/i', $res['body'], $m))
                $nonce = $m[1];
            elseif (preg_match('/nonce["\s:]+([a-f0-9]{10})/i', $res['body'], $m))
                $nonce = $m[1];
            if (!$nonce)
                return $this->resultado(false, [], 'No se pudo extraer el nonce.');
        }

        $endpoint = $urlBase . $cfg['endpoint']
            . '?' . $cfg['param_keyword'] . '=' . urlencode($query)
            . ($nonce ? '&nonce=' . urlencode($nonce) : '');

        $res2 = HttpHelper::get($endpoint, [
            'Accept: application/json, text/javascript, */*; q=0.01',
            'X-Requested-With: XMLHttpRequest',
            'Referer: ' . $urlBase . '/',
        ], $cookieFile);

        if ($res2['code'] !== 200 || !$res2['body'])
            return $this->resultado(false, [], 'Error en la API. HTTP ' . $res2['code']);

        $data = json_decode($res2['body'], true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data))
            return $this->resultado(false, [], 'Respuesta JSON inválida.');

        $items = [];
        foreach ($data as $item) {
            $img = $item['img'] ?? '';
            $items[] = [
                'titulo' => $item['title'] ?? '',
                'url'    => $item['url']   ?? '',
                'img'    => (strpos($img, 'dt_poster') !== false) ? '' : $img,
                'fecha'  => $item['extra']['date'] ?? '',
                'imdb'   => $item['extra']['imdb']  ?? false,
            ];
        }
        return $this->resultado(count($items) > 0, $items);
    }
}
