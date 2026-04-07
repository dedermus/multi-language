<div class="dropdown">
    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#"
       role="button" id="dropdownMenuLanguage"
       data-bs-toggle="dropdown" aria-expanded="false">
        @if($currentLocale && $currentLocale->flag())
            <span class="me-1">{{ $currentLocale->flag() }}</span>
        @endif
        <span>{{ strtoupper($current) }}</span>
    </a>

    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuLanguage">
        @foreach($languages as $code => $language)
            <li>
                <a class="dropdown-item language {{ $code === $current ? 'active' : '' }}"
                   href="#"
                   data-id="{{ $code }}"
                   data-direction="{{ $language['direction'] }}">
                    @if($language['flag'])
                        <span class="me-2">{{ $language['flag'] }}</span>
                    @endif
                    <span>{{ $language['name'] }}</span>

                    @if($code === $current)
                        <i class="icon-check float-end ms-2"></i>
                    @endif
                </a>
            </li>
        @endforeach
    </ul>
</div>


