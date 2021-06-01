<!-- Modal -->
<div class="modal fade" id="modal_signup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">¿Ya tienes cuenta de usuario?</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-pills nav-fill mb-2">
                    <li class="nav-item">
                        <a class="nav-link" href="#" v-on:click="set_section('signup')" v-bind:class="{'active': section == 'signup' }">Nuevo</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" v-on:click="set_section('login')" v-bind:class="{'active': section == 'login' }">Ya estoy registrado</a>
                    </li>
                </ul>

                <div v-show="section == 'signup'">
                    <form accept-charset="utf-8" method="POST" id="signup_form" @submit.prevent="send_form">
                        
                    </form>
                </div>

                <div v-show="section == 'login'">
                    voya a ingresar con mi cuenta
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    new Vue({
        el: '#modal_signup',
        created: function(){
            //this.get_list();
        },
        data: {
            section: 'signup'
        },
        methods: {
            set_section: function(section){
                this.section = section  
            },
        }
    });
</script>