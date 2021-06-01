<div id="password_app" class="card center_box_750">
    <div class="card-body">
        <form accept-charset="utf-8" id="password_form" @submit.prevent="send_form" v-show="success == 0">
            <fieldset v-bind:disabled="loading">
                <div class="form-group row">
                    <label for="current_password" class="col-md-5 col-form-label text-right">
                        <span class="float-right">Contraseña actual</span>
                    </label>
                    <div class="col-md-7">
                        <input
                            name="current_password" type="password" class="form-control"
                            title="Contraseña actual" required autofocus
                            v-bind:class="{'is-invalid': validation.current_password == 0 }" v-model="current_password" v-on:change="restart_current_password"
                            >
                            <div class="invalid-feedback">Su contraseña actual es incorrecta</div>
                    </div>
                </div>
                
                <div class="form-group row">
                        <label for="password" class="col-md-5 col-form-label text-right">
                            <span class="float-right">Nueva contraseña</span>
                        </label>
                        <div class="col-md-7">
                            <input
                                name="password" type="password" class="form-control"
                                title="Al menos un número y una letra minúscula, y al menos 8 caractéres"
                                pattern="(?=.*\d)(?=.*[a-z]).{8,}" required v-model="password" v-on:change="clear_passconf"
                                >
                        </div>
                    </div>
            
                <div class="form-group row">
                    <label for="passconf" class="col-md-5 col-form-label text-right">
                        <span class="float-right">Confirmar contraseña</span>
                    </label>
                    <div class="col-md-7">
                        <input
                            name="passconf" type="password" class="form-control" title="Confirme la nueva contraseña"
                            pattern="(?=.*\d)(?=.*[a-z]).{8,}" required
                            v-bind:class="{'is-invalid': validation.passwords_match == 0 }" v-model="passconf" v-on:change="check_match"
                            >
                        <div class="invalid-feedback">La contraseña de confirmación no coincide</div>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-7 offset-md-5">
                        <button class="btn btn-primary btn-block" type="submit">
                            <i class="fa fa-spin fa-spinner" v-show="loading"></i>
                            Cambiar
                        </button>
                    </div>
                </div>
            </fieldset>
            

            <div class="row">
                <div class="col-md-7 offset-md-5">
                </div>
            </div>
        </form>

        <div class="alert alert-success" role="alert" v-show="success == 1">
            <i class="fa fa-check"></i> La contraseña fue cambiada exitosamente.
        </div>
    </div>
</div>

<script>
var password_app = new Vue({
    el: '#password_app',
    data: {
        current_password: '',
        password: '',
        passconf: '',
        validation: { current_password: -1, passwords_match: -1 },
        loading: false,
        success: 0,
    },
    methods: {
        send_form: function(){
            this.loading = true
            axios.post(url_api + 'accounts/change_password/', $('#password_form').serialize())
            .then(response => {
                this.validation = response.data.validation
                if ( response.data.status == 1)
                {
                    this.success = 1
                } else {
                    this.loading = false
                    this.clear_form()
                }
            })
            .catch(function(error) {console.log(error)})
        },
        check_match: function(){
            this.validation.passwords_match = -1
            if ( this.password != this.passconf && this.passconf.length > 0 ) {
                this.validation.passwords_match = 0
            } else {
                this.validation.passwords_match = 1
            }
        },
        clear_passconf: function(){
            this.passconf = ''
            this.check_match()
        },
        clear_form: function(){
            if ( this.validation.current_password == 0 ) this.current_password = ''  
            if ( this.validation.passwords_match == 0 ) {
                this.password = ''
                this.passconf = ''
            }
        },
        restart_current_password: function(){
            this.validation.current_password = -1  
        },
    }
})
</script>