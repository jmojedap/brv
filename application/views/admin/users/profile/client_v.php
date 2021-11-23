<script>
// Variables
//-----------------------------------------------------------------------------
    user_id = '<?= $row->id ?>';

// Document Ready
//-----------------------------------------------------------------------------

    $(document).ready(function(){
        $('#btn_set_activation_key').click(function(){
            set_activation_key();
        });
    });

// Functions
//-----------------------------------------------------------------------------

    function set_activation_key(){
        $.ajax({        
            type: 'POST',
            url: url_app + 'users/set_activation_key/' + user_id,
            success: function(response){
                $('#activation_key').html(url_app + 'accounts/recover/' + response);
                toastr['success']('Se actualizó la clave de activación');
            }
        });
    }
</script>

<?php 
    //Imagen
        $att_img = $this->App_model->att_img_user($row);
        $att_img['class'] = 'card-img-top';

    $qty_login = $this->Db_model->num_rows('events', "user_id = {$row->id} AND type_id = 101");
?>

<div class="row">
    <div class="col col-md-4">
        <!-- Page Widget -->
        <div class="card text-center">
            <img src="<?= $att_img['src'] ?>" alt="Profile picture" width="100%">
            <div class="card-body">
                <h4 class="profile-user"><?= $this->Item_model->name(58, $row->role) ?></h4>

                <?php if (in_array($this->session->userdata('role'), array(1,2))) { ?>
                    <a href="<?= URL_ADMIN . "accounts/ml/{$row->id}" ?>" role="button" class="btn btn-primary" title="Log in as this user">
                        <i class="fa fa-sign-in-alt"></i>Log In
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
                    <td width="25%" class="text-right"><span class="text-muted">Name</span></td>
                    <td><?= $row->display_name ?></td>
                </tr>
                <tr>
                    <td class="text-right"><span class="text-muted">Username</span></td>
                    <td><?= $row->username ?></td>
                </tr>

                <tr>
                    <td class="text-right"><span class="text-muted">E-mail</span></td>
                    <td><?= $row->email ?></td>
                </tr>

                <tr>
                    <td class="text-right text-muted">Address</td>
                    <td>
                        <?= $row->address ?><br>
                        <?= $row->address_line_2 ?>
                    </td>
                </tr>
                <tr>
                    <td class="text-right text-muted">Phone number</td>
                    <td>
                        <?= $row->phone_number ?>
                    </td>
                </tr>
                <tr>
                    <td class="text-right text-muted">City</td>
                    <td>
                        <?= $row->city ?>, <?= $row->state_province ?> <?= $row->zip_code ?>
                        <br> <?= $row->country ?>
                    </td>
                </tr>

                <tr>
                    <td class="text-right"><span class="text-muted">Role</span></td>
                    <td><?= $this->Item_model->name(58, $row->role) ?></td>
                </tr>

                <tr>
                    <td class="text-right"><span class="text-muted">Updated at</span></td>
                    <td>
                        <?= $this->pml->date_format($row->updated_at, 'Y-m-d h:i') ?> by <?= $this->App_model->name_user($row->updater_id, 'du') ?>
                    </td>
                </tr>
                <tr>
                    <td class="text-right"><span class="text-muted">Created at</span></td>
                    <td>
                        <?= $this->pml->date_format($row->created_at, 'Y-m-d H:i') ?> by <?= $this->App_model->name_user($row->creator_id, 'du') ?>
                    </td>
                </tr>
                <tr>
                    <td class="text-right"><span class="text-muted">Colibri ID</span></td>
                    <td>
                        <?= $row->code ?>
                    </td>
                </tr>
                <tr>
                    <td class="text-right"><span class="text-muted">About</span></td>
                    <td>
                        <?= $row->about ?>
                    </td>
                </tr>
                <?php if ( in_array($this->session->userdata('role'), array(1,2,3))  ) { ?>
                    <tr>
                        <td class="text-right">
                            <button class="btn btn-primary btn-sm" id="btn_set_activation_key">
                                <i class="fa fa-redo-alt"></i>
                            </button>
                            <span class="text-muted">Activation</span>
                        </td>
                        <td>
                            <span id="activation_key"></span>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>