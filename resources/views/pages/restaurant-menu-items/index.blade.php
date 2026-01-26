@extends('layouts.main')

@section('title', 'Меню ресторана')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="page-title">
                <div class="row">
                    <div class="col-4">
                        <h3>{{ $restaurant->branch_name }} - Меню</h3>
                    </div>
                    <div class="col-8 d-flex justify-content-end align-items-center">
                        <div class="me-3">
                            <select class="form-select form-select-sm language-switcher" id="menu-language-select" style="width: auto;">
                                @php
                                    $currentLang = \App\Helpers\LanguageHelper::getCurrentLang();
                                    $allLanguages = \App\Models\Language::all();
                                @endphp
                                @foreach($allLanguages as $lang)
                                <option value="{{ $lang->code }}" {{ $currentLang == $lang->code ? 'selected' : '' }}>
                                    {{ $lang->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <ol class="breadcrumb mb-0">
                            @role('superadmin')
                            <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}"><i class="fa fa-home"></i></a></li>
                            @else
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="fa fa-home"></i></a></li>
                            @endrole
                            <li class="breadcrumb-item"><a href="{{ route('restaurants.index') }}">Рестораны</a></li>
                            <li class="breadcrumb-item active">Меню</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa fa-exclamation-triangle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header pb-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5>Меню ресторана</h5>
                                @can(\App\Permissions\RestaurantMenuItemPermissions::CREATE)
                                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addMenuItemModal">
                                    <i class="fa fa-plus"></i> Добавить блюдо
                                </button>
                                @endcan
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <form method="GET" action="{{ route('restaurant-menu-items.index', $restaurant->id) }}">
                                        <div class="input-group">
                                            <input type="text" name="search" class="form-control" placeholder="Поиск..." value="{{ request('search') }}">
                                            <button type="submit" class="btn btn-outline-info"><i class="fa fa-search"></i></button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="row">
                                @forelse($restaurantMenuItems as $rmi)
                                <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="position-relative">
                                            @if($rmi->menuItem->image_path)
                                            <img src="{{ asset('storage/' . $rmi->menuItem->image_path) }}" class="card-img-top" alt="{{ $rmi->menuItem->name }}" style="height: 150px; object-fit: cover;">
                                            @else
                                            <img src="https://via.placeholder.com/300x150?text=No+Image" class="card-img-top" alt="No image" style="height: 150px; object-fit: cover;">
                                            @endif

                                            <span class="badge {{ $rmi->is_available ? 'bg-success' : 'bg-danger' }} position-absolute top-0 start-0 m-2">
                                                {{ $rmi->is_available ? 'Доступно' : 'Недоступно' }}
                                            </span>

                                            <span class="badge bg-primary position-absolute top-0 end-0 m-2">
                                                {{ number_format($rmi->price, 0, '.', ' ') }} сум
                                            </span>
                                        </div>

                                        <div class="card-body pb-0">
                                            <h6 class="card-title mb-2 fw-bold">{{ $rmi->menuItem->name }}</h6>
                                            <p class="text-muted small mb-2">
                                                <i class="fa fa-folder text-primary me-1"></i> {{ $rmi->menuItem->menuSection->name }}
                                            </p>
                                            @if($rmi->menuItem->weight)
                                            <p class="text-muted small mb-2">
                                                <i class="fa fa-weight text-info me-1"></i> {{ $rmi->menuItem->weight }} г
                                            </p>
                                            @endif
                                            @if($rmi->menuItem->description)
                                            <p class="text-muted small mb-2">{{ Str::limit($rmi->menuItem->description, 60) }}</p>
                                            @endif
                                        </div>

                                        <div class="card-footer bg-transparent border-top-0 pt-0 px-3 pb-3">
                                            <div class="d-flex justify-content-center gap-2">
                                                @can('update', $rmi)
                                                <button type="button" class="btn btn-outline-warning btn-sm edit-btn" data-id="{{ $rmi->id }}">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                @endcan

                                                @can('delete', $rmi)
                                                <form id="delete-rmi-{{ $rmi->id }}" action="{{ route('restaurant-menu-items.destroy', $rmi->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button"
                                                        class="btn btn-outline-danger btn-sm btn-delete-confirm"
                                                        data-form-id="delete-rmi-{{ $rmi->id }}"
                                                        data-title="Удалить блюдо?"
                                                        data-text="Вы уверены, что хотите удалить {{ $rmi->menuItem->name }} из меню?"
                                                        data-confirm-text="Да, удалить"
                                                        data-cancel-text="Отмена">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="col-12">
                                    <div class="alert alert-light text-center py-5" role="alert">
                                        <i class="fa fa-utensils fa-3x mb-3 text-primary d-block"></i>
                                        <p>Меню ресторана пусто. Добавьте блюда!</p>
                                    </div>
                                </div>
                                @endforelse
                            </div>
                        </div>
                        <div class="card-footer">{{ $restaurantMenuItems->links() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@include('pages.restaurant-menu-items.add')
@include('pages.restaurant-menu-items.edit')

@endsection

@push('scripts')
<script>
    // Language Switcher
    $('.language-switcher').on('change', function() {
        let langCode = $(this).val();

        $.ajax({
            url: "{{ route('language.switch') }}",
            method: 'POST',
            data: {
                lang: langCode,
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                }
            },
            error: function(xhr) {
                console.error('Error switching language:', xhr);
            }
        });
    });

    $(document).on('click', '.edit-btn', function() {
        let id = $(this).data('id');
        let url = "{{ route('restaurant-menu-items.edit', ':id') }}".replace(':id', id);

        $('#editMenuItemForm').trigger("reset");
        $('.is-invalid').removeClass('is-invalid');

        $.ajax({
            url: url,
            method: 'GET',
            success: function(data) {
                $('#edit_rmi_id').val(data.id);
                $('#edit_menu_item_name').val(data.menu_item_name);
                $('#edit_price').val(data.price);
                $('#edit_is_available').prop('checked', data.is_available);

                let updateUrl = "{{ route('restaurant-menu-items.update', ':id') }}".replace(':id', data.id);
                $('#editMenuItemForm').attr('action', updateUrl);

                var editModal = new bootstrap.Modal(document.getElementById('editMenuItemModal'));
                editModal.show();
            },
            error: function(xhr) {
                console.error('Error:', xhr);
                swal({
                    title: "Ошибка!",
                    text: "Ошибка при загрузке данных",
                    icon: "error",
                    button: "Закрыть",
                });
            }
        });
    });
</script>
@endpush
