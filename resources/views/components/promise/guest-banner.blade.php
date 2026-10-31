@if (\App\Support\DemoData::present() || \App\Support\GuestMode::isGuest())
    <div class="pm-banner pm-banner-demo">
        <b>{{ \App\Support\DemoData::TITLE }}</b> {{ \App\Support\DemoData::NOTICE }}
        @if (\App\Support\GuestMode::isGuest())
            <br>Anda masuk sebagai <b>Tamu</b> (hanya lihat). <a href="{{ url('/') }}">Halaman depan</a>
        @endif
    </div>
@endif
