{{-- Show Restaurant Modal --}}
<div class="modal fade" id="showRestaurantModal" tabindex="-1" aria-labelledby="showRestaurantModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white">
                <div class="d-flex align-items-center w-100 justify-content-between">
                    <div>
                        <h4 class="modal-title mb-0" id="show-name"></h4>
                        <small id="show-brand-badge"></small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body">
                <input type="hidden" id="show-id" name="id">

                <div class="row">
                    {{-- Left Column --}}
                    <div class="col-lg-8">
                        {{-- Info Cards --}}
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3">
                                <div class="card border-start border-primary border-3 shadow-sm h-100">
                                    <div class="card-body">
                                        <h6 class="text-primary mb-3"><i class="fa fa-info-circle me-2"></i>Основная информация</h6>
                                        <div class="mb-2">
                                            <small class="text-muted d-block">Бренд</small>
                                            <strong id="show-brand"></strong>
                                        </div>
                                        <div class="mb-2">
                                            <small class="text-muted d-block">Город</small>
                                            <strong id="show-city"></strong>
                                        </div>
                                        <div class="mb-2">
                                            <small class="text-muted d-block">Статус</small>
                                            <span id="show-status" class="badge"></span>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block">Создан</small>
                                            <strong id="show-created-at"></strong>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="card border-start border-success border-3 shadow-sm h-100">
                                    <div class="card-body">
                                        <h6 class="text-success mb-3"><i class="fa fa-phone me-2"></i>Контакты</h6>
                                        <div class="mb-2">
                                            <small class="text-muted d-block">Телефон</small>
                                            <strong id="show-phone"></strong>
                                        </div>
                                        <div class="mb-2">
                                            <small class="text-muted d-block">Адрес</small>
                                            <strong id="show-address"></strong>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block">Координаты</small>
                                            <strong><span id="show-latitude"></span>, <span id="show-longitude"></span></strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Categories --}}
                        <div class="card shadow-sm mb-3">
                            <div class="card-body">
                                <h6 class="text-warning mb-3"><i class="fa fa-tags me-2"></i>Категории</h6>
                                <div id="show-categories"></div>
                            </div>
                        </div>

                        {{-- Description --}}
                        <div class="card shadow-sm mb-3">
                            <div class="card-body">
                                <h6 class="text-info mb-3"><i class="fa fa-align-left me-2"></i>Описание</h6>
                                <p id="show-description" class="mb-0"></p>
                            </div>
                        </div>

                        {{-- Map --}}
                        <div id="show-map-container" class="card shadow-sm">
                            <div class="card-body">
                                <h6 class="text-danger mb-3"><i class="fa fa-map-marked-alt me-2"></i>Расположение на карте</h6>
                                <div id="show-map" style="height: 350px; border-radius: 8px; border: 1px solid #e0e0e0;"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Right Column --}}
                    <div class="col-lg-4">
                        {{-- QR Code --}}
                        <div class="card shadow-sm mb-3">
                            <div class="card-body text-center">
                                <h6 class="mb-3"><i class="fa fa-qrcode me-2"></i>QR-код ресторана</h6>
                                <div id="qr-code-container">
                                    <img id="show-qr-code" src="" alt="QR-код" class="img-fluid rounded" style="max-width: 200px;">
                                    <div id="no-qr-code" class="text-muted" style="display: none;">
                                        <i class="fa fa-times-circle fa-3x mb-2 text-muted"></i>
                                        <p>QR-код не доступен</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Photos --}}
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h6 class="mb-3"><i class="fa fa-images me-2"></i>Фотографии ресторана</h6>
                                <div id="show-images">
                                    <!-- Rasmlar JavaScript orqali yuklanadi -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fa fa-times me-1"></i> Закрыть
                </button>

                {{-- Tahrirlash tugmasi --}}
                @if(auth()->user()->hasPermissionTo('edit_restaurant'))
                <button type="button"
                    id="show-edit-button"
                    class="btn btn-warning"
                    data-bs-toggle="modal"
                    data-bs-target="#editRestaurantModal"
                    data-bs-dismiss="modal">
                    <i class="fa fa-edit me-1"></i> Редактировать
                </button>
                @endif

                {{-- O'chirish tugmasi --}}
                @if(auth()->user()->hasPermissionTo('delete_restaurant'))
                <form id="show-delete-form" action="" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        id="show-delete-button"
                        class="btn btn-danger"
                        onclick="return confirm('Вы действительно хотите удалить этот ресторан?')">
                        <i class="fa fa-trash me-1"></i> Удалить
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>