<div class="p-2">
    @forelse($reviews as $review)
    <div class="review-card card mb-1 border-0 border-bottom">
        <div class="card-body py-2 px-3">
            <div class="row align-items-center g-2">
                <div class="col-auto">
                    @if($review->client)
                        @if($review->client->image_path)
                        <img src="{{ asset('storage/' . $review->client->image_path) }}"
                             alt="{{ $review->client->first_name }}"
                             class="rounded-circle"
                             style="width: 35px; height: 35px; object-fit: cover;">
                        @else
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center"
                             style="width: 35px; height: 35px; font-size: 14px;">
                            {{ strtoupper(substr($review->client->first_name, 0, 1)) }}
                        </div>
                        @endif
                    @else
                        <div class="bg-secondary text-white rounded-circle d-inline-flex align-items-center justify-content-center"
                             style="width: 35px; height: 35px;">
                            <i class="fa fa-user fa-sm"></i>
                        </div>
                    @endif
                </div>
                <div class="col">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <strong class="mb-0 small">
                            @if($review->client)
                                {{ $review->client->first_name }} {{ $review->client->last_name }}
                            @else
                                Гость
                            @endif
                        </strong>
                        @if(!$review->client)
                        <span class="badge bg-secondary badge-sm">Guest</span>
                        @endif
                        <span class="badge bg-light text-dark border small">{{ $review->restaurant->branch_name }}</span>
                        <div class="ms-auto d-flex align-items-center gap-1">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $review->rating)
                                <i class="fa fa-star text-warning" style="font-size: 12px;"></i>
                                @else
                                <i class="fa fa-star text-muted" style="font-size: 12px;"></i>
                                @endif
                            @endfor
                            <small class="text-muted ms-2">{{ $review->created_at->format('d.m.Y') }}</small>
                        </div>
                    </div>
                    @if($review->comment)
                    <p class="mb-0 mt-1 small text-secondary">{{ $review->comment }}</p>
                    @endif
                </div>
                <div class="col-auto">
                    @can('delete', $review)
                    <form id="delete-review-{{ $review->id }}" action="{{ route('reviews.destroy', $review->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="button"
                            class="btn btn-outline-danger btn-sm btn-delete-confirm"
                            data-form-id="delete-review-{{ $review->id }}"
                            data-title="Удалить отзыв?"
                            data-text="Вы уверены, что хотите удалить этот отзыв?"
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
    <div class="text-center py-5">
        <i class="fa fa-comments fa-3x text-muted mb-3 d-block"></i>
        <p class="text-muted mb-0">Отзывы не найдены</p>
    </div>
    @endforelse
</div>

@if($reviews->hasPages())
<div class="card-footer bg-white py-2 px-3">
    {{ $reviews->links() }}
</div>
@endif
