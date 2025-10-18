/**
 * Funciones comunes para el sistema de Averías
 * Manejo de WebSocket, notificaciones y animaciones
 */

class AveriasWebSocket {
  constructor() {
    this.conn = null
    this.reconnectTimeout = null
    this.statusElement = document.getElementById('status-connection')
    this.callbacks = {
      onOpen: [],
      onMessage: [],
      onClose: [],
      onError: []
    }
  }

  connect() {
    if (this.conn && this.conn.readyState === WebSocket.OPEN) {
      console.log('⚠ Ya existe una conexión activa')
      return
    }

    console.log('🔌 Intentando conectar al WebSocket...')
    this.conn = new WebSocket('ws://localhost:8080')

    this.conn.onopen = (e) => {
      console.log('✓ Conexión WebSocket establecida')
      this.updateStatus('connected', 'Conectado')
      clearTimeout(this.reconnectTimeout)
      this.callbacks.onOpen.forEach(cb => cb(e))
    }

    this.conn.onmessage = (e) => {
      try {
        const data = JSON.parse(e.data)
        console.log('📨 Mensaje recibido:', data)
        this.callbacks.onMessage.forEach(cb => cb(data))
      } catch (error) {
        console.error('❌ Error al parsear mensaje:', error)
      }
    }

    this.conn.onclose = (e) => {
      console.log('✗ Conexión finalizada')
      this.updateStatus('reconnecting', 'Reconectando...')
      this.callbacks.onClose.forEach(cb => cb(e))
      
      // Reconectar después de 3 segundos
      this.reconnectTimeout = setTimeout(() => this.connect(), 3000)
    }

    this.conn.onerror = (e) => {
      console.error('✗ Error en la conexión WebSocket')
      this.updateStatus('disconnected', 'Error')
      this.callbacks.onError.forEach(cb => cb(e))
    }
  }

  updateStatus(type, text) {
    if (!this.statusElement) return

    this.statusElement.className = 'status-badge'
    
    switch(type) {
      case 'connected':
        this.statusElement.classList.add('connected')
        this.statusElement.innerHTML = `<span>🟢</span> ${text}`
        break
      case 'reconnecting':
        this.statusElement.classList.add('reconnecting')
        this.statusElement.innerHTML = `<span class="spinner"></span> ${text}`
        break
      case 'disconnected':
      default:
        this.statusElement.classList.add('disconnected')
        this.statusElement.innerHTML = `<span>🔴</span> ${text}`
        break
    }
  }

  send(message) {
    if (this.conn && this.conn.readyState === WebSocket.OPEN) {
      const data = typeof message === 'string' ? message : JSON.stringify(message)
      this.conn.send(data)
      console.log('📤 Mensaje enviado:', message)
      return true
    } else {
      console.warn('⚠ WebSocket no está conectado')
      return false
    }
  }

  on(event, callback) {
    if (this.callbacks[event]) {
      this.callbacks[event].push(callback)
    }
  }

  disconnect() {
    if (this.conn) {
      this.conn.close()
      this.conn = null
    }
    clearTimeout(this.reconnectTimeout)
  }
}

/**
 * Sistema de notificaciones toast
 */
class NotificationSystem {
  constructor() {
    this.container = this.createContainer()
  }

  createContainer() {
    let container = document.getElementById('notification-container')
    if (!container) {
      container = document.createElement('div')
      container.id = 'notification-container'
      container.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        max-width: 400px;
      `
      document.body.appendChild(container)
    }
    return container
  }

  show(message, type = 'info', duration = 3000) {
    const notification = document.createElement('div')
    notification.className = `notification notification-${type} notification-slide`
    
    const icons = {
      success: '✓',
      error: '✗',
      warning: '⚠',
      info: 'ℹ'
    }

    const colors = {
      success: 'linear-gradient(135deg, #10B981 0%, #059669 100%)',
      error: 'linear-gradient(135deg, #EF4444 0%, #DC2626 100%)',
      warning: 'linear-gradient(135deg, #F59E0B 0%, #D97706 100%)',
      info: 'linear-gradient(135deg, #3B82F6 0%, #2563EB 100%)'
    }

    notification.style.cssText = `
      background: ${colors[type] || colors.info};
      color: white;
      padding: 1rem 1.5rem;
      border-radius: 12px;
      margin-bottom: 1rem;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
      display: flex;
      align-items: center;
      gap: 0.75rem;
      font-weight: 600;
      cursor: pointer;
    `

    notification.innerHTML = `
      <span style="font-size: 1.5rem;">${icons[type] || icons.info}</span>
      <span>${message}</span>
    `

    this.container.appendChild(notification)

    // Auto-eliminar
    setTimeout(() => {
      notification.classList.add('fade-out')
      setTimeout(() => {
        if (notification.parentNode) {
          notification.parentNode.removeChild(notification)
        }
      }, 400)
    }, duration)

    // Eliminar al hacer clic
    notification.addEventListener('click', () => {
      notification.classList.add('fade-out')
      setTimeout(() => {
        if (notification.parentNode) {
          notification.parentNode.removeChild(notification)
        }
      }, 400)
    })
  }

  success(message, duration) {
    this.show(message, 'success', duration)
  }

  error(message, duration) {
    this.show(message, 'error', duration)
  }

  warning(message, duration) {
    this.show(message, 'warning', duration)
  }

  info(message, duration) {
    this.show(message, 'info', duration)
  }
}

/**
 * Utilidades de animación
 */
const AnimationUtils = {
  // Animar salida de fila
  animateRowOut(row, direction = 'right') {
    return new Promise((resolve) => {
      row.classList.add(direction === 'right' ? 'slide-out-right' : 'slide-out-left')
      setTimeout(() => {
        resolve()
      }, 500)
    })
  },

  // Animar entrada de fila
  animateRowIn(row, direction = 'right') {
    row.classList.add(direction === 'right' ? 'slide-in-right' : 'slide-in-left')
  },

  // Highlight temporal
  highlight(element) {
    element.classList.add('highlight')
    setTimeout(() => {
      element.classList.remove('highlight')
    }, 1000)
  },

  // Flash de éxito
  successFlash(element) {
    element.classList.add('success-flash')
    setTimeout(() => {
      element.classList.remove('success-flash')
    }, 600)
  },

  // Shake de error
  errorShake(element) {
    element.classList.add('error-shake')
    setTimeout(() => {
      element.classList.remove('error-shake')
    }, 500)
  }
}

/**
 * Formateador de fechas
 */
const DateFormatter = {
  toLocal(dateString) {
    const date = new Date(dateString)
    return date.toLocaleString('es-PE', {
      year: 'numeric',
      month: '2-digit',
      day: '2-digit',
      hour: '2-digit',
      minute: '2-digit'
    })
  },

  toRelative(dateString) {
    const date = new Date(dateString)
    const now = new Date()
    const diff = now - date
    const seconds = Math.floor(diff / 1000)
    const minutes = Math.floor(seconds / 60)
    const hours = Math.floor(minutes / 60)
    const days = Math.floor(hours / 24)

    if (days > 0) return `Hace ${days} día${days > 1 ? 's' : ''}`
    if (hours > 0) return `Hace ${hours} hora${hours > 1 ? 's' : ''}`
    if (minutes > 0) return `Hace ${minutes} minuto${minutes > 1 ? 's' : ''}`
    return 'Hace un momento'
  }
}

/**
 * Inicializar sistema global
 */
window.AveriasWebSocket = AveriasWebSocket
window.NotificationSystem = NotificationSystem
window.AnimationUtils = AnimationUtils
window.DateFormatter = DateFormatter

// Instancia global de notificaciones
window.notify = new NotificationSystem()

console.log('✓ Sistema de Averías cargado correctamente')