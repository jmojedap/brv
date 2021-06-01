<?php
    $app_cf_index = $this->uri->segment(2) . '_' . $this->uri->segment(3);
    
    $cl_nav_2['products_explore'] = '';
    $cl_nav_2['products_info'] = '';
    $cl_nav_2['products_images'] = '';
    $cl_nav_2['products_edit'] = '';
    //$cl_nav_2['products_import'] = '';
    
    $cl_nav_2[$app_cf_index] = 'active';
    //if ( $app_cf == 'products/explore' ) { $cl_nav_2['products_explore'] = 'active'; }
?>

<script>
    var sections = [];
    var nav_2 = [];
    var sections_role = [];
    var element_id = '<?= $row->id ?>';
    
    sections.explore = {
        icon: 'fa fa-arrow-left',
        text: 'Explorar',
        class: '<?= $cl_nav_2['products_explore'] ?>',
        cf: 'products/explore/',
        anchor: true
    };

    sections.info = {
        icon: 'fa fa-info-circle',
        text: 'Información',
        class: '<?= $cl_nav_2['products_info'] ?>',
        cf: 'products/info/' + element_id
    };

    sections.images = {
        icon: 'fa fa-image',
        text: 'Imágenes',
        class: '<?= $cl_nav_2['products_images'] ?>',
        cf: 'products/images/' + element_id
    };

    sections.posts = {
        icon: 'fa fa-book',
        text: 'Contenidos',
        class: '<?= $cl_nav_2['products_posts'] ?>',
        cf: 'products/posts/' + element_id
    };

    sections.edit = {
        icon: 'fa fa-pencil-alt',
        text: 'Editar',
        class: '<?= $cl_nav_2['products_edit'] ?>',
        cf: 'products/edit/' + element_id,
        anchor: true
    };
    
    //Secciones para cada rol
    sections_role[1] = ['explore', 'info', 'images', 'posts', 'edit'];
    sections_role[2] = ['info'];
    
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