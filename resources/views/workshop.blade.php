@php
    $wa = fn (string $text) => 'https://wa.me/'.$whatsapp.'?text='.rawurlencode($text);
    $daftar = $wa('Halo, saya ingin mendaftar Workshop Project Management for Executive (Promise).');
    $partner = 'Pusat Studi Manajemen Perguruan Tinggi UIN Sunan Kalijaga';
    $benefits = [
        ['Kendali lebih baik atas sumber daya keuangan, fisik, dan manusia', 'Anggaran per paket kerja dengan Earned Value (PV, EV, AC, SPI, CPI), daftar peralatan, serta peta kapasitas dan penugasan SDM memperlihatkan ke mana uang, alat, dan orang dipakai.'],
        ['Hubungan pelanggan yang lebih baik', 'Register pemangku kepentingan, rencana komunikasi, dan laporan status merah-kuning-hijau membuat pelanggan dan pemilik proyek menerima informasi yang konsisten dan tepat waktu.'],
        ['Waktu pengembangan lebih singkat', 'Gantt chart dengan keterkaitan FS/SS/FF/SF, milestone, dan garis tanggal status menunjukkan keterlambatan sejak dini, sehingga tindakan korektif diambil sebelum terlambat.'],
        ['Biaya lebih rendah', 'CPI dan perkiraan akhir (EAC) memberi tanda pemborosan lebih awal; pengendalian perubahan (CCB) dan pengelolaan kontrak mencegah tambahan biaya yang tidak disetujui.'],
        ['Mutu lebih tinggi dan keandalan meningkat', 'Rencana inspeksi, pengujian, dan audit dengan kriteria penerimaan, ditambah pelajaran (lessons learned) dari proyek sebelumnya, menjaga hasil sesuai standar.'],
        ['Margin laba lebih tinggi', 'Dasbor portofolio membantu direksi memprioritaskan proyek bernilai tinggi, memantau VAC (selisih anggaran di akhir), dan memutuskan lebih cepat atas proyek yang bermasalah.'],
        ['Produktivitas meningkat', 'Lembar waktu mingguan, penugasan yang jelas, dan pusat dokumen digital mengurangi waktu mencari data dan menyusun laporan secara manual.'],
        ['Koordinasi internal lebih baik', 'Satu sumber kebenaran untuk PMO, manajer proyek, keuangan, pengadaan, dan direksi: semua melihat jadwal, biaya, risiko, dan dokumen yang sama.'],
        ['Semangat kerja lebih tinggi', 'Peran dan tanggung jawab yang jelas, beban kerja yang seimbang lewat peta kapasitas, serta capaian yang terlihat di dasbor membuat tim lebih termotivasi.'],
    ];
    $day1 = [
        ['08.30–09.00', 'Pembukaan', 'Tantangan proyek di perusahaan peserta: terlambat, melampaui biaya, dan data yang tersebar.'],
        ['09.00–10.30', 'Manajemen proyek untuk eksekutif', 'Portofolio, program, dan proyek; peran direksi, sponsor, PMO, dan manajer proyek; keputusan apa yang perlu diambil di tingkat eksekutif.'],
        ['10.45–12.00', 'Kerangka PMBOK®', 'Siklus hidup proyek dan sembilan area pengetahuan: integrasi, lingkup, waktu, biaya, mutu, SDM, komunikasi, risiko, pengadaan.'],
        ['13.00–14.30', 'Mengenal Promise', 'Demonstrasi langsung: dasbor portofolio, ruang proyek, pusat dokumen; menjelajah data demo energi, perbankan, dan properti.'],
        ['14.45–16.30', 'Lingkup dan jadwal (praktik)', 'Menyusun piagam proyek, WBS level 1–2, dan Gantt chart dengan keterkaitan FS/SS/FF/SF serta milestone di Promise.'],
    ];
    $day2 = [
        ['08.30–10.15', 'Biaya dan Earned Value', 'Membaca PV, EV, AC, SPI, CPI, EAC, dan VAC untuk keputusan: kapan proyek perlu intervensi dan berapa perkiraan biaya akhirnya.'],
        ['10.30–12.00', 'Risiko dan isu', 'Register risiko, matriks probabilitas × dampak 5×5, strategi respons, eskalasi isu, dan cadangan kontinjensi.'],
        ['13.00–14.15', 'SDM, pengadaan, dan mutu', 'Peta kapasitas dan lembar waktu, paket pengadaan dan kontrak vendor, rencana inspeksi dan pengujian.'],
        ['14.30–15.30', 'Komunikasi dan tata kelola', 'Laporan status bulanan merah-kuning-hijau, pengendalian perubahan (CCB), dan pusat dokumen digital.'],
        ['15.30–16.30', 'Simulasi rapat tinjauan portofolio', 'Peserta memimpin rapat direksi memakai dasbor Promise, lalu menyusun rencana aksi penerapan di perusahaannya. Penutupan.'],
    ];
    $gets = [
        ['Akses uji coba Promise selama 3 bulan', 'Akun perusahaan untuk mencoba Promise dengan proyek sendiri, lengkap dengan data demo sebagai contoh.'],
        ['Materi dan templat siap pakai', 'Bahan presentasi, contoh piagam proyek, struktur WBS, register risiko, dan format laporan status.'],
        ['Panduan penggunaan Promise', 'Penjelasan konsep, data yang diisi, dan contoh tangkapan layar untuk setiap layar.'],
        ['Rencana aksi implementasi', 'Draf langkah penerapan manajemen proyek dan Promise yang disusun peserta di hari kedua.'],
        ['Sertifikat keikutsertaan', 'Diterbitkan bersama '.$partner.'.'],
        ['Sesi konsultasi lanjutan', 'Diskusi tindak lanjut selama masa uji coba untuk menjawab pertanyaan penerapan.'],
    ];
    $steps = [
        ['1', 'Workshop Mengenal Promise', '2 hari', 'Untuk direksi, eksekutif, dan kepala PMO. Memahami manajemen proyek berbasis PMBOK dan bagaimana Promise membantu pengambilan keputusan. Peserta mendapat akses uji coba 3 bulan.'],
        ['2', 'Workshop Persiapan Implementasi (in-house)', '2 hari', 'Di perusahaan Anda, bersama tim PMO dan manajer proyek: memetakan portofolio dan proyek berjalan, menyepakati struktur WBS standar, kategori risiko, peran dan hak akses pengguna, format laporan, serta data awal yang akan dimasukkan.'],
        ['3', 'Instalasi dan Pendampingan', '6 bulan', 'Instalasi Promise di server perusahaan, pemasukan data proyek awal, pelatihan pengguna per peran, pendampingan rapat tinjauan bulanan, dan evaluasi manfaat di akhir masa pendampingan.'],
    ];
    $cycle = [
        ['Rencanakan', 'Piagam, WBS, jadwal baseline, anggaran per paket kerja, rencana risiko dan komunikasi.'],
        ['Laksanakan', 'Penugasan tim, lembar waktu mingguan, kontrak vendor, inspeksi mutu, unggah dokumen.'],
        ['Pantau', 'Progres paket kerja, laporan status bulanan, Earned Value, risiko dan isu terbuka.'],
        ['Putuskan', 'Rapat tinjauan portofolio dari dasbor: setujui perubahan, eskalasi risiko, alihkan sumber daya.'],
    ];
@endphp
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Memanfaatkan Promise · Workshop Project Management for Executive</title>
    <meta name="description" content="Workshop 2 hari Project Management for Executive: mengenal Promise (Project Management Information System) bersama {{ $partner }}. Termasuk akses uji coba Promise 3 bulan.">
    <meta property="og:title" content="Workshop Project Management for Executive · Promise">
    <meta property="og:description" content="Workshop 2 hari mengenal Promise bersama {{ $partner }}.">
    <meta property="og:image" content="{{ asset('images/kenali/dasbor.jpg') }}?v=2">
    <link rel="icon" href="{{ asset('images/favicon.svg') }}" type="image/svg+xml">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}?v=7">
</head>
<body>
<header class="l-top">
    <div class="l-wrap l-top-in">
        <a href="{{ url('/') }}"><picture><source srcset="{{ asset('images/promise-logo-dark.svg') }}?v=2" media="(prefers-color-scheme: dark)"><img src="{{ asset('images/promise-logo.svg') }}?v=2" alt="Promise" class="l-logo"></picture></a>
        <nav class="l-nav" aria-label="Navigasi workshop">
            <a href="{{ url('/') }}">Beranda</a>
            <a href="#manfaat">Manfaat</a>
            <a href="#materi">Materi</a>
            <a href="#didapat">Yang didapat</a>
            <a href="#langkah">Langkah penerapan</a>
        </nav>
        <a href="{{ $daftar }}" class="l-btn l-btn-sm l-btn-amber" target="_blank" rel="noopener">Daftar</a>
    </div>
</header>

<main>
    <section class="l-hero">
        <div class="l-wrap">
            <span class="l-eyebrow">Workshop 2 hari · Project Management for Executive</span>
            <h1>Mengenal Promise: sistem informasi manajemen proyek untuk perusahaan Anda</h1>
            <p class="l-lead"><b>Promise</b> (Project Management Information System) membantu perusahaan merencanakan, memantau, dan memutuskan proyek dari satu sumber data, mengikuti <b>PMBOK® Guide</b>. Workshop ini mengajak para eksekutif memahami cara kerja manajemen proyek modern dan mencoba langsung Promise untuk perusahaannya.</p>
            <div class="l-actions">
                <a href="{{ $daftar }}" class="l-btn l-btn-amber" target="_blank" rel="noopener">Daftar workshop</a>
                <a href="{{ url('/') }}#data-demo" class="l-btn">Coba data demo</a>
            </div>
            <div class="w-facts">
                <div class="l-stat"><b>2 hari</b><span>tatap muka & praktik</span></div>
                <div class="l-stat"><b>9</b><span>area pengetahuan PMBOK</span></div>
                <div class="l-stat"><b>3 bulan</b><span>akses uji coba Promise</span></div>
            </div>
        </div>
    </section>

    <section class="w-partner" aria-label="Mitra penyelenggara">
        <div class="l-wrap w-partner-in">
            <img src="{{ asset('images/mitra/uin-sunan-kalijaga-putih.png') }}" alt="Logo UIN Sunan Kalijaga Yogyakarta" width="210" height="44">
            <p>Diselenggarakan bekerja sama dengan<br><b>{{ $partner }}</b></p>
        </div>
    </section>

    <section class="l-section" id="manfaat">
        <div class="l-wrap">
            <span class="l-eyebrow">Manfaat bagi perusahaan</span>
            <h2>Bagaimana Promise membantu perusahaan Anda</h2>
            <p class="l-lead">Sembilan manfaat yang dibahas selama workshop, beserta fitur Promise yang mewujudkannya.</p>
            <div class="l-grid l-grid-3">
                @foreach ($benefits as $i => [$t, $d])
                    <div class="l-card l-ka"><span class="l-ic">{{ $i + 1 }}</span><h3>{{ $t }}</h3><p>{{ $d }}</p></div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="l-section l-alt" id="cara">
        <div class="l-wrap">
            <span class="l-eyebrow">Cara memanfaatkan Promise</span>
            <h2>Siklus bulanan pengelolaan proyek dengan Promise</h2>
            <p class="l-lead">Setiap proyek dikelola dalam siklus yang sama, sehingga direksi selalu mendapat gambaran portofolio yang terkini tanpa menunggu laporan manual.</p>
            <ol class="w-cycle">
                @foreach ($cycle as $i => [$t, $d])
                    <li><b>{{ $t }}</b><span>{{ $d }}</span></li>
                @endforeach
            </ol>
        </div>
    </section>

    <section class="l-section" id="materi">
        <div class="l-wrap">
            <span class="l-eyebrow">Materi workshop</span>
            <h2>Dua hari: dari konsep ke keputusan</h2>
            <div class="w-days">
                @foreach (['Hari 1 · Konsep, kerangka, dan perencanaan' => $day1, 'Hari 2 · Pengendalian dan pengambilan keputusan' => $day2] as $title => $sessions)
                    <div class="w-day">
                        <h3>{{ $title }}</h3>
                        <ol>
                            @foreach ($sessions as [$time, $t, $d])
                                <li><time>{{ $time }}</time><div><b>{{ $t }}</b><p>{{ $d }}</p></div></li>
                            @endforeach
                        </ol>
                    </div>
                @endforeach
            </div>
            <p class="w-note">Setiap sesi praktik memakai Promise dengan data demo; peserta disarankan membawa laptop.</p>
        </div>
    </section>

    <section class="l-section l-alt" id="didapat">
        <div class="l-wrap">
            <span class="l-eyebrow">Setelah workshop</span>
            <h2>Yang peserta dapatkan</h2>
            <div class="l-grid l-grid-3">
                @foreach ($gets as $i => [$t, $d])
                    <div class="l-card {{ $i === 0 ? 'w-highlight' : '' }}"><h3>{{ $t }}</h3><p>{{ $d }}</p></div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="l-section" id="langkah">
        <div class="l-wrap">
            <span class="l-eyebrow">Langkah menggunakan Promise</span>
            <h2>Tiga tahap penerapan di perusahaan</h2>
            <div class="w-steps">
                @foreach ($steps as [$n, $t, $dur, $d])
                    <div class="w-step"><span class="w-step-n">{{ $n }}</span><div><h3>{{ $t }} <small>{{ $dur }}</small></h3><p>{{ $d }}</p></div></div>
                @endforeach
            </div>
            <div class="l-actions">
                <a href="{{ $daftar }}" class="l-btn l-btn-amber" target="_blank" rel="noopener">Daftar workshop</a>
                <a href="{{ $wa('Halo, saya ingin konsultasi penerapan Promise di perusahaan kami.') }}" class="l-btn" target="_blank" rel="noopener">Konsultasi penerapan</a>
            </div>
        </div>
    </section>
</main>

<footer class="l-foot">
    <div class="l-wrap"><b>Promise</b> · Project Management Information System · Workshop bersama {{ $partner }} · © {{ date('Y') }} Rucisoft. PMBOK adalah merek terdaftar Project Management Institute, Inc.</div>
</footer>
</body>
</html>
