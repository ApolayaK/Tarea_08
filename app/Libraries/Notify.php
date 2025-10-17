<?php

namespace App\Libraries;

class Notify
{
  public static function send($message)
  {
    // Guardamos la notificación en sesión para que el frontend pueda leerla
    session()->setFlashdata('notify', $message);
  }
}
