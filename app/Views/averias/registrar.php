<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registrar</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
    crossorigin="anonymous"></script>

</head>

<body>

  <main class="container mt-3">
    <h4>Registro de averías</h4>
    <form action="" autocomplete="off" id="form-averias">
      <div class="card rounded-0">
        <div class="card-header">
          <strong>Complete el formulario</strong>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-12 mb-2">
              <div class="form-floating">
                <input type="text" class="form-control rounded-0" id="cliente" placeholder="Cliente" autofocus required>
                <label for="cliente" class="form-label">Cliente</label>
              </div>
            </div>

            <div class="col-md-12 mb-2">
              <div class="form-floating">
                <input type="text" class="form-control rounded-0" id="problema" placeholder="Problema" required>
                <label for="problema" class="form-label">Problema</label>
              </div>
            </div>

            <div class="col-md-12 mb-2">
              <div class="form-floating">
                <input type="datetime-local" class="form-control rounded-0" id="fechahora" placeholder="Fecha evento"
                  required>
                <label for="fechahora" class="form-label">Fecha evento</label>
              </div>
            </div>
          </div>
        </div>
        <div class="card-footer text-end">
          <button class="btn btn-sm rounded-0 btn-outline-secondary" type="reset">Nuevo</button>
          <button class="btn btn-sm rounded-0 btn-primary" type="submit">Guardar</button>
        </div>
      </div>
    </form>

    <div class="alert d-none mt-2" id="notificacion">
      <strong>Aviso</strong>
      <span id="comentario">No se pudo guardar el registro</span>
    </div>

  </main>

  <script>
    document.addEventListener("DOMContentLoaded", () => {

      let conn = null;

      const cliente = document.getElementById('cliente')
      const problema = document.getElementById('problema')
      const fechahora = document.getElementById('fechahora')
      const formulario = document.getElementById('form-averias')
      const notificacion = document.getElementById('notificacion')
      const comentario = document.getElementById('comentario')

      // ✅ CORRECCIÓN: function connect (NO connet)
      function connect() {
        //1. Objeto conexión
        conn = new WebSocket('ws://localhost:8080')

        //2. Apertura de conexión
        conn.onopen = function (e) {
          console.log("✓ Conexión establecida")
        }

        //3. Envío de mensaje
        conn.onmessage = function (e) {
          const data = JSON.parse(e.data)
          console.log(data)
        }

        //4. Cierre de comunicación
        conn.onclose = function (e) {
          console.log("✗ Conexión finalizada, reconectando...")
          setTimeout(connect, 3000) // ✅ Ahora sí existe la función
        }

        //5. Manejo de errores
        conn.onerror = function (e) {
          console.error("✗ Problemas en la conexión")
        }
      }

      function sendMessage(message) {
        if (conn && conn.readyState == WebSocket.OPEN) {
          const data = {
            message: message
          }
          conn.send(JSON.stringify(data))
          console.log("Mensaje enviado:", message)
        }
      } //sendMessage

      async function registrar() {
        const data = {
          cliente: cliente.value,
          problema: problema.value,
          fechahora: fechahora.value
        }

        try {
          const response = await fetch(`<?= base_url('public/api/averias/registrar') ?>`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
          })

          const result = await response.json()
          notificacion.classList.remove('d-none', 'alert-danger', 'alert-success')

          if (result.success) {
            sendMessage('nuevoregistro') // <<< COMUNICAMOS DEL ÉXITO POR SOCKET
            notificacion.classList.add('alert-success')
            comentario.textContent = 'Avería registrada correctamente'
            formulario.reset()
          } else {
            notificacion.classList.add('alert-danger')
            comentario.textContent = 'No se pudo completar el proceso'
          }
        } catch (error) {
          console.error('Error:', error)
          notificacion.classList.remove('d-none')
          notificacion.classList.add('alert-danger')
          comentario.textContent = 'Error de conexión con el servidor'
        }
      }

      formulario.addEventListener("submit", (event) => {
        event.preventDefault()

        if (confirm("¿Está seguro de guardar?")) {
          registrar()
        }
      })

      formulario.addEventListener("reset", (event) => {
        notificacion.classList.add('d-none')
        cliente.focus()
      })

      // ✅ CORRECCIÓN: connect() ahora está bien escrito
      connect()

    })
  </script>

</body>

</html>