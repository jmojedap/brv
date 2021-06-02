<?php
    $cl_nav_3['basic'] = '';
    $cl_nav_3['image'] = '';
    $cl_nav_3['password'] = '';

    $app_cf_index = $this->uri->segment(5);
    if ( strlen($app_cf_index) == 0 ) { $app_cf_index = 'basic'; }
    
    $cl_nav_3[$app_cf_index] = 'active';
    if ( $app_cf_index == 'crop' ) { $cl_nav_3['image'] = 'active'; }
?>

<script>
    var sections = [];
    var nav_3 = [];
    var sections_role = [];
    var element_id = '<?= $this->uri->segment(4) ?>';
    
    sections.basic = {
        icon: '',
        text: 'General',
        class: '<?= $cl_nav_3['basic'] ?>',
        cf: 'users/edit/' + element_id + '/basic'
    };
    
    sections.image = {
        icon: 'fa fa-user-circle',
        text: 'Imagen',
        class: '<?= $cl_nav_3['image'] ?>',
        cf: 'users/edit/' + element_id + '/image'
    };
    
    //Secciones para cada rol
    sections_role[1] = ['basic', 'image'];
    sections_role[2] = ['basic', 'image'];
    
    //Recorrer el sections del rol actual y cargarlos en el menú
    for ( key_section in sections_role[app_rid]) 
    {
        var key = sections_role[app_rid][key_section];   //Identificar elemento
        nav_3.push(sections[key]);    //Agregar el elemento correspondiente
    }
</script>

<?php
$this->load->view('common/nav_3_v');