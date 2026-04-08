{{-- Это будет вставлено в @section('admin.login.language_selector') --}}
<div class="form-group mb-3">
    <label for="language_switcher">{{ __('multi-language::multi-language.select_language') }}</label>
    <select name="language_switcher" id="language_switcher" class="form-control" style="width: auto; display: inline-block;">
        @foreach($languages ?? [] as $locale)
            <option value="{{ $locale->value }}" data-flag="{{ $locale->flag() }}" {{ app()->getLocale() == $locale->value ? 'selected' : '' }}>
                {{ $locale->flag() }} {{ $locale->label() }}
            </option>
        @endforeach
    </select>

    {{-- Прелоадер --}}
    <div id="language-preloader" style="display: none; margin-left: 10px;">
        <i class="fa fa-spinner fa-spin"></i> {{ __('multi-language::multi-language.loading') }}
    </div>
</div>

@push('scripts')
    <script src="{{ asset("vendor/laravel-packages/multi-language/js/multilanguage.js")}}"></script>
@endpush
