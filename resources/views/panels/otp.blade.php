<div id="panel-user-otp">
<form class="card" method="POST" action="{{ route('user.otp') }}" id="form-user-otp">
    @csrf
    @if($otp->status)
        @method('delete')
        <div class="card-header">{{ __('register OTP') }}</div>
        <div class="card-body">
            @if (session('register_otp_status'))
                <div class="alert alert-success" role="alert">{{ session('register_otp_status') }}</div>
            @endif
            <div class="mb-3">
                <div class="form-floating mb-3">
                    <input id="otp" type="number" maxlength="6" class="form-control rounded @error('otp_code') is-invalid @enderror" name="otp_code" required placeholder="OTP code">
                    <label for="otp">OTP</label>
                    @error('otp_code')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                </div>
            </div>
            <div class="mb-3">
                <div class="input-group" id="input-otp-password-group">
                    <div class="form-floating mb-3">
                        <input id="otp-password" type="password" class="form-control rounded @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="current password">
                        <label for="otp-password">password</label>
                        @error('password')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                    </div>
                    <button type="button" class="d-none toggle-password" aria-label="Show password as plain text. Warning: this will display your password on the screen."></button>
                </div>
            </div>
        </div>
        <div class="card-footer text-end">
            <button type="submit" class="btn btn-danger">{{ __('Remove OTP') }}</button>
        </div>
    @else
        <div class="card-header">{{ __('register OTP') }}</div>
        <div class="card-body">
            @if (session('register_otp_status'))
                <div class="alert alert-success" role="alert">{{ session('register_otp_status') }}</div>
            @endif
            <div class="mb-3">
                <div class="form-text">Open authenticator app, then scan this QR code or enter code below.</div>
            </div>
            <div class="mb-3">
                <div class="text-center">
                    <img src="{{$otp->qr_image}}" class="img-thumbnail">
                </div>
            </div>
            <div class="mb-3">
                <input type="text" name="otp_secret" class="form-control" value="{{$otp->otp_secret}}" readonly>
            </div>
        </div>
        <div class="card-footer text-end">
            <button type="submit" class="btn btn-primary">{{ __('Set OTP') }}</button>
        </div>
    @endif
</form>
</div>

<script type="module">
    const { createApp, ref } = Vue
    createApp({
        data(){
            return {}
        },
        mounted(){
            window.passwordToggle.init('input-otp-password-group');
        }
    }).mount('#panel-user-otp')    
</script>