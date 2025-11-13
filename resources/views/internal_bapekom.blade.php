<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Peminjaman Sarpras Dan Kegiatan Pelatihan</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Google Fonts: Geist & Geist Mono -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Geist:wght@400;600;700&family=Geist+Mono:wght@400;600;700&display=swap" rel="stylesheet">

  <style>
    :root{
      /* Brand System */
      --primary: #0d6efd;           /* PUPR-ish Blue */
      --primary-700: #0b5ed7;
      --accent: #f4c93a;            /* Warm accent (PUPR yellow) */
      --success-soft: #d1fae5;
      --success-ink: #059669;
      --ink: #0f172a;
      --muted:#5b6b7c;
      --soft-border: #e9eef5;
      --hr-color: #eef2f7;
      --card-radius: 18px;
      /* Typography */
      --font-sans: 'Geist','Geist Fallback', ui-sans-serif, system-ui, -apple-system, 'Segoe UI', Roboto,'Helvetica Neue', Arial, 'Noto Sans', 'Liberation Sans', sans-serif;
      --font-mono: 'Geist Mono', ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace;
      --text-base: 16px;           /* Tailwind's base */
      --text-3xl: 30px;            /* h1 */
      --text-2xl: 24px;            /* h2 */
      --text-xl: 20px;             /* h3 */
      --text-sm: 14px;             /* small */
      --text-xs: 12px;             /* extra small */
    }

    /* App background */
    body{
      font-family: var(--font-sans);
      font-weight: 400; /* Regular */
      line-height: 1.625; /* leading-relaxed */
      background:
        radial-gradient(1200px 600px at -10% -10%, #eef4ff 0%, transparent 60%),
        radial-gradient(800px 500px at 120% -20%, #fff7db 0%, transparent 55%),
        #ffffff;
    }

    /* Header */
    .header-logo { display:flex; align-items:center; gap:1rem; }
    .header-logo img { width:44px; height:44px; }
    .header-logo h1 { font-weight:800; font-size: clamp(1.2rem, 1rem + 1.2vw, 1.6rem); margin:0; }
    .header-logo h1 span { font-size:1rem; color:var(--primary); }

    .page-title{ font-weight:700; color:var(--ink); font-size: var(--text-2xl); letter-spacing:-0.01em; }

    /* Toolbar card with subtle gradient */
    .toolbar-card{
      border-radius: 14px;
      border: 1px solid var(--soft-border);
      background: linear-gradient(180deg, #fbfdff 0%, #ffffff 100%);
      box-shadow: 0 2px 10px rgba(15,23,42,.06);
    }

    /* Search */
    .input-group .form-control{
      border-color: var(--soft-border);
    }
    .input-group .form-control:focus{ box-shadow: 0 0 0 .2rem rgba(13,110,253,.15); border-color: var(--primary); }

    /* Filter buttons */
    .btn-filter{
      border: 1px solid var(--soft-border);
      background: #ffffff;
      color: var(--ink);
      padding: .5rem 1rem;
      border-radius: 999px;
      font-weight: 600;
      transition: .2s ease;
    }
    .btn-filter:hover{ background: #f8fafc; }
    .btn-filter.active{
      background: var(--primary);
      color: #fff;
      border-color: var(--primary);
      box-shadow: 0 6px 16px rgba(13,110,253,.25);
    }
    .btn-filter:focus-visible{ outline: 3px solid rgba(13,110,253,.35); outline-offset: 2px; }

    /* Print button */
    .btn-ghost{
      border: 1px dashed var(--soft-border);
      color: var(--muted);
      background: #fff;
    }
    .btn-ghost:hover{ border-color: var(--primary); color: var(--primary); background: #f8fbff; }

    /* Card */
    .booking-card{
      border: 1px solid var(--soft-border);
      border-radius: var(--card-radius);
      background: #fff;
      box-shadow: 0 8px 24px rgba(15,23,42,.06);
      overflow: hidden;
      transition: transform .15s ease, box-shadow .15s ease;
    }
    .booking-card:hover{ transform: translateY(-2px); box-shadow: 0 12px 28px rgba(15,23,42,.1); }

    .booking-head{ padding: 22px 24px 12px; }
/* Title band gradient like sample */
    .title-band{ background: linear-gradient(90deg, #eef4ff 0%, #ffffff 70%); }
    .title-badges{ gap:.5rem; }
    .badge-pill{ border-radius: 999px; padding: .35rem .75rem; font-weight: 700; font-size:.8rem; }
    .badge-approve{ background:var(--success-soft); color:var(--success-ink); }
    .badge-state-ongoing{ background:#f4c93a; color:#eef4ff; }
    .badge-state-upcoming{ background:#ece9ff; color:#5b47d6; }
    .booking-title{ font-size: var(--text-xl); font-weight: 600; color: var(--ink); line-height: 1.3; margin: 0 0 6px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; overflow-wrap:anywhere; word-break: break-word; letter-spacing:-0.01em; }

    .badge-pill{ border-radius: 999px; padding: .35rem .75rem; font-weight: 700; font-size:.8rem; }
    .badge-approve{ background:var(--success-soft); color:var(--success-ink); }
    .chip-grey{ background:#eef2f7; color:#374151; }

    .hr-soft{ margin:0; border:0; border-top:1px solid var(--hr-color); }
    .booking-body{ padding: 18px 24px; }

    /* Info items */
    .info-icon{ width: 36px; height: 36px; border-radius: 10px; display:flex; align-items:center; justify-content:center; background: #f5f8fb; color:#4062a1; font-size: 18px; flex:none; }
    .label{ font-size:.82rem; color: var(--muted); margin-bottom:2px; }
    .val{ font-size:1rem; color:#111827; overflow-wrap:anywhere; word-break:break-word; }

    /* PIC panel with accent border */
    .pic-panel{
      border:1px solid var(--soft-border);
      border-radius: 14px;
      padding:16px;
      background:
        linear-gradient(180deg, #fbfdff 0%, #ffffff 100%);
    }
    .pic-title{ font-weight:600; margin:0; color: var(--ink); }

    .see-more{ color:var(--primary); text-decoration:none; font-weight:700; }
    .see-more:hover{ text-decoration:underline; }

    /* Mobile toolbar: filters wrap neatly */
    @media (max-width: 576.98px) {
      .filter-group .btn-filter{ flex: 1 1 auto; }
    }
    /* Global Headings & Text Sizes */
    h1,.h1{ font-size: var(--text-3xl); font-weight:700; letter-spacing:-0.01em; line-height:1.2; }
    h2,.h2{ font-size: var(--text-2xl); font-weight:700; letter-spacing:-0.01em; line-height:1.25; }
    h3,.h3{ font-size: var(--text-xl);  font-weight:600; letter-spacing:-0.01em; line-height:1.3; }
    p{ line-height: 1.5; }
    small,.small{ font-size: var(--text-sm); }
    .text-xs{ font-size: var(--text-xs); }
    .tracking-tight{ letter-spacing:-0.01em; }
    code,pre,kbd,samp,.font-mono{ font-family: var(--font-mono); }
  </style>
</head>
<body>
@php
  use Carbon\Carbon;
  $items = collect($data ?? [])->map(function ($it) { return is_array($it) ? (object) $it : $it; });
  function fmtDate($d) { if (!$d) return '—'; return Carbon::parse($d)->translatedFormat('d M Y'); }
  function fmtDateRange($start,$end){ if(!$start && !$end) return '—'; if($start && !$end) return fmtDate($start); if(!$start && $end) return fmtDate($end); $s=Carbon::parse($start); $e=Carbon::parse($end); if($s->month===$e->month && $s->year===$e->year){ return $s->format('d').'–'.$e->translatedFormat('d M Y'); } return $s->translatedFormat('d M Y').' – '.$e->translatedFormat('d M Y'); }
  function chipAffiliation($aff){ return match($aff){ 'external_pu'=>'Eksternal PU','internal_pu'=>'Internal PU', default => ucfirst(str_replace('_',' ', (string)$aff)), }; }
@endphp

  <div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-3">
      <div class="header-logo">
        <img src="{{ asset('/assets/img/favicon/logo.png') }}" alt="Logo PUPR" class="img-fluid">
        <h1>Topang<span>+</span></h1>
      </div>
      <button class="btn btn-ghost btn-sm" onclick="window.print()"><i class="bi bi-printer me-2"></i>Cetak</button>
    </div>

    <h2 class="page-title h3 mb-3 text-center" style="font-size: var(--text-3xl);">Peminjaman Sarpras Dan Kegiatan Pelatihan</h2>
    
    <!-- Toolbar: mobile stack, desktop inline -->
    <div class="card toolbar-card mb-4">
      <div class="card-body">
        <div class="row g-2 align-items-md-center">
          <div class="col-12 col-md">
            <div class="input-group">
              <input type="text" class="form-control" id="searchBar" placeholder="Cari kegiatan atau instansi..." oninput="searchData()">
              <span class="input-group-text d-none d-md-inline"><i class="bi bi-search"></i></span>
              <button class="btn btn-outline-secondary d-md-none" type="button" onclick="applyFilters()"><i class="bi bi-search"></i></button>
            </div>
          </div>
          <div class="col-12 col-md-auto">
            <div class="d-flex flex-wrap gap-2 filter-group">
              <button class="btn btn-filter active" onclick="activateFilter(this, 'all')">All</button>
              <button class="btn btn-filter" onclick="activateFilter(this, 'today')">Today</button>
              <button class="btn btn-filter" onclick="activateFilter(this, 'upcoming')">Upcoming</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- GRID KARTU -->
    <div id="grid" class="row row-cols-1 row-cols-lg-2 g-4">
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
          // timeline badge: Ongoing if today within range, else Upcoming
          $today = Carbon::today();
          $startC = !empty($t->start) ? Carbon::parse($t->start) : null;
          $endC   = !empty($t->end)   ? Carbon::parse($t->end)   : $startC;
          $ongoing = $startC && $endC ? ($startC->lte($today) && $endC->gte($today)) : false;
          $timelineLabel = $ongoing ? 'Ongoing' : 'Upcoming';
          $timelineClass = $ongoing ? 'badge-state-ongoing' : 'badge-state-upcoming';
        @endphp

        <div class="col">
          <div class="booking-card h-100" data-start="{{ $t->start }}" data-end="{{ $t->end }}">
            <div class="booking-head title-band">
            <h2 class="booking-title">{{ $t->kegiatan ?? '—' }}</h2>
            <div class="d-flex flex-wrap align-items-center gap-2 mt-1 title-meta">
              <span class="badge badge-pill {{ $timelineClass }}">{{ $timelineLabel }}</span>
              <span class="badge badge-pill badge-approve">{{ ucfirst($status) }}</span>
              <span class="badge badge-pill chip-grey">{{ $aff }}</span>
            </div>
          </div>
          <hr class="hr-soft">
            <div class="booking-body">
              <div class="row g-4">
                <!-- Row 1: Tanggal, Jam -->
                <div class="col-12 col-lg-6">
                  <div class="d-flex align-items-start gap-3">
                    <div class="info-icon"><i class="bi bi-calendar-event"></i></div>
                    <div>
                      <div class="label">Tanggal</div>
                      <div class="val">{{ fmtDateRange($t->start ?? null, $t->end ?? null) }} @if($t->start && $t->end) · {{ Carbon::parse($t->start)->diffInDays(Carbon::parse($t->end)) + 1 }} hari @endif</div>
                    </div>
                  </div>
                </div>
                <div class="col-12 col-lg-6">
                  <div class="d-flex align-items-start gap-3">
                    <div class="info-icon"><i class="bi bi-clock"></i></div>
                    <div>
                      <div class="label">Jam</div>
                      <div class="val">{{ $jam }}</div>
                    </div>
                  </div>
                </div>

                <!-- Row 2: Instansi, Ruangan -->
                <div class="col-12 col-lg-6">
                  <div class="d-flex align-items-start gap-3">
                    <div class="info-icon"><i class="bi bi-building"></i></div>
                    <div>
                      <div class="label">Instansi</div>
                      <div class="val">{{ $instansi }}</div>
                    </div>
                  </div>
                </div>
                <div class="col-12 col-lg-6">
                  <div class="d-flex align-items-start gap-3">
                    <div class="info-icon"><i class="bi bi-geo-alt"></i></div>
                    <div>
                      <div class="label">Ruangan</div>
                      <div class="val">{{ $room }} · Unit dipesan: {{ $unit }}</div>
                    </div>
                  </div>
                </div>

                <!-- Row 3: Deskripsi (full width) -->
                <div class="col-12">
                  <div class="d-flex align-items-start gap-3">
                    <div class="info-icon"><i class="bi bi-file-text"></i></div>
                    <div>
                      <div class="label">Deskripsi</div>
                      <div class="val" data-desc>
                        <span class="desc-short">{{ $descShort }}</span>
                        @if(strlen($desc) > strlen($descShort))
                          <span class="desc-full d-none">{{ $desc }}</span>
                          <a href="#" class="see-more ms-1" data-expand>Lihat lebih banyak</a>
                        @endif
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Row 4: Pemesan (full width) -->
                <div class="col-12">
                  <p class="pic-title mb-1">Pemesan (PIC)</p>
                  <div class="val">{{ $t->name ?? '—' }}</div>
                </div>

                <!-- Row 5: Telepon, Email -->
                <div class="col-12 col-lg-6">
                  <div class="d-flex align-items-start gap-3">
                    <div class="info-icon"><i class="bi bi-telephone"></i></div>
                    <div>
                      <div class="label">Telepon</div>
                      <div class="val">@if(!empty($t->phone_number))<a href="tel:{{ $t->phone_number }}">{{ $t->phone_number }}</a>@else — @endif</div>
                    </div>
                  </div>
                </div>
                <div class="col-12 col-lg-6">
                  <div class="d-flex align-items-start gap-3">
                    <div class="info-icon"><i class="bi bi-envelope"></i></div>
                    <div>
                      <div class="label">Email</div>
                      <div class="val">@if(!empty($t->email))<a href="mailto:{{ $t->email }}">{{ $t->email }}</a>@else — @endif</div>
                    </div>
                  </div>
               </div>
              </div>
            </div>
          </div>
        </div>
      @empty
        <div class="col">
          <div class="alert alert-info mb-0">Belum ada data pemesanan.</div>
        </div>
      @endforelse

      <!-- Empty state for filtered results -->
      <div id="emptyState" class="col-12 d-none">
        <div class="text-center text-muted py-5">
          <i class="bi bi-search" style="font-size:2rem"></i>
          <p class="mt-2 mb-0">Tidak ada hasil yang cocok dengan filter/pencarian.</p>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Toggle "Lihat lebih banyak"
    document.addEventListener('click', function (e) {
      const btn = e.target.closest('[data-expand]');
      if (!btn) return;
      e.preventDefault();
      const wrap = btn.closest('[data-desc]');
      if (!wrap) return;
      const shortEl = wrap.querySelector('.desc-short');
      const fullEl  = wrap.querySelector('.desc-full');
      const isExpanded = wrap.classList.toggle('is-expanded');
      if (shortEl) shortEl.classList.toggle('d-none', isExpanded);
      if (fullEl)  fullEl.classList.toggle('d-none', !isExpanded);
      btn.textContent = isExpanded ? 'Sembunyikan' : 'Lihat lebih banyak';
    });

    // ====== GRID FILTERING ======
    let currentRange = 'all';

    function setVisibility(card, visible) {
      const col = card.closest('.col') || card.parentElement;
      if (!col) return;
      col.classList.toggle('d-none', !visible);
    }

    // Normalisasi string tanggal ke format YYYY-MM-DD saja
    function normalizeDateStr(value) {
      if (!value) return '';
      return value.toString().slice(0, 10); // ambil hanya bagian YYYY-MM-DD
    }

    // range cek berbasis tanggal murni (bukan jam)
    function inRange(range, startStr, endStr, todayStr) {
      if (range === 'all') return true;

      const s = startStr || endStr;
      const e = endStr || startStr;

      if (!s && !e) return false;

      if (range === 'today') {
        // tampilkan semua kegiatan yang tanggalnya overlap dengan hari ini
        // (start <= today <= end) dengan end bersifat inklusif
        return (s <= todayStr && e >= todayStr);
      }

      if (range === 'upcoming') {
        // kegiatan yang baru akan dimulai setelah hari ini
        return s > todayStr;
      }

      return true;
    }

    function applyFilters() {
      const q = (document.getElementById('searchBar')?.value || '').toLowerCase().trim();

      // todayStr: YYYY-MM-DD
      const todayStr = new Date().toISOString().slice(0, 10);

      let visibleCount = 0;

      document.querySelectorAll('.booking-card').forEach(card => {
        const startStr = normalizeDateStr(card.dataset.start || '');
        const endStr   = normalizeDateStr(card.dataset.end || card.dataset.start || '');

        const title = card.querySelector('.booking-title')?.textContent.toLowerCase() || '';
        const bodyText = card.querySelector('.booking-body')?.textContent.toLowerCase() || '';

        const matchRange = inRange(currentRange, startStr, endStr, todayStr);
        const matchSearch = !q || title.includes(q) || bodyText.includes(q);
        const show = matchRange && matchSearch;

        setVisibility(card, show);
        if (show) visibleCount++;
      });

      document.getElementById('emptyState')?.classList.toggle('d-none', visibleCount !== 0);
    }

    function activateFilter(button, range) {
      document.querySelectorAll('.btn-filter').forEach(btn => btn.classList.remove('active'));
      button.classList.add('active');
      currentRange = range;
      applyFilters();
    }

    function searchData() { applyFilters(); }

    document.addEventListener('DOMContentLoaded', applyFilters);
  </script>
</body>

</html>
