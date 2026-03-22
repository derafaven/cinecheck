<?php
require_once __DIR__ . '/../helpers/HttpHelper.php';
require_once __DIR__ . '/BaseAdaptador.php';

class ScraperAdaptador extends BaseAdaptador {
    public function buscar(string $query): array {
        $urlBase  = rtrim($this->sitio['url_base'], '/');
        $cfg      = $this->sitio['config'];
        $url      = $urlBase . str_replace('{keyword}', urlencode($query), $cfg['endpoint']);

        $res = HttpHelper::get($url, [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Referer: ' . $urlBase . '/',
        ]);
        if ($res['code'] !== 200 || !$res['body'])
            return $this->resultado(false, [], 'HTTP ' . $res['code']);

        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadHTML(mb_convert_encoding($res['body'], 'HTML-ENTITIES', 'UTF-8'));
        libxml_clear_errors();
        $xpath = new DOMXPath($dom);
        $nodos = $xpath->query('//article | //div[contains(@class,"item")] | //div[contains(@class,"movie")]');
        $items = []; $seen = [];

        foreach ($nodos as $nodo) {
            $tNode  = $xpath->query('.//h2|.//h3|.//h1', $nodo)->item(0);
            $titulo = $tNode ? trim($tNode->textContent) : '';
            if (strlen($titulo) < 2) continue;
            $k = strtolower($titulo);
            if (isset($seen[$k])) continue;
            $seen[$k] = true;
            $aNode  = $xpath->query('.//a[@href]', $nodo)->item(0);
            $enlace = $aNode ? $aNode->getAttribute('href') : $url;
            $iNode  = $xpath->query('.//img', $nodo)->item(0);
            $img    = $iNode ? ($iNode->getAttribute('src') ?: $iNode->getAttribute('data-src')) : '';
            $items[] = ['titulo' => $titulo, 'url' => $enlace, 'img' => $img, 'fecha' => '', 'imdb' => false];
        }
        return $this->resultado(count($items) > 0, array_slice($items, 0, 20));
    }
}
