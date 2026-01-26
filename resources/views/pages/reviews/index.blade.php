@extends('layouts.main')

@section('title', 'Отзывы и рейтинги')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="page-title">
                <div class="row">
                    <div class="col-6">
                        <h3>Отзывы и рейтинги</h3>
                    </div>
                    <div class="col-6">
                        <ol class="breadcrumb float-end">
                            @role('superadmin')
                            <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}"><i class="fa fa-home"></i></a></li>
                            @else
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="fa fa-home"></i></a></li>
                            @endrole
                            <li class="breadcrumb-item active">Отзывы</li>
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

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-muted small mb-1">Всего отзывов</div>
                                    <h4 class="mb-0 fw-bold">{{ number_format($statistics['total_reviews']) }}</h4>
                                </div>
                                <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                    <i class="fa fa-comments fa-2x text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-muted small mb-1">Средний рейтинг</div>
                                    <h4 class="mb-0 fw-bold">
                                        {{ $statistics['average_rating'] }}
                                        <span class="text-warning"><i class="fa fa-star"></i></span>
                                    </h4>
                                </div>
                                <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                                    <i class="fa fa-star fa-2x text-warning"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-muted small mb-1">5 звезд</div>
                                    <h4 class="mb-0 fw-bold text-success">{{ number_format($statistics['five_star']) }}</h4>
                                </div>
                                <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                    <i class="fa fa-thumbs-up fa-2x text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-muted small mb-1">1-2 звезды</div>
                                    <h4 class="mb-0 fw-bold text-danger">{{ number_format($statistics['one_star'] + $statistics['two_star']) }}</h4>
                                </div>
                                <div class="bg-danger bg-opacity-10 rounded-circle p-3">
                                    <i class="fa fa-thumbs-down fa-2x text-danger"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rating Distribution -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Распределение рейтингов</h5>
                        </div>
                        <div class="card-body">
                            @php
                                $maxCount = max($statistics['five_star'], $statistics['four_star'], $statistics['three_star'], $statistics['two_star'], $statistics['one_star']);
                            @endphp
                            @foreach([5, 4, 3, 2, 1] as $rating)
                                @php
                                    $count = $statistics[match($rating) {
                                        5 => 'five_star',
                                        4 => 'four_star',
                                        3 => 'three_star',
                                        2 => 'two_star',
                                        1 => 'one_star',
                                    }];
                                    $percentage = $statistics['total_reviews'] > 0 ? ($count / $statistics['total_reviews']) * 100 : 0;
                                @endphp
                                <div class="row align-items-center mb-2">
                                    <div class="col-auto">
                                        <span class="text-muted">{{ $rating }} <i class="fa fa-star text-warning"></i></span>
                                    </div>
                                    <div class="col">
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $percentage }}%" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                                                {{ number_format($percentage, 1) }}%
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <span class="badge bg-secondary">{{ $count }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reviews List -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Все отзывы</h5>
                                <form method="GET" action="{{ route('reviews.index') }}" class="d-flex gap-2">
                                    <select name="rating" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                                        <option value="">Все рейтинги</option>
                                        <option value="5" {{ request('rating') == 5 ? 'selected' : '' }}>5 звезд</option>
                                        <option value="4" {{ request('rating') == 4 ? 'selected' : '' }}>4 звезды</option>
                                        <option value="3" {{ request('rating') == 3 ? 'selected' : '' }}>3 звезды</option>
                                        <option value="2" {{ request('rating') == 2 ? 'selected' : '' }}>2 звезды</option>
                                        <option value="1" {{ request('rating') == 1 ? 'selected' : '' }}>1 звезда</option>
                                    </select>
                                </form>
                            </div>
                        </div>
                        <div class="card-body">
                            @forelse($reviews as $review)
                            <div class="card mb-3 border">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-2 text-center">
                                            @if($review->client->image_path)
                                            <img src="{{ asset('storage/' . $review->client->image_path) }}" alt="{{ $review->client->first_name }}" class="rounded-circle mb-2" style="width: 60px; height: 60px; object-fit: cover;">
                                            @else
                                            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 60px; height: 60px; font-size: 24px;">
                                                {{ strtoupper(substr($review->client->first_name, 0, 1)) }}
                                            </div>
                                            @endif
                                            <div class="fw-bold">{{ $review->client->first_name }} {{ $review->client->last_name }}</div>
                                            <small class="text-muted">{{ $review->created_at->format('d.m.Y') }}</small>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="mb-2">
                                                <span class="badge bg-secondary">{{ $review->restaurant->branch_name }}</span>
                                                <span class="ms-2">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($i <= $review->rating)
                                                            <i class="fa fa-star text-warning"></i>
                                                        @else
                                                            <i class="fa fa-star text-muted"></i>
                                                        @endif
                                                    @endfor
                                                </span>
                                            </div>
                                            @if($review->comment)
                                            <p class="mb-0">{{ $review->comment }}</p>
                                            @else
                                            <p class="mb-0 text-muted fst-italic">Комментарий отсутствует</p>
                                            @endif
                                        </div>
                                        <div class="col-md-2 text-end">
                                            @can('delete', $review)
                                            <form id="delete-review-{{ $review->id }}" action="{{ route('reviews.destroy', $review->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                    class="btn btn-outline-danger btn-sm btn-delete-confirm"
                                                    data-form-id="delete-review-{{ $review->id }}"
                                                    data-title="Удалить отзыв?"
                                                    data-text="Вы уверены, что хотите удалить этот отзыв?"
                                                    data-confirm-text="Да, удалить"
                                                    data-cancel-text="Отмена">
                                                    <i class="fa fa-trash"></i> Удалить
                                                </button>
                                            </form>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-5">
                                <i class="fa fa-comments fa-3x text-muted mb-3 d-block"></i>
                                <p class="text-muted">Отзывы не найдены</p>
                            </div>
                            @endforelse
                        </div>
                        <div class="card-footer bg-white">
                            {{ $reviews->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
