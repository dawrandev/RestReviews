<div class="modal fade" id="editRestaurantModal" tabindex="-1" aria-labelledby="editRestaurantModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editRestaurantModalLabel">Редактировать ресторан</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editRestaurantForm" action="" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_restaurant_id" name="restaurant_id">

                <div class="modal-body">
                    <div class="row">
                        {{-- Brand (always read-only in edit mode) --}}
                        <div class="col-md-4 mb-3">
                            <label for="edit_brand_id" class="form-label">Бренд <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_brand_name_display" value="" readonly style="background-color: #e9ecef;">
                            <input type="hidden" id="edit_brand_id" name="brand_id">
                        </div>

                        {{-- City --}}
                        <div class="col-md-4 mb-3">
                            <label for="edit_city_id" class="form-label">Город <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_city_id" name="city_id" required>
                                <option value="">Выберите город</option>
                                @foreach(getCities() as $city)
                                <option value="{{ $city->id }}">
                                    {{ $city->translations->first()->name ?? 'N/A' }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Branch Name --}}
                        <div class="col-md-4 mb-3">
                            <label for="edit_branch_name" class="form-label">Название филиала <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_branch_name" name="branch_name" required>
                        </div>
                    </div>

                    <div class="row">
                        {{-- Phone --}}
                        <div class="col-md-6 mb-3">
                            <label for="edit_phone" class="form-label">Телефон</label>
                            <input type="text" class="form-control" id="edit_phone" name="phone">
                        </div>

                        {{-- Status --}}
                        <div class="col-md-6 mb-3">
                            <label for="edit_is_active" class="form-label">Статус</label>
                            <select class="form-select" id="edit_is_active" name="is_active">
                                <option value="1">Активен</option>
                                <option value="0">Неактивен</option>
                            </select>
                        </div>
                    </div>

                    {{-- Address --}}
                    <div class="mb-3">
                        <label for="edit_address" class="form-label">Адрес <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_address" name="address" required>
                    </div>

                    {{-- Categories with Select2 --}}
                    <div class="mb-3">
                        <label for="edit_categories" class="form-label">Категории</label>
                        <select class="form-control select2-edit-categories" id="edit_categories" name="categories[]" multiple="multiple">
                            @foreach(getCategories() as $category)
                            <option value="{{ $category->id }}">
                                {{ $category->translations->first()->name ?? 'N/A' }}
                            </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Выберите одну или несколько категорий</small>
                    </div>

                    {{-- Map --}}
                    <div class="mb-3">
                        <label class="form-label">Местоположение на карте</label>
                        <div id="editMap" style="height: 400px; border-radius: 8px; border: 1px solid #ddd;"></div>
                        <input type="hidden" name="latitude" id="edit_latitude">
                        <input type="hidden" name="longitude" id="edit_longitude">
                        <small class="text-muted d-block mt-2">Кликните на карту или перетащите маркер для изменения местоположения</small>
                    </div>

                    {{-- Existing Images --}}
                    <div class="mb-3">
                        <label class="form-label">Текущие фотографии</label>
                        <div class="row" id="edit_images_preview">
                            <div class="col-12">
                                <p class="text-muted text-center">Загрузка...</p>
                            </div>
                        </div>
                        <small class="text-muted">Нажмите на кнопку <i class="fa fa-times"></i> чтобы удалить фотографию</small>
                    </div>

                    {{-- Dropzone for New Images --}}
                    <div class="mb-3">
                        <label class="form-label">Добавить новые фотографии</label>
                        <div class="dropzone" id="editRestaurantImagesDropzone">
                            <div class="dz-message needsclick">
                                <i class="icon-cloud-up"></i>
                                <h6>Перетащите файлы сюда или нажмите для загрузки</h6>
                                <span class="note needsclick">Максимум 5 новых фотографий</span>
                            </div>
                        </div>
                        <small class="text-muted">Форматы: JPG, PNG. Максимальный размер: 5MB на файл</small>
                    </div>

                    {{-- Description --}}
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Описание</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="4"></textarea>
                    </div>

                    {{-- Operating Hours --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Режим работы <span class="text-danger">*</span></label>
                        <div class="card">
                            <div class="card-body">
                                @php
                                $daysOfWeek = [
                                0 => 'Воскресенье',
                                1 => 'Понедельник',
                                2 => 'Вторник',
                                3 => 'Среда',
                                4 => 'Четверг',
                                5 => 'Пятница',
                                6 => 'Суббота',
                                ];
                                @endphp

                                @foreach($daysOfWeek as $dayNumber => $dayName)
                                <div class="row mb-3 align-items-center">
                                    <div class="col-md-3">
                                        <label class="form-label mb-0">{{ $dayName }}</label>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="time"
                                            class="form-control form-control-sm opening-time"
                                            name="operating_hours[{{ $dayNumber }}][opening_time]"
                                            id="edit_opening_time_{{ $dayNumber }}"
                                            data-day="{{ $dayNumber }}">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="time"
                                            class="form-control form-control-sm closing-time"
                                            name="operating_hours[{{ $dayNumber }}][closing_time]"
                                            id="edit_closing_time_{{ $dayNumber }}"
                                            data-day="{{ $dayNumber }}">
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input is-closed-check"
                                                type="checkbox"
                                                name="operating_hours[{{ $dayNumber }}][is_closed]"
                                                value="1"
                                                id="edit_is_closed_{{ $dayNumber }}"
                                                data-day="{{ $dayNumber }}">
                                            <label class="form-check-label" for="edit_is_closed_{{ $dayNumber }}">
                                                Выходной
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                @endforeach

                                <div class="alert alert-info mt-3">
                                    <i class="fa fa-info-circle"></i> Установите время работы для каждого дня. Отметьте "Выходной" для нерабочих дней.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                </div>
            </form>
        </div>
    </div>
</div>