<?php
abstract class BaseAdaptador {
    protected array $sitio;
    public function __construct(array $sitio) { $this->sitio = $sitio; }
    abstract public function buscar(string $query): array;
    protected function resultado(bool $encontrada, array $items, string $error = ''): array {
        return [
            'id'         => $this->sitio['id'],
            'nombre'     => $this->sitio['nombre'],
            'url_base'   => $this->sitio['url_base'],
            'encontrada' => $encontrada,
            'total'      => count($items),
            'items'      => $items,
            'error'      => $error,
        ];
    }
}
