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


     public static function quitarTildes(string $texto): string
    {
        $originales = ['á','é','í','ó','ú','Á','É','Í','Ó','Ú'];
        $sinTilde   = ['a','e','i','o','u','A','E','I','O','U'];
        return str_replace($originales, $sinTilde, $texto);
    }

     public static function procesarTexto(string $texto, array $stopwords): array
    {
        $texto = mb_convert_encoding($texto, 'UTF-8', 'auto');
        $texto = mb_strtolower($texto);
        $texto = self::quitarTildes($texto);
        $texto = preg_replace('/[^a-zñü\s]/iu', ' ', $texto); // solo letras, sin puntuación ni números
        $palabrasRaw = explode(' ', $texto);

        $frecuencias = [];

        foreach ($palabrasRaw as $palabra) {
            $palabra = trim($palabra);
            if ($palabra !== '' && !isset($stopwords[$palabra])) {
                if (!isset($frecuencias[$palabra])) {
                    $frecuencias[$palabra] = 1;
                } else {
                    $frecuencias[$palabra]++;
                }
            }
        }

        arsort($frecuencias);
        return $frecuencias;
    }  
}