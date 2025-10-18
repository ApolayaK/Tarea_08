<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Averías Pendientes</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .fila-saliendo {
      animation: slideOut 0.5s ease-out forwards;
    }

    @keyframes slideOut {
      0% {
        transform: translateX(0);
        opacity: 1;
      }

      100% {
        transform: translateX(100%);
        opacity: 0;
      }
    }
  </style>
</head>

<body>
  <main class="container mt-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4>🔧 Averías Pendientes</h4>
      <div>
        <span id="status-connection" class="badge bg-secondary">Desconectado</span>
        <a href="<?= base_url('averias/solucionados') ?>" class="btn btn-success btn-sm">
          ✓ Ver Solucionados
        </a>
        <a href="<?= base_url('averias/registrar') ?>" class="btn btn-primary btn-sm">
          ➕ Registrar
        </a>
      </div>
    </div>

    <table class="table table-sm table-striped table-bordered" id="tabla-averias">
      <colgroup>
        <col style="width: 8%;">
        <col style="width: 25%;">
        <col style="width: 37%;">
        <col style="width: 20%;">
        <col style="width: 10%;">
      </colgroup>
      <thead>
        <tr>
          <th>#</th>
          <th>Cliente</th>
          <th>Problema</th>
          <th>Fecha</th>
          <th>Acción</th>
        </tr>
      </thead>
      <tbody>
      </tbody>
    </table>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      let conn = null;
      const cuerpoTabla = document.querySelector('#tabla-averias tbody')
      const statusConnection = document.getElementById('status-connection')

      function connect() {
        conn = new WebSocket('ws://localhost:8080')

        conn.onopen = function (e) {
          console.log("✓ Conexión WebSocket establecida")
          statusConnection.textContent = 'Conectado'
          statusConnection.classList.remove('bg-secondary', 'bg-danger')
          statusConnection.classList.add('bg-success')
        }

        conn.onmessage = function (e) {
          const data = JSON.parse(e.data)
          console.log("Mensaje recibido:", data)

          if (data.message === "nuevoregistro") {
            console.log("Nueva avería detectada, actualizando tabla...")
            obtenerDatos()
          }

          if (data.message === "statusCambiado") {
            console.log("Status cambiado, actualizando tabla...")
            obtenerDatos()
          }
        }

        conn.onclose = function (e) {
          console.log("✗ Conexión finalizada, reconectando...")
          statusConnection.textContent = 'Reconectando...'
          statusConnection.classList.remove('bg-success')
          statusConnection.classList.add('bg-warning')
          setTimeout(connect, 3000)
        }

        conn.onerror = function (e) {
          console.error("✗ Error en la conexión WebSocket")
          statusConnection.textContent = 'Error'
          statusConnection.classList.remove('bg-success', 'bg-warning')
          statusConnection.classList.add('bg-danger')
        }
      }

      async function obtenerDatos() {
        try {
          const response = await fetch('/public/api/averias/listar', {
            method: 'GET'
          })
          const averias = await response.json()

          cuerpoTabla.innerHTML = ''

          if (averias.length > 0) {
            averias.forEach(averia => {
              const row = cuerpoTabla.insertRow()
              row.dataset.id = averia.id
              row.insertCell().textContent = averia.id
              row.insertCell().textContent = averia.cliente
              row.insertCell().textContent = averia.problema
              row.insertCell().textContent = averia.fechahora

              const cellAccion = row.insertCell()
              cellAccion.innerHTML = `
                <button class="btn btn-sm btn-success" onclick="resolverAveria(${averia.id})">
                  ✓ Resolver
                </button>
              `
            })
          } else {
            const row = cuerpoTabla.insertRow()
            const cell = row.insertCell()
            cell.colSpan = 5
            cell.className = 'text-center text-muted'
            cell.textContent = '✅ No hay averías pendientes'
          }
        } catch (error) {
          console.error('Error al obtener datos:', error)
        }
      }

      window.resolverAveria = async function (id) {
        if (!confirm('¿Marcar esta avería como resuelta?')) {
          return
        }

        try {
          const response = await fetch('/public/api/averias/cambiarStatus', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id, status: 'S' })
          })

          const result = await response.json()

          if (result.success) {
            // Animar la fila antes de eliminarla
            const fila = document.querySelector(`tr[data-id="${id}"]`)
            if (fila) {
              fila.classList.add('fila-saliendo')
              setTimeout(() => {
                // Notificar por WebSocket
                if (conn && conn.readyState === WebSocket.OPEN) {
                  conn.send(JSON.stringify({ message: 'statusCambiado' }))
                }
                obtenerDatos()
              }, 500)
            }
          } else {
            alert('Error al cambiar el status')
          }
        } catch (error) {
          console.error('Error:', error)
          alert('Error de conexión')
        }
      }

      obtenerDatos()
      connect()
    })
  </script>
</body>

</html>