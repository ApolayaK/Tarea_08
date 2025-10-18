<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Averías Solucionadas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .fila-entrando {
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            0% {
                transform: translateX(100%);
                opacity: 0;
            }

            100% {
                transform: translateX(0);
                opacity: 1;
            }
        }
    </style>
</head>

<body>
    <main class="container mt-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>✅ Averías Solucionadas</h4>
            <div>
                <span id="status-connection" class="badge bg-secondary">Desconectado</span>
                <a href="<?= base_url('averias') ?>" class="btn btn-warning btn-sm">
                    ⏳ Ver Pendientes
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
                    <th>Estado</th>
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
                    const response = await fetch('/public/api/averias/solucionados', {
                        method: 'GET'
                    })
                    const averias = await response.json()

                    cuerpoTabla.innerHTML = ''

                    if (averias.length > 0) {
                        averias.forEach(averia => {
                            const row = cuerpoTabla.insertRow()
                            row.classList.add('fila-entrando')
                            row.dataset.id = averia.id
                            row.insertCell().textContent = averia.id
                            row.insertCell().textContent = averia.cliente
                            row.insertCell().textContent = averia.problema
                            row.insertCell().textContent = averia.fechahora
                            row.insertCell().innerHTML = '<span class="badge bg-success">Solucionado</span>'
                        })
                    } else {
                        const row = cuerpoTabla.insertRow()
                        const cell = row.insertCell()
                        cell.colSpan = 5
                        cell.className = 'text-center text-muted'
                        cell.textContent = '📋 No hay averías solucionadas aún'
                    }
                } catch (error) {
                    console.error('Error al obtener datos:', error)
                }
            }

            obtenerDatos()
            connect()
        })
    </script>
</body>

</html>