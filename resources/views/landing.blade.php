@php
    $img = fn (string $n) => asset('images/kenali/'.$n.'.jpg').'?v=3';
    $guest = route('promise.guest');
    $wa = fn (string $text) => 'https://wa.me/'.$whatsapp.'?text='.rawurlencode($text);
    $areas = [
        ['Integrasi', 'Piagam proyek, portofolio & program, pengendalian perubahan terpadu (CCB), dan pelajaran saat penutupan.'],
        ['Ruang Lingkup', 'Pernyataan lingkup dan Work Breakdown Structure level 1 (deliverable) dan level 2 (paket kerja) beserta kamus WBS.'],
        ['Waktu', 'Gantt chart dengan baseline, milestone, garis tanggal status, dan keterkaitan FS, SS, FF, SF beserta lag/lead.'],
        ['Biaya', 'Anggaran per paket kerja dan Earned Value Management: PV, EV, AC, SPI, CPI, EAC, ETC, VAC, TCPI, kurva S.'],
        ['Mutu', 'Rencana inspeksi, pengujian, audit, dan FAT/SAT terhadap standar serta kriteria penerimaan.'],
        ['Sumber Daya Manusia', 'Kelompok sumber daya, penugasan tim, lembar waktu mingguan dengan persetujuan, peta kapasitas.'],
        ['Komunikasi', 'Register pemangku kepentingan, rencana komunikasi, laporan status bulanan merah-kuning-hijau.'],
        ['Risiko', 'Register risiko, matriks probabilitas × dampak 5×5, strategi respons ancaman/peluang, log isu.'],
        ['Pengadaan', 'Paket pengadaan, metode tender, jenis kontrak, vendor, progres dan pembayaran kontrak.'],
    ];
    $cases = \App\Support\DemoCompany::bypass(fn () => \App\Models\Project::with('portfolio')->orderBy('code')->get());
    $companyInfo = \App\Support\DemoCompany::COMPANIES;
    $caseGroups = $cases->groupBy(fn ($c) => isset($companyInfo[$c->company]) ? $companyInfo[$c->company]['sector'].' · '.$companyInfo[$c->company]['name'] : 'Proyek lain')
        ->sortBy(fn ($list, $group) => $group === 'Proyek lain' ? 'zz' : $group);
    $shots = [
        ['dasbor', 'Dasbor', 'Seluruh portofolio dalam satu layar', 'Kurva S, kuadran SPI × CPI, kesehatan proyek merah-kuning-hijau, dan peta panas risiko, diperbarui setiap kali tim memperbarui progres.', ['Proyek bermasalah langsung terlihat', 'Nilai portofolio dalam Rupiah', 'Filter per portofolio dan unit bisnis']],
        ['gantt', 'Gantt', 'Jadwal yang tahu urutan kerjanya', 'WBS level 1 dan 2 dengan baseline, milestone, dan panah keterkaitan Finish-to-Start, Start-to-Start, Finish-to-Finish, Start-to-Finish.', ['Lag dan lead dalam hari', 'Keterkaitan yang dilanggar ditandai merah', 'Keterlambatan terhadap baseline per tugas']],
        ['evm', 'Earned Value', 'Biaya dan jadwal dibaca bersama', 'PV dari baseline, EV dari progres, AC dari biaya aktual. SV, CV, SPI, CPI, EAC, ETC, VAC, dan TCPI dihitung otomatis per proyek dan portofolio.', ['Tanda bahaya sebelum anggaran habis', 'Perkiraan biaya akhir (EAC)', 'Perbandingan BAC, EAC, dan AC']],
        ['risiko', 'Risiko', 'Risiko dan isu tidak lagi tersimpan di notulen', 'Matriks probabilitas × dampak 5×5, strategi respons, pemilik risiko, dan log isu yang terhubung ke risikonya.', ['Risiko teratas per proyek', 'Strategi mitigasi, alihkan, hindari, terima', 'Isu dengan tenggat dan penanggung jawab']],
        ['dokumen', 'Dokumen', 'Semua berkas proyek di satu tempat', 'PDF, Word, Excel, PowerPoint, dan gambar dilampirkan ke proyek, tugas WBS, risiko, kontrak, atau laporan status, lengkap dengan nomor dokumen dan revisi.', ['Pratinjau PDF dan gambar langsung', 'Tersimpan privat', 'Filter per proyek, kategori, dan jenis berkas']],
        ['ruang-proyek', 'Ruang proyek', 'Satu halaman untuk setiap proyek', 'KPI Earned Value, piagam, WBS, jadwal, risiko, mutu, sumber daya, pengadaan, dan komunikasi tersusun per area pengetahuan.', ['Tab per area pengetahuan PMBOK', 'Manajer hanya mengubah proyeknya', 'Riwayat perubahan terpadu (CCB)']],
    ];
    $personas = [
        ['direksi', 'Direksi', 'Proyek mana yang perlu perhatian saya minggu ini?', 'Dasbor portofolio menandai proyek merah lebih dulu, lengkap dengan perkiraan biaya akhir dalam Rupiah.'],
        ['pmo', 'PMO', 'Bisakah laporan status bulanan selesai tanpa menagih berkas ke setiap proyek?', 'Progres, risiko, perubahan, dan laporan status semua proyek terkumpul otomatis di satu tempat.'],
        ['manajer', 'Manajer proyek', 'Paket kerja mana yang menggeser jadwal dan menguras anggaran?', 'Gantt dengan baseline dan Earned Value per paket kerja menunjukkannya sebelum rapat dimulai.'],
        ['tim', 'Tim lapangan', 'Di mana saya mengisi jam kerja dan menyimpan foto serta dokumen?', 'Lembar waktu mingguan dan unggah berkas ke tugas WBS, bahkan dari ponsel.'],
    ];
    $rag = ['hijau' => '#2E7D4F', 'kuning' => '#D98E04', 'merah' => '#D32F2F'];
@endphp
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Promise · Sistem Informasi Manajemen Proyek</title>
    <meta name="description" content="Promise (Project Management Information System): ketahui proyek yang terlambat dan boros lebih awal. Portofolio, WBS, Gantt, Earned Value, risiko, dan dokumen proyek sesuai PMBOK, dalam Bahasa Indonesia dan Rupiah. Coba data demo tanpa akun.">
    <meta property="og:title" content="Promise · Sistem Informasi Manajemen Proyek">
    <meta property="og:description" content="Ketahui proyek mana yang terlambat dan boros, sebelum terlambat.">
    <meta property="og:image" content="{{ $img('dasbor') }}">
    <link rel="icon" href="{{ asset('images/favicon.svg') }}" type="image/svg+xml">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}?v=9">
</head>
<body>
<header class="l-top">
    <div class="l-wrap l-top-in">
        <a href="{{ url('/') }}"><picture><source srcset="{{ asset('images/promise-logo-dark.svg') }}?v=2" media="(prefers-color-scheme: dark)"><img src="{{ asset('images/promise-logo.svg') }}?v=2" alt="Promise" class="l-logo"></picture></a>
        <nav class="l-nav" aria-label="Navigasi utama">
            <a href="#masalah">Kenapa perlu</a>
            <a href="#tampilan">Tampilan</a>
            <a href="#peran">Untuk siapa</a>
            <a href="#coba">Coba demo</a>
            <a href="#tanya">Tanya jawab</a>
            <a href="{{ route('promise.workshop') }}" class="l-nav-hl">Memanfaatkan Promise</a>
        </nav>
        <a href="{{ route('promise.workshop') }}" class="l-btn l-btn-sm l-btn-amber l-only-m">Memanfaatkan Promise</a>
        <a href="{{ url('/admin/login') }}" class="l-btn l-btn-sm">Masuk</a>
    </div>
</header>

<main>
    {{-- Pembuka: kait perhatian --}}
    <section class="l-hero">
        <div class="l-wrap l-hero-in">
            <div>
                <span class="l-eyebrow">Sistem informasi manajemen proyek</span>
                <h1>Tahu proyek mana yang terlambat dan boros, <em>sebelum terlambat.</em></h1>
                <p class="l-lead">Promise menyatukan jadwal, biaya, risiko, dan dokumen seluruh proyek perusahaan Anda. Direksi melihat portofolio, PMO melihat polanya, manajer proyek melihat paket kerja yang bermasalah, semuanya dari data yang sama.</p>
                <div class="l-actions">
                    <a href="#coba" class="l-btn l-btn-amber">Coba sekarang tanpa akun</a>
                    <a href="#tampilan" class="l-btn">Lihat tampilannya</a>
                </div>
                <ul class="l-trust">
                    <li>Mengacu PMBOK® Guide</li>
                    <li>Bahasa Indonesia</li>
                    <li>Rupiah</li>
                    <li>Bisa di server sendiri</li>
                </ul>
            </div>
            <div class="l-hero-vis">
                <figure class="l-browser">
                    <div class="l-browser-bar" aria-hidden="true"><i></i><i></i><i></i><span>promise / dasbor portofolio</span></div>
                    <img src="{{ $img('dasbor') }}" alt="Tangkapan layar dasbor portofolio Promise" width="1440" height="900" loading="eager">
                </figure>
                <figure class="l-phone">
                    <img src="{{ $img('ponsel') }}" alt="Tangkapan layar Promise di ponsel" width="780" height="1688" loading="eager">
                </figure>
            </div>
        </div>
        <div class="l-wrap">
            <div class="l-warn"><span><b>{{ \App\Support\DemoData::TITLE }}</b> Proyek yang ditampilkan adalah contoh untuk keperluan pembelajaran, bukan data resmi perusahaan. <a href="#data-demo">Selengkapnya</a></span></div>
        </div>
    </section>

    {{-- Minat: masalah yang dikenali, lalu cara Promise menjawabnya --}}
    <section class="l-section" id="masalah">
        <div class="l-wrap">
            <span class="l-eyebrow">Terdengar akrab?</span>
            <h2>Laporan proyek yang terlambat membuat keputusan ikut terlambat.</h2>
            <div class="l-grid l-grid-3">
                <div class="l-card"><span class="l-ic">1</span><h3>Data tersebar</h3><p>Jadwal di satu berkas, biaya di berkas lain, risiko di notulen rapat, dokumen di surel. Satu laporan status butuh berhari-hari.</p></div>
                <div class="l-card"><span class="l-ic">2</span><h3>Masalah ketahuan belakangan</h3><p>Proyek tampak "80% selesai" padahal biaya sudah melampaui rencana. Angka SPI dan CPI sebenarnya sudah memberi tanda sejak bulan-bulan awal.</p></div>
                <div class="l-card"><span class="l-ic">3</span><h3>Pengetahuan hilang</h3><p>Pelajaran proyek, gambar teknik, dan kontrak tidak terkumpul, sehingga proyek berikutnya mengulang kesalahan yang sama.</p></div>
            </div>
        </div>
    </section>

    <section class="l-section l-alt" id="tampilan">
        <div class="l-wrap">
            <span class="l-eyebrow">Tampilan nyata aplikasi</span>
            <h2>Begini rasanya bila semua proyek ada di satu tempat.</h2>
            <p class="l-lead">Tangkapan layar di bawah diambil langsung dari Promise dengan data demo.</p>
            <div class="l-gal" data-gal>
                <div class="l-gal-tabs" role="tablist" aria-label="Tangkapan layar">
                    @foreach ($shots as $i => [$key, $tab])
                        <button type="button" role="tab" id="tab-{{ $key }}" aria-controls="gal-{{ $key }}" aria-selected="{{ $i === 0 ? 'true' : 'false' }}" @if ($i) tabindex="-1" @endif>{{ $tab }}</button>
                    @endforeach
                </div>
                @foreach ($shots as $i => [$key, $tab, $title, $desc, $points])
                    <div class="l-gal-panel" role="tabpanel" id="gal-{{ $key }}" aria-labelledby="tab-{{ $key }}" @if ($i) hidden @endif>
                        <figure class="l-browser">
                            <div class="l-browser-bar" aria-hidden="true"><i></i><i></i><i></i><span>promise / {{ strtolower($tab) }}</span></div>
                            <img src="{{ $img($key) }}" alt="Tangkapan layar {{ $tab }} di Promise" loading="lazy">
                        </figure>
                        <div class="l-gal-text">
                            <h3>{{ $title }}</h3>
                            <p>{{ $desc }}</p>
                            <ul class="l-ticks">@foreach ($points as $pt)<li>{{ $pt }}</li>@endforeach</ul>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="l-section" id="pmbok">
        <div class="l-wrap">
            <span class="l-eyebrow">Mengacu PMBOK® Guide</span>
            <h2>Disusun mengikuti cara kerja manajemen proyek yang diakui dunia.</h2>
            <ol class="l-phases">
                <li><b>Inisiasi</b>Piagam proyek, sponsor, pemangku kepentingan.</li>
                <li><b>Perencanaan</b>WBS, baseline jadwal & biaya, risiko, pengadaan.</li>
                <li><b>Eksekusi</b>Penugasan tim, lembar waktu, kontrak, mutu.</li>
                <li><b>Pemantauan & Pengendalian</b>EVM, perubahan terpadu, isu, laporan status.</li>
                <li><b>Penutupan</b>Serah terima, pelajaran, arsip dokumen.</li>
            </ol>
            <div class="l-areas">
                @foreach ($areas as [$t, $d])
                    <details class="l-area"><summary>{{ $t }}</summary><p>{{ $d }}</p></details>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Keinginan: orang-orangnya, manfaatnya, buktinya --}}
    <section class="l-section l-alt" id="peran">
        <div class="l-wrap">
            <span class="l-eyebrow">Untuk setiap orang di proyek</span>
            <h2>Setiap orang mendapat jawaban yang dibutuhkannya, tanpa menunggu rapat.</h2>
            <div class="l-grid l-grid-4">
                @foreach ($personas as [$who, $role, $q, $a])
                    <article class="l-persona">
                        <x-promise.persona :who="$who" />
                        <span class="l-chip">{{ $role }}</span>
                        <p class="l-persona-q">“{{ $q }}”</p>
                        <p class="l-persona-a">{{ $a }}</p>
                    </article>
                @endforeach
            </div>
            <div class="l-gains">
                <div><b>Laporan dalam hitungan menit</b><span>Laporan status merah-kuning-hijau tersusun dari data yang sudah ada.</span></div>
                <div><b>Peringatan lebih awal</b><span>SPI dan CPI memberi tanda sebelum anggaran dan jadwal benar-benar meleset.</span></div>
                <div><b>Satu sumber data</b><span>Direksi, PMO, dan tim membaca angka yang sama, tidak ada lagi versi berkas yang berbeda.</span></div>
                <div><b>Pengetahuan tersimpan</b><span>Dokumen, keputusan perubahan, dan pelajaran proyek terarsip untuk proyek berikutnya.</span></div>
            </div>
        </div>
    </section>

    <section class="l-section" id="data-demo">
        <div class="l-wrap">
            <span class="l-eyebrow">Sudah berisi contoh nyata</span>
            <h2>{{ $cases->count() }} proyek dari tiga sektor sudah menunggu untuk dijelajahi.</h2>
            <div class="l-stats">
                <div class="l-stat"><b>{{ $stats['projects'] }}</b><span>proyek contoh</span></div>
                <div class="l-stat"><b>{{ number_format($stats['wbs'], 0, ',', '.') }}</b><span>elemen WBS</span></div>
                <div class="l-stat"><b>{{ $stats['risks'] }}</b><span>risiko tercatat</span></div>
                <div class="l-stat"><b>{{ \App\Support\Fmt::money($stats['budget']) }}</b><span>nilai portofolio</span></div>
            </div>
            <p class="l-lead" style="margin-top:1rem">Nama dan gambaran proyek disarikan dari Laporan Tahunan 2025 PT Medco Energi Internasional Tbk (migas, pembangkit, tambang), PT Bank SMBC Indonesia Tbk (core banking, AI generatif, cloud, keamanan siber) dan PT Pakuwon Jati Tbk (superblok, mal, hotel, hunian), khusus untuk pembelajaran.</p>
            @foreach ($caseGroups as $group => $list)
                <details class="l-casebox" @if ($loop->first) open @endif>
                    <summary class="l-cases-h">{{ $group }} <small>({{ $list->count() }} proyek)</small></summary>
                    <div class="l-cases">
                        @foreach ($list as $c)
                            <div class="l-case"><i style="background: {{ $rag[$c->health()] ?? '#64748B' }}"></i><div><b>{{ $c->name }}</b><small>{{ $c->code }} · {{ \App\Support\Pmbok::PHASES[$c->phase] ?? $c->phase }} · {{ $c->location }}</small></div></div>
                        @endforeach
                    </div>
                </details>
            @endforeach
        </div>
    </section>

    <section class="l-workshop" aria-label="Workshop">
        <div class="l-wrap l-workshop-in">
            <div>
                <span class="l-eyebrow">Workshop 2 hari · Project Management for Executive</span>
                <h2>Ingin tim Anda langsung bisa memakainya?</h2>
                <p>Kerjasama dengan Pusat Studi Manajemen Perguruan Tinggi UIN Sunan Kalijaga Jogjakarta. Termasuk akses uji coba Promise selama 3 bulan. Tahap penerapan: workshop pengenalan (2 hari), workshop persiapan implementasi in-house (2 hari), instalasi dan pendampingan (6 bulan).</p>
            </div>
            <span class="w-logo w-logo-sm"><img src="{{ asset('images/mitra/uin-sunan-kalijaga.png') }}" alt="Logo UIN Sunan Kalijaga Yogyakarta" width="200" height="247"></span>
            <a href="{{ route('promise.workshop') }}" class="l-btn l-btn-amber">Lihat materi workshop</a>
        </div>
    </section>

    {{-- Tindakan: coba sekarang --}}
    <section class="l-section l-alt l-try" id="coba">
        <div class="l-wrap">
            <span class="l-eyebrow">Gratis, tanpa akun, tanpa instalasi</span>
            <h2>Lihat sendiri dalam dua klik.</h2>
            @if (count(\App\Support\DemoCompany::available()))
                <p class="l-pick">1. Pilih perusahaan &nbsp;·&nbsp; 2. Masuk sebagai Tamu</p>
                <x-promise.demo-cards variant="landing" />
            @else
                <div class="l-actions"><a href="{{ $guest }}" class="l-btn l-btn-amber">Masuk sebagai Tamu</a></div>
            @endif
            <div class="l-warn"><span><b>{{ \App\Support\DemoData::TITLE }}</b> {{ \App\Support\DemoData::NOTICE }} Mode Tamu hanya melihat, data tidak bisa diubah.</span></div>
        </div>
    </section>

    <section class="l-section" id="tanya">
        <div class="l-wrap">
            <span class="l-eyebrow">Tanya jawab</span>
            <h2>Pertanyaan yang sering diajukan</h2>
            <details class="l-faq"><summary>Apakah Promise bisa dipasang di server sendiri?</summary><p>Bisa. Promise berbasis Laravel dan MySQL/MariaDB, dapat dipasang di hosting cPanel biasa (PHP 8.3) atau server internal.</p></details>
            <details class="l-faq"><summary>Bagaimana progres dan Earned Value dihitung?</summary><p>Manajer proyek memperbarui % selesai dan biaya aktual tiap paket kerja WBS level 2. PV dihitung dari baseline jadwal dan anggaran pada tanggal status; EV = anggaran × % selesai.</p></details>
            <details class="l-faq"><summary>Jenis berkas apa yang bisa diunggah?</summary><p>PDF, Word (DOC/DOCX), Excel (XLS/XLSX/CSV), PowerPoint (PPT/PPTX), PNG, JPG, dan WEBP, maksimal 20 MB per berkas.</p></details>
            <details class="l-faq"><summary>Apakah sesuai PMBOK edisi terbaru?</summary><p>Struktur modul mengikuti lima kelompok proses dan sembilan area pengetahuan yang lazim dipakai di PMBOK, dan tetap selaras dengan prinsip serta domain kinerja pada edisi terbaru.</p></details>
            <details class="l-faq"><summary>Bagaimana memulainya di perusahaan kami?</summary><p>Mulai dari workshop pengenalan 2 hari, lanjut workshop persiapan implementasi in-house 2 hari, lalu instalasi dan pendampingan 6 bulan. <a href="{{ route('promise.workshop') }}">Lihat rinciannya</a>.</p></details>
            <div class="l-cta" style="margin-top:2rem">
                <div><h2>Siap membawa proyek Anda ke satu layar?</h2><p>Hubungi kami untuk demo bersama, workshop, instalasi, dan penyesuaian alur kerja PMO.</p></div>
                <div class="l-actions">
                    <a class="l-btn l-btn-amber" href="{{ $wa('Halo Rucisoft, saya tertarik dengan Promise (Project Management Information System).') }}" target="_blank" rel="noopener">Hubungi via WhatsApp</a>
                    <a class="l-btn" style="border-color:#fff;color:#fff" href="{{ route('promise.workshop') }}">Memanfaatkan Promise</a>
                </div>
            </div>
        </div>
    </section>
</main>
<footer class="l-foot">
    <div class="l-wrap"><b>Promise</b> · Project Management Information System · © {{ date('Y') }} Rucisoft. {{ \App\Support\DemoData::SHORT }} PMBOK adalah merek terdaftar Project Management Institute, Inc.</div>
</footer>
<script>
document.querySelectorAll('[data-gal]').forEach(function (g) {
    var tabs = Array.prototype.slice.call(g.querySelectorAll('[role=tab]'));
    function show(t, focus) {
        tabs.forEach(function (x) {
            var on = x === t;
            x.setAttribute('aria-selected', on ? 'true' : 'false');
            x.tabIndex = on ? 0 : -1;
            document.getElementById(x.getAttribute('aria-controls')).hidden = !on;
        });
        if (focus) t.focus();
    }
    tabs.forEach(function (t, i) {
        t.addEventListener('click', function () { show(t); });
        t.addEventListener('keydown', function (e) {
            var d = e.key === 'ArrowRight' ? 1 : e.key === 'ArrowLeft' ? -1 : 0;
            if (d) { e.preventDefault(); show(tabs[(i + d + tabs.length) % tabs.length], true); }
        });
    });
});
</script>
</body>
</html>
