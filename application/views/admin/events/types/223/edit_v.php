<?php
    $current_user = '';
    if ( $row->user_id > 0 ) {
        $current_user = $this->App_model->name_user($row->user_id);
    }
?>

<div id="edit_event_app">
    <div class="center_box_750">
        <div class="card">
            <div class="card-body">
                <fieldset>
                    <div class="form-group row">
                        <label for="start" class="col-md-4 col-form-label text-right">Tipo</label>
                        <div class="col-md-8">
                        <input type="text" readonly class="form-control-plaintext" value="Cita de control nutricional">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="start" class="col-md-4 col-form-label text-right">Inicio</label>
                        <div class="col-md-8">
                        <input type="text" readonly class="form-control-plaintext" value="<?= $row->start ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="user_id" class="col-md-4 col-form-label text-right">Asignada a</label>
                        <div class="col-md-8">

                            
                            <div class="input-group mb-3">
                                <input
                                    type="text" class="form-control"
                                    title="Buscar usuario" placeholder="Buscar..."
                                    v-model="q" v-on:change="get_users"
                                >
                                <div class="input-group-append">
                                    <button class="btn btn-light" type="button" v-on:click="unset_user"><i class="fa fa-times"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <form accept-charset="utf-8" method="POST" id="event_form" @submit.prevent="send_form">
                    <fieldset v-bind:disabled="loading">
                        <input type="hidden" name="id" value="<?= $row->id ?>">
                        <input type="hidden" name="user_id" v-model="form_values.user_id">

                        <div class="form-group row" v-show="users.length > 0">
                            <label for="user_id" class="col-md-4 col-form-label text-right">Clic para elegir usuario <i class="fa fa-arrow-right"></i></label>
                            <div class="col-md-8">
                                <div class="list-group">
                                    <a href="#" class="list-group-item list-group-item-action"
                                        v-for="(user, key) in users" v-on:click="set_user(key)" v-bind:class="{'active': user.id == form_values.user_id }"
                                        >
                                        {{ user.display_name }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <div class="col-md-8 offset-md-4">
                                <button class="btn btn-primary w120p" type="submit">Guardar</button>
                            </div>
                        </div>
                    <fieldset>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
var edit_event_app = new Vue({
    el: '#edit_event_app',
    created: function(){
        //this.get_list()
    },
    data: {
        form_values: {
            user_id: '<?= $row->user_id ?>'
        },
        q: '<?= $current_user ?>',
        users: [],
        loading: false,
    },
    methods: {
        send_form: function(){
            this.loading = true
            var form_data = new FormData(document.getElementById('event_form'))
            axios.post(url_api + 'events/update/', form_data)
            .then(response => {
                if ( response.data.saved_id > 0 ) {
                    toastr['success']('Guardado')
                }
                this.loading = false
            })
            .catch( function(error) {console.log(error)} )
        },
        get_users: function(){
            if ( this.q.length > 3 ) {
                this.loading = true
                var form_data = new FormData
                form_data.append('q', this.q)
                axios.post(url_api + 'users/get/', form_data)
                .then(response => {
                    this.users = response.data.list
                    this.loading = false
                })
                .catch( function(error) {console.log(error)} )
            } else {
                this.users = []
            }
        },
        set_user: function(user_key){
            this.form_values.user_id = this.users[user_key].id
            this.q = this.users[user_key].display_name
            this.users = []
        },
        unset_user: function(){
            this.form_values.user_id = 0
            this.q = ''
        },
    }
})
</script>