<link rel="stylesheet" href="<?= base_url('css/listado.css') ?>">

<div id="alert"></div>
<h2>Listado de Averías Pendientes</h2>

<table id="tablaAverias" border="1">
  <tr>
    <th>ID</th>
    <th>Cliente</th>
    <th>Problema</th>
    <th>Fecha y Hora</th>
    <th>Status</th>
  </tr>
  <?php foreach ($averias as $a): ?>
    <tr>
      <td><?= $a['id'] ?></td>
      <td><?= $a['cliente'] ?></td>
      <td><?= $a['problema'] ?></td>
      <td><?= $a['fechahora'] ?></td>
      <td><?= $a['status'] ?></td>
    </tr>
  <?php endforeach; ?>
</table>