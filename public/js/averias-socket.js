async function cargarAverias() {
  const res = await fetch('/averias/json');
  const data = await res.json();

  const tabla = document.getElementById('tablaAverias');
  tabla.querySelectorAll('tr:not(:first-child)').forEach(r => r.remove());

  data.forEach(a => {
    const fila = document.createElement('tr');
    fila.innerHTML = `
          <td>${a.id}</td>
          <td>${a.cliente}</td>
          <td>${a.problema}</td>
          <td>${a.fechahora}</td>
          <td>${a.status}</td>
    `;
    fila.style.opacity = 0;
    tabla.appendChild(fila);
    setTimeout(() => fila.style.opacity = 1, 50);
  });
}

// Recarga cada 3 segundos (backup si WS falla)
setInterval(cargarAverias, 3000);

// Notificación flash PHP
const notify = <?= json_encode(session()->getFlashdata("notify")) ?>;
if (notify) {
  const alertDiv = document.getElementById('alert');
  const mensaje = notify.averia?.cliente ? `Nueva avería para: ${notify.averia.cliente}` : notify;
  alertDiv.innerHTML = `<p>${mensaje}</p>`;
  setTimeout(() => alertDiv.innerHTML = '', 4000);
}

// --- WebSocket para actualización en tiempo real ---
let ws = new WebSocket('ws://localhost:8080');

ws.onopen = () => console.log("Conectado al WebSocket");
ws.onmessage = (event) => {
  const data = JSON.parse(event.data);

  if(data.type === 'new_averia') {
    const tabla = document.getElementById('tablaAverias');
    const fila = document.createElement('tr');
    fila.innerHTML = `
          <td>${data.averia.id || '-'}</td>
          <td>${data.averia.cliente}</td>
          <td>${data.averia.problema}</td>
          <td>${data.averia.fechahora}</td>
          <td>${data.averia.status}</td>
    `;
    fila.style.opacity = 0;
    tabla.appendChild(fila);
    setTimeout(() => fila.style.opacity = 1, 50);

    const alertDiv = document.getElementById('alert');
    alertDiv.innerHTML = `<p>Nueva avería para: ${data.averia.cliente}</p>`;
    setTimeout(() => alertDiv.innerHTML = '', 4000);
  }
};

ws.onclose = () => console.log("Desconectado del WebSocket");
ws.onerror = (err) => console.error("Error WebSocket:", err);
