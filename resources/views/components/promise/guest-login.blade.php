@if (\App\Support\GuestMode::enabled())
    <div class="pm-guest-login" id="pilih-demo">
        <div class="pm-guest-or"><span>atau coba data demo</span></div>
        @if (count(\App\Support\DemoCompany::available()))
            <p class="pm-demo-pick">1. Pilih perusahaan &nbsp;·&nbsp; 2. Masuk sebagai Tamu</p>
            <x-promise.demo-cards />
        @else
            <a href="{{ route('promise.guest') }}" class="pm-guest-btn">Masuk sebagai Tamu</a>
        @endif
        <p class="pm-demo-note"><b>{{ \App\Support\DemoData::TITLE }}</b> {{ \App\Support\DemoData::NOTICE }} Mode Tamu hanya melihat, data tidak bisa diubah.</p>
    </div>
@endif
