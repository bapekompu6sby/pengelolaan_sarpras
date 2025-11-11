<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Daftar Pemesanan</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    :root{
      --card-radius: 18px;
      --hr-color: #eef2f7;
      --soft-border: #e9eef5;
      --ink: #0f172a;
      --muted:#5b6b7c;
    }
    .booking-card{
      border: 1px solid var(--soft-border);
      border-radius: var(--card-radius);
      background: #fff;
      box-shadow: 0 2px 8px rgba(15,23,42,.06);
      overflow: hidden;
    }
    .booking-head{
      padding: 22px 24px 12px;
    }
    .booking-title{
      font-size: 1.6rem;
      font-weight: 700;
      color: var(--ink);
      line-height: 1.3;
      margin: 0 0 10px;
    }
    .badge-pill{
      border-radius: 999px;
      padding: .35rem .75rem;
      font-weight: 600;
    }
    .badge-approve{ background:#d1fae5; color:#059669; }
    .chip-grey{ background:#eef2f7; color:#374151; }

    .hr-soft{ margin: 0; border:0; border-top:1px solid var(--hr-color); }
    .booking-body{ padding: 18px 24px; }

    .info-icon{
      width: 36px; height: 36px;
      border-radius: 10px;
      display:flex; align-items:center; justify-content:center;
      background:#f5f8fb; color:#6b7280; font-size: 18px;
      flex: none;
    }
    .label{ font-size:.9rem; color: var(--muted); margin-bottom:2px;}
    .val{ font-size:1rem; color:#111827; }

    .pic-panel{
      border:1px solid var(--soft-border);
      border-radius: 14px;
      padding:16px;
      background:#fbfdff;
    }
    .pic-title{ font-weight:700; margin:0; }
    .muted{ color: var(--muted); }
    .meta{ font-size:.85rem; color: var(--muted); }

    .see-more{ color:#0d6efd; text-decoration:none; font-weight:600; }
    .see-more:hover{ text-decoration:underline; }

    /* header area yang sudah kamu punya */
    .header-logo { display:flex; align-items:center; gap:1rem; }
    .header-logo img { width:44px; height:44px; }
    .header-logo h1 { font-size:1.5rem; font-weight:700; }
    .header-logo h1 span { font-size:1rem; color:#0d6efd; }
  </style>
</head>
<body class="bg-light">
@php
  use Carbon\Carbon;

  // Normalisasi agar bisa handle array atau object
  $items = collect($data ?? [])->map(function ($it) {
      return is_array($it) ? (object) $it : $it;
  });

  function rupiah($v) {
      if ($v === null || $v === '') return '—';
      return 'Rp ' . number_format((int)$v, 0, ',', '.');
  }

  function fmtDate($d) {
      if (!$d) return '—';
      return Carbon::parse($d)->translatedFormat('d M Y');
  }

  function fmtDateRange($start, $end) {
      if (!$start && !$end) return '—';
      if ($start && !$end) return fmtDate($start);
      if (!$start && $end)  return fmtDate($end);
      $s = Carbon::parse($start);
      $e = Carbon::parse($end);
      // Jika bulan sama → "11–14 Nov 2025", else → "29 Nov 2025 – 02 Des 2025"
      if ($s->month === $e->month && $s->year === $e->year) {
          return $s->format('d') . '–' . $e->translatedFormat('d M Y');
      }
      return $s->translatedFormat('d M Y') . ' – ' . $e->translatedFormat('d M Y');
  }

  function badgeStatusClass($status) {
      return match($status) {
          'approved'   => 'success',
          'pending'    => 'warning',
          'rejected'   => 'danger',
          default      => 'secondary',
      };
  }

  function chipAffiliation($aff) {
      return match($aff) {
          'external_pu' => 'Eksternal PUPR',
          'internal_pu' => 'Internal PUPR',
          default       => ucfirst(str_replace('_',' ', (string)$aff)),
      };
  }
@endphp

  <div class="container py-4">
    <div class="header-logo mb-4">
      <img src="{{ asset('/assets/img/favicon/logo.png') }}" alt="Logo PUPR" class="img-fluid">
      <h1>Topang<span>+</span></h1>
    </div>

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
      <h1 class="h4 mb-0">Daftar Pemesanan</h1>
      <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
          <i class="bi bi-printer"></i> Cetak
        </button>
      </div>
    </div>

    {{-- Toolbar filter (dummy UI, siap di-wire ke controller bila perlu) --}}
    <div class="card shadow-sm mb-4">
      <div class="card-body">
        <form class="row g-2">
          <div class="col-12 col-md-4">
            <label class="form-label">Cari Instansi / Kegiatan</label>
            <input type="text" class="form-control" placeholder="Ketik kata kunci..." />
          </div>
          <div class="col-6 col-md-3">
            <label class="form-label">Status</label>
            <select class="form-select">
              <option value="">Semua</option>
              <option>approved</option>
              <option>pending</option>
              <option>rejected</option>
            </select>
          </div>
          <div class="col-6 col-md-3">
            <label class="form-label">Rentang Tanggal</label>
            <input type="date" class="form-control" />
          </div>
          <div class="col-12 col-md-2 d-grid">
            <label class="form-label d-none d-md-block">&nbsp;</label>
            <button type="button" class="btn btn-primary">Terapkan</button>
          </div>
        </form>
      </div>
    </div>


  @forelse($items as $t)
    @php
      $jam = ($t->jam_start && $t->jam_end) ? ($t->jam_start.' — '.$t->jam_end) : 'Full day';
      $aff = chipAffiliation($t->affiliation ?? '');
      $status = $t->status ?? 'approved';
      $instansi = $t->instansi ?? '—';
      $room = $t->properties->name ?? ($t->room_name ?? '—');
      $unit = $t->ordered_unit ?? '—';
      $desc = trim((string)($t->description ?? '—'));
      $descShort = \Illuminate\Support\Str::limit($desc, 160);
      $created = $t->created_at ? Carbon::parse($t->created_at)->translatedFormat('d M Y') : '—';
      $updated = $t->updated_at ? Carbon::parse($t->updated_at)->translatedFormat('d M Y') : '—';
    @endphp

    <div class="booking-card mb-4">
      {{-- Header title + badges --}}
      <div class="booking-head">
        <h2 class="booking-title">{{ $t->kegiatan ?? '—' }}</h2>
        <div class="d-flex flex-wrap align-items-center gap-2">
          <span class="badge badge-pill badge-approve text-capitalize">{{ $status }}</span>
          <span class="badge badge-pill chip-grey">{{ $aff }}</span>
        </div>
      </div>

      <hr class="hr-soft">

      <div class="booking-body">
        <div class="row g-4">
          {{-- Left info list --}}
          <div class="col-12 col-lg-7">
            <div class="d-flex align-items-start gap-3 mb-3">
              <div class="info-icon"><i class="bi bi-calendar-event"></i></div>
              <div>
                <div class="label">Tanggal</div>
                <div class="val">{{ fmtDateRange($t->start ?? null, $t->end ?? null) }} ·
                  @if($t->start && $t->end)
                    {{ Carbon::parse($t->start)->diffInDays(Carbon::parse($t->end)) + 1 }} hari
                  @endif
                </div>
              </div>
            </div>

            <div class="d-flex align-items-start gap-3 mb-3">
              <div class="info-icon"><i class="bi bi-clock"></i></div>
              <div>
                <div class="label">Jam</div>
                <div class="val">{{ $jam }}</div>
              </div>
            </div>

            <div class="d-flex align-items-start gap-3 mb-3">
              <div class="info-icon"><i class="bi bi-building"></i></div>
              <div>
                <div class="label">Instansi</div>
                <div class="val">{{ $instansi }}</div>
              </div>
            </div>

            <div class="d-flex align-items-start gap-3 mb-3">
              <div class="info-icon"><i class="bi bi-geo-alt"></i></div>
              <div>
                <div class="label">Ruangan</div>
                <div class="val">{{ $room }} · Unit dipesan: {{ $unit }}</div>
              </div>
            </div>

            <div class="d-flex align-items-start gap-3">
              <div class="info-icon"><i class="bi bi-file-text"></i></div>
              <div>
                <div class="label">Deskripsi</div>
                <div class="val">
                  <span class="desc-short">{{ $descShort }}</span>
                  @if(strlen($desc) > strlen($descShort))
                    <span class="desc-full d-none">{{ $desc }}</span>
                    <a href="#" class="see-more ms-1" data-expand> Lihat lebih banyak</a>
                  @endif
                </div>
              </div>
            </div>
          </div>

          {{-- Right PIC panel --}}
          <div class="col-12 col-lg-5">
            <div class="pic-panel h-100">
              <p class="pic-title mb-1">Pemesan (PIC)</p>
              <div class="mb-3">{{ $t->name ?? '—' }}</div>

              <div class="d-flex align-items-start gap-3 mb-2">
                <div class="info-icon"><i class="bi bi-telephone"></i></div>
                <div>
                  <div class="label">Telepon</div>
                  <div class="val">
                    @if(!empty($t->phone_number))
                      <a href="tel:{{ $t->phone_number }}">{{ $t->phone_number }}</a>
                    @else
                      —
                    @endif
                  </div>
                </div>
              </div>

              <div class="d-flex align-items-start gap-3 mb-3">
                <div class="info-icon"><i class="bi bi-envelope"></i></div>
                <div>
                  <div class="label">Email</div>
                  <div class="val">
                    @if(!empty($t->email))
                      <a href="mailto:{{ $t->email }}">{{ $t->email }}</a>
                    @else
                      —
                    @endif
                  </div>
                </div>
              </div>

              <div class="meta">
                Dibuat: {{ $created }} · Diperbarui: {{ $updated }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  @empty
    <div class="alert alert-info">Belum ada data pemesanan.</div>
  @endforelse
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Toggle "Lihat lebih banyak"
  document.querySelectorAll('[data-expand]').forEach(function(btn){
    btn.addEventListener('click', function(e){
      e.preventDefault();
      const wrap = btn.closest('.val');
      const shortEl = wrap.querySelector('.desc-short');
      const fullEl  = wrap.querySelector('.desc-full');
      const expanded = fullEl.classList.toggle('d-none') === false;
      shortEl.classList.toggle('d-none', expanded);
      btn.textContent = expanded ? ' Sembunyikan' : ' Lihat lebih banyak';
    });
  });
</script>
</body>

</html>
