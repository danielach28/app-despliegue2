<?php

use PHPUnit\Framework\TestCase;
use App\AnalizarTexto;

final class AnalizarTextoTest extends TestCase
{
    public function test_quitar_tildes_con_acentos(): void
    {
        $texto = "áéíóú ÁÉÍÓÚ";
        $analizador = new AnalizarTexto();
        $resultado = $analizador->quitar_tildes($texto);
        $this->assertEquals("aeiou AEIOU", $resultado);
    }

    public function test_quitar_tildes_sin_acentos(): void
    {
        $texto = "hola mundo";
        $analizador = new AnalizarTexto();
        $resultado = $analizador->quitar_tildes($texto);
        $this->assertEquals("hola mundo", $resultado);
    }

    public function test_cargar_stopwords_archivo_no_existe(): void
    {
        $ruta_original = __DIR__ . '/../stopwords.txt';
        $ruta_temporal = __DIR__ . '/../stopwords_backup.txt';

        if (file_exists($ruta_original)) {
            rename($ruta_original, $ruta_temporal);
        }

        $analizador = new AnalizarTexto();
        $stopwords = $analizador->cargar_stopwords();
        $this->assertIsArray($stopwords);
        $this->assertEmpty($stopwords);

        if (file_exists($ruta_temporal)) {
            rename($ruta_temporal, $ruta_original);
        }
    }

    public function test_limpiar_texto_normaliza_caracteres(): void
    {
        $analizador = new AnalizarTexto();
        $texto = "¡Hola, mundo! ¿Qué tal? Ñandú";
        $esperado = "hola mundo que tal ñandu";
        $resultado = $analizador->limpiar_texto($texto);
        $this->assertEquals($esperado, $resultado);
    }

    public function test_contar_palabras_ignora_stopwords(): void
    {
        $analizador = new AnalizarTexto();
        $texto = "el gato y el perro son amigos";
        $stopwords = ['el' => true, 'y' => true];
        $resultado = $analizador->contar_palabras($texto, $stopwords);

        $this->assertArrayNotHasKey('el', $resultado);
        $this->assertArrayNotHasKey('y', $resultado);
        $this->assertArrayHasKey('gato', $resultado);
        $this->assertEquals(1, $resultado['gato']);
    }

    public function test_procesar_texto_con_mock(): void
    {
        $mock = $this->getMockBuilder(AnalizarTexto::class)
                     ->onlyMethods(['limpiar_texto'])
                     ->getMock();

        $mock->expects($this->once())
             ->method('limpiar_texto')
             ->with("El gÁto y el perro son amigos.")
             ->willReturn("el gato y el perro son amigos");

        $stopwords = ['el' => true, 'y' => true];
        $resultado = $mock->procesar_texto("El gÁto y el perro son amigos.", $stopwords);

        $this->assertArrayHasKey('gato', $resultado);
        $this->assertArrayNotHasKey('el', $resultado);
    }

    public function test_analizar_texto_formulario_con_post(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['texto'] = 'Hola mundo';

        $analizador = new AnalizarTexto();
        [$texto, $resultado] = $analizador->analizar_texto_formulario();

        $this->assertEquals('Hola mundo', $texto);
        $this->assertIsArray($resultado);
        $this->assertArrayHasKey('hola', $resultado);
    }

    public function test_analizar_texto_formulario_sin_post(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_POST['texto'] = 'Hola mundo';

        $analizador = new AnalizarTexto();
        [$texto, $resultado] = $analizador->analizar_texto_formulario();

        $this->assertEquals('', $texto);
        $this->assertEmpty($resultado);
    }
}
