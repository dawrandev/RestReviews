<div class="modal fade" id="createRestaurantModal" tabindex="-1" aria-labelledby="createRestaurantModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createRestaurantModalLabel">Создать новый ресторан</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('restaurants.store') }}" method="POST" enctype="multipart/form-data" id="createRestaurantForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        {{-- Brand (disabled for admin) --}}
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Бренд <span class="text-danger">*</span></label>
                            @if(auth()->user()->hasRole('admin'))
                            <input type="hidden" name="brand_id" value="{{ auth()->user()->brand_id }}">
                            <input type="text" class="form-control"
                                value="{{ auth()->user()->brand ? auth()->user()->brand->name : 'N/A' }}"
                                readonly style="background-color: #e9ecef;">
                            @else
                            <select class="form-select @error('brand_id') is-invalid @enderror" id="brand_id" name="brand_id" required>
                                <option value="">Выберите бренд</option>
                                @foreach(getBrands() as $brand)
                                <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('brand_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @endif
                        </div>

                        {{-- City --}}
                        <div class="col-md-4 mb-3">
                            <label for="city_id" class="form-label">Город <span class="text-danger">*</span></label>
                            <select class="form-select @error('city_id') is-invalid @enderror" id="city_id" name="city_id" required>
                                <option value="">Выберите город</option>
                                @foreach(getCities() as $city)
                                <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>
                                    {{ $city->translations->first()->name ?? 'N/A' }}
                                </option>
                                @endforeach
                            </select>
                            @error('city_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Branch Name --}}
                        <div class="col-md-4 mb-3">
                            <label for="branch_name" class="form-label">Название филиала <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('branch_name') is-invalid @enderror"
                                id="branch_name" name="branch_name"
                                value="{{ old('branch_name') }}"
                                placeholder="Например: Чиланзар" required>
                            @error('branch_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        {{-- Phone --}}
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Телефон</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                id="phone" name="phone"
                                value="{{ old('phone') }}"
                                placeholder="+998 90 123 45 67">
                            @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Status --}}
                        <div class="col-md-6 mb-3">
                            <label for="is_active" class="form-label">Статус</label>
                            <select class="form-select @error('is_active') is-invalid @enderror" id="is_active" name="is_active">
                                <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Активен</option>
                                <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Неактивен</option>
                            </select>
                            @error('is_active')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Address --}}
                    <div class="mb-3">
                        <label for="address" class="form-label">Адрес <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('address') is-invalid @enderror"
                            id="address" name="address"
                            value="{{ old('address') }}"
                            placeholder="Улица, дом, квартира" required>
                        @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Categories with Select2 (Cuba Admin style) --}}
                    <div class="mb-3">
                        <label class="col-form-label">Категории</label>
                        <select class="form-control select2-create-categories @error('categories') is-invalid @enderror"
                            id="create_categories"
                            name="categories[]"
                            multiple="multiple">
                            @foreach(getCategories() as $category)
                            <option value="{{ $category->id }}">
                                {{ $category->translations->first()->name ?? 'N/A' }}
                            </option>
                            @endforeach
                        </select>
                        <small class="text-muted d-block mt-1">Выберите одну или несколько категорий</small>
                        @error('categories')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Map --}}
                    <div class="mb-3">
                        <label class="form-label">Укажите местоположение на карте <span class="text-danger">*</span></label>
                        <div id="createMap" style="height: 400px; border-radius: 8px; border: 1px solid #ddd;"></div>
                        <input type="hidden" name="latitude" id="create_latitude">
                        <input type="hidden" name="longitude" id="create_longitude">
                        <small class="text-muted d-block mt-2">Кликните на карту или перетащите маркер для выбора точного местоположения</small>
                        @error('latitude')
                        <div class="text-danger small">{{ $message }}</div>
                        @enderror
                        @error('longitude')
                        <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Dropzone for Images (Cuba Admin style) --}}
                    <div class="mb-3">
                        <label class="form-label">Фотографии ресторана</label>
                        <div class="dropzone" id="restaurantImagesDropzone">
                            <div class="dz-message needsclick">
                                <i class="icon-cloud-up"></i>
                                <h6>Перетащите файлы сюда или нажмите для загрузки.</h6>
                                <span class="note needsclick">Максимум <strong>5 фотографий</strong>. Первое изображение будет обложкой. (JPG, PNG, до 5MB)</span>
                            </div>
                        </div>
                        <small class="text-muted">Форматы: JPG, PNG. Максимальный размер: 5MB на файл</small>
                        @error('images')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                        @error('images.*')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
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
                                            value="09:00"
                                            data-day="{{ $dayNumber }}">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="time"
                                            class="form-control form-control-sm closing-time"
                                            name="operating_hours[{{ $dayNumber }}][closing_time]"
                                            value="22:00"
                                            data-day="{{ $dayNumber }}">
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input is-closed-check"
                                                type="checkbox"
                                                name="operating_hours[{{ $dayNumber }}][is_closed]"
                                                value="1"
                                                id="is_closed_{{ $dayNumber }}"
                                                data-day="{{ $dayNumber }}">
                                            <label class="form-check-label" for="is_closed_{{ $dayNumber }}">
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
                        @error('operating_hours')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div class="mb-3">
                        <label for="description" class="form-label">Описание</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                            id="description" name="description" rows="4"
                            placeholder="Краткое описание ресторана">{{ old('description') }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">Создать ресторан</button>
                </div>
            </form>
        </div>
    </div>
</div>