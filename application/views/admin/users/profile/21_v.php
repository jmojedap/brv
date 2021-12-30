<?php
    $condition = "creator_id = {$row->id} AND type_id IN (7,6)";
    $qty_posts = $this->Db_model->num_rows('posts', $condition);
?>

<div class="container" id="profile_app">
    <div class="row">
        <div class="col col-md-4">
            <!-- Page Widget -->
            <div class="card text-center">
                <img src="<?= $row->url_image ?>" alt="Imagen del usuario"  onerror="this.src='<?= URL_IMG ?>users/user.png'" class="w100pc">
                <div class="card-body">
                    <h4 class="profile-user"><?= $this->Item_model->name(58, $row->role) ?></h4>

                    <?php if ($this->session->userdata('role') <= 2) { ?>
                        <a href="<?= URL_ADMIN . "accounts/ml/{$row->id}" ?>" role="button" class="btn btn-primary" title="Ingresar como este usuario">
                            <i class="fa fa-sign-in"></i>Acceder
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
                        <td class="td-title">Documento</td>
                        <td><?= $this->Item_model->name(53, $row->document_type, 'abbreviation') ?> &middot; <?= $row->document_number ?></td>
                    </tr>
                    <tr>
                        <td class="td-title">Plan comercial</td>
                        <td>
                            <i class="fa fa-circle prtp_<?= $row->commercial_plan ?>"></i>
                            <?= $this->Item_model->name(189, $row->commercial_plan) ?> &middot; 
                            <small class="text-muted">(<?= $row->commercial_plan ?>)</small>
                        </td>
                    </tr>
                    <tr>
                        <td class="td-title">Suscripción hasta</td>
                        <td>
                            {{ user.expiration_at }} &middot;
                            {{ user.expiration_at | ago }} 
                        </td>
                    </tr>
                    <tr>
                        <td class="td-title">Seguidores / Seguidos</td>
                        <td><?= $row->qty_followers ?> / <?= $row->qty_following ?></td>
                    </tr>
                    <tr>
                        <td class="td-title">Publicaciones</td>
                        <td>
                            <a href="<?= URL_ADMIN . "posts/explore/?u={$row->id}" ?>" class="btn btn-sm btn-light w40p">
                                <?= $qty_posts ?>
                            </a>
                        </td>
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
// Variables
//-----------------------------------------------------------------------------
    var user = {
        id: '<?= $row->id ?>',
        first_name: '<?= $row->first_name ?>',
        last_name: '<?= $row->last_name ?>',
        display_name: '<?= $row->display_name ?>',
        username: '<?= $row->username ?>',
        email: '<?= $row->email ?>',
        role: '0<?= $row->role ?>',
        document_number: '<?= $row->document_number ?>',
        document_type: '0<?= $row->document_type ?>',
        city_id: '0<?= $row->city_id ?>',
        birth_date: '<?= $row->birth_date ?>',
        gender: '0<?= $row->gender ?>',
        phone_number: '<?= $row->phone_number ?>',
        expiration_at: '<?= $row->expiration_at ?>',
        admin_notes: '<?= $row->admin_notes ?>',
    };

// Filtros
//-----------------------------------------------------------------------------
Vue.filter('ago', function (date) {
    if (!date) return ''
    return moment(date, 'YYYY-MM-DD HH:mm:ss').fromNow()
});

// VueApp
//-----------------------------------------------------------------------------
var profile_app = new Vue({
    el: '#profile_app',
    data: {
        user: user,
        activation_link: 'Restaurar contraseña'
    },
    methods: {
        setActivationKey: function(){
            axios.get(url_api + 'users/set_activation_key/' + this.user.id)
            .then(response => {
                this.activation_link = '<?= URL_APP ?>accounts/recover/' + response.data
                toastr['success']('Copie el link y ábralo en otro navegador para establecer una nueva contraseña')
            })
            .catch(function(error) { console.log(error) })   
        },
    }
})
</script>