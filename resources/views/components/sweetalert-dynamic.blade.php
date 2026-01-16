<script>
    document.addEventListener('DOMContentLoaded', function() {

        // DELETE CONFIRMATION - Danger style
        document.querySelectorAll('.btn-delete-confirm').forEach(function(button) {
            button.addEventListener('click', function(e) {
                e.preventDefault();

                const formId = this.getAttribute('data-form-id');
                const title = this.getAttribute('data-title') || 'Вы уверены?';
                const text = this.getAttribute('data-text') || 'Это действие нельзя отменить!';
                const confirmText = this.getAttribute('data-confirm-text') || 'Да, удалить';
                const cancelText = this.getAttribute('data-cancel-text') || 'Отмена';

                swal({
                    title: title,
                    text: text,
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: cancelText,
                            value: null,
                            visible: true,
                            className: "",
                            closeModal: true,
                        },
                        confirm: {
                            text: confirmText,
                            value: true,
                            visible: true,
                            className: "btn-danger",
                            closeModal: true
                        }
                    },
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        document.getElementById(formId).submit();
                    }
                });
            });
        });

        // GENERAL CONFIRMATION - Info/Question style
        document.querySelectorAll('.btn-confirm').forEach(function(button) {
            button.addEventListener('click', function(e) {
                e.preventDefault();

                const formId = this.getAttribute('data-form-id');
                const title = this.getAttribute('data-title') || 'Вы уверены?';
                const text = this.getAttribute('data-text') || '';
                const icon = this.getAttribute('data-icon') || 'info';
                const confirmText = this.getAttribute('data-confirm-text') || 'Да';
                const cancelText = this.getAttribute('data-cancel-text') || 'Нет';

                swal({
                    title: title,
                    text: text,
                    icon: icon,
                    buttons: {
                        cancel: {
                            text: cancelText,
                            value: null,
                            visible: true,
                            closeModal: true,
                        },
                        confirm: {
                            text: confirmText,
                            value: true,
                            visible: true,
                            closeModal: true
                        }
                    }
                }).then((result) => {
                    if (result) {
                        document.getElementById(formId).submit();
                    }
                });
            });
        });

        // SUCCESS CONFIRMATION
        document.querySelectorAll('.btn-success-confirm').forEach(function(button) {
            button.addEventListener('click', function(e) {
                e.preventDefault();

                const formId = this.getAttribute('data-form-id');
                const title = this.getAttribute('data-title') || 'Готово!';
                const text = this.getAttribute('data-text') || '';
                const confirmText = this.getAttribute('data-confirm-text') || 'Продолжить';
                const cancelText = this.getAttribute('data-cancel-text') || 'Отмена';

                swal({
                    title: title,
                    text: text,
                    icon: "success",
                    buttons: {
                        cancel: {
                            text: cancelText,
                            value: null,
                            visible: true,
                            closeModal: true,
                        },
                        confirm: {
                            text: confirmText,
                            value: true,
                            visible: true,
                            className: "btn-success",
                            closeModal: true
                        }
                    }
                }).then((result) => {
                    if (result) {
                        document.getElementById(formId).submit();
                    }
                });
            });
        });
    });
</script>