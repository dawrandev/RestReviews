@extends('layouts.main')

@section('title', 'Рестораны')

@section('content')
<div class="container-fluid"
    data-can-edit="{{ auth()->user()->hasPermissionTo('restaurant-edit') ? '1' : '0' }}"
    data-can-delete="{{ auth()->user()->hasPermissionTo('restaurant-delete') ? '1' : '0' }}"
    data-can-view="{{ auth()->user()->hasPermissionTo('restaurant-view') ? '1' : '0' }}"
    data-can-create="{{ auth()->user()->hasPermissionTo('restaurant-create') ? '1' : '0' }}">

    <div class="page-title">
        <div class="row">
            <div class="col-6">
                <h3>Рестораны</h3>
            </div>
            <div class="col-6">
                <ol class="breadcrumb">
                    @role('superadmin')
                    <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}"><i data-feather="home"></i></a></li>
                    @else
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i data-feather="home"></i></a></li>
                    @endrole
                    <li class="breadcrumb-item active">Рестораны</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5>Список ресторанов</h5>

                        @if(auth()->user()->hasPermissionTo('restaurant-create'))
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#createRestaurantModal">
                            <i class="fa fa-plus"></i> Добавить ресторан
                        </button>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fa fa-check-circle"></i> {{ session('success') }}
                        <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    @if($errors->has('delete'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fa fa-exclamation-triangle"></i> {{ $errors->first('delete') }}
                        <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif
                    @can('restaurant-search')
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa fa-search"></i></span>
                                    <input type="text" class="form-control" placeholder="Поиск ресторанов...">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="btn-group float-end" role="group">
                                <button type="button" class="btn btn-outline-primary active">Все</button>
                                <button type="button" class="btn btn-outline-primary">Новые</button>
                                <button type="button" class="btn btn-outline-primary">Популярные</button>
                                <button type="button" class="btn btn-outline-primary">Рекомендуемые</button>
                            </div>
                        </div>
                    </div>
                    @endcan

                    @if($restaurants->count() > 0)
                    <div class="row">
                        @foreach($restaurants as $restaurant)
                        <div class="col-xl-4 col-md-6">
                            <div class="card o-hidden border-0 shadow-sm mb-4">
                                <div class="card-header p-0">

                                    <img src="https://picsum.photos/seed/{{ $restaurant->id }}/800/400.jpg" class="card-img-top" alt="{{ $restaurant->name }}">
                                    @if($restaurant->is_active)
                                    <span class="badge bg-success position-absolute top-0 end-0 m-2">Активен</span>
                                    @else
                                    <span class="badge bg-danger position-absolute top-0 end-0 m-2">Неактивен</span>
                                    @endif
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="card-title mb-0">{{ $restaurant->name }}</h5>
                                        @if(auth()->user()->role === 'admin')
                                        <span class="badge bg-info">Ваш ресторан</span>
                                        @endif
                                    </div>
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="text-warning me-2">
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star-half-alt"></i>
                                        </div>
                                        <span class="text-muted">4.5 (120 отзывов)</span>
                                    </div>
                                    <div class="mb-2">
                                        <i class="fa fa-phone me-2 text-primary"></i>
                                        <span class="text-muted">{{ $restaurant->phone ?? 'N/A' }}</span>
                                    </div>
                                    <div class="mb-3">
                                        <i class="fa fa-map-marker-alt me-2 text-primary"></i>
                                        <span class="text-muted">{{ Str::limit($restaurant->address, 40) }}</span>
                                    </div>
                                    <div class="mb-2">
                                        <small class="text-muted">Добавлен: {{ $restaurant->created_at->format('d.m.Y') }}</small>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent border-top-0 text-center">
                                    <div class="btn-group" role="group">
                                        @if(auth()->user()->hasPermissionTo('restaurant-view'))
                                        <button type="button"
                                            class="btn btn-outline-info btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#showRestaurantModal"
                                            data-id="{{ $restaurant->id }}"
                                            data-name="{{ $restaurant->name }}"
                                            data-phone="{{ $restaurant->phone ?? '' }}"
                                            data-description="{{ $restaurant->description ?? '' }}"
                                            data-address="{{ $restaurant->address ?? '' }}"
                                            data-longitude="{{ $restaurant->longitude ?? '' }}"
                                            data-latitude="{{ $restaurant->latitude ?? '' }}"
                                            data-is-active="{{ $restaurant->is_active ? '1' : '0' }}"
                                            data-qr-code="{{ $restaurant->qr_code ?? '' }}"
                                            title="Просмотр">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                        @endif

                                        @if(auth()->user()->hasPermissionTo('restaurant-edit'))
                                        <button type="button"
                                            class="btn btn-outline-warning btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editRestaurantModal"
                                            data-id="{{ $restaurant->id }}"
                                            data-name="{{ $restaurant->name }}"
                                            data-phone="{{ $restaurant->phone ?? '' }}"
                                            data-address="{{ $restaurant->address ?? '' }}"
                                            data-is-active="{{ $restaurant->is_active ? '1' : '0' }}"
                                            title="Редактировать">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        @endif

                                        @if(auth()->user()->hasPermissionTo('restaurant-delete'))
                                        <form action="{{ route('restaurants.destroy', $restaurant) }}"
                                            method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Вы действительно хотите удалить ресторан {{ $restaurant->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="btn btn-outline-danger btn-sm"
                                                data-bs-toggle="tooltip"
                                                title="Удалить">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="mt-4">
                        {{ $restaurants->links() }}
                    </div>
                    @else
                    <div class="alert alert-light text-center py-5" role="alert">
                        <i class="fa fa-store fa-3x mb-3 text-primary d-block"></i>
                        @if(auth()->user()->hasRole('admin'))
                        <h4 class="mb-3">Добро пожаловать, {{ auth()->user()->name }}!</h4>
                        <p class="mb-4 text-muted">Для начала работы вам необходимо зарегистрировать свой ресторан в системе.</p>
                        <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#createRestaurantModal">
                            <i class="fa fa-plus-circle me-2"></i> Создать мой ресторан
                        </button>
                        @else
                        <p>Пока не добавлено ни одного ресторана.</p>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@include('pages.restaurants.modals.create')
@include('pages.restaurants.modals.show')
@include('pages.restaurants.modals.edit')

@endsection

@push('scripts')
<script src="{{ asset('js/restaurants/index.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Blade ma'lumotlarini JS o'zgaruvchilariga olamiz
        const isCreateMode = new URLSearchParams(window.location.search).get('create') === '1';
        const isAdmin = {
            {
                auth() - > user() - > hasRole('admin') ? 'true' : 'false'
            }
        };

        if (isCreateMode) {
            const modalElement = document.getElementById('createRestaurantModal');

            if (modalElement) {
                const myModal = new bootstrap.Modal(modalElement, {
                    backdrop: 'static',
                    keyboard: false
                });
                myModal.show();

                if (isAdmin) {
                    const closeBtn = modalElement.querySelector('.btn-close');
                    const cancelBtn = modalElement.querySelector('.btn-outline-secondary');

                    if (closeBtn) closeBtn.style.display = 'none';
                    if (cancelBtn) cancelBtn.style.display = 'none';
                }
            }
        }
    });
</script>
@endpush