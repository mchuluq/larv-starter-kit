<div id="panel-user-update">
<form class="card" method="POST" action="{{ route('user.update') }}" id="form-user-update" enctype="multipart/form-data">
    <div class="card-header">{{ __('Profile') }}</div>
    <div class="card-body">
        @csrf
        <div class="mb-3 p-3 text-center">
            <img :src="user.photo_url" onerror="this.style.display='none'" width="128" class="img-thumbnail rounded" alt="photo">
        </div>
        <div class="mb-3">
            <input id="photo_url" type="file" class="form-control" name="photo_url" placeholder="Photo" autofocus>
            <span class="invalid-feedback" data-field="photo_url" role="alert"></span>
        </div>
        <div class="mb-3">
            <div class="form-floating">
                <input id="email" type="email" class="form-control" name="email" v-model="user.email" required autocomplete="email" placeholder="email" autofocus>
                <label for="name">email</label>
                <span class="invalid-feedback" data-field="email" role="alert"></span>
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
                user : {},
            }
        },
        methods : {
            async getUser(){
                await xhttps.get('user/update').then( resp => {
                    this.user = resp.data.user;
                })
            },
        },
        mounted(){
            var self = this;
            this.getUser();
            window.formSubmit.init("#form-user-update",{
                secure : false,
                callback : function(res){
                    self.getUser();
                }
            })
        }
    }).mount('#panel-user-update')    
</script>