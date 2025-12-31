<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Device Monitor</title>
    @vite(['resources/js/app.js'])

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f7f7fb;
            color: #1f2937;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 960px;
            margin: 32px auto;
            padding: 0 16px;
        }
        h1 {
            margin-bottom: 4px;
            font-size: 28px;
        }
        .subtitle {
            margin-top: 0;
            color: #6b7280;
        }
        .table-wrapper {
            margin-top: 24px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.08);
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 16px;
            text-align: left;
        }
        thead {
            background: #111827;
            color: #fff;
        }
        .status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-weight: bold;
            text-transform: capitalize;
        }
        .status.online {
            color: #10b981;
        }
        .status.offline {
            color: #ef4444;
        }
        .last-seen {
            color: #4b5563;
        }
    </style>
</head>
<body>
    <main class="container">
        <h1>Realtime Device Monitor</h1>
        <p class="subtitle">Menggunakan Laravel Reverb (Websocket laravel).</p>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Perangkat</th>
                        <th>Serial</th>
                        <th>Status</th>
                        <th>Terakhir Terlihat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($devices as $device)
                        <tr>
                            <td>{{ $device->name }}</td>
                            <td>{{ $device->serial_number }}</td>
                            <td>
                                <span
                                    id="device-status-{{ $device->id }}"
                                    class="status {{ $device->status }}"
                                >
                                    {{ ucfirst($device->status) }}
                                </span>
                            </td>
                            <td>
                                <time
                                    id="device-last-seen-{{ $device->id }}"
                                    datetime="{{ $device->last_seen_at?->toISOString() }}"
                                    class="last-seen"
                                >
                                    {{ $device->last_seen_at?->format('d M Y H:i:s') ?? '-' }}
                                </time>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">Belum ada perangkat yang terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
