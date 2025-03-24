<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Lokasi</th>
            <th>Luas</th>
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
