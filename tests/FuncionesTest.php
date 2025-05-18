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
}