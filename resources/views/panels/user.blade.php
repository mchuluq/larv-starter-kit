<div id="panel-user-update">
<form class="card" method="POST" action="{{ route('user.update') }}" id="form-user-update">
    <div class="card-header">{{ __('Profil') }}</div>
    <div class="card-body">
        @csrf
        @if (session('update_status'))
            <div class="alert alert-success" role="alert">{{ session('update_status') }}</div>
        @endif
        <div class="form-floating">
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') ?? $user->email }}" required autocomplete="email" placeholder="email" autofocus>
            <label for="name">email</label>
            @error('email')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
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