<?php $this->load->view('assets/recaptcha') ?>

<div id="order_user_app" class="center_box_450">
    <?php if ( $row->total_weight == 0 ) { ?>
        <p class="text-center">Con estos datos de usuario podrás ingresar a leer tus contenidos en línea</p>
    <?php } else { ?>
        <p class="text-center">Con estos datos de usuario podrás ingresar a realizar seguimiento a tu compra.</p>
    <?php } ?>
    <form accept-charset="utf-8" method="POST" id="email_form" @submit.prevent="check_email" v-show="show_email_form">
        <div class="form-group">
            <label for="email" class="">Correo electrónico</label>
            <input
                type="email"
                id="field-email"
                name="email"
                autofocus
                required
                class="form-control form-control-lg"
                placeholder="Correo electrónico"
                title="Correo electrónico"
                v-model="email"
                >
        </div>
        <div class="form-group">
            <button class="btn btn-primary btn-lg btn-block">
                CONTINUAR
            </button>
        </div>
    </form>

    <form accept-charset="utf-8" method="POST" id="register_form" @submit.prevent="check_pw" v-show="show_pw_form">
        <!-- Campo para validación Google ReCaptcha V3 -->
        <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response">        
        
        <input type="hidden" v-model="email" name="email">
        <h3 class="text-center text-success">{{ email }}</h3>
        <p class="text-center">
            Crea <b>tu contraseña</b> para DistriCatolicas.com
        </p>
        <div class="form-group">
            <label for="password">Tu contraseña</label>
            <input
                type="password"
                id="field-password"
                name="password"
                required
                class="form-control form-control-lg"
                pattern="(?=.*\d)(?=.*[a-z]).{8,}"
                title="8 caractéres o más, al menos un número y una letra minúscula"
                v-model="pw"
                >
                <p class="help-block">8 caracteres o más, con letras y números</p>
            
        </div>
        <div class="form-group">
            <label for="password">Confirmar contraseña</label>
            <input
                type="password"
                id="field-passconf"
                name="passconf"
                required
                class="form-control form-control-lg"
                placeholder=""
                title="Confirma tu contraseña"
                v-model="pc"
                >
        </div>
        
        <div class="form-group">
            <button class="btn btn-primary btn-lg btn-block">
                ENVIAR
            </button>
        </div>
        <div class="text-center alert alert-warning" v-show="!pw_match">
            <i class="fa fa-warning"></i>
            La contraseña de confirmación no coincide con la primera
        </div>
    </form>

    <div v-show="show_success_register">
        <div class="alert alert-success text-center">
            <i class="fa fa-user fa-2x"></i> <br>
            Tu cuenta de usuario fue creada con el email: <b>{{ email }}</b>
            y la contraseña que acabaste escribir.
        </div>
        <a href="<?= URL_ADMIN . "orders/compra_a/" ?>" class="btn btn-main btn-block">
            CONTINUAR COMPRA <i class="fa fa-arrow-right"></i>
        </a>
    </div>

    <div v-show="show_existing">
        <div class="alert alert-success text-center">
            <i class="fa fa-user fa-2x"></i> <br>
            Hola {{ user.display_name }}, ya eres usuario de VeBonit.com
        </div>
        <a href="<?= URL_ADMIN . "orders/compra_a/" ?>" class="btn btn-main btn-lg btn-block">
            CONTINUAR
        </a>
    </div>
</div>

<script>
    new Vue({
        el: '#order_user_app',
        created: function(){
            //this.get_list();
        },
        data: {
            user: {},
            email: 'jmojedap@gmail.com.co',
            pw: '',
            pc: '',
            pw_match: true,
            show_email_form: true,
            show_pw_form: false,
            show_success_register: false,
            show_existing: false,
        },
        methods: {
            check_email: function(){
                var params = new FormData();
                params.append('email', this.email);
                
                axios.post(url_app + 'accounts/check_email/', params)
                .then(response => {
                    console.log(response.data);
                    this.show_email_form = false;
                    if ( response.data.status == 1 )
                    {
                        this.user = response.data.user;
                        this.show_existing = true;
                        this.set_user();
                    } else {
                        this.show_pw_form = true;
                        console.log('NO existe');
                    }
                })
                .catch(function (error) {
                    console.log(error);
                });  
            },
            set_user: function(){
                axios.get(url_app + 'orders/set_user/' + this.user.id)
                .then(response => {
                    console.log(response.data)
                })
                .catch(function (error) {
                    console.log(error);
                });  
            },
            check_pw: function(){
                console.log(this.pw + '-' + this.pc);
                if ( this.pw == this.pc )
                {
                    this.fast_register();
                } else {
                    this.pw_match = false;
                }
            },
            fast_register: function(){                
                axios.post(url_app + 'accounts/fast_register/', $('#register_form').serialize())
                .then(response => {
                    if ( response.data.saved_id > 0 )
                    {
                        this.show_success_register = true;
                        this.show_pw_form = false;
                    }
                    console.log(response.data);
                })
                .catch(function (error) {
                    console.log(error);
                });
            },

        }
    });
</script>