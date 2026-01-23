@extends('layouts.main')

@section('title', 'Рестораны')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="page-title">
                <div class="row">
                    <div class="col-6">
                        <h3>Рестораны</h3>
                    </div>
                    <div class="col-6">
                        <ol class="breadcrumb">
                            @role('superadmin')
                            <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}"><i class="fa fa-home"></i></a></li>
                            @else
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="fa fa-home"></i></a></li>
                            @endrole
                            <li class="breadcrumb-item active">Рестораны</li>
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
                <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa fa-exclamation-triangle"></i> {{ session('error') }}
                <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header pb-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5>Список ресторанов</h5>

                                @can(\App\Permissions\RestaurantPermissions::CREATE)
                                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createRestaurantModal">
                                    <i class="fa fa-plus"></i> Добавить ресторан
                                </button>
                                @endcan
                            </div>
                        </div>

                        <div class="card-body">
                            @if($restaurants->count() > 0)
                            <div class="row">
                                @foreach($restaurants as $restaurant)
                                <div class="col-xl-4 col-md-6 mb-4">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="position-relative">
                                            @if($restaurant->coverImage)
                                            <img src="{{ asset('storage/' . $restaurant->coverImage->image_path) }}" class="card-img-top" alt="{{ $restaurant->branch_name }}" style="height: 180px; object-fit: cover;">
                                            @else
                                            <img src="https://via.placeholder.com/400x200?text=No+Image" class="card-img-top" alt="No image" style="height: 180px; object-fit: cover;">
                                            @endif

                                            <span class="badge {{ $restaurant->is_active ? 'bg-success' : 'bg-danger' }} position-absolute top-0 end-0 m-2">
                                                {{ $restaurant->is_active ? 'Активен' : 'Неактивен' }}
                                            </span>
                                        </div>

                                        <div class="card-body pb-0">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="card-title mb-0 fw-bold">{{ $restaurant->branch_name }}</h6>
                                                @if($restaurant->brand)
                                                <span class="badge badge-info">{{ $restaurant->brand->name }}</span>
                                                @endif
                                            </div>

                                            <div class="mb-1">
                                                <small class="text-muted"><i class="fa fa-map-marker-alt me-1"></i> {{ $restaurant->city->translations->first()->name ?? 'N/A' }}</small>
                                            </div>

                                            @if($restaurant->phone)
                                            <div class="mb-1">
                                                <small class="text-muted"><i class="fa fa-phone me-1 text-primary"></i> {{ $restaurant->phone }}</small>
                                            </div>
                                            @endif

                                            @if($restaurant->address)
                                            <div class="mb-2">
                                                <small class="text-muted"><i class="fa fa-location-arrow me-1 text-primary"></i> {{ Str::limit($restaurant->address, 40) }}</small>
                                            </div>
                                            @endif

                                            @if($restaurant->categories->count() > 0)
                                            <div class="mb-2">
                                                @foreach($restaurant->categories->take(2) as $category)
                                                <span class="badge badge-secondary" style="font-size: 10px;">{{ $category->translations->first()->name ?? 'N/A' }}</span>
                                                @endforeach
                                                @if($restaurant->categories->count() > 2)
                                                <span class="badge badge-light" style="font-size: 10px;">+{{ $restaurant->categories->count() - 2 }}</span>
                                                @endif
                                            </div>
                                            @endif

                                            <div class="mb-2">
                                                <small style="font-size: 11px;" class="text-muted">Создан: {{ $restaurant->created_at->format('d.m.Y') }}</small>
                                            </div>
                                        </div>

                                        <div class="card-footer bg-transparent border-top-0 pt-0 px-3 pb-3">
                                            <div class="d-flex justify-content-center align-items-center gap-2">

                                                @can(\App\Permissions\RestaurantPermissions::VIEW)
                                                <button type="button" class="btn btn-outline-info btn-xs d-flex align-items-center justify-content-center view-btn"
                                                    data-id="{{ $restaurant->id }}"
                                                    style="width: 35px; height: 35px; padding: 0;">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                                @endcan

                                                @can('update', $restaurant)
                                                <button type="button" class="btn btn-outline-warning btn-xs d-flex align-items-center justify-content-center edit-btn"
                                                    data-id="{{ $restaurant->id }}"
                                                    style="width: 35px; height: 35px; padding: 0;">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                @endcan

                                                @can('delete', $restaurant)
                                                <form id="delete-restaurant-{{ $restaurant->id }}"
                                                    action="{{ route('restaurants.destroy', $restaurant->id) }}"
                                                    method="POST"
                                                    class="m-0 p-0"> @csrf
                                                    @method('DELETE')
                                                    <button type="button"
                                                        class="btn btn-outline-danger btn-xs d-flex align-items-center justify-content-center btn-delete-confirm"
                                                        data-form-id="delete-restaurant-{{ $restaurant->id }}"
                                                        data-title="Удалить ресторан?"
                                                        data-text="Вы уверены, что хотите удалить {{ $restaurant->branch_name }}?"
                                                        data-confirm-text="Да, удалить"
                                                        data-cancel-text="Отмена"
                                                        style="width: 35px; height: 35px; padding: 0;">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                                @endcan

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
                                @if(isset($needsRestaurant) && $needsRestaurant)
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
    </section>
</div>

@include('pages.restaurants.modals.create')
@include('pages.restaurants.modals.show')
@include('pages.restaurants.modals.edit')

@endsection

@push('styles')
<style>
    /* Custom marker for Leaflet map */
    .custom-marker {
        background: transparent;
        border: none;
    }

    /* Gradient header for show modal */
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    /* Card hover effects */
    .card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1) !important;
    }

    /* Image preview styling */
    #edit_images_preview .card img {
        transition: transform 0.2s ease-in-out;
    }

    #edit_images_preview .card:hover img {
        transform: scale(1.05);
    }

    /* Badge styling */
    .badge {
        font-weight: 500;
        padding: 0.35em 0.65em;
    }

    /* Delete button on images */
    .delete-image-btn {
        opacity: 0.8;
        transition: opacity 0.2s ease-in-out;
    }

    .delete-image-btn:hover {
        opacity: 1;
    }
</style>
@endpush

@push('scripts')
<script>
    // IMPORTANT: Disable Dropzone auto-discover BEFORE any modal opens
    Dropzone.autoDiscover = false;

    let createMap, createMarker;
    let editMap, editMarker;
    let createDropzone, editDropzone;

    // CREATE MODAL
    $('#createRestaurantModal').on('shown.bs.modal', function() {
        // Initialize Select2 for create modal
        $('#create_categories').select2({
            placeholder: 'Выберите категории',
            allowClear: true,
            dropdownParent: $('#createRestaurantModal')
        });

        // Initialize Map
        if (!createMap) {
            createMap = L.map('createMap').setView([42.4653, 59.6112], 12);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap'
            }).addTo(createMap);

            createMarker = L.marker([42.4653, 59.6112], {
                draggable: true
            }).addTo(createMap);

            function updateCreateCoords(lat, lng) {
                document.getElementById('create_latitude').value = lat;
                document.getElementById('create_longitude').value = lng;
            }

            createMarker.on('dragend', function(e) {
                let pos = createMarker.getLatLng();
                updateCreateCoords(pos.lat, pos.lng);
            });

            createMap.on('click', function(e) {
                createMarker.setLatLng(e.latlng);
                updateCreateCoords(e.latlng.lat, e.latlng.lng);
            });

            updateCreateCoords(42.4653, 59.6112);
        } else {
            createMap.invalidateSize();
        }

        // Initialize Dropzone
        if (!createDropzone) {
            createDropzone = new Dropzone("#restaurantImagesDropzone", {
                url: "{{ route('restaurants.store') }}",
                autoProcessQueue: false,
                uploadMultiple: true,
                parallelUploads: 5,
                maxFiles: 5,
                maxFilesize: 5,
                acceptedFiles: 'image/*',
                addRemoveLinks: true,
                dictDefaultMessage: "Перетащите файлы сюда или нажмите для загрузки",
                dictRemoveFile: "Удалить",
                dictMaxFilesExceeded: "Максимум 5 фотографий",
                dictInvalidFileType: "Допустимы только изображения (JPG, PNG)",

                init: function() {
                    let myDropzone = this;

                    $('#createRestaurantForm').on('submit', function(e) {
                        e.preventDefault();

                        let formData = new FormData(this);

                        let files = myDropzone.getAcceptedFiles();
                        files.forEach(function(file, index) {
                            formData.append('images[' + index + ']', file);
                        });

                        $.ajax({
                            url: $(this).attr('action'),
                            method: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            dataType: 'json',
                            success: function(response) {
                                console.log('Success response:', response);

                                if (response.success || response.message || response.restaurant) {
                                    swal({
                                        title: "Успешно!",
                                        text: response.message || "Ресторан успешно создан",
                                        icon: "success",
                                        button: "ОК",
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                } else {
                                    swal({
                                        title: "Внимание",
                                        text: "Ресторан создан, но получен неожиданный ответ",
                                        icon: "warning",
                                        button: "ОК",
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                }
                            },
                            error: function(xhr) {
                                console.error('Error response:', xhr);

                                if (xhr.status === 422) {
                                    let errors = xhr.responseJSON.errors;
                                    let errorList = '<ul style="text-align: left; margin: 0; padding-left: 20px;">';
                                    Object.keys(errors).forEach(function(key) {
                                        errors[key].forEach(function(error) {
                                            errorList += '<li>' + error + '</li>';
                                        });
                                    });
                                    errorList += '</ul>';

                                    swal({
                                        title: "Ошибка валидации",
                                        content: {
                                            element: "div",
                                            attributes: {
                                                innerHTML: errorList
                                            }
                                        },
                                        icon: "error",
                                        button: "Закрыть",
                                    });
                                } else if (xhr.status === 201 || xhr.status === 200) {
                                    // Sometimes 201 goes to error handler
                                    swal({
                                        title: "Успешно!",
                                        text: "Ресторан успешно создан",
                                        icon: "success",
                                        button: "ОК",
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                } else {
                                    let errorMessage = "Произошла ошибка при создании ресторана";
                                    if (xhr.responseJSON && xhr.responseJSON.message) {
                                        errorMessage = xhr.responseJSON.message;
                                    }

                                    swal({
                                        title: "Ошибка!",
                                        text: errorMessage,
                                        icon: "error",
                                        button: "Закрыть",
                                    });
                                }
                            }
                        });
                    });
                }
            });
        }
    });

    $('#createRestaurantModal').on('hidden.bs.modal', function() {
        if (createDropzone) {
            createDropzone.removeAllFiles(true);
        }
    });

    // EDIT MODAL
    $('#editRestaurantModal').on('shown.bs.modal', function() {
        // Initialize Select2 for edit modal
        $('#edit_categories').select2({
            placeholder: 'Выберите категории',
            allowClear: true,
            dropdownParent: $('#editRestaurantModal')
        });

        setTimeout(function() {
            if (!editMap) {
                let lat = parseFloat($('#edit_latitude').val()) || 42.4653;
                let lng = parseFloat($('#edit_longitude').val()) || 59.6112;

                editMap = L.map('editMap').setView([lat, lng], 12);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap'
                }).addTo(editMap);

                editMarker = L.marker([lat, lng], {
                    draggable: true
                }).addTo(editMap);

                function updateEditCoords(lat, lng) {
                    document.getElementById('edit_latitude').value = lat;
                    document.getElementById('edit_longitude').value = lng;
                }

                editMarker.on('dragend', function(e) {
                    let pos = editMarker.getLatLng();
                    updateEditCoords(pos.lat, pos.lng);
                });

                editMap.on('click', function(e) {
                    editMarker.setLatLng(e.latlng);
                    updateEditCoords(e.latlng.lat, e.latlng.lng);
                });
            } else {
                editMap.invalidateSize();
                let lat = parseFloat($('#edit_latitude').val()) || 42.4653;
                let lng = parseFloat($('#edit_longitude').val()) || 59.6112;
                editMap.setView([lat, lng], 12);
                editMarker.setLatLng([lat, lng]);
            }
        }, 300);

        if (!editDropzone) {
            editDropzone = new Dropzone("#editRestaurantImagesDropzone", {
                url: "{{ route('restaurants.store') }}",
                autoProcessQueue: false,
                uploadMultiple: true,
                parallelUploads: 5,
                maxFiles: 5,
                maxFilesize: 5,
                acceptedFiles: 'image/*',
                addRemoveLinks: true,
                dictDefaultMessage: "Добавить новые фотографии",
                dictRemoveFile: "Удалить",
                dictMaxFilesExceeded: "Максимум 5 новых фотографий",
                dictInvalidFileType: "Допустимы только изображения (JPG, PNG)",

                init: function() {
                    let myDropzone = this;

                    $('#editRestaurantForm').on('submit', function(e) {
                        e.preventDefault();

                        let formData = new FormData(this);

                        let files = myDropzone.getAcceptedFiles();
                        files.forEach(function(file, index) {
                            formData.append('images[' + index + ']', file);
                        });

                        $.ajax({
                            url: $(this).attr('action'),
                            method: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            dataType: 'json',
                            success: function(response) {
                                console.log('Success response:', response);

                                if (response.success || response.message || response.restaurant) {
                                    swal({
                                        title: "Успешно!",
                                        text: response.message || "Ресторан успешно обновлен",
                                        icon: "success",
                                        button: "ОК",
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                } else {
                                    swal({
                                        title: "Внимание",
                                        text: "Ресторан обновлен, но получен неожиданный ответ",
                                        icon: "warning",
                                        button: "ОК",
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                }
                            },
                            error: function(xhr) {
                                console.error('Error response:', xhr);

                                if (xhr.status === 422) {
                                    let errors = xhr.responseJSON.errors;
                                    let errorList = '<ul style="text-align: left; margin: 0; padding-left: 20px;">';
                                    Object.keys(errors).forEach(function(key) {
                                        errors[key].forEach(function(error) {
                                            errorList += '<li>' + error + '</li>';
                                        });
                                    });
                                    errorList += '</ul>';

                                    swal({
                                        title: "Ошибка валидации",
                                        content: {
                                            element: "div",
                                            attributes: {
                                                innerHTML: errorList
                                            }
                                        },
                                        icon: "error",
                                        button: "Закрыть",
                                    });
                                } else if (xhr.status === 200) {
                                    // Sometimes 200 goes to error handler
                                    swal({
                                        title: "Успешно!",
                                        text: "Ресторан успешно обновлен",
                                        icon: "success",
                                        button: "ОК",
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                } else {
                                    let errorMessage = "Произошла ошибка при обновлении ресторана";
                                    if (xhr.responseJSON && xhr.responseJSON.message) {
                                        errorMessage = xhr.responseJSON.message;
                                    }

                                    swal({
                                        title: "Ошибка!",
                                        text: errorMessage,
                                        icon: "error",
                                        button: "Закрыть",
                                    });
                                }
                            }
                        });
                    });
                }
            });
        } else {
            editDropzone.removeAllFiles(true);
        }
    });

    $('#editRestaurantModal').on('hidden.bs.modal', function() {
        if (editDropzone) {
            editDropzone.removeAllFiles(true);
        }
    });

    // VIEW button
    let showMap;
    $(document).on('click', '.view-btn', function() {
        let id = $(this).data('id');
        let url = "{{ route('restaurants.show', ':id') }}".replace(':id', id);

        $.ajax({
            url: url,
            method: 'GET',
            success: function(data) {
                console.log('Restaurant data:', data);

                // Set basic info
                $('#show-id').val(data.id);
                $('#show-name').text(data.branch_name);
                $('#show-brand-badge').html('<span class="badge bg-light text-dark"><i class="fa fa-store me-1"></i>' + data.brand + '</span>');
                $('#show-brand').text(data.brand);
                $('#show-city').text(data.city);
                $('#show-description').text(data.description || 'Нет описания');
                $('#show-phone').text(data.phone || 'Не указан');
                $('#show-address').text(data.address || 'Не указан');
                $('#show-latitude').text(data.latitude || 'N/A');
                $('#show-longitude').text(data.longitude || 'N/A');
                $('#show-created-at').text(data.created_at);

                // Status badge
                $('#show-status').removeClass('badge-success badge-danger bg-success bg-danger');
                if (data.is_active) {
                    $('#show-status').addClass('badge bg-success').html('<i class="fa fa-check-circle me-1"></i>Активен');
                } else {
                    $('#show-status').addClass('badge bg-danger').html('<i class="fa fa-times-circle me-1"></i>Неактивен');
                }

                // Categories
                if (data.category_names && data.category_names.length > 0) {
                    let categoriesHtml = '';
                    data.category_names.forEach(function(catName) {
                        categoriesHtml += '<span class="badge bg-warning text-dark me-1 mb-1" style="font-size: 0.9rem;">' + catName + '</span>';
                    });
                    $('#show-categories').html(categoriesHtml);
                } else {
                    $('#show-categories').html('<span class="text-muted"><i class="fa fa-info-circle me-1"></i>Нет категорий</span>');
                }

                // QR Code
                if (data.qr_code) {
                    $('#show-qr-code').attr('src', data.qr_code).show();
                    $('#no-qr-code').hide();
                } else {
                    $('#show-qr-code').hide();
                    $('#no-qr-code').show();
                }

                // Images
                if (data.images && data.images.length > 0) {
                    let imagesHtml = '';
                    data.images.forEach(function(img) {
                        imagesHtml += '<div class="mb-3 position-relative">';
                        imagesHtml += '<img src="' + img.url + '" class="img-fluid rounded shadow-sm" alt="Image" style="width: 100%; height: auto;">';
                        if (img.is_cover) {
                            imagesHtml += '<span class="position-absolute top-0 end-0 m-2">';
                            imagesHtml += '<span class="badge bg-success"><i class="fa fa-star me-1"></i>Обложка</span>';
                            imagesHtml += '</span>';
                        }
                        imagesHtml += '</div>';
                    });
                    $('#show-images').html(imagesHtml);
                } else {
                    $('#show-images').html('<div class="text-center text-muted py-4"><i class="fa fa-image fa-3x mb-2 d-block"></i><p>Нет изображений</p></div>');
                }

                // Update edit button
                $('#show-edit-button').attr('data-id', data.id);

                // Update delete form action
                let deleteUrl = "{{ route('restaurants.destroy', ':id') }}".replace(':id', data.id);
                $('#show-delete-form').attr('action', deleteUrl);

                // Show modal
                $('#showRestaurantModal').modal('show');

                // Initialize map after modal is shown
                setTimeout(function() {
                    if (data.latitude && data.longitude) {
                        let lat = parseFloat(data.latitude);
                        let lng = parseFloat(data.longitude);

                        if (!showMap) {
                            showMap = L.map('show-map').setView([lat, lng], 15);
                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                attribution: '© OpenStreetMap'
                            }).addTo(showMap);

                            // Custom marker icon
                            let customIcon = L.divIcon({
                                html: '<i class="fa fa-map-marker-alt fa-3x text-danger"></i>',
                                iconSize: [30, 30],
                                className: 'custom-marker'
                            });

                            L.marker([lat, lng], {
                                    icon: customIcon
                                }).addTo(showMap)
                                .bindPopup('<strong>' + data.branch_name + '</strong><br>' + data.address);
                        } else {
                            showMap.setView([lat, lng], 15);
                            showMap.eachLayer(function(layer) {
                                if (layer instanceof L.Marker) {
                                    showMap.removeLayer(layer);
                                }
                            });

                            let customIcon = L.divIcon({
                                html: '<i class="fa fa-map-marker-alt fa-3x text-danger"></i>',
                                iconSize: [30, 30],
                                className: 'custom-marker'
                            });

                            L.marker([lat, lng], {
                                    icon: customIcon
                                }).addTo(showMap)
                                .bindPopup('<strong>' + data.branch_name + '</strong><br>' + data.address);
                            showMap.invalidateSize();
                        }
                        $('#show-map-container').show();
                    } else {
                        $('#show-map-container').hide();
                    }
                }, 300);
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

    // EDIT button
    $(document).on('click', '.edit-btn', function() {
        let id = $(this).data('id');
        let url = "{{ route('restaurants.edit', ':id') }}".replace(':id', id);

        $('#editRestaurantForm').trigger("reset");
        $('.is-invalid').removeClass('is-invalid');
        $('#edit_images_preview').empty();

        if (editDropzone) {
            editDropzone.removeAllFiles(true);
        }

        $.ajax({
            url: url,
            method: 'GET',
            success: function(data) {
                $('#edit_restaurant_id').val(data.id);

                var userRole = '{{ auth()->user()->hasRole("admin") ? "admin" : "superadmin" }}';
                if (userRole !== 'admin') {
                    $('#edit_brand_id').val(data.brand_id);
                }

                $('#edit_city_id').val(data.city_id);
                $('#edit_branch_name').val(data.branch_name);
                $('#edit_phone').val(data.phone);
                $('#edit_address').val(data.address);
                $('#edit_description').val(data.description);
                $('#edit_latitude').val(data.latitude);
                $('#edit_longitude').val(data.longitude);
                $('#edit_is_active').val(data.is_active ? '1' : '0');

                // Set categories - wait for Select2 to initialize
                setTimeout(function() {
                    if (data.categories && data.categories.length > 0) {
                        $('#edit_categories').val(data.categories).trigger('change');
                    } else {
                        $('#edit_categories').val(null).trigger('change');
                    }
                }, 100);

                // Display existing images with better design
                if (data.images && data.images.length > 0) {
                    let imagesHtml = '';
                    data.images.forEach(function(img, index) {
                        imagesHtml += '<div class="col-4 col-md-3 mb-3 position-relative" id="image-' + img.id + '">';
                        imagesHtml += '<div class="card border shadow-sm h-100">';
                        imagesHtml += '<img src="' + img.url + '" class="card-img-top" alt="Image" style="height: 150px; object-fit: cover;">';
                        imagesHtml += '<div class="card-body p-2 text-center">';
                        if (img.is_cover) {
                            imagesHtml += '<span class="badge bg-success w-100"><i class="fa fa-star me-1"></i>Обложка</span>';
                        } else {
                            imagesHtml += '<span class="badge bg-secondary w-100">Фото ' + (index + 1) + '</span>';
                        }
                        imagesHtml += '</div>';
                        imagesHtml += '<button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 delete-image-btn" data-image-id="' + img.id + '" title="Удалить">';
                        imagesHtml += '<i class="fa fa-times"></i></button>';
                        imagesHtml += '</div>';
                        imagesHtml += '</div>';
                    });
                    $('#edit_images_preview').html(imagesHtml);
                } else {
                    $('#edit_images_preview').html('<div class="col-12"><p class="text-muted text-center py-3">Нет загруженных фотографий</p></div>');
                }

                let updateUrl = "{{ route('restaurants.update', ':id') }}".replace(':id', data.id);
                $('#editRestaurantForm').attr('action', updateUrl);

                $('#editRestaurantModal').modal('show');
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

    // Delete image button
    $(document).on('click', '.delete-image-btn', function() {
        let imageId = $(this).data('image-id');

        swal({
            title: "Удалить изображение?",
            text: "Это действие нельзя отменить!",
            icon: "warning",
            buttons: {
                cancel: {
                    text: "Отмена",
                    value: null,
                    visible: true,
                    closeModal: true,
                },
                confirm: {
                    text: "Да, удалить",
                    value: true,
                    visible: true,
                    className: "btn-danger",
                    closeModal: true
                }
            },
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                let url = "{{ url('restaurants/images') }}/" + imageId;

                $.ajax({
                    url: url,
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('#image-' + imageId).remove();
                        swal({
                            title: "Удалено!",
                            text: "Изображение успешно удалено",
                            icon: "success",
                            button: "ОК",
                        });
                    },
                    error: function(xhr) {
                        swal({
                            title: "Ошибка!",
                            text: "Ошибка при удалении изображения",
                            icon: "error",
                            button: "Закрыть",
                        });
                    }
                });
            }
        });
    });

    // Handle edit button from show modal
    $(document).on('click', '#show-edit-button', function() {
        let id = $(this).data('id');
        // Trigger edit button click
        setTimeout(function() {
            $('.edit-btn[data-id="' + id + '"]').trigger('click');
        }, 500);
    });

    // Auto-open modal
    var needsRestaurant = {
        {
            isset($needsRestaurant) && $needsRestaurant ? 'true' : 'false'
        }
    };
    if (needsRestaurant) {
        $(document).ready(function() {
            $('#createRestaurantModal').modal('show');
        });
    }
</script>
@endpush