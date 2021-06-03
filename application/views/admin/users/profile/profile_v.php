<div class="container" id="profile_app">
    <div class="row">
        <div class="col col-md-4">
            <!-- Page Widget -->
            <div class="card text-center">
                <img src="<?= $row->url_image ?>" alt="Imagen del usuario"  onerror="this.src='<?= URL_IMG ?>users/user.png'" class="w100pc">
                <div class="card-body">
                    <h4 class="profile-user"><?= $this->Item_model->name(58, $row->role) ?></h4>

                    <?php if ($this->session->userdata('rol_id') <= 1) { ?>
                        <a href="<?= URL_ADMIN . "config/ml/{$row->id}" ?>" role="button" class="btn btn-primary" title="Ingresar como este usuario">
                            <i class="fa fa-sign-in"></i>
                            Acceder
                        </a>
                    <?php } ?>

                </div>
            </div>
            <!-- End Page Widget -->
        </div>
        <div class="col col-md-8">
            <table class="table bg-white">
                <tbody>
                    <tr>
                        <td class="td-title">Nombre</td>
                        <td><?= $row->display_name ?></td>
                    </tr>

                    <tr>
                        <td class="td-title">Username</td>
                        <td><?= $row->username ?></td>
                    </tr>

                    <tr>
                        <td class="td-title">Correo electrónico</td>
                        <td><?= $row->email ?></td>
                    </tr>

                    <tr>
                        <td class="td-title">Rol de usuario</td>
                        <td><?= $this->Item_model->name(58, $row->role) ?></td>
                    </tr>
                    <tr>
                        <td class="td-title">Seguidores / Seguidos</td>
                        <td><?= $row->qty_followers ?> / <?= $row->qty_following ?></td>
                    </tr>
                    <tr>
                        <td class="td-title">Notas privadas</td>
                        <td><?= $row->admin_notes ?></td>
                    </tr>
                    <tr>
                        <td class="td-title">Acerca de mí</td>
                        <td><?= $row->about ?></td>
                    </tr>

                    <tr>
                        <td class="td-title">Actualizado</td>
                        <td>
                            <?= $this->pml->date_format($row->updated_at, 'Y-m-d h:i') ?> por <?= $this->App_model->name_user($row->updater_id, 'du') ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="td-title">Creado</td>
                        <td>
                            <?= $this->pml->date_format($row->created_at, 'Y-m-d H:i') ?> por <?= $this->App_model->name_user($row->creator_id, 'du') ?>
                        </td>
                    </tr>
                    <?php if ( $this->session->userdata('role') <= 2  ) { ?>
                        <tr>
                            <td class="td-title">
                                <button class="btn btn-primary btn-sm" v-on:click="setActivationKey">
                                    <i class="fa fa-redo-alt"></i>
                                </button>
                                <span class="text-muted"></span>
                            </td>
                            <td>
                                <span id="activation_key" class="text-info">{{ activation_link }}</span>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
var profile_app = new Vue({
    el: '#profile_app',
    data: {
        user_id: <?= $row->id ?>,
        activation_link: 'Restaurar contraseña'
    },
    methods: {
        setActivationKey: function(){
            axios.get(url_api + 'users/set_activation_key/' + this.user_id)
            .then(response => {
                this.activation_link = '<?= URL_APP ?>accounts/recover/' + response.data
                toastr['success']('Copie el link y ábralo en otro navegador para establecer una nueva contraseña')
            })
            .catch(function(error) { console.log(error) })   
        },
    }
})
</script>