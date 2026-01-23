{{-- Show Restaurant Modal --}}
<div class="modal fade" id="showRestaurantModal" tabindex="-1" aria-labelledby="showRestaurantModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white" id="showRestaurantModalLabel">
                    <i class="fa fa-store me-2"></i><span id="show-name"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="show-id" name="id">

                <div class="row">
                    {{-- Left Column - Images Carousel --}}
                    <div class="col-lg-5 mb-3">
                        <div class="card">
                            <div class="card-header pb-2">
                                <h6 class="mb-0"><i class="fa fa-images me-1"></i> Фотографии</h6>
                            </div>
                            <div class="card-body">
                                <div class="owl-carousel owl-theme" id="restaurant-images-carousel">
                                    {{-- Images will be loaded via JavaScript --}}
                                </div>
                            </div>
                        </div>

                        {{-- QR Code --}}
                        <div class="card mt-3">
                            <div class="card-header pb-2">
                                <h6 class="mb-0"><i class="fa fa-qrcode me-1"></i> QR-код</h6>
                            </div>
                            <div class="card-body text-center py-3">
                                <img id="show-qr-code" src="" alt="QR-код" class="img-fluid" style="max-width: 180px;">
                                <div id="no-qr-code" class="text-muted" style="display: none;">
                                    <i class="fa fa-exclamation-circle fa-2x mb-2"></i>
                                    <p class="mb-0">QR-код не доступен</p>
                                </div>
                            </div>
                        </div>

                        {{-- Map --}}
                        <div id="show-map-container" class="card mt-3">
                            <div class="card-header pb-2">
                                <h6 class="mb-0"><i class="fa fa-map me-1"></i> Местоположение</h6>
                            </div>
                            <div class="card-body p-0">
                                <div id="show-map" style="height: 300px;"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Right Column - Details --}}
                    <div class="col-lg-7">
                        {{-- Basic Info --}}
                        <div class="card mb-3">
                            <div class="card-header pb-2">
                                <h6 class="mb-0"><i class="fa fa-info-circle me-1"></i> Основная информация</h6>
                            </div>
                            <div class="card-body py-2">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <small class="text-muted d-block mb-1">Бренд</small>
                                        <p class="mb-2 fw-bold" id="show-brand">-</p>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted d-block mb-1">Город</small>
                                        <p class="mb-2 fw-bold" id="show-city">-</p>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted d-block mb-1">Статус</small>
                                        <div>
                                            <span id="show-status" class="badge"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted d-block mb-1">Дата создания</small>
                                        <p class="mb-2" id="show-created-at">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Contact Info --}}
                        <div class="card mb-3">
                            <div class="card-header pb-2">
                                <h6 class="mb-0"><i class="fa fa-phone me-1"></i> Контакты</h6>
                            </div>
                            <div class="card-body py-2">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <small class="text-muted d-block mb-1"><i class="fa fa-phone-alt me-1"></i> Телефон</small>
                                        <p class="mb-2" id="show-phone">-</p>
                                    </div>
                                    <div class="col-12">
                                        <small class="text-muted d-block mb-1"><i class="fa fa-location-arrow me-1"></i> Адрес</small>
                                        <p class="mb-2" id="show-address">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Categories --}}
                        <div class="card mb-3">
                            <div class="card-header pb-2">
                                <h6 class="mb-0"><i class="fa fa-tags me-1"></i> Категории</h6>
                            </div>
                            <div class="card-body py-2">
                                <div id="show-categories">-</div>
                            </div>
                        </div>

                        {{-- Description --}}
                        <div class="card mb-3">
                            <div class="card-header pb-2">
                                <h6 class="mb-0"><i class="fa fa-align-left me-1"></i> Описание</h6>
                            </div>
                            <div class="card-body py-2">
                                <p id="show-description" class="mb-0">-</p>
                            </div>
                        </div>

                        {{-- Operating Hours --}}
                        <div class="card mb-3">
                            <div class="card-header pb-2">
                                <h6 class="mb-0"><i class="fa fa-clock me-1"></i> Режим работы</h6>
                            </div>
                            <div class="card-body py-2">
                                <div id="show-operating-hours">
                                    <small class="text-muted">Загрузка...</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fa fa-times me-1"></i> Закрыть
                </button>

                @if(auth()->user()->hasPermissionTo('edit_restaurant'))
                <button type="button" id="show-edit-button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editRestaurantModal" data-bs-dismiss="modal">
                    <i class="fa fa-edit me-1"></i> Редактировать
                </button>
                @endif

                @if(auth()->user()->hasPermissionTo('delete_restaurant'))
                <form id="show-delete-form" action="" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" id="show-delete-button" class="btn btn-danger" onclick="return confirm('Вы действительно хотите удалить этот ресторан?')">
                        <i class="fa fa-trash me-1"></i> Удалить
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>