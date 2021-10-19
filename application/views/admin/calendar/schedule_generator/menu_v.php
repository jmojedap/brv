<?php
    $cl_nav_3['trainings'] = '';
    $cl_nav_3['nutritional_control'] = '';

    $app_cf_index = $this->uri->segment(4);
    if ( strlen($app_cf_index) == 0 ) { $app_cf_index = 'trainings'; }
    
    $cl_nav_3[$app_cf_index] = 'active';
    //if ( $app_cf_index == 'crop' ) { $cl_nav_3['image'] = 'active'; }
?>

<script>
    var sections = [];
    var nav_3 = [];
    var sections_role = [];
    var element_id = '<?= $this->uri->segment(3) ?>';
    
    sections.trainings = {
        icon: '',
        text: 'Entrenamientos',
        class: '<?= $cl_nav_3['trainings'] ?>',
        cf: 'calendar/schedule_generator/trainings'
    };

    sections.nutritional_control = {
        icon: '',
        text: 'Nutrición',
        class: '<?= $cl_nav_3['nutritional_control'] ?>',
        cf: 'calendar/schedule_generator/nutritional_control'
    };
    
    //Secciones para cada rol
    sections_role[1] = ['trainings', 'nutritional_control'];
    sections_role[2] = ['trainings', 'nutritional_control'];
    
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