<?php
    $app_cf_index = $this->uri->segment(2) . '_' . $this->uri->segment(3);

    $cl_nav_3['users_reservations'] = '';
    $cl_nav_3['users_appointments'] = '';

    //if ( strlen($app_cf_index) == 0 ) { $app_cf_index = 'reservations'; }
    
    $cl_nav_3[$app_cf_index] = 'active';
?>

<script>
    var sections = [];
    var nav_3 = [];
    var sections_role = [];
    var element_id = '<?= $this->uri->segment(4) ?>';
    
    sections.reservations = {
        icon: '',
        text: 'Entrenamientos',
        class: '<?= $cl_nav_3['users_reservations'] ?>',
        cf: 'users/reservations/' + element_id
    };
    
    sections.appointments = {
        icon: '',
        text: 'Citas',
        class: '<?= $cl_nav_3['users_appointments'] ?>',
        cf: 'users/appointments/' + element_id
    };
    
    //Secciones para cada rol
    sections_role[1] = ['reservations', 'appointments'];
    sections_role[2] = ['reservations', 'appointments'];
    sections_role[3] = ['reservations', 'appointments'];
    
    //Recorrer el sections del rol actual y cargarlos en el menú
    for ( key_section in sections_role[app_rid]) 
    {
        var key = sections_role[app_rid][key_section];   //Identificar elemento
        nav_3.push(sections[key]);    //Agregar el elemento correspondiente
    }
    
    //Si el perfil visitado es el mismo al de el usuario en sesión
    if ( element_id === '<?= $this->session->userdata('user_id'); ?>'  ) { nav_3.push(sections.password); }
</script>

<?php
$this->load->view('common/nav_3_v');