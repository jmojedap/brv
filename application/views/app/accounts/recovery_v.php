<?php $this->load->view('assets/recaptcha') ?>

<div id="recovery_app" class="text-center">

    <div v-show="app_status == 'start'">
        <p>
            Escribe tu dirección de correo electrónico.
            Te enviaremos un enlace para que asignes una nueva contraseña.
        </p>

        <form id="app_form" @submit.prevent="send_form" >
            <!-- Campo para validación Google ReCaptcha V3 -->
            <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response">

            <div class="form-group">
                <label class="sr-only" for="email">Correo electrónico</label>
                <input
                    name="email" type="email" class="form-control form-control-lg" required
                    placeholder="Correo electrónico" title="Correo electrónico" v-model="email"
                    >
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block btn-lg">Enviar</button>
            </div>
        </form>
    </div>

    <div v-show="app_status == 'no_user'">
        <a href="<?= URL_APP . "accounts/recovery" ?>" class="btn btn-light mb-2">
            <i class="fa fa-arrow-left"></i> Volver
        </a>
        <div class="alert alert-warning" role="alert">
            <i class="fa fa-user-slash"></i>
            <br/>
            No existe ningún usuario con el correo electrónico: <b>{{ email }}</b>.
        </div>
    </div>

    <div v-show="app_status == 'sent'" class="my-2">
        <i class="fa fa-check fa-2x text-success"></i>
        <p>
            Enviamos un enlace al correo electrónico <strong class="text-success">{{ email }}</strong> para reestablecer tu contraseña.
        <p>
        <p>Recuerda revisar también tu carpeta de correo no deseado.</p>
    </div>

    <p>¿No tienes una cuenta? <a href="<?= URL_APP . 'accounts/signup' ?>">Regístrate</a></p>
</div>

<script>
    new Vue({
        el: '#recovery_app',
        data: {
            email: '',
            app_status: 'start'
        },
        methods: {
            send_form: function(){
                axios.post(url_api + 'accounts/recovery_email/', $('#app_form').serialize())
                .then(response => {
                    console.log(response.data.status);
                    if ( response.data.status == 1 ) {
                        this.no_user = false;
                        this.app_status = 'sent';
                    } else if ( response.data.status == 0 ) {
                        this.app_status = 'no_user';
                    }
                })
                .catch(function (error) {
                    console.log(error);
                });
            }
        }
    });
</script>
