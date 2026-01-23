@extends('layouts.main')

@section('title', __('Панель управления'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="card shadow-none bg-transparent" style="margin-top: 10vh;">
                <div class="card-body text-center">
                    <div class="mb-4">
                        <i class="icon-dashboard d-block mb-3" style="font-size: 100px; color: #7366ff; opacity: 0.3;"></i>
                        <img src="{{ asset('assets/images/dashboard/welcome.png') }}" alt="" class="img-fluid d-none" style="max-width: 300px;">
                    </div>

                    <h3 class="fw-bold">{{ __('Добро пожаловать в панель управления!') }}</h3>
                    <p class="text-muted mx-auto" style="max-width: 500px;">
                        {{ __('На данный момент здесь нет активных данных для отображения. Скоро здесь появятся графики, статистика и ключевые показатели вашего бизнеса.') }}
                    </p>

                    <div class="mt-4">
                        <a href="{{ route('restaurants.index') }}" class="btn btn-primary btn-lg px-4 shadow-sm">
                            <i class="fa fa-plus me-2"></i> {{ __('Перейти к ресторанам') }}
                        </a>
                        <button class="btn btn-outline-light text-dark ms-2 btn-lg px-4" onclick="location.reload()">
                            <i class="fa fa-refresh me-2"></i> {{ __('Обновить') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    // Cuba admin-dagi Feather ikonkalarni faollashtirish
    if (feather) {
        feather.replace();
    }
</script>
@endpush