<div class="modal fade" id="createCityModal" tabindex="-1" aria-labelledby="createCityModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createCityModalLabel">Новый город</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('cities.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="fw-bold">Переводы <span class="text-danger">*</span></label>
                        @foreach(getLanguages() as $language)
                        <div class="mb-2">
                            <label for="translation_{{ $language->code }}" class="form-label">
                                {{ $language->name }} ({{ strtoupper($language->code) }})
                            </label>
                            <input type="text"
                                class="form-control @error('translations.' . $language->code) is-invalid @enderror"
                                id="translation_{{ $language->code }}"
                                name="translations[{{ $language->code }}]"
                                value="{{ old('translations.' . $language->code) }}"
                                placeholder="Название на {{ $language->name }}"
                                required>
                            @error('translations.' . $language->code)
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @endforeach
                        <small class="form-text text-muted">Укажите название города на всех языках</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">Создать</button>
                </div>
            </form>
        </div>
    </div>
</div>