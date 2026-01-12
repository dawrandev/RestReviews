<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ __('Cuba admin — это супер гибкий, мощный, чистый и современный адаптивный шаблон админ-панели на Bootstrap 5 с неограниченными возможностями.') }}">
    <meta name="keywords" content="{{ __('шаблон админки, шаблон Cuba, панель управления, плоский дизайн, адаптивный шаблон, веб-приложение') }}">
    <meta name="author" content="{{ __('pixelstrap') }}">
    <link rel="icon" href="../assets/images/favicon.png" type="image/x-icon">
    <link rel="shortcut icon" href="../assets/images/favicon.png" type="image/x-icon">
    <title>{{ __('Cuba - Премиум шаблон админ-панели') }}</title>
    <link href="https://fonts.googleapis.com/css?family=Rubik:400,400i,500,500i,700,700i&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,700,700i,900&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../assets/css/font-awesome.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/vendors/icofont.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/vendors/themify.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/vendors/flag-icon.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/vendors/feather-icon.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/vendors/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
    <link id="color" rel="stylesheet" href="../assets/css/color-1.css" media="screen">
    <link rel="stylesheet" type="text/css" href="../assets/css/responsive.css">
</head>

<body>
    <div class="container-fluid p-0">
        <div class="row m-0">
            <div class="col-12 p-0">
                <div class="login-card">
                    <div>
                        <div class="login-main">
                            <form class="theme-form" action="{{ route('login') }}" method="POST">
                                @csrf
                                <h4>{{ __('Войти в аккаунт') }}</h4>
                                <p>{{ __('Введите ваш логин и пароль для входа') }}</p>
                                <div class="form-group">
                                    <label class="col-form-label">{{ __('Логин') }}</label>
                                    <input
                                        class="form-control @error('login.login') is-invalid @enderror"
                                        type="text"
                                        placeholder="{{ __('Логин') }}"
                                        name="login"
                                        value="{{ old('login.login') }}">

                                    @error('login')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label class="col-form-label">{{ __('Пароль') }}</label>
                                    <div class="form-input position-relative">
                                        <input
                                            class="form-control @error('login.password') is-invalid @enderror"
                                            type="password"
                                            name="password"
                                            placeholder="{{ __('*********') }}">
                                        <div class="show-hide"><span class="show"></span></div>

                                        @error('password')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group mb-0">
                                    <div class="text-end mt-3">
                                        <button class="btn btn-primary btn-block w-100" type="submit">{{ __('Войти') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="../assets/js/jquery-3.5.1.min.js"></script>
        <script src="../assets/js/bootstrap/bootstrap.bundle.min.js"></script>
        <script src="../assets/js/icons/feather-icon/feather.min.js"></script>
        <script src="../assets/js/icons/feather-icon/feather-icon.js"></script>
        <script src="../assets/js/config.js"></script>
        <script src="../assets/js/script.js"></script>
    </div>
</body>

</html>