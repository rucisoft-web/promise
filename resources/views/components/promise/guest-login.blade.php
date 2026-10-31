@if (\App\Support\GuestMode::enabled())
    <div class="pm-guest-login">
        <div class="pm-guest-or"><span>atau coba data demo</span></div>
        @if (count(\App\Support\DemoCompany::available()))
            <p class="pm-demo-pick">Pilih data demo perusahaan:</p>
            <x-promise.demo-cards />
        @endif
        <a href="{{ route('promise.guest') }}" class="pm-guest-btn">Masuk sebagai Tamu</a>
        @if (count(\App\Support\DemoCompany::available()) > 1)
            <p class="pm-demo-pick-all">Tombol di atas menampilkan semua perusahaan demo sekaligus.</p>
        @endif
        <p class="pm-demo-note"><b>{{ \App\Support\DemoData::TITLE }}</b> {{ \App\Support\DemoData::NOTICE }} Mode Tamu hanya melihat, data tidak bisa diubah.</p>
    </div>
@endif
