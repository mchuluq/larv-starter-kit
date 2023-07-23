<div id="panel-user-password">
<form form method="POST" action="{{ route('user.password') }}" id="form-password-update" class="card">
    <div class="card-header">{{ __('Password') }}</div>
    <div class="card-body">
        @csrf
        <div class="alert" role="alert"></div>
        <div class="mb-3">
            <div class="form-floating">
                <input id="name" type="text" class="form-control" name="name" v-model="user.name" required autocomplete="name" placeholder="username" autofocus>
                <label for="name">username</label>
                <span class="invalid-feedback" data-field="name" role="alert"></span>
            </div>
        </div>
        <div>
            <div class="input-group" id="input-current-password-group">
                <div class="form-floating mb-3">
                    <input id="current-password" type="password" class="form-control rounded" name="current_password" required autocomplete="current-password" placeholder="current password">
                    <label for="password">current password</label>
                    <span class="invalid-feedback" data-field="current_password" role="alert"></span>
                </div>
                <button type="button" class="d-none toggle-password" aria-label="Show password as plain text. Warning: this will display your password on the screen."></button>
            </div>
        </div>
        <div>
            <div class="input-group" id="input-password-group">
                <div class="form-floating mb-3">
                    <input id="password" type="password" class="form-control rounded" name="password" required autocomplete="current-password" placeholder="password">
                    <label for="password">password</label>
                    <span class="invalid-feedback" data-field="password" role="alert"></span>
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
            return {
                user : {}
            }
        },
        methods : {
            async getUser(){
                await xhttps.get('user/password').then( resp => {
                    this.user = resp.data.user;
                })
            }
        },
        mounted(){
            var self = this;
            this.getUser();

            window.passwordToggle.init('input-current-password-group');
            window.passwordToggle.init('input-password-group');
            window.passwordToggle.init('input-password-confirm-group');
            window.formSubmit.init("#form-password-update",{
                secure : true,
                callback : function(res){
                    self.getUser();
                },
                disable_submit : true
            })
        }
    }).mount('#panel-user-password')    
</script>