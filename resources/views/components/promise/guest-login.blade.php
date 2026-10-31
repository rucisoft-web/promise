@if (\App\Support\GuestMode::enabled())
    <div class="pm-guest-login">
        <div class="pm-guest-or"><span>atau coba data demo</span></div>
        <a href="{{ route('promise.guest') }}" class="pm-guest-btn">Masuk sebagai Tamu</a>
        <p class="pm-demo-note"><b>{{ \App\Support\DemoData::TITLE }}</b> {{ \App\Support\DemoData::NOTICE }} Mode Tamu hanya melihat, data tidak bisa diubah.</p>
    </div>
@endif
