@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <h6 class="alert-heading">Обнаружены ошибки валидации:</h6>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif



