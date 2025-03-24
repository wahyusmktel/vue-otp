<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Export Data Tanah - PDF</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #444;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #eee;
        }
        h2 {
            text-align: center;
        }
    </style>
</head>
<body>
    <h2>Data Tanah SMK Telkom Lampung</h2>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Lokasi</th>
                <th>Luas (m²)</th>
                <th>Status Tanah</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($dataTanah as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->nama_lokasi }}</td>
                    <td>{{ $item->luas }}</td>
                    <td>{{ $item->status_tanah }}</td>
                    <td>{{ $item->keterangan }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
