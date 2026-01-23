<div class="modal fade" id="editCityModal" tabindex="-1" aria-labelledby="editCityModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCityModalLabel">Редактировать город</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editCityForm" action="" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_city_id" name="city_id">
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="fw-bold">Переводы <span class="text-danger">*</span></label>
                        @foreach(getLanguages() as $language)
                        <div class="mb-2">
                            <label for="edit_translation_{{ $language->code }}" class="form-label">
                                {{ $language->name }} ({{ strtoupper($language->code) }})
                            </label>
                            <input type="text"
                                class="form-control"
                                id="edit_translation_{{ $language->code }}"
                                name="translations[{{ $language->code }}]"
                                placeholder="Название на {{ $language->name }}"
                                required>
                        </div>
                        @endforeach
                        <small class="form-text text-muted">Укажите название города на всех языках</small>
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