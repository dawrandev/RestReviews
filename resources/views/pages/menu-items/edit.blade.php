<div class="modal fade" id="editItemModal" tabindex="-1" aria-labelledby="editItemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form action="" method="POST" enctype="multipart/form-data" id="editItemForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_item_id" name="item_id">

                <div class="modal-header">
                    <h5 class="modal-title" id="editItemModalLabel">Редактировать блюдо</h5>
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
                                        class="form-control"
                                        id="edit_name_{{ $lang->code }}"
                                        name="translations[{{ $lang->code }}][name]"
                                        placeholder="Название на {{ $lang->name }}"
                                        required>
                                </div>
                                @endforeach
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Описание (на разных языках)</label>
                                @foreach($languages as $lang)
                                <div class="mb-2">
                                    <label class="form-label small">{{ $lang->name }}</label>
                                    <textarea
                                        class="form-control"
                                        id="edit_description_{{ $lang->code }}"
                                        name="translations[{{ $lang->code }}][description]"
                                        rows="2"
                                        placeholder="Описание на {{ $lang->name }}"></textarea>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit_menu_section_id" class="form-label">Раздел меню <span class="text-danger">*</span></label>
                                <select class="form-control" id="edit_menu_section_id" name="menu_section_id" required>
                                    <option value="">Выберите раздел</option>
                                    @foreach($sections as $section)
                                    <option value="{{ $section->id }}">{{ $section->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="edit_base_price" class="form-label">Рекомендуемая цена (сум)</label>
                                <input type="number" class="form-control" id="edit_base_price" name="base_price" min="0" step="0.01">
                                <small class="text-muted">Эта цена может быть изменена для каждого филиала</small>
                            </div>

                            <div class="mb-3">
                                <label for="edit_weight" class="form-label">Вес (граммы)</label>
                                <input type="number" class="form-control" id="edit_weight" name="weight" min="0" step="1">
                                <small class="text-muted">Только для блюд и закусок, для напитков оставьте пустым</small>
                            </div>

                            <div class="mb-3">
                                <label for="edit_image" class="form-label">Изображение</label>
                                <div class="mb-2">
                                    <img id="edit_current_image" src="" alt="Current Image" class="img-thumbnail" style="max-height: 100px; display: none;">
                                </div>
                                <input type="file" class="form-control" id="edit_image" name="image" accept="image/*">
                                <small class="text-muted">JPEG, PNG, JPG. Макс: 2MB</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">Обновить</button>
                </div>
            </form>
        </div>
    </div>
</div>
