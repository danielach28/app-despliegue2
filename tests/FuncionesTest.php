<?php

use PHPUnit\Framework\TestCase;
use App\Funciones;

final class FuncionesTest extends TestCase
{
    public function testQuitarTildesConTextoConAcentos(): void
    {
        $texto = "áéíóú ÁÉÍÓÚ";
        $resultado = Funciones::quitarTildes($texto);
        $this->assertEquals("aeiou AEIOU", $resultado);
    }

    public function testQuitarTildesConTextoSinAcentos(): void
    {
        $texto = "hola mundo";
        $resultado = Funciones::quitarTildes($texto);
        $this->assertEquals("hola mundo", $resultado);
    }

    public function testCargarStopwordsArchivoNoExiste(): void
    {
        // Renombramos temporalmente el archivo para simular que no existe
        $rutaOriginal = __DIR__ . '/../stopwords.txt';
        $rutaTemporal = __DIR__ . '/../stopwords_backup.txt';

        if (file_exists($rutaOriginal)) {
            rename($rutaOriginal, $rutaTemporal);
        }

        $stopwords = Funciones::cargarStopwords();
        $this->assertIsArray($stopwords);
        $this->assertEmpty($stopwords);

        // Restauramos archivo
        if (file_exists($rutaTemporal)) {
            rename($rutaTemporal, $rutaOriginal);
        }
    }

    public function testProcesarTextoConStopwordsYMayusculas(): void
    {
        $stopwords = ['el' => true, 'y' => true];
        $texto = "El gato y el perro son amigos.";
        $resultado = Funciones::procesarTexto($texto, $stopwords);
        $this->assertArrayNotHasKey('el', $resultado);
        $this->assertArrayNotHasKey('y', $resultado);
        $this->assertArrayHasKey('gato', $resultado);
        $this->assertEquals(1, $resultado['gato']);
    }

    public function testAnalizarTextoFormularioConPost(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['texto'] = 'Hola mundo';

        [$texto, $resultado] = Funciones::analizarTextoFormulario();

        $this->assertEquals('Hola mundo', $texto);
        $this->assertIsArray($resultado);
        $this->assertArrayHasKey('hola', $resultado);
    }

    public function testAnalizarTextoFormularioSinPost(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_POST['texto'] = 'Hola mundo';

        [$texto, $resultado] = Funciones::analizarTextoFormulario();

        $this->assertEquals('', $texto);
        $this->assertEmpty($resultado);
    }
}
