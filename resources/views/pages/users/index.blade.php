@extends('layouts.main')

@section('title', 'Администраторы')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="page-title">
                <div class="row">
                    <div class="col-6">
                        <h3>Администраторы</h3>
                    </div>
                    <div class="col-6">
                        <ol class="breadcrumb">
                            @role('superadmin')
                            <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}"><i class="fa fa-home"></i></a></li>
                            @else
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="fa fa-home"></i></a></li>
                            @endrole
                            <li class="breadcrumb-item active">Администраторы</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header pb-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5>Все администраторы</h5>
                                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createUserModal">
                                    <i class="fa fa-plus"></i> Новый администратор
                                </button>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <form method="GET" action="{{ route('users.index') }}">
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
                                        <th>Имя</th>
                                        <th>Логин</th>
                                        <th>Ресторан</th>
                                        <th>Статус</th>
                                        <th style="width: 120px">Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $counter = ($users->currentPage() - 1) * $users->perPage() + 1; @endphp
                                    @forelse ($users as $user)
                                    @if($user->login !== 'superadmin')
                                    <tr>
                                        <td>{{ $counter++ }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->login }}</td>
                                        <td>
                                            @if($user->restaurants->isNotEmpty())
                                            <span class="badge badge-info">{{ $user->restaurants->first()->branch_name }}</span>
                                            @else
                                            <span class="text-muted">Не назначен</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge {{ $user->hasRole('admin') ? 'badge-warning' : 'badge-secondary' }}">
                                                {{ $user->hasRole('admin') ? 'Администратор' : 'Пользователь' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-outline-primary btn-sm edit-btn" data-id="{{ $user->id }}">
                                                    <i class="fa fa-edit"></i>
                                                </button>

                                                <form id="delete-form-{{ $user->id }}" action="{{ route('users.destroy', $user->id) }}" method="POST" style="margin-left: 5px;">
                                                    @csrf @method('DELETE')
                                                    <button type="button" class="btn btn-outline-danger btn-sm btn-delete-confirm" data-form-id="delete-form-{{ $user->id }}">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endif
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Данные не найдены</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer">{{ $users->links() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@include('pages.users.create')
@include('pages.users.edit') {{-- Endi bitta umumiy modal --}}

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.edit-btn').on('click', function() {
            let id = $(this).data('id');
            let url = "{{ route('users.edit', ':id') }}".replace(':id', id);

            $('#editUserForm').trigger("reset");
            $('.is-invalid').removeClass('is-invalid');

            $.ajax({
                url: url,
                method: 'GET',
                success: function(data) {
                    // Formaga ma'lumotlarni yuklash
                    $('#edit_user_id').val(data.id);
                    $('#edit_name').val(data.name);
                    $('#edit_login').val(data.login);

                    // Form action-ni dinamik o'zgartirish
                    let updateUrl = "{{ route('users.update', ':id') }}".replace(':id', data.id);
                    $('#editUserForm').attr('action', updateUrl);

                    $('#editUserModal').modal('show');
                }
            });
        });
    });
</script>
@endpush