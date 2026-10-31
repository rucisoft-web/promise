@if (\App\Support\GuestMode::isGuest())
    <div class="pm-banner">Anda masuk sebagai <b>Tamu</b> (hanya lihat). Data proyek MedcoEnergi di sini adalah <b>data demo</b> yang disimulasikan dari Laporan Tahunan 2025, bukan data resmi. <a href="{{ url('/') }}">Halaman depan</a></div>
@endif
