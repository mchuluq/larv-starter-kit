<div id="panel-user-update">
<form class="card" method="POST" action="{{ route('user.update') }}" id="form-user-update" enctype="multipart/form-data">
    <div class="card-header">{{ __('Profil') }}</div>
    <div class="card-body">
        @csrf
        @if (session('update_status'))
            <div class="alert alert-success" role="alert">{{ session('update_status') }}</div>
        @endif
        <div class="mb-3 p-3 text-center">
            <img src="{{$user->photo_url}}" onerror="this.style.display='none'" width="128" class="img-thumbnail rounded" alt="photo">
        </div>
        <div class="mb-3">
            <input id="photo_url" type="file" class="form-control @error('photo_url') is-invalid @enderror" name="photo_url" placeholder="Photo" autofocus>
            @error('photo_url')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
        </div>
        <div class="mb-3">
            <div class="form-floating">
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') ?? $user->email }}" required autocomplete="email" placeholder="email" autofocus>
                <label for="name">email</label>
                @error('email')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
            </div>
        </div>
    </div>
    <div class="card-footer text-end">
        <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
    </div>
</form>
</div>

<script type="module">
    const { createApp, ref } = Vue
    createApp({
        data(){
            return {}
        },
        mounted(){}
    }).mount('#panel-user-update')    
</script>