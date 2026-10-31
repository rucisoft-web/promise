@if (\App\Support\GuestMode::enabled())
    <div class="pm-guest-login">
        <div class="pm-guest-or"><span>atau coba data demo</span></div>
        <a href="{{ route('promise.guest') }}" class="pm-guest-btn">Masuk sebagai Tamu</a>
        <p class="pm-demo-note"><b>Data demo:</b> portofolio proyek contoh disusun dari gambaran proyek dalam Laporan Tahunan 2025 PT Medco Energi Internasional Tbk. Jadwal, anggaran, progres, risiko, dan nama personel adalah simulasi untuk memperagakan fitur, bukan data resmi perusahaan. Mode Tamu hanya melihat, data tidak bisa diubah.</p>
    </div>
@endif
