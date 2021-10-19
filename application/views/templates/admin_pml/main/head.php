<meta charset="UTF-8">
        <title><?= $head_title ?></title>
        <link rel="shortcut icon" href="<?= URL_BRAND ?>favicon.png" type="image/png"/>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

        <!--Icons font-->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.0/css/all.min.css" integrity="sha512-BnbUDfEUfV0Slx6TunuB042k9tuKe3xrD6q4mg5Ed72LTgzDIcLPxg6yI2gcMFRyomt+yJJxE+zJwNmxki6/RA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

        <!--JQuery-->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>

        <!-- Bootstrap 4.3.1 -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

        <!-- Vue.js -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.0.5/vue.min.js" integrity="sha256-GOrA4t6mqWceQXkNDAuxlkJf2U1MF0O/8p1d/VPiqHw=" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.18.0/axios.min.js"></script>

        <!-- Toastr: Alertas y Notificaciones -->
        <link href="<?= URL_RESOURCES ?>templates/admin_pml/css/skins/skin-blue-toastr.css" rel="stylesheet" type="text/css" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
        <script src="<?= URL_RESOURCES ?>config/admin_pml/toastr-options.js"></script>

        <!-- Moment.js -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.js" integrity="sha256-H9jAz//QLkDOy/nzE9G4aYijQtkLt9FvGmdUTwBk6gs=" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/locale/es.js" integrity="sha256-bETP3ndSBCorObibq37vsT+l/vwScuAc9LRJIQyb068=" crossorigin="anonymous"></script>

        <!-- Tema AdminPML -->
        <link href="<?= URL_RESOURCES ?>templates/admin_pml/css/admin-pml.css" rel="stylesheet" type="text/css" />
        <link href="<?= URL_RESOURCES ?>templates/admin_pml/css/mobile.css" rel="stylesheet" type="text/css" />
        <link href="<?= URL_RESOURCES ?>templates/admin_pml/css/skins/skin-brave.css" rel="stylesheet" type="text/css" />
        <script src="<?= URL_RESOURCES ?>templates/admin_pml/js/app.js"></script>
        <script src="<?= URL_RESOURCES ?>templates/admin_pml/js/routing.js"></script>

        <!-- Recursos PML -->
        <link type="text/css" rel="stylesheet" href="<?= URL_RESOURCES ?>css/pacarina.css">
        <link type="text/css" rel="stylesheet" href="<?= URL_RESOURCES ?>css/brave_admin.css">
        <script src="<?= URL_RESOURCES . 'js/pcrn.js' ?>"></script>
        <script>
            const url_app = '<?= URL_ADMIN ?>'; const url_admin = '<?= URL_ADMIN ?>'; const url_api = '<?= URL_API ?>'; const url_front = '<?= URL_FRONT ?>';
            const url_eapi = '<?= URL_EAPI ?>';
            const url_base = '<?= base_url() ?>';
            var app_cf = '<?= $this->uri->segment(2) . '/' . $this->uri->segment(3); ?>';
        </script>

        <!-- Usuario con sesión iniciada -->
        <?php if ( $this->session->userdata('logged') ) : ?>
            <!-- Elementos del menú -->
            <script src="<?= URL_RESOURCES ?>config/admin_pml/menus/nav_1_elements_<?= $this->session->userdata('role') ?>.js"></script>

            <script>
                const app_rid = <?= $this->session->userdata('role') ?>;
                const app_uid = <?= $this->session->userdata('user_id') ?>;
            </script>
        <?php endif; ?>