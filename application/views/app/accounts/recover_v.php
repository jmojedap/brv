<div id="recover_app">
    <div class="mt-2" v-show="user_id > 0">
        <h4 class="white"><?= $row->display_name ?></h4>
        <p class="text-muted">
            <i class="fa fa-user"></i>
            <?= $row->username ?>
        </p>
        <p>Establece tu nueva contraseña en <?= APP_NAME ?></p>
    </div>

    <form id="recover_form" method="post" accept-charset="utf-8" @submit.prevent="send_form" v-show="user_id > 0">
        <div class="form-group">
            <input
                name="password" type="password"
                class="form-control" 
                placeholder="contrase&ntilde;a" title="Debe tener un número y una letra minúscula, y al menos 8 caractéres"
                required autofocus pattern="(?=.*\d)(?=.*[a-z]).{8,}"
                >
        </div>
        <div class="form-group">
            <input
                name="passconf" type="password"
                class="form-control" placeholder="confirma tu contrase&ntilde;a" title="passconf contrase&ntilde;a"
                required
                >
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary btn-block">Guardar</button>
        </div>
    </form>

    <div class="alert alert-danger" v-show="errors">
        <i class="fa fa-info-circle"></i>
        {{ errors }}
    </div>

    <div class="alert alert-danger" v-show="user_id == 0">
        <i class="fa fa-info-circle"></i>
        Usuario no identificado con código: <strong>{{ activation_key }}</strong>
    </div>

</div>

<script>
    new Vue({
        el: '#recover_app',
        data: {
            user_id: <?= $user_id ?>,
            activation_key: '<?= $activation_key ?>',
            hide_message: true,
            errors: 0,
        },
        methods: {
            send_form: function(){
                axios.post(url_api + 'accounts/reset_password/' + this.activation_key, $('#recover_form').serialize())
                .then(response => {
                    this.errors = response.data.errors;
                    if ( response.data.status == 1 ) {
                        toastr['success']('Tu contraseña fue cambiada exitosamente');
                        setTimeout(function(){ window.location = url_app + 'app/logged'; }, 3000);
                    }
                })
                .catch(function (error) {
                    console.log(error);
                });
            }
        }
    });
</script>