<?php
    $app_cf_index = $this->uri->segment(2) . '_' . $this->uri->segment(3);
    
    $cl_nav_2['noticias_info'] = '';
    $cl_nav_2['noticias_edit'] = '';
    $cl_nav_2['noticias_image'] = '';
    //$cl_nav_2['noticias_import'] = '';
    
    $cl_nav_2[$app_cf_index] = 'active';
    if ( $app_cf_index == 'noticias_cropping' ) { $cl_nav_2['noticias_image'] = 'active'; }
?>

<script>
    var sections = [];
    var nav_2 = [];
    var sections_role = [];
    var element_id = '<?= $row->id ?>';
    
    sections.info = {
        icon: 'fa fa-info-circle',
        text: 'Información',
        class: '<?= $cl_nav_2['noticias_info'] ?>',
        cf: 'noticias/info/' + element_id
    };

    sections.edit = {
        icon: 'fa fa-pencil-alt',
        text: 'Editar',
        class: '<?= $cl_nav_2['noticias_edit'] ?>',
        cf: 'noticias/edit/' + element_id
    };
    
    sections.image = {
        icon: 'fa fa-image',
        text: 'Imagen',
        class: '<?= $cl_nav_2['noticias_image'] ?>',
        cf: 'noticias/image/' + element_id
    };
    
    //Secciones para cada rol
    sections_role[1] = ['info', 'edit', 'image'];
    
    //Recorrer el sections del rol actual y cargarlos en el menú
    for ( key_section in sections_role[app_rid]) 
    {
        //console.log(sections_role[rol][key_section]);
        var key = sections_role[app_rid][key_section];   //Identificar elemento
        nav_2.push(sections[key]);    //Agregar el elemento correspondiente
    }
    
</script>

<?php
$this->load->view('common/nav_2_v');