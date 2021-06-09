<?php $this->load->view('assets/recaptcha') ?>

<div id="signup_app">
    <div v-show="saved_id == 0">
        <p class="only-lg">
            Crear nueva cuenta de usuario
        </p>

        <?php $this->load->view($this->views_folder . 'start_alerts_v') ?>

        <div class="text-center mb-2" v-show="loading == true">
            <i class="fa fa-spin fa-spinner fa-3x"></i>
        </div>

        <form id="signup_form" @submit.prevent="register" v-show="loading == false">
            <!-- Campo para validación Google ReCaptcha V3 -->
            <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response">

            <div class="form-group">
                <label class="sr-only" for="display_name">Tu nombre</label>
                <input
                    class="form-control" name="display_name"
                    title="Debe tener al menos cinco letras" placeholder="¿Cómo te llamas?"
                    minlength="5" required autofocus
                    v-model="display_name"
                    >
            </div>

            <div class="form-group">
                <label class="sr-only" for="email">Correo electrónico</label>
                <input
                    name="email" type="email"
                    class="form-control"
                    placeholder="Correo electrónico"
                    required
                    title="Correo electrónico"
                    v-model="email"
                    v-on:change="validate_form"
                    v-bind:class="{'is-invalid': validation.email_unique == 0, 'is-valid': validation.email_unique == 1}"
                    >
                <div class="invalid-feedback" v-show="validation.email_unique == 0">
                    Ya existe una cuenta con este correo electrónico
                </div>
            </div>

            <div class="form-group">
                <label class="sr-only" for="password">Contraseña</label>

                    <div class="input-group mb-3">
                        <input
                            name="new_password" v-bind:type="pw_type"
                            class="form-control" placeholder="Elige tu contraseña"
                            required
                            pattern="(?=.*\d)(?=.*[a-z]).{8,}"
                            title="8 caractéres o más, al menos un número y una letra minúscula"
                            v-model="pw"
                            >
                        <div class="input-group-append">
                            <button class="btn btn-light" type="button" v-on:click="toggle_password">
                                <i class="far fa-eye-slash" v-show="pw_type == 'password'"></i>
                                <i class="far fa-eye" v-show="pw_type == 'text'"></i>
                            </button>
                        </div>
                    </div>
            </div>    
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block" v-bind:disabled="loading == true">Crear cuenta</button>
            </div>
        </form>
    </div>

    <!-- Sección si se registró exitosamente -->
    <div v-show="saved_id > 0">
        <div class="text-center mb-2">
            <h1>
                <i class="fa fa-check text-success"></i><br/>
                Listo {{ display_name }}
            </h1>
            <p>
                ¡Ya haces parte de <?= APP_NAME ?>!
            </p>
            <a href="<?= URL_APP . 'accounts/logged' ?>" class="btn btn-primary btn-lg">
                CONTINUAR <i class="fa fa-arrow-right"></i>
            </a>
        </div>
    </div>

    <p>¿Ya tienes una cuenta? <a href="<?= URL_APP . 'accounts/login' ?>">Iniciar sesión</a></p>
</div>

<script>
    new Vue({
        el: '#signup_app',
        data: {
            display_name: '',
            email: '',
            pw: '',
            validated: -1,
            validation: {
                email_unique: -1
            },
            pw_type: 'text',
            loading: false,
            saved_id: 0,
        },
        methods: {
            register: function(){
                if ( this.validated )
                {
                    this.loading = true
                    axios.post(url_api + 'accounts/register/', $('#signup_form').serialize())
                    .then(response => {
                        if ( response.data.saved_id > 0 ) {
                            this.saved_id = response.data.saved_id
                        } else {
                            this.recaptcha_message = response.data.recaptcha_message;
                        }
                    })
                    .catch(function (error) { console.log(error) })
                } else {
                    toastr['error']('Revisa las casillas en rojo')
                }
            },
            validate_form: function(){
                axios.post(url_api + 'accounts/validate_signup/', $('#signup_form').serialize())
                .then(response => {
                    this.validation = response.data.validation
                    this.validated = response.data.status
                })
                .catch(function (error) {
                    console.log(error);
                });  
            },
            toggle_password: function(){
                if ( this.pw_type == 'text' )
                {
                    this.pw_type = 'password'
                } else {
                    this.pw_type = 'text'
                }
            },
        }
    });
</script>