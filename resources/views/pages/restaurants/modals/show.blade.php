{{-- Show Restaurant Modal --}}
<div class="modal fade" id="showRestaurantModal" tabindex="-1" aria-labelledby="showRestaurantModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="showRestaurantModalLabel">
                    <span id="show-name"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="show-id" name="id">

                <div class="row">
                    {{-- Left Column --}}
                    <div class="col-lg-8">
                        {{-- Basic Info --}}
                        <div class="card mb-3">
                            <div class="card-header pb-0">
                                <h6 class="f-w-600">Основная информация</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted">Бренд</label>
                                        <p class="f-w-600" id="show-brand">-</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted">Город</label>
                                        <p class="f-w-600" id="show-city">-</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted">Статус</label>
                                        <div>
                                            <span id="show-status" class="badge"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted">Дата создания</label>
                                        <p class="f-w-600" id="show-created-at">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Contact Info --}}
                        <div class="card mb-3">
                            <div class="card-header pb-0">
                                <h6 class="f-w-600">Контактная информация</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted">Телефон</label>
                                        <p id="show-phone">-</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted">Адрес</label>
                                        <p id="show-address">-</p>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label text-muted">Координаты</label>
                                        <p><span id="show-latitude">-</span>, <span id="show-longitude">-</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Categories --}}
                        <div class="card mb-3">
                            <div class="card-header pb-0">
                                <h6 class="f-w-600">Категории</h6>
                            </div>
                            <div class="card-body">
                                <div id="show-categories">-</div>
                            </div>
                        </div>

                        {{-- Description --}}
                        <div class="card mb-3">
                            <div class="card-header pb-0">
                                <h6 class="f-w-600">Описание</h6>
                            </div>
                            <div class="card-body">
                                <p id="show-description" class="mb-0">-</p>
                            </div>
                        </div>

                        {{-- Map --}}
                        <div id="show-map-container" class="card">
                            <div class="card-header pb-0">
                                <h6 class="f-w-600">Расположение на карте</h6>
                            </div>
                            <div class="card-body">
                                <div id="show-map" style="height: 350px;"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Right Column --}}
                    <div class="col-lg-4">
                        {{-- QR Code --}}
                        <div class="card mb-3">
                            <div class="card-header pb-0">
                                <h6 class="f-w-600">QR-код</h6>
                            </div>
                            <div class="card-body text-center">
                                <div id="qr-code-container">
                                    <img id="show-qr-code" src="" alt="QR-код" class="img-fluid" style="max-width: 200px;">
                                    <div id="no-qr-code" class="text-muted" style="display: none;">
                                        <p>QR-код не доступен</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Photos --}}
                        <div class="card">
                            <div class="card-header pb-0">
                                <h6 class="f-w-600">Фотографии</h6>
                            </div>
                            <div class="card-body">
                                <div id="show-images">
                                    <!-- Rasmlar JavaScript orqali yuklanadi -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>

                {{-- Tahrirlash tugmasi --}}
                @if(auth()->user()->hasPermissionTo('edit_restaurant'))
                <button type="button"
                    id="show-edit-button"
                    class="btn btn-warning"
                    data-bs-toggle="modal"
                    data-bs-target="#editRestaurantModal"
                    data-bs-dismiss="modal">
                    <i class="fa fa-edit"></i> Редактировать
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
                        <i class="fa fa-trash"></i> Удалить
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>