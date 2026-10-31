@php
    $R = 'App\\Filament\\Resources\\';
    $u = fn ($c) => ($R.$c)::getUrl();
    $areas = [
        ['Integrasi', 'Mengembangkan piagam proyek, mengarahkan pekerjaan, pengendalian perubahan terpadu, dan menutup proyek.', [['Proyek & Piagam', $u('Projects\\ProjectResource')], ['Pengendalian Perubahan', $u('ChangeRequests\\ChangeRequestResource')], ['Pelajaran', $u('LessonsLearned\\LessonLearnedResource')], ['Portofolio & Program', $u('Portfolios\\PortfolioResource')]]],
        ['Ruang Lingkup', 'Pernyataan lingkup di piagam, WBS level 1 (deliverable) dan level 2 (paket kerja), kamus WBS.', [['Work Breakdown Structure', $u('WbsItems\\WbsItemResource')]]],
        ['Waktu (Jadwal)', 'Urutan aktivitas dengan Precedence Diagramming Method: FS, SS, FF, SF beserta lag/lead; baseline, milestone, Gantt chart.', [['Gantt Chart', \App\Filament\Pages\JadwalGantt::getUrl()], ['Keterkaitan Tugas', $u('TaskDependencies\\TaskDependencyResource')]]],
        ['Biaya', 'Anggaran per paket kerja (PV/BAC), biaya aktual (AC), dan Earned Value Management: SV, CV, SPI, CPI, EAC, ETC, VAC, TCPI.', [['Earned Value (EVM)', \App\Filament\Pages\KinerjaBiaya::getUrl()]]],
        ['Mutu', 'Rencana dan pengendalian mutu: inspeksi, pengujian, audit, FAT/SAT terhadap standar dan kriteria penerimaan.', [['Pengendalian Mutu', $u('QualityChecks\\QualityCheckResource')]]],
        ['Sumber Daya Manusia', 'Kelompok sumber daya, penugasan tim, lembar waktu mingguan dengan persetujuan, dan peta kapasitas.', [['Kelompok Sumber Daya', $u('ResourcePool\\ResourcePoolResource')], ['Penugasan', $u('Assignments\\AssignmentResource')], ['Lembar Waktu', $u('Timesheets\\TimesheetResource')], ['Peta Kapasitas', \App\Filament\Pages\KapasitasSdm::getUrl()]]],
        ['Komunikasi', 'Register pemangku kepentingan (pengaruh × kepentingan), rencana komunikasi, dan laporan status bulanan RAG.', [['Pemangku Kepentingan', $u('Stakeholders\\StakeholderResource')], ['Rencana Komunikasi', $u('CommunicationPlans\\CommunicationPlanResource')], ['Laporan Status', $u('StatusReports\\StatusReportResource')]]],
        ['Risiko', 'Identifikasi, analisis kualitatif probabilitas × dampak (matriks 5×5), strategi respons ancaman/peluang, log isu.', [['Register Risiko', $u('Risks\\RiskResource')], ['Log Isu', $u('Issues\\IssueResource')]]],
        ['Pengadaan', 'Rencana pengadaan, metode tender, jenis kontrak (lump sum, harga satuan, cost plus, EPCI), progres dan pembayaran.', [['Kontrak', $u('Contracts\\ContractResource')], ['Vendor', $u('Vendors\\VendorResource')]]],
    ];
@endphp
<x-filament-panels::page>
    <div class="pm-card pm-guide">
        <p><b>Promise</b> (Project Management Information System) membantu PMO dan manajer proyek mengelola portofolio, program, dan proyek mengikuti <i>A Guide to the Project Management Body of Knowledge</i> (PMBOK® Guide) dari Project Management Institute. Fitur intinya serupa dengan pusat proyek berbasis web: Pusat Proyek, Pusat Sumber Daya, lembar waktu, analisis portofolio, pustaka dokumen, serta risiko dan isu.</p>
        <h3 class="pm-h3">Lima kelompok proses (fase proyek)</h3>
        <ol class="pm-phases">
            @foreach (\App\Support\Pmbok::PHASES as $k => $label)<li><b>{{ $label }}</b></li>@endforeach
        </ol>
        <h3 class="pm-h3">Sembilan area pengetahuan</h3>
        <div class="pm-areas">
            @foreach ($areas as [$title, $desc, $links])
                <div class="pm-area"><h4>{{ $title }}</h4><p>{{ $desc }}</p><p>@foreach ($links as [$l, $href])<a href="{{ $href }}">{{ $l }}</a>@if (! $loop->last) · @endif @endforeach</p></div>
            @endforeach
        </div>
        <h3 class="pm-h3">Pusat dokumen digital</h3>
        <p>Setiap tugas WBS, proyek, risiko, isu, permintaan perubahan, pemeriksaan mutu, kontrak, laporan status, dan pelajaran dapat diberi lampiran PDF, Word, Excel, PowerPoint, PNG, atau JPG (maksimal 20 MB per berkas). Semua lampiran terkumpul di <a href="{{ $u('Documents\\DocumentResource') }}">Pusat Dokumen</a> dan di tab Dokumen pada Ruang Proyek, lengkap dengan kategori, nomor dokumen, dan revisi.</p>
        <h3 class="pm-h3">Peran pengguna</h3>
        <ul>
            <li><b>Administrator</b>: semua data dan akun pengguna.</li>
            <li><b>PMO / Portofolio</b>: semua proyek, portofolio, sumber daya, vendor.</li>
            <li><b>Manajer Proyek</b>: mengubah proyek yang dikelolanya beserta WBS, jadwal, risiko, dokumen, dan menyetujui lembar waktu.</li>
            <li><b>Anggota Tim</b>: melihat proyek dan mengisi lembar waktu sendiri.</li>
            <li><b>Eksekutif</b>: melihat dasbor dan seluruh proyek tanpa mengubah.</li>
        </ul>
    </div>

    @php($guides = collect(\App\Support\Help::all())->except(['umum', 'panduan']))
    <div class="pm-card" id="panduan-layar" style="margin-top: 1.5rem">
        <h3 class="pm-h3">Panduan per layar</h3>
        <p>Penjelasan konsep, cara memakai, data yang diisi, contoh, dan tangkapan layar untuk setiap layar. Panduan yang sama muncul saat menekan tombol <b>Bantuan</b> di pojok kanan atas.</p>
        <ul class="pm-help-toc">@foreach ($guides as $key => $g)<li><a href="#bantuan-{{ $key }}">{{ $g['title'] }}</a></li>@endforeach</ul>
    </div>
    <div class="pm-help-doc">
        @foreach ($guides as $key => $g)
            <article class="pm-card" id="bantuan-{{ $key }}">
                <small>{{ $g['area'] }}</small>
                <h2>{{ $g['title'] }}</h2>
                @include('filament.partials.help-body', ['h' => $g, 'shots' => \App\Support\Help::shots($key)])
                <a class="pm-help-anchor" href="#panduan-layar">↑ Daftar panduan</a>
            </article>
        @endforeach
    </div>
</x-filament-panels::page>
