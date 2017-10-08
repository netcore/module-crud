
<!-- Nav tabs -->
<ul class="nav nav-tabs clean" role="tablist">
    @foreach($languages as $language)
        <li
            role="presentation"
            class="{{ $loop->first ? 'active' : '' }}"
        >
            <a
                href="#{{ $language->iso_code }}"
                aria-controls="{{ $language->iso_code }}"
                role="tab"
                data-toggle="tab"
            >
                {{ $language->title_localized }}
            </a>
        </li>
    @endforeach
</ul>