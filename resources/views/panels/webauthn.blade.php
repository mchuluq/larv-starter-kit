<div id="panel-user-webauthn">
    <div class="card" v-if="mode == 'list'">
        <div class="card-header">Webauthn credential list</div>
        <div class="list-group list-group-flush" v-if="lists.length > 0">
            <div class="list-group-item" v-for="(cred,i) in lists" :key="i">
                <div class="d-flex w-100 justify-content-between">
                  <h5 class="mb-0 fw-bold">@{{implode_string([cred.user_device.device,cred.user_device.brand,cred.user_device.model])}}</h5>
                  <button class="btn btn-sm btn-link" type="button" @click="deleteCredential(cred)">delete</button>
                </div>
                <p class="mb-0">@{{implode_string([cred.alias,cred.user_device.os,cred.user_device.client])}}</p>
                <small>@{{format_time(cred.updated_at)}}</small>
            </div>
        </div>
        <div v-else class="list-group list-group-flush">
            <div class="list-group-item">
                <span>No webauthn credential</span>
            </div>
        </div>
        <div class="card-footer text-end">
            <button type="button" class="btn btn-secondary" @click="mode = 'create'">Register</button>
        </div>
    </div>
    <div class="card" v-if="mode == 'create'">
        <div class="card-header">Create webauthn credential</div>
        <div class="card-body">
            <div class="mb-3">
                <div class="form-floating">
                    <input id="device_alias" v-model="alias" type="text" class="form-control" name="alias" placeholder="alias">
                    <label for="device_alias">device alias</label>
                </div>
            </div>
        </div>
        <div class="card-footer text-end">
            <button type="button" class="btn btn-secondary me-2" @click="mode = 'list'">List</button>
            <button type="button" class="btn btn-primary" @click="registerWebauthn">Register</button>
        </div>
    </div>
</div>
<script type="module">
    const { createApp, ref } = Vue
    createApp({
        data(){
            return {
                lists : [],
                mode : 'list', // list, create
                alias : null,
            }
        },
        methods : {
            registerWebauthn(){
                new WebAuthn().register({
                    alias : this.alias,
                })
                .then(response => {
                    toastr.toast({
                        body : "registrastion success",
                        type : 'success'
                    }).show();
                    this.getCredentials()
                })
                .catch(error => {
                    toastr.toast({
                        body : 'Something went wrong, try again!',
                        type : 'error'
                    }).show();
                })
            },
            async getCredentials(){
                await api_axios.get('user/webauthn').then( resp => {
                    this.lists = resp.data.credentials;
                })
            },
            async deleteCredential(row){
                if(confirm("are you sure you want to delete these credentials ?") == true){
                    await api_axios.delete(`user/webauthn/${row.id}`).then( resp => {
                        this.getCredentials()
                        toastr.toast({
                            body : "test",
                            type : 'success'
                        }).show();
                    })
                }                
            },
            format_time(val){
                return app_helper.format_time(val);
            },
            implode_string(val){
                return app_helper.implode_string(val);
            }
        },
        mounted(){
            this.getCredentials();
        }
    }).mount('#panel-user-webauthn')    
</script>