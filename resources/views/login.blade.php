<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>{{config('admin.title')}} | {{ __('admin.login') }}</title>
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        @if(!is_null($favicon = Admin::favicon()))
            <link rel="shortcut icon" href="{{$favicon}}">
        @endif
        <link rel="stylesheet" href="{{ Admin::asset("open-admin/css/styles.css")}}">
        <link rel="stylesheet" href="{{ asset("vendor/laravel-packages/multi-language/css/multilanguage.css")}}">
        <script src="{{ Admin::asset("bootstrap5/bootstrap.bundle.min.js")}}"></script>
        <!-- Передача URL-адреса в глобальную переменную -->
        <script>
            window.localeUrl = "{{ admin_url('/locale') }}";
        </script>
    </head>
    <body class="bg-light" @if(config('admin.login_background_image')) style="background: url({{config('admin.login_background_image')}}) no-repeat;background-size: cover;"@endif>
    <div id="preloader" class="preloader" style="display: none">Loading...</div>
    <div class="d-flex justify-content-center align-items-center h-100">
            <div class="container m-4" style="max-width:400px;">

                <h1 class="text-center mb-3 h2"><a class="text-decoration-none text-dark" href="{{ admin_url('/') }}">{{config('admin.name')}}</a></h1>
                <div class="bg-body p-4 shadow-sm rounded-3">
                    @if($errors->has('attempts'))
                        <div class="alert alert-danger m-0 text-center">{{$errors->first('attempts')}}</div>
                    @else
                    <form id="myForm" action="{{ admin_url('auth/login') }}" method="post">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="mb-3">
                            @if($errors->has('username'))
                                <div class="alert alert-danger">{{$errors->first('username')}}</div>
                            @endif
                            <label for="username" class="form-label">{{ __('admin.username') }}</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="icon-user"></i></span>
                                <input type="text" class="form-control" autocomplete="on" placeholder="{{ __('admin.username') }}" name="username" id="username" value="{{ session('form_data.username', old('username')) }}" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">{{ __('admin.password') }}</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="icon-eye"></i></span>
                                <input type="password" class="form-control" autocomplete="on" placeholder="{{ __('admin.password') }}" name="password" id="password" value="{{ session('form_data.password', old('password')) }}" aria-describedby="passwordHelpInline" required>
                            </div>
                            @if($errors->has('password'))
                                <div class="alert alert-danger">{{$errors->first('password') }}</div>
                            @endif
                        </div>
                        <div class="mb-3">
                            @if(config('admin.auth.remember'))
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="remember" id="remember" value="1"  {{ (session('form_data.remember', old('remember'))) ? 'checked="checked"' : '' }}>
                                    <label class="form-check-label" for="remember">{{ __('admin.remember_me') }}</label>
                                </div>
                            @endif
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">{{ __('admin.login') }}</button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <select class="form-control" id="locale">
                                @foreach($languages as $key => $language)
                                    <option value="{{$key}}" {!! $key != $current ?: 'selected' !!}>{{$language}}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                    @endif
                </div>
            </div>
            <!-- /.login-box-body -->
        </div>
        <!-- /.login-box -->
        <script src="{{ asset("vendor/laravel-packages/multi-language/js/multilanguage.js")}}"></script>
    </body>
</html>
