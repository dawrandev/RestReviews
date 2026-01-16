@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        swal({
            title: "Успешно!",
            text: "{{ session('success') }}",
            icon: "success",
            timer: 1500, // 1.5 sekund (1500 millisekund)
            buttons: false
        });
    });
</script>
@endif

@if(session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        swal({
            title: "Ошибка!",
            text: "{{ session('error') }}",
            icon: "error",
            button: "Закрыть"
        });
    });
</script>
@endif

@if(session('warning'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        swal({
            title: "Внимание!",
            text: "{{ session('warning') }}",
            icon: "warning",
            button: "Понятно"
        });
    });
</script>
@endif

@if(session('info'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        swal({
            title: "Информация",
            text: "{{ session('info') }}",
            icon: "info",
            timer: 1500, // 1.5 sekund
            buttons: false
        });
    });
</script>
@endif

@if($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function() {
        swal({
            title: "Ошибка валидации",
            text: "{!! implode('\n', $errors->all()) !!}",
            icon: "error",
            button: "Закрыть"
        });
    });
</script>
@endif