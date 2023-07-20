<div id="panel-user-otp">
<form class="card" method="POST" action="{{ route('user.otp') }}" id="form-user-otp">
    @csrf
    <div v-if="otp.status">
        @method('delete')
        <div class="card-header">{{ __('register OTP') }}</div>
        <div class="card-body">
            <div class="mb-3">
                <div class="form-floating mb-3">
                    <input id="otp" type="number" maxlength="6" class="form-control rounded" name="otp_code" required placeholder="OTP code">
                    <label for="otp">OTP</label>
                    <span class="invalid-feedback" data-field="otp_code" role="alert"></span>
                </div>
            </div>
            <div class="mb-3">
                <div class="input-group" id="input-otp-password-group">
                    <div class="form-floating mb-3">
                        <input id="otp-password" type="password" class="form-control rounded" name="password" required autocomplete="current-password" placeholder="current password">
                        <label for="otp-password">password</label>
                        <span class="invalid-feedback" data-field="password" role="alert"></span>
                    </div>
                    <button type="button" class="d-none toggle-password" aria-label="Show password as plain text. Warning: this will display your password on the screen."></button>
                </div>
            </div>
        </div>
        <div class="card-footer text-end">
            <button type="submit" class="btn btn-danger">{{ __('Remove OTP') }}</button>
        </div>
    </div>
    <div v-else>
        <div class="card-header">{{ __('register OTP') }}</div>
        <div class="card-body">
            <div class="mb-3">
                <div class="form-text">Open authenticator app, then scan this QR code or enter code below.</div>
            </div>
            <div class="mb-3">
                <div class="text-center">
                    <img :src="otp.qr_image" class="img-thumbnail">
                </div>
            </div>
            <div class="mb-3">
                <input type="text" name="otp_secret" class="form-control" v-model="otp.otp_secret_key" readonly>
            </div>
            <div class="mb-3">
                <div class="input-group" id="input-otp-password-group">
                    <div class="form-floating mb-3">
                        <input id="otp-password" type="password" class="form-control rounded" name="password" required autocomplete="current-password" placeholder="current password">
                        <label for="otp-password">password</label>
                        <span class="invalid-feedback" data-field="password" role="alert">test</span>
                    </div>
                    <button type="button" class="d-none toggle-password" aria-label="Show password as plain text. Warning: this will display your password on the screen."></button>
                </div>
            </div>
        </div>
        <div class="card-footer text-end">
            <button type="submit" class="btn btn-primary">{{ __('Set OTP') }}</button>
        </div>
    </div>
</form>
</div>

<script type="module">
    const { createApp, ref } = Vue
    createApp({
        data(){
            return {
                otp : {
                    status : false,
                    otp_secret_key : null,
                    qr_image : null,
                }
            }
        },
        methods : {
            async getOtpStatus(){
                await xhttps.get('user/otp').then( resp => {
                    this.otp = resp.data.otp;
                })
            }
        },
        mounted(){
            var self = this;
            window.passwordToggle.init('input-otp-password-group');
            this.getOtpStatus();
            window.formSubmit.init("#form-user-otp",{
                callback : function(res){
                    self.getOtpStatus();
                },
                disable_submit : true
            })
        }
    }).mount('#panel-user-otp')    
</script>