@props(['type' => 'success', 'message'])

@if(session($type))
    <div class="alert alert-{{ $type === 'error' ? 'danger' : $type }} alert-dismissible fade show" role="alert">
        {{ session($type) }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif



