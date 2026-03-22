<?php
class HttpHelper {
    public static function get(string $url, array $headers = [], string $cookieFile = ''): array {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_ENCODING       => '',
            CURLOPT_HTTPHEADER     => array_merge([
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0',
                'Accept-Language: es-MX,es;q=0.9,en-US;q=0.8',
                'Referer: https://google.com/',
            ], $headers),
        ]);
        if ($cookieFile) {
            curl_setopt($ch, CURLOPT_COOKIEJAR,  $cookieFile);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
        }
        $body = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err  = curl_error($ch);
        curl_close($ch);
        return ['body' => $body, 'code' => $code, 'error' => $err];
    }
}
