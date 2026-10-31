@php
    $img = fn (string $n) => asset('images/kenali/'.$n.'.jpg').'?v=1';
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
    $rag = ['hijau' => '#2E7D4F', 'kuning' => '#D98E04', 'merah' => '#D32F2F'];
@endphp
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Promise · Sistem Informasi Manajemen Proyek</title>
    <meta name="description" content="Promise (Project Management Information System): manajemen portofolio dan proyek sesuai PMBOK. WBS, Gantt chart FS/SS/FF/SF, Earned Value, risiko, sumber daya, lembar waktu, pengadaan, dan pusat dokumen. Coba data demo tanpa akun.">
    <meta property="og:title" content="Promise · Sistem Informasi Manajemen Proyek">
    <meta property="og:description" content="Portofolio, jadwal, biaya, risiko, dan dokumen proyek dalam satu sistem berbasis PMBOK.">
    <meta property="og:image" content="{{ $img('dasbor') }}">
    <link rel="icon" href="{{ asset('images/favicon.svg') }}" type="image/svg+xml">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}?v=3">
</head>
<body>
<header class="l-top">
    <div class="l-wrap l-top-in">
        <a href="{{ url('/') }}"><picture><source srcset="{{ asset('images/promise-logo-dark.svg') }}" media="(prefers-color-scheme: dark)"><img src="{{ asset('images/promise-logo.svg') }}" alt="Promise" class="l-logo"></picture></a>
        <nav class="l-nav" aria-label="Navigasi utama">
            <a href="#masalah">Kenapa perlu</a>
            <a href="#pmbok">PMBOK</a>
            <a href="#fitur">Fitur</a>
            <a href="#data-demo">Data demo</a>
            <a href="#tanya">Tanya jawab</a>
        </nav>
        <a href="{{ url('/admin/login') }}" class="l-btn l-btn-sm">Masuk</a>
    </div>
</header>

<main>
    <section class="l-hero">
        <div class="l-wrap l-hero-in">
            <div>
                <span class="l-eyebrow">Untuk PMO, manajer proyek, dan direksi</span>
                <h1>Semua proyek, satu sumber kebenaran.</h1>
                <p class="l-lead"><b>Promise</b> (Project Management Information System) mengelola portofolio dan proyek mengikuti <b>PMBOK® Guide</b> dari Project Management Institute: piagam, WBS, jadwal Gantt dengan keterkaitan FS/SS/FF/SF, Earned Value, risiko, sumber daya, lembar waktu, pengadaan, dan pusat dokumen digital.</p>
                @if (count(\App\Support\DemoCompany::available()))
                    <p class="l-pick">Pilih data demo, lalu masuk sebagai Tamu:</p>
                    <x-promise.demo-cards variant="landing" />
                @endif
                <div class="l-actions">
                    <a href="{{ $guest }}" class="l-btn l-btn-amber">Masuk sebagai Tamu</a>
                    <a href="#fitur" class="l-btn">Lihat fiturnya</a>
                </div>
                <div class="l-warn"><span><b>{{ \App\Support\DemoData::TITLE }}</b> Proyek yang ditampilkan adalah contoh untuk keperluan pembelajaran, bukan data resmi perusahaan. <a href="#data-demo">Selengkapnya</a></span></div>
                <div class="l-stats">
                    <div class="l-stat"><b>{{ $stats['projects'] }}</b><span>proyek contoh</span></div>
                    <div class="l-stat"><b>{{ number_format($stats['wbs'], 0, ',', '.') }}</b><span>elemen WBS</span></div>
                    <div class="l-stat"><b>{{ $stats['risks'] }}</b><span>risiko tercatat</span></div>
                    <div class="l-stat"><b>{{ \App\Support\Fmt::money($stats['budget']) }}</b><span>nilai portofolio</span></div>
                </div>
            </div>
            <figure class="l-shot"><img src="{{ $img('dasbor') }}" alt="Dasbor portofolio Promise" loading="eager"><figcaption>Dasbor portofolio: kurva S, SPI × CPI, kesehatan proyek, peta panas risiko (data demo).</figcaption></figure>
        </div>
    </section>

    <section class="l-section" id="masalah">
        <div class="l-wrap">
            <span class="l-eyebrow">Kenapa perlu</span>
            <h2>Laporan proyek yang terlambat membuat keputusan ikut terlambat.</h2>
            <div class="l-grid l-grid-3">
                <div class="l-card"><span class="l-ic">1</span><h3>Data tersebar</h3><p>Jadwal di satu berkas, biaya di berkas lain, risiko di catatan rapat, dokumen di surel. Butuh berhari-hari untuk menyusun satu laporan status.</p></div>
                <div class="l-card"><span class="l-ic">2</span><h3>Masalah ketahuan belakangan</h3><p>Tanpa Earned Value, proyek terlihat "80% selesai" padahal biaya sudah melampaui rencana. SPI dan CPI menunjukkannya sejak awal.</p></div>
                <div class="l-card"><span class="l-ic">3</span><h3>Pengetahuan hilang</h3><p>Pelajaran proyek, gambar teknik, notulen, dan kontrak tidak terkumpul, sehingga proyek berikutnya mengulang kesalahan yang sama.</p></div>
            </div>
        </div>
    </section>

    <section class="l-section l-alt" id="pmbok">
        <div class="l-wrap">
            <span class="l-eyebrow">Mengacu PMBOK® Guide</span>
            <h2>Lima kelompok proses, sembilan area pengetahuan.</h2>
            <ol class="l-phases">
                <li><b>Inisiasi</b>Piagam proyek, sponsor, pemangku kepentingan.</li>
                <li><b>Perencanaan</b>WBS, baseline jadwal & biaya, risiko, pengadaan.</li>
                <li><b>Eksekusi</b>Penugasan tim, lembar waktu, kontrak, mutu.</li>
                <li><b>Pemantauan & Pengendalian</b>EVM, perubahan terpadu, isu, laporan status.</li>
                <li><b>Penutupan</b>Serah terima, pelajaran, arsip dokumen.</li>
            </ol>
            <div class="l-grid l-grid-3">
                @foreach ($areas as [$t, $d])
                    <div class="l-card l-ka"><h3>{{ $t }}</h3><p>{{ $d }}</p></div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="l-section" id="fitur">
        <div class="l-wrap">
            <span class="l-eyebrow">Fitur</span>
            <h2>Seperti pusat proyek berbasis web, dalam Bahasa Indonesia.</h2>
            <div class="l-feature">
                <figure class="l-shot"><img src="{{ $img('gantt') }}" alt="Gantt chart Promise" loading="lazy"><figcaption>Gantt chart: baseline, progres, milestone, panah keterkaitan FS/SS/FF/SF.</figcaption></figure>
                <div>
                    <span class="l-chip">Waktu & Ruang Lingkup</span>
                    <h3>WBS level 2 dan Gantt dengan keterkaitan antartugas</h3>
                    <p>Susun deliverable dan paket kerja, tetapkan baseline, lalu hubungkan tugas dengan Precedence Diagramming Method.</p>
                    <ul class="l-ticks"><li>Finish-to-Start, Start-to-Start, Finish-to-Finish, Start-to-Finish</li><li>Lag dan lead dalam hari</li><li>Keterkaitan yang dilanggar langsung ditandai merah</li><li>Keterlambatan terhadap baseline terlihat per tugas</li></ul>
                </div>
            </div>
            <div class="l-feature l-feature-rev">
                <div>
                    <span class="l-chip">Biaya</span>
                    <h3>Earned Value per proyek dan portofolio</h3>
                    <p>PV dihitung dari baseline paket kerja, EV dari progres, AC dari biaya aktual. Promise menampilkan SV, CV, SPI, CPI, EAC, ETC, VAC, TCPI dan kurva S bulanan.</p>
                    <ul class="l-ticks"><li>Kesehatan proyek otomatis merah-kuning-hijau</li><li>Kuadran SPI × CPI seluruh portofolio</li><li>BAC vs EAC vs AC per proyek</li></ul>
                </div>
                <figure class="l-shot"><img src="{{ $img('ruang-proyek') }}" alt="Ruang proyek Promise" loading="lazy"><figcaption>Ruang proyek: KPI Earned Value dan tab per area pengetahuan.</figcaption></figure>
            </div>
            <div class="l-feature">
                <figure class="l-shot"><img src="{{ $img('dokumen') }}" alt="Pusat dokumen Promise" loading="lazy"><figcaption>Pusat dokumen: PDF, Word, Excel, gambar per proyek dan per tugas.</figcaption></figure>
                <div>
                    <span class="l-chip">Pusat dokumen digital</span>
                    <h3>Setiap tugas dan record bisa diberi lampiran</h3>
                    <p>Unggah PDF, DOC/DOCX, XLS/XLSX, PPT, PNG, dan JPG ke tugas WBS, proyek, risiko, isu, perubahan, pemeriksaan mutu, kontrak, dan laporan status. Semua terkumpul di Pusat Dokumen dengan kategori, nomor dokumen, dan revisi.</p>
                    <ul class="l-ticks"><li>Pratinjau gambar dan PDF langsung</li><li>Disimpan privat, hanya untuk pengguna yang masuk</li><li>Filter per proyek, kategori, dan jenis berkas</li></ul>
                </div>
            </div>
            <div class="l-grid l-grid-4" style="margin-top:2.4rem">
                <div class="l-card"><h3>Risiko & isu</h3><p>Matriks 5×5, strategi respons, eksposur biaya, isu yang berasal dari risiko.</p></div>
                <div class="l-card"><h3>Sumber daya</h3><p>Penugasan, peta kapasitas bulanan, lembar waktu mingguan dengan persetujuan manajer.</p></div>
                <div class="l-card"><h3>Pengadaan</h3><p>Kontrak lump sum, harga satuan, cost plus, EPCI; progres dan pembayaran per vendor.</p></div>
                <div class="l-card"><h3>Peran & akses</h3><p>Administrator, PMO, Manajer Proyek, Anggota Tim, Eksekutif. Manajer hanya mengubah proyeknya.</p></div>
            </div>
        </div>
    </section>

    <section class="l-section l-alt" id="data-demo">
        <div class="l-wrap">
            <span class="l-eyebrow">Data demo</span>
            <h2>Dua portofolio contoh: {{ $cases->count() }} proyek energi, pertambangan, dan perbankan</h2>
            <p class="l-lead">Nama dan gambaran proyek disarikan dari Laporan Tahunan 2025 PT Medco Energi Internasional Tbk (pengembangan lapangan migas, pembangkit, tambang) dan PT Bank SMBC Indonesia Tbk (core banking, AI generatif, cloud, keamanan siber, Jenius, TOUCHBIZ, keuangan berkelanjutan), khusus untuk pembelajaran. Pilih kartu perusahaan untuk menjelajah datanya tanpa akun, atau klik "Masuk sebagai Tamu" untuk melihat semuanya.</p>
            @foreach ($caseGroups as $group => $list)
                <h3 class="l-cases-h">{{ $group }} <small>({{ $list->count() }} proyek)</small></h3>
                <div class="l-cases">
                    @foreach ($list as $c)
                        <div class="l-case"><i style="background: {{ $rag[$c->health()] ?? '#64748B' }}"></i><div><b>{{ $c->name }}</b><small>{{ $c->code }} · {{ \App\Support\Pmbok::PHASES[$c->phase] ?? $c->phase }} · {{ $c->location }}</small></div></div>
                    @endforeach
                </div>
            @endforeach
            <div class="l-warn"><span><b>{{ \App\Support\DemoData::TITLE }}</b> {{ \App\Support\DemoData::NOTICE }} Mode Tamu hanya melihat, data tidak bisa diubah.</span></div>
            @if (count(\App\Support\DemoCompany::available()))
                <p class="l-pick">Pilih data demo yang ingin dijelajahi:</p>
                <x-promise.demo-cards variant="landing" />
            @endif
            <div class="l-actions"><a href="{{ $guest }}" class="l-btn l-btn-amber">Masuk sebagai Tamu</a></div>
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
            <div class="l-cta" style="margin-top:2rem">
                <div><h2>Ingin Promise untuk organisasi Anda?</h2><p>Hubungi kami untuk demo, instalasi, dan penyesuaian alur kerja PMO.</p></div>
                <div class="l-actions"><a class="l-btn" style="border-color:#fff;color:#fff" href="{{ $wa('Halo Rucisoft, saya tertarik dengan Promise (Project Management Information System).') }}" target="_blank" rel="noopener">Hubungi via WhatsApp</a></div>
            </div>
        </div>
    </section>
</main>
<footer class="l-foot">
    <div class="l-wrap"><b>Promise</b> · Project Management Information System · © {{ date('Y') }} Rucisoft. {{ \App\Support\DemoData::SHORT }} PMBOK adalah merek terdaftar Project Management Institute, Inc.</div>
</footer>
</body>
</html>
