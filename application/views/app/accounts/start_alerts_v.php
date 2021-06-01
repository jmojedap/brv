<?php if ( $this->uri->segment(3) == 'album' ) { ?>
    <div class="alert alert-warning">
        <i class="fa fa-user-plus fa-2x"></i>
        <br>
        Para abrir este album debes registrarte
    </div>
<?php } ?>

<?php if ( $this->uri->segment(3) == 'order' ) { ?>
    <div class="alert alert-warning">
        <i class="fa fa-user-plus fa-2x"></i>
        <br>
        Regístrate para comprar en <?= APP_NAME ?>
    </div>
<?php } ?>