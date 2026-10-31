@props(['who' => 'direksi'])
{{-- Ilustrasi tokoh Indonesia (bila ada foto di public/images/foto/{who}.jpg, foto dipakai) --}}
@php $photo = public_path('images/foto/'.$who.'.jpg'); @endphp
@if (is_file($photo))
    <img src="{{ asset('images/foto/'.$who.'.jpg') }}" alt="" class="l-persona-img" loading="lazy">
@else
<svg viewBox="0 0 120 120" class="l-persona-img" role="img" aria-hidden="true">
    @switch($who)
        @case('direksi')
            <circle cx="60" cy="60" r="60" fill="#DCE9F6"/>
            <path d="M18 120c3-24 20-34 42-34s39 10 42 34z" fill="#7A4A26"/>
            <g fill="#E3B77A" opacity=".9"><circle cx="34" cy="104" r="3"/><circle cx="48" cy="112" r="3"/><circle cx="72" cy="112" r="3"/><circle cx="86" cy="104" r="3"/><circle cx="60" cy="104" r="2.5"/><path d="M40 96l6 4-6 4-6-4z"/><path d="M80 96l6 4-6 4-6-4z"/></g>
            <path d="M52 84h16v8l-8 6-8-6z" fill="#A8754F"/>
            <ellipse cx="60" cy="58" rx="20" ry="24" fill="#A8754F"/>
            <path d="M40 54c0-18 10-25 20-25s20 7 20 25c-3-8-9-12-20-12s-17 4-20 12z" fill="#1F1A17"/>
            <g fill="none" stroke="#1F1A17" stroke-width="2"><circle cx="52" cy="59" r="5.5"/><circle cx="68" cy="59" r="5.5"/><path d="M57.5 59h5"/></g>
            <circle cx="52" cy="59" r="1.8" fill="#1F1A17"/><circle cx="68" cy="59" r="1.8" fill="#1F1A17"/>
            <path d="M53 71c4 3 10 3 14 0" stroke="#5A3416" stroke-width="2" fill="none" stroke-linecap="round"/>
            @break
        @case('pmo')
            <circle cx="60" cy="60" r="60" fill="#FCEBD0"/>
            <path d="M16 120c4-25 22-36 44-36s40 11 44 36z" fill="#2E7D4F"/>
            <path d="M34 96c0-38 10-66 26-66s26 28 26 66c-8 6-17 9-26 9s-18-3-26-9z" fill="#0F5E9C"/>
            <ellipse cx="60" cy="60" rx="17" ry="20" fill="#C58C64"/>
            <path d="M43 56c1-14 8-22 17-22s16 8 17 22c-4-6-10-9-17-9s-13 3-17 9z" fill="#0B4A7C"/>
            <circle cx="53" cy="61" r="2" fill="#1F1A17"/><circle cx="67" cy="61" r="2" fill="#1F1A17"/>
            <path d="M54 71c3.5 2.5 8.5 2.5 12 0" stroke="#7A3E22" stroke-width="2" fill="none" stroke-linecap="round"/>
            @break
        @case('manajer')
            <circle cx="60" cy="60" r="60" fill="#E4F2E8"/>
            <path d="M18 120c3-24 20-34 42-34s39 10 42 34z" fill="#E8742A"/>
            <path d="M44 88l-6 32M76 88l6 32" stroke="#F4F4F4" stroke-width="4"/>
            <path d="M53 84h14v8l-7 5-7-5z" fill="#8D5A3B"/>
            <ellipse cx="60" cy="60" rx="19" ry="23" fill="#8D5A3B"/>
            <path d="M37 50c0-15 10-24 23-24s23 9 23 24z" fill="#F2B233"/><rect x="33" y="48" width="54" height="6" rx="3" fill="#D98E04"/><rect x="57" y="26" width="6" height="22" rx="2" fill="#D98E04"/>
            <circle cx="53" cy="62" r="2" fill="#1F1A17"/><circle cx="67" cy="62" r="2" fill="#1F1A17"/>
            <path d="M52 69c4 1 12 1 16 0" stroke="#1F1A17" stroke-width="2.5" fill="none" stroke-linecap="round"/>
            <path d="M53 73c4 3 10 3 14 0" stroke="#4A2A16" stroke-width="2" fill="none" stroke-linecap="round"/>
            @break
        @default
            <circle cx="60" cy="60" r="60" fill="#EDE7F6"/>
            <path d="M38 50c0-16 10-24 22-24s22 8 22 24v40H38z" fill="#1F1A17"/>
            <path d="M18 120c3-24 20-34 42-34s39 10 42 34z" fill="#3F7CC4"/>
            <path d="M53 84h14v8l-7 5-7-5z" fill="#B97A56"/>
            <ellipse cx="60" cy="60" rx="17" ry="21" fill="#B97A56"/>
            <path d="M43 56c0-14 8-21 17-21s17 7 17 21c-5-9-11-12-17-12s-12 3-17 12z" fill="#1F1A17"/>
            <circle cx="53" cy="61" r="2" fill="#1F1A17"/><circle cx="67" cy="61" r="2" fill="#1F1A17"/>
            <path d="M54 70c3.5 2.5 8.5 2.5 12 0" stroke="#7A3E22" stroke-width="2" fill="none" stroke-linecap="round"/>
    @endswitch
</svg>
@endif
