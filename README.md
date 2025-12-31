# Sentuh-Challenge Realtime Device Monitor

This project developed using Laravel 12 + Reverb that keeps track of device availability (online / offline) in real-time.

## Features
- Simple device CRUD with `name`, `serial_number`, `status` and `last_seen_at` columns.
- REST endpoints for devices to `connect`, `ping`, and `disconnect`.
- Broadcasts `DeviceStatusUpdated` events over Laravel Reverb and Laravel Echo.
- Automatic offline detection when a heartbeat timeout occurs (no scheduler needed—uses delayed queue jobs).
- Live status dashboard at `/` that receives updates without hot reload.

### Required environment variables
`BROADCAST_CONNECTION` is pre-configured for Reverb. Adjust these if necessary:

```
DEVICE_TIMEOUT_MINUTES=2 // Adjustable value
REVERB_APP_ID=152620
REVERB_APP_KEY=zoracxfsj2w5vz26j6ja
REVERB_APP_SECRET=uxgj0v4npiue9nui5ezn
REVERB_HOST="realtime-device-monitoring.test"
REVERB_PORT=8080
REVERB_SCHEME=https
VITE_REVERB_* mirrors the same values
```

### Local services (Manual Via Terminal)

Run these processes in separate terminals (or a process manager such as `npm run dev` + `php artisan reverb:start`):

```bash
# 1. Run Laravel HTTP server
php artisan serve

# 2. Start Reverb WebSocket server
php artisan reverb:start

# 3. Queue worker (required for auto update the device status)
php artisan queue:work

# 4. Vite build for assets/Echo client
npm run dev
```

Now open `https://realtime-device-monitoring.test` to see the dashboard.

## Device API

Hardware or simulators can push their state via HTTP:

```bash
# Device boots
curl -X POST https://realtime-device-monitoring.test/api/devices/connect -d serial_number=SN-001

# Heartbeat (keeps it online + updates last_seen_at)
curl -X POST https://realtime-device-monitoring.test/api/devices/ping -d serial_number=SN-001

# Graceful shutdown
curl -X POST https://realtime-device-monitoring.test/api/devices/disconnect -d serial_number=SN-001
```

Every call updates the database and fires a `DeviceStatusUpdated` event.
The dashboard immediately reflects the change, so operators can see device uptime in real-time without refresh the browser.

### CLI heartbeat simulator (Instead of manual cURL)
Run:

```bash
php artisan device:simulate-heartbeat SN-001 --name="Demo Device" --interval=15 --count=20 --disconnect
```

Flags:
- `--interval` seconds between pings (default 30).
- `--count` number of pings to send (0 = infinite).
- `--disconnect` will call the disconnect endpoint at the end.
- Provide `--name` once to auto-create the device if it doesn’t exist.

## Demo Video
[Google Drive](https://drive.google.com/drive/folders/1rldroOQgWn4gi_YTf8e8WZYPsSeTK_1v?usp=sharing)