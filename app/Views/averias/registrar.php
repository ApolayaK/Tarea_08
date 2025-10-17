<link rel="stylesheet" href="<?= base_url('css/registrar.css') ?>">

<div class="form-container">
  <h2>Registrar Nueva Avería</h2>
  <form action="<?= base_url('/averias/guardar') ?>" method="POST">
    <label for="cliente">Cliente:</label>
    <input type="text" name="cliente" id="cliente" required>

    <label for="problema">Problema:</label>
    <textarea name="problema" id="problema" rows="4" required></textarea>

    <button type="submit">Registrar</button>
  </form>
</div>