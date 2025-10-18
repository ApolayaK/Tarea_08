<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AveriaModel;

class Averias extends BaseController
{
  public function index()
  {
    return view('averias/listar');
  }

  public function registrar()
  {
    return view('averias/registrar');
  }

  // Nueva vista para solucionados
  public function solucionados()
  {
    return view('averias/solucionados');
  }

  public function agregarRegistro()
  {
    $averia = new AveriaModel();
    $this->response->setContentType('application/json');
    $data = $this->request->getJSON();

    $newRecord = [
      'cliente' => $data->cliente,
      'problema' => $data->problema,
      'fechahora' => $data->fechahora,
      'status' => 'P'
    ];

    try {
      $averia->insert($newRecord);

      return $this->response->setJSON([
        'success' => true,
        'id' => $averia->getInsertID()
      ]);
    } catch (\Exception $e) {
      return $this->response->setJSON([
        'success' => false,
        'message' => $e->getMessage()
      ]);
    }
  }

  public function listarAverias()
  {
    $averia = new AveriaModel();
    $this->response->setContentType('application/json');

    try {
      $rows = $averia->where('status', 'P')
        ->orderBy('id', 'DESC')
        ->findAll();

      return $this->response->setJSON($rows);
    } catch (\Exception $e) {
      return $this->response->setJSON([
        'success' => false,
        'message' => $e->getMessage()
      ]);
    }
  }

  // Nueva función para listar solucionados
  public function listarSolucionados()
  {
    $averia = new AveriaModel();
    $this->response->setContentType('application/json');

    try {
      $rows = $averia->where('status', 'S')
        ->orderBy('id', 'DESC')
        ->findAll();

      return $this->response->setJSON($rows);
    } catch (\Exception $e) {
      return $this->response->setJSON([
        'success' => false,
        'message' => $e->getMessage()
      ]);
    }
  }

  // Nueva función para cambiar el status
  public function cambiarStatus()
  {
    $averia = new AveriaModel();
    $this->response->setContentType('application/json');
    $data = $this->request->getJSON();

    try {
      $averia->update($data->id, [
        'status' => $data->status
      ]);

      return $this->response->setJSON([
        'success' => true,
        'message' => 'Status actualizado'
      ]);
    } catch (\Exception $e) {
      return $this->response->setJSON([
        'success' => false,
        'message' => $e->getMessage()
      ]);
    }
  }
}