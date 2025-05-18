<?php


class Funciones
{
    public static function cargarStopwords(): array
    {
        $archivo = __DIR__ . '/../stopwords.txt';
        $stopwords = [];

        if (file_exists($archivo)) {
            $contenido = mb_convert_encoding(file_get_contents($archivo), 'UTF-8', 'auto');
            $lineas = explode("\n", $contenido);
            foreach ($lineas as $linea) {
                $palabra = trim(mb_strtolower($linea));
                if ($palabra !== '') {
                    $stopwords[$palabra] = true;
                }
            }
        }

        return $stopwords;
    }
}