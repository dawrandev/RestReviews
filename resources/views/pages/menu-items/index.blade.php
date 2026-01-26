@extends('layouts.main')

@section('title', 'Блюда')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="page-title">
                <div class="row">
                    <div class="col-4">
                        <h3>Блюда</h3>
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
                            <li class="breadcrumb-item active">Блюда</li>
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
                                <h5>Все блюда</h5>
                                @can(\App\Permissions\MenuItemPermissions::CREATE)
                                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createItemModal">
                                    <i class="fa fa-plus"></i> Новое блюдо
                                </button>
                                @endcan
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <form method="GET" action="{{ route('menu-items.index') }}">
                                        <div class="input-group">
                                            <input type="text" name="search" class="form-control" placeholder="Поиск..." value="{{ request('search') }}">
                                            <button type="submit" class="btn btn-outline-info"><i class="fa fa-search"></i></button>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-md-6">
                                    <form method="GET" action="{{ route('menu-items.index') }}" id="filterForm">
                                        <select name="section_id" class="form-select section-filter-select" id="section_filter">
                                            <option value="">Все разделы</option>
                                            @foreach($sections as $section)
                                            <option value="{{ $section->id }}" {{ request('section_id') == $section->id ? 'selected' : '' }}>
                                                {{ $section->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </form>
                                </div>
                            </div>
                            {{$menuItems}}
                            <div class="row">
                                @forelse($menuItems as $item)
                                <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="position-relative">
                                            @if($item->image_path)
                                            <img src="{{ asset('storage/' . $item->image_path) }}" class="card-img-top" alt="{{ $item->name }}" style="height: 150px; object-fit: cover;">
                                            @else
                                            <img src="https://via.placeholder.com/300x150?text=No+Image" class="card-img-top" alt="No image" style="height: 150px; object-fit: cover;">
                                            @endif
                                            @if($item->base_price)
                                            <span class="badge bg-primary position-absolute top-0 end-0 m-2">
                                                {{ number_format($item->base_price, 0, '.', ' ') }} сум
                                            </span>
                                            @endif
                                        </div>

                                        <div class="card-body pb-0">
                                            <h6 class="card-title mb-2 fw-bold">{{ $item->name }}</h6>
                                            <p class="text-muted small mb-2">
                                                <i class="fa fa-folder text-primary me-1"></i> {{ $item->menuSection->name }}
                                            </p>
                                            @if($item->weight)
                                            <p class="text-muted small mb-2">
                                                <i class="fa fa-weight text-info me-1"></i> {{ $item->weight }}
                                            </p>
                                            @endif
                                            @if($item->description)
                                            <p class="text-muted small mb-2">{{ Str::limit($item->description, 60) }}</p>
                                            @endif
                                        </div>

                                        <div class="card-footer bg-transparent border-top-0 pt-0 px-3 pb-3">
                                            <div class="d-flex justify-content-center gap-2">
                                                @can('update', $item)
                                                <button type="button" class="btn btn-outline-warning btn-sm edit-btn" data-id="{{ $item->id }}">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                @endcan

                                                @can('delete', $item)
                                                <form id="delete-item-{{ $item->id }}" action="{{ route('menu-items.destroy', $item->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button"
                                                        class="btn btn-outline-danger btn-sm btn-delete-confirm"
                                                        data-form-id="delete-item-{{ $item->id }}"
                                                        data-title="Удалить блюдо?"
                                                        data-text="Вы уверены, что хотите удалить {{ $item->name }}?"
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
                                        <p>Блюда не найдены</p>
                                    </div>
                                </div>
                                @endforelse
                            </div>
                        </div>
                        <div class="card-footer">{{ $menuItems->links() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@include('pages.menu-items.create')
@include('pages.menu-items.edit')

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize Select2 for filter
        if ($.fn.select2) {
            $('#section_filter').select2({
                placeholder: 'Все разделы',
                allowClear: true,
                width: '100%'
            }).on('change', function() {
                $('#filterForm').submit();
            });

            // Initialize Select2 for create modal
            $('#menu_section_id').select2({
                placeholder: 'Выберите раздел',
                allowClear: true,
                dropdownParent: $('#createItemModal'),
                width: '100%'
            });

            // Initialize Select2 for edit modal
            $('#edit_menu_section_id').select2({
                placeholder: 'Выберите раздел',
                allowClear: true,
                dropdownParent: $('#editItemModal'),
                width: '100%'
            });
        }
    });

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
        let url = "{{ route('menu-items.edit', ':id') }}".replace(':id', id);

        $('#editItemForm').trigger("reset");
        $('.is-invalid').removeClass('is-invalid');
        $('#edit_current_image').attr('src', '').hide();

        $.ajax({
            url: url,
            method: 'GET',
            success: function(data) {
                $('#edit_item_id').val(data.id);
                $('#edit_menu_section_id').val(data.menu_section_id).trigger('change');
                $('#edit_base_price').val(data.base_price);
                $('#edit_weight').val(data.weight);

                // Fill translations
                @foreach($languages as $lang)
                if (data.translations['{{ $lang->code }}']) {
                    $('#edit_name_{{ $lang->code }}').val(data.translations['{{ $lang->code }}'].name);
                    $('#edit_description_{{ $lang->code }}').val(data.translations['{{ $lang->code }}'].description);
                }
                @endforeach

                if (data.image_url) {
                    $('#edit_current_image').attr('src', data.image_url).show();
                }

                let updateUrl = "{{ route('menu-items.update', ':id') }}".replace(':id', data.id);
                $('#editItemForm').attr('action', updateUrl);

                var editModal = new bootstrap.Modal(document.getElementById('editItemModal'));
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