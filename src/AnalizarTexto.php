<?php

namespace App;

class AnalizarTexto
{
    public function cargar_stopwords(): array
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

    public function quitar_tildes(string $texto): string
    {
        $originales = ['á', 'é', 'í', 'ó', 'ú', 'Á', 'É', 'Í', 'Ó', 'Ú'];
        $sin_tilde = ['a', 'e', 'i', 'o', 'u', 'A', 'E', 'I', 'O', 'U'];
        return str_replace($originales, $sin_tilde, $texto);
    }

    public function limpiar_texto(string $texto): string
    {
        $texto = mb_convert_encoding($texto, 'UTF-8', 'auto');
        $texto = mb_strtolower($texto);
        $texto = $this->quitar_tildes($texto);
        $texto = preg_replace('/[^a-zñü\s]/iu', ' ', $texto); // solo letras
        $texto = preg_replace('/\s+/', ' ', $texto); // normalizar espacios múltiples
        return trim($texto); // quitar espacios al principio y al final
    }

    public function contar_palabras(string $texto, array $stopwords): array
    {
        $palabras_raw = explode(' ', $texto);
        $frecuencias = [];

        foreach ($palabras_raw as $palabra) {
            $palabra = trim($palabra);
            if ($palabra !== '' && !isset($stopwords[$palabra])) {
                $frecuencias[$palabra] = ($frecuencias[$palabra] ?? 0) + 1;
            }
        }

        arsort($frecuencias);
        return $frecuencias;
    }

    public function procesar_texto(string $texto, array $stopwords): array
    {
        $texto_limpio = $this->limpiar_texto($texto);
        return $this->contar_palabras($texto_limpio, $stopwords);
    }

    public function analizar_texto_formulario(): array
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['texto'])) {
            $texto = $_POST['texto'];
            $stopwords = $this->cargar_stopwords();
            $resultado = $this->procesar_texto($texto, $stopwords);
            return [$texto, $resultado];
        }

        return ['', []];
    }
}
