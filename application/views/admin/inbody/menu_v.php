<?php
    $app_cf_index = $this->uri->segment(2) . '_' . $this->uri->segment(3);
    
    $cl_nav_2['places_explore'] = '';
    $cl_nav_2['places_info'] = '';
    $cl_nav_2['places_edit'] = '';
    $cl_nav_2['places_details'] = '';
    $cl_nav_2['places_images'] = '';
    //$cl_nav_2['places_import'] = '';
    
    $cl_nav_2[$app_cf_index] = 'active';
    if ( $app_cf_index == 'places_cropping' ) { $cl_nav_2['places_image'] = 'active'; }
?>

<script>
    var sections = [];
    var nav_2 = [];
    var sections_role = [];
    var element_id = '<?= $row->id ?>';
    
    sections.explore = {
        icon: 'fa fa-arrow-left',
        text: 'Explorar',
        class: '<?= $cl_nav_2['places_explore'] ?>',
        cf: 'places/explore/',
        anchor: true
    };

    sections.info = {
        icon: '',
        text: 'Información',
        class: '<?= $cl_nav_2['places_info'] ?>',
        cf: 'places/info/' + element_id
    };

    sections.edit = {
        icon: '',
        text: 'Editar',
        class: '<?= $cl_nav_2['places_edit'] ?>',
        cf: 'places/edit/' + element_id,
        anchor: true
    };
    
    //Secciones para cada rol
    sections_role[1] = ['info', 'edit'];
    sections_role[2] = ['info', 'edit'];
    
    //Recorrer el sections del rol actual y cargarlos en el menú
    for ( key_section in sections_role[app_rid]) 
    {
        var key = sections_role[app_rid][key_section];   //Identificar elemento
        nav_2.push(sections[key]);    //Agregar el elemento correspondiente
    }
    
</script>

<?php
$this->load->view('common/nav_2_v');