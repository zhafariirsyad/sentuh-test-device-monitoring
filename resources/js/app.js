import './bootstrap'
import './echo'

const formatDateTime = (value) => {
    if (!value) {
        return '-'
    }

    const date = new Date(value)
    if (Number.isNaN(date.getTime())) {
        return '-'
    }

    return new Intl.DateTimeFormat('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false
    }).format(date)
}

function waitForEcho(callback) {
    if (window.Echo) {
        callback()
    } else {
        setTimeout(() => waitForEcho(callback), 50)
    }
}

waitForEcho(() => {
    window.Echo
        .channel('devices.status')
        .listen('.devices.status.updated', (payload) => {
            const statusEl = document.getElementById(`device-status-${payload.device_id}`)
            if (statusEl) {
                statusEl.textContent = payload.status.charAt(0).toUpperCase() + payload.status.slice(1)
                statusEl.classList.remove('online', 'offline')
                statusEl.classList.add(payload.status)
            }

            const lastSeenEl = document.getElementById(`device-last-seen-${payload.device_id}`)
            if (lastSeenEl) {
                lastSeenEl.textContent = formatDateTime(payload.last_seen_at)
                lastSeenEl.dateTime = payload.last_seen_at ?? ''
            }
        })
})
