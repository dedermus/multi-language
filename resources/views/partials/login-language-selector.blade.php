{{-- Это будет вставлено в @section('admin.login.language_selector') --}}
<div class="mb-3">
    <label for="locale" class="form-label">{{ __('multi-language::multi-language.languages_list') }}</label>
    <select class="form-control" id="locale">
        @foreach($languages as $key => $language)
            <option value="{{$key}}" {!! $key != $current ?: 'selected' !!}>{{$language}}</option>
        @endforeach
    </select>
</div>

@push('scripts')
    <script src="{{ asset("vendor/laravel-packages/multi-language/js/multilanguage.js")}}"></script>
@endpush
