<?php

namespace App\Controllers;

use App\Models\AveriaModel;
use App\Libraries\Notify;
use CodeIgniter\Controller;

class Averias extends Controller
{
  protected $averiaModel;

  public function __construct()
  {
    $this->averiaModel = new AveriaModel();
  }

  /**
   * Listar todas las averías pendientes
   */
  public function index()
  {
    $data['averias'] = $this->averiaModel
      ->where('status', 'pendiente')
      ->findAll();

    return view('averias/listar', $data);
  }

  /**
   * Mostrar el formulario para registrar una nueva avería
   */
  public function registrar()
  {
    return view('averias/registrar');
  }

  /**
   * Guardar una nueva avería
   */
  public function guardar()
  {
    // Validar datos
    if (
      !$this->validate([
        'cliente' => 'required|max_length[50]',
        'problema' => 'required|max_length[100]'
      ])
    ) {
      return redirect()->back()
        ->withInput()
        ->with('errors', $this->validator->getErrors());
    }

    // Preparar datos de la nueva avería
    $averiaData = [
      'cliente' => $this->request->getPost('cliente'),
      'problema' => $this->request->getPost('problema'),
      'fechahora' => date('Y-m-d H:i:s'),
      'status' => 'pendiente'
    ];

    // Insertar en la base de datos usando el modelo
    $this->averiaModel->insert($averiaData);

    // Enviar notificación al WebSocket
    Notify::send([
      'type' => 'new_averia',
      'averia' => $averiaData
    ]);

    return redirect()->to('/averias')
      ->with('message', 'Avería registrada correctamente');
  }

  /**
   * API JSON para obtener averías pendientes
   */
  public function json()
  {
    $averias = $this->averiaModel
      ->where('status', 'pendiente')
      ->findAll();

    return $this->response->setJSON($averias);
  }
}
