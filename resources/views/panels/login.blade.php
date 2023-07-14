<div id="panel-user-password">
<form form method="POST" action="{{ route('user.password') }}" id="form-password-update" class="card">
    <div class="card-header">{{ __('Password') }}</div>
    <div class="card-body">
        @csrf
        @if (session('password_status'))
            <div class="alert alert-success" role="alert">{{ session('password_status') }}</div>
        @endif
        <div class="mb-3">
            <div class="form-floating">
                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') ?? $user->name }}" required autocomplete="name" placeholder="username" autofocus>
                <label for="name">username</label>
                @error('name')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
            </div>
        </div>
        <div>
            <div class="input-group" id="input-current-password-group">
                <div class="form-floating mb-3">
                    <input id="current-password" type="password" class="form-control rounded @error('current_password') is-invalid @enderror" name="current_password" required autocomplete="current-password" placeholder="current password">
                    <label for="password">current password</label>
                    @error('current_password')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                </div>
                <button type="button" class="d-none toggle-password" aria-label="Show password as plain text. Warning: this will display your password on the screen."></button>
            </div>
        </div>
        <div>
            <div class="input-group" id="input-password-group">
                <div class="form-floating mb-3">
                    <input id="password" type="password" class="form-control rounded @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="password">
                    <label for="password">password</label>
                    @error('password')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                </div>
                <button type="button" class="d-none toggle-password" aria-label="Show password as plain text. Warning: this will display your password on the screen."></button>
            </div>
        </div>
        <div>
            <div class="input-group" id="input-password-confirm-group">
                <div class="form-floating mb-3">
                    <input id="password-confirm" type="password" class="form-control rounded" name="password_confirmation" required placeholder="password">
                    <label for="password">confirm password</label>
                </div>
                <button type="button" class="d-none toggle-password" aria-label="Show password as plain text. Warning: this will display your password on the screen."></button>
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
        mounted(){
            window.passwordToggle.init('input-password-group');
            window.passwordToggle.init('input-password-confirm-group');
        }
    }).mount('#panel-user-password')    
</script>