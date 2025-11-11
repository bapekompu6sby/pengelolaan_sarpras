<table class="table table-bordered table-hover align-middle text-center mb-0">
    <thead class="table-light">
        <tr>
            <th class="text-start">Properti</th>
            <th>Jan</th>
            <th>Feb</th>
            <th>Mar</th>
            <th>Apr</th>
            <th>Mei</th>
            <th>Jun</th>
            <th>Jul</th>
            <th>Agu</th>
            <th>Sep</th>
            <th>Okt</th>
            <th>Nov</th>
            <th>Des</th>
            <th class="bg-light fw-bold">Total</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($rekapBulanan as $r)
            <tr>
                <td class="text-start fw-semibold">{{ $r->properti }}</td>
                <td>{{ $r->jan }}</td>
                <td>{{ $r->feb }}</td>
                <td>{{ $r->mar }}</td>
                <td>{{ $r->apr }}</td>
                <td>{{ $r->mei }}</td>
                <td>{{ $r->jun }}</td>
                <td>{{ $r->jul }}</td>
                <td>{{ $r->agu }}</td>
                <td>{{ $r->sep }}</td>
                <td>{{ $r->okt }}</td>
                <td>{{ $r->nov }}</td>
                <td>{{ $r->des }}</td>
                <td class="fw-bold bg-light text-primary">{{ $r->total }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="14" class="text-muted py-3">Tidak ada data untuk tahun ini.</td>
            </tr>
        @endforelse
    </tbody>
</table>
