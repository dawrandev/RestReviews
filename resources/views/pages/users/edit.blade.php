<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Редактировать администратора</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editUserForm" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="edit_name">Имя <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                        <div class="invalid-feedback" id="error_name"></div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="edit_login">Логин <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_login" name="login" required>
                        <div class="invalid-feedback" id="error_login"></div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="edit_password">Новый пароль</label>
                        <input type="password" class="form-control" id="edit_password" name="password" placeholder="Оставьте пустым, если не хотите менять">
                        <small class="form-text text-muted">Минимум 8 символов</small>
                    </div>

                    <div class="form-group mb-3">
                        <label for="edit_password_confirmation">Подтвердите пароль</label>
                        <input type="password" class="form-control" name="password_confirmation">
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