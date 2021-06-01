<div id="activation_app">
    <div v-show="status == 1">
        <p class="text-center"><i class="fa fa-check-circle text-success fa-2x"></i></p>
        <h1>{{ display_name }}</h1>
        <p>
            Tu cuenta fue activada correctamente
        </p>
        <a class="btn btn-success btn-lg btn-block" href="<?= URL_ADMIN . 'appplication/logged' ?>">
            Continuar
        </a>
    </div>
    <div v-show="status == 0">
        <p class="text-center"><i class="fa fa-exclamation-triangle text-warning"></i> No activada</p>
        <h1>Cuenta no identificada</h1>
        <p>El código de activación no corresponde a ninguna cuenta de usuario</p>
    </div>
</div>

<script>
    new Vue({
        el: '#activation_app',
        created: function(){
            this.activate();
        },
        data: {
            status: -1,
            url_api: '<?= URL_API ?>',
            user_id: -1,
            display_name: '',
            activation_key: '<?= $activation_key ?>'
        },
        methods: {
            activate: function(){
                axios.get(this.url_api + 'accounts/activate/' + this.activation_key)
                .then(response => {
                    this.status = response.data.status;
                    if ( response.data.status == 1 ) {
                        this.user_id = response.data.user_id;
                        this.display_name = response.data.display_name;
                    }
                })
                .catch(function (error) {
                    console.log(error);
                });  
            },
        }
    });
</script>