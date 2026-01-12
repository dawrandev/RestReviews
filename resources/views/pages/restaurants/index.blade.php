@extends('layouts.main')

@section('content')
<div class="container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-6">
                <h3>Restoranlar</h3>
            </div>
            <div class="col-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i data-feather="home"></i></a></li>
                    <li class="breadcrumb-item active">Restoranlar</li>
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
                        <h5>Restoranlar Ro'yxati</h5>

                        {{-- Faqat superadmin yangi restoran qo'sha oladi --}}
                        @role('superadmin')
                        <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#createRestaurantModal">
                            <i class="fa fa-plus"></i> Yangi Restoran
                        </button>
                        @endrole
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

                    @if($restaurants->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nomi</th>
                                    <th>Manzil</th>
                                    <th>Telefon</th>
                                    <th>Email</th>
                                    <th>Qo'shilgan</th>
                                    <th class="text-end">Harakatlar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($restaurants as $restaurant)
                                <tr>
                                    <td>{{ $restaurant->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">{{ $restaurant->name }}</h6>
                                                @role('admin')
                                                <span class="badge badge-info">Sizning restoraningiz</span>
                                                @endrole
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ Str::limit($restaurant->address, 30) }}</td>
                                    <td>{{ $restaurant->phone }}</td>
                                    <td>{{ $restaurant->email ?? 'N/A' }}</td>
                                    <td>{{ $restaurant->created_at->format('d.m.Y') }}</td>
                                    <td class="text-end">
                                        <div class="btn-group" role="group">
                                            {{-- Ko'rish - Admin va Superadmin --}}
                                            <button class="btn btn-info btn-sm" type="button"
                                                onclick="viewRestaurant({{ $restaurant->id }})"
                                                data-bs-toggle="tooltip" title="Ko'rish">
                                                <i class="fa fa-eye"></i>
                                            </button>

                                            {{-- Tahrirlash - Faqat Superadmin --}}
                                            @role('superadmin')
                                            <button class="btn btn-warning btn-sm" type="button"
                                                onclick="editRestaurant({{ $restaurant }})"
                                                data-bs-toggle="tooltip" title="Tahrirlash">
                                                <i class="fa fa-edit"></i>
                                            </button>

                                            {{-- O'chirish - Faqat Superadmin --}}
                                            <button class="btn btn-danger btn-sm" type="button"
                                                onclick="deleteRestaurant({{ $restaurant->id }}, '{{ $restaurant->name }}')"
                                                data-bs-toggle="tooltip" title="O'chirish">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                            @endrole
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-3">
                        {{ $restaurants->links() }}
                    </div>
                    @else
                    <div class="alert alert-light text-center" role="alert">
                        <i class="fa fa-info-circle fa-2x mb-3 d-block"></i>
                        @role('superadmin')
                        <p>Hozircha birorta ham restoran qo'shilmagan.</p>
                        <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#createRestaurantModal">
                            <i class="fa fa-plus"></i> Birinchi Restoranni Qo'shish
                        </button>
                        @endrole

                        @role('admin')
                        <p>Sizga hali restoran biriktirilmagan. Administrator bilan bog'laning.</p>
                        @endrole
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Create Modal --}}
@role('superadmin')
@include('restaurants.modals.create')
@endrole

{{-- Edit Modal --}}
@role('superadmin')
@include('restaurants.modals.edit')
@endrole

{{-- View Modal --}}
@include('restaurants.modals.view')

{{-- Delete Form (hidden) --}}
<form id="deleteRestaurantForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
    // Tooltip'larni faollashtirish
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Restoranni ko'rish
    function viewRestaurant(id) {
        fetch(`/restaurants/${id}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('view_name').textContent = data.name;
                document.getElementById('view_address').textContent = data.address;
                document.getElementById('view_phone').textContent = data.phone;
                document.getElementById('view_email').textContent = data.email || 'N/A';
                document.getElementById('view_description').textContent = data.description || 'Tavsif yo\'q';
                document.getElementById('view_created').textContent = new Date(data.created_at).toLocaleDateString('uz-UZ');

                new bootstrap.Modal(document.getElementById('viewRestaurantModal')).show();
            })
            .catch(error => console.error('Error:', error));
    }

    @role('superadmin')
    // Restoranni tahrirlash
    function editRestaurant(restaurant) {
        document.getElementById('edit_restaurant_id').value = restaurant.id;
        document.getElementById('edit_name').value = restaurant.name;
        document.getElementById('edit_address').value = restaurant.address;
        document.getElementById('edit_phone').value = restaurant.phone;
        document.getElementById('edit_email').value = restaurant.email || '';
        document.getElementById('edit_description').value = restaurant.description || '';

        document.getElementById('editRestaurantForm').action = `/restaurants/${restaurant.id}`;

        new bootstrap.Modal(document.getElementById('editRestaurantModal')).show();
    }

    // Restoranni o'chirish
    function deleteRestaurant(id, name) {
        if (confirm(`Rostdan ham "${name}" restoranini o'chirmoqchimisiz?`)) {
            const form = document.getElementById('deleteRestaurantForm');
            form.action = `/restaurants/${id}`;
            form.submit();
        }
    }
    @endrole
</script>
@endpush