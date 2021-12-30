<?php
    $app_cf_index = $this->uri->segment(2) . '_' . $this->uri->segment(3);
    
    $cl_nav_2['users_explore'] = '';
    $cl_nav_2['users_profile'] = '';
    $cl_nav_2['users_reservations'] = '';
    $cl_nav_2['users_orders'] = '';
    $cl_nav_2['users_subscriptions'] = '';
    $cl_nav_2['users_inbody'] = '';
    $cl_nav_2['follow_following'] = '';
    $cl_nav_2['follow_followers'] = '';
    $cl_nav_2['users_edit'] = '';
    
    $cl_nav_2[$app_cf_index] = 'active';
    if ( $app_cf_index == 'users_appointments' ) { $cl_nav_2['users_reservations'] = 'active'; }
?>

<script>
    var sections = [];
    var nav_2 = [];
    var sections_role = [];
    var element_id = '<?= $row->id ?>';    

    sections.profile = {
        icon: 'fa fa-user',
        text: 'Información',
        class: '<?= $cl_nav_2['users_profile'] ?>',
        cf: 'users/profile/' + element_id
    };

    sections.calendar = {
        icon: '',
        text: 'Calendario',
        class: '<?= $cl_nav_2['users_reservations'] ?>',
        cf: 'users/reservations/' + element_id
    };

    sections.orders = {
        icon: '',
        text: 'Pagos',
        class: '<?= $cl_nav_2['users_orders'] ?>',
        cf: 'users/orders/' + element_id
    };
    
    sections.subscriptions = {
        icon: '',
        text: 'Suscripciones',
        class: '<?= $cl_nav_2['users_subscriptions'] ?>',
        cf: 'users/subscriptions/' + element_id
    };

    sections.lists = {
        icon: '',
        text: 'Listas',
        class: '<?= $cl_nav_2['users_lists'] ?>',
        cf: 'users/lists/' + element_id
    };

    sections.inbody = {
        icon: '',
        text: 'InBody',
        class: '<?= $cl_nav_2['users_inbody'] ?>',
        cf: 'users/inbody/' + element_id,
        anchor: true
    };

    sections.followers = {
        icon: '',
        text: 'Seguidores',
        class: '<?= $cl_nav_2['follow_followers'] ?>',
        cf: 'follow/followers/' + element_id
    };

    sections.following = {
        icon: '',
        text: 'Seguidos',
        class: '<?= $cl_nav_2['follow_folloging'] ?>',
        cf: 'follow/following/' + element_id
    };

    sections.edit = {
        icon: 'fa fa-pencil-alt',
        text: 'Editar',
        class: '<?= $cl_nav_2['users_edit'] ?>',
        cf: 'users/edit/' + element_id
    };
    
    //Secciones para cada rol
    sections_role[1] = ['profile', 'calendar', 'orders', 'subscriptions', 'inbody', 'edit'];
    sections_role[2] = ['profile', 'calendar', 'orders', 'subscriptions', 'inbody', 'edit'];
    sections_role[4] = ['calendar', 'inbody'];
    
    //Recorrer el sections del rol actual y cargarlos en el menú
    for ( key_section in sections_role[app_rid]) 
    {
        var key = sections_role[app_rid][key_section];   //Identificar elemento
        nav_2.push(sections[key]);    //Agregar el elemento correspondiente
    }
</script>

<?php
$this->load->view('common/nav_2_v');