<?php


$texto = '';
$resultado = [];

[$texto, $resultado] = Funciones::analizarTextoFormulario();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Analizador de Texto</title>
  <link rel="stylesheet" href="style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
  <h1>Analizador de Texto</h1>
  <form method="post">
    <div class="mb-3">
      <label for="texto" class="form-label">Texto a analizar:</label>
      <textarea id="texto" class="form-control" name="texto" rows="5"><?php echo htmlspecialchars($texto); ?></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Analizar</button>
    <button type="button" class="btn btn-secondary" onclick="document.querySelector('textarea').value = '';">Borrar</button>
  </form>

  <?php if ($_SERVER["REQUEST_METHOD"] === "POST") : ?>
    <h2 class="mt-4">Frecuencia de Palabras</h2>
    <?php if (!empty($resultado)) : ?>
      <table class="table table-striped table-bordered">
        <thead>
        <tr><th>Palabra</th><th>Frecuencia</th></tr>
        </thead>
        <tbody>
        <?php foreach ($resultado as $palabra => $frecuencia): ?>
          <tr>
            <td><?= htmlspecialchars($palabra) ?></td>
            <td><?= $frecuencia ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p>No se han encontrado palabras significativas.</p>
    <?php endif; ?>
  <?php endif; ?>
</div>
</body>
</html>
