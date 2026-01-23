@extends('layouts.main')

@section('title', 'Города')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="page-title">
                <div class="row">
                    <div class="col-6">
                        <h3>Города</h3>
                    </div>
                    <div class="col-6">
                        <ol class="breadcrumb">
                            @role('superadmin')
                            <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}"><i class="fa fa-home"></i></a></li>
                            @else
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="fa fa-home"></i></a></li>
                            @endrole
                            <li class="breadcrumb-item active">Города</li>
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
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header pb-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5>Все города</h5>
                                @can(\App\Permissions\CityPermissions::CREATE)
                                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createCityModal">
                                    <i class="fa fa-plus"></i> Новый город
                                </button>
                                @endcan
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <form method="GET" action="{{ route('cities.index') }}">
                                        <div class="input-group">
                                            <input type="text" name="search" class="form-control" placeholder="Поиск..." value="{{ request('search') }}">
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-outline-info"><i class="fa fa-search"></i></button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">№</th>
                                        <th>Переводы</th>
                                        <th style="width: 100px;">Рестораны</th>
                                        <th style="width: 120px">Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $counter = ($cities->currentPage() - 1) * $cities->perPage() + 1; @endphp
                                    @forelse ($cities as $city)
                                    <tr>
                                        <td>{{ $counter++ }}</td>
                                        <td>
                                            @if($city->translations->count() > 0)
                                            @foreach($city->translations as $translation)
                                            <span class="badge badge-info me-1">{{ strtoupper($translation->code) }}: {{ $translation->name }}</span>
                                            @endforeach
                                            @else
                                            <span class="text-muted">Нет переводов</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-info">{{ $city->restaurants_count }}</span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                @can(\App\Permissions\CityPermissions::UPDATE)
                                                <button type="button" class="btn btn-outline-primary btn-sm edit-btn" data-id="{{ $city->id }}">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                @endcan

                                                @can(\App\Permissions\CityPermissions::DELETE)
                                                <form id="delete-city-{{ $city->id }}" action="{{ route('cities.destroy', $city->id) }}" method="POST" style="margin-left: 5px;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button"
                                                        class="btn btn-outline-danger btn-sm btn-delete-confirm"
                                                        data-form-id="delete-city-{{ $city->id }}"
                                                        data-title="Удалить город?"
                                                        data-text="Вы уверены, что хотите удалить этот город?"
                                                        data-confirm-text="Да, удалить"
                                                        data-cancel-text="Отмена">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Данные не найдены</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer">{{ $cities->links() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@include('pages.cities.create')
@include('pages.cities.edit')

@endsection
@push('scripts')
<script>
    $(document).ready(function() {
        // Edit button
        $(document).on('click', '.edit-btn', function() {
            let id = $(this).data('id');
            let url = "{{ route('cities.edit', ':id') }}".replace(':id', id);

            $('#editCityForm').trigger("reset");
            $('.is-invalid').removeClass('is-invalid');

            // Clear all translation fields first
            $('[id^="edit_translation_"]').val('');

            $.ajax({
                url: url,
                method: 'GET',
                success: function(data) {
                    $('#edit_city_id').val(data.id);
                    // edit_name fieldini olib tashladik

                    // Load translations
                    if (data.translations) {
                        $.each(data.translations, function(langCode, name) {
                            $('#edit_translation_' + langCode).val(name);
                        });
                    }

                    let updateUrl = "{{ route('cities.update', ':id') }}".replace(':id', data.id);
                    $('#editCityForm').attr('action', updateUrl);

                    $('#editCityModal').modal('show');
                },
                error: function(xhr) {
                    console.error('Error:', xhr);
                    alert('Ошибка при загрузке данных');
                }
            });
        });
    });
</script>
@endpush