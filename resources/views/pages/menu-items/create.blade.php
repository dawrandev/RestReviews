<div class="modal fade" id="createItemModal" tabindex="-1" aria-labelledby="createItemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form action="{{ route('menu-items.store') }}" method="POST" enctype="multipart/form-data" id="createItemForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createItemModalLabel">Новое блюдо</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Название блюда (на разных языках) <span class="text-danger">*</span></label>
                                @foreach($languages as $lang)
                                <div class="input-group mb-2">
                                    <span class="input-group-text" style="min-width: 100px;">{{ $lang->name }}</span>
                                    <input type="text"
                                        class="form-control @error('translations.'.$lang->code.'.name') is-invalid @enderror"
                                        name="translations[{{ $lang->code }}][name]"
                                        value="{{ old('translations.'.$lang->code.'.name') }}"
                                        placeholder="Название на {{ $lang->name }}"
                                        required>
                                    @error('translations.'.$lang->code.'.name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                @endforeach
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Описание (на разных языках)</label>
                                @foreach($languages as $lang)
                                <div class="mb-2">
                                    <label class="form-label small">{{ $lang->name }}</label>
                                    <textarea
                                        class="form-control @error('translations.'.$lang->code.'.description') is-invalid @enderror"
                                        name="translations[{{ $lang->code }}][description]"
                                        rows="2"
                                        placeholder="Описание на {{ $lang->name }}">{{ old('translations.'.$lang->code.'.description') }}</textarea>
                                    @error('translations.'.$lang->code.'.description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="menu_section_id" class="form-label">Раздел меню <span class="text-danger">*</span></label>
                                <select class="form-control @error('menu_section_id') is-invalid @enderror" id="menu_section_id" name="menu_section_id" required>
                                    <option value="">Выберите раздел</option>
                                    @foreach($sections as $section)
                                    <option value="{{ $section->id }}" {{ old('menu_section_id') == $section->id ? 'selected' : '' }}>
                                        {{ $section->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('menu_section_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="base_price" class="form-label">Рекомендуемая цена (сум)</label>
                                <input type="number" class="form-control @error('base_price') is-invalid @enderror" id="base_price" name="base_price" value="{{ old('base_price') }}" min="0" step="0.01">
                                @error('base_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Эта цена может быть изменена для каждого филиала</small>
                            </div>

                            <div class="mb-3">
                                <label for="weight" class="form-label">Вес (граммы)</label>
                                <input type="number" class="form-control @error('weight') is-invalid @enderror" id="weight" name="weight" value="{{ old('weight') }}" min="0" step="1">
                                @error('weight')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Только для блюд и закусок, для напитков оставьте пустым</small>
                            </div>

                            <div class="mb-3">
                                <label for="image" class="form-label">Изображение</label>
                                <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                                @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">JPEG, PNG, JPG. Макс: 2MB</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">Сохранить</button>
                </div>
            </form>
        </div>
    </div>
</div>
