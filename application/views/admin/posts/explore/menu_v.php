<?php
    $app_cf_index = $this->uri->segment(2) . '_' . $this->uri->segment(3);
    
    $cl_nav_2['posts_explore'] = '';
    $cl_nav_2['posts_import'] = '';
    $cl_nav_2['posts_add'] = '';
    
    $cl_nav_2[$app_cf_index] = 'active';
    if ( $app_cf_index == 'posts_import_e' ) { $cl_nav_2['posts_import'] = 'active'; }
?>

<script>
    var sections = [];
    var nav_2 = [];
    var sections_role = [];
    
    sections.explore = {
        icon: 'fa fa-search',
        text: 'Explorar',
        class: '<?= $cl_nav_2['posts_explore'] ?>',
        cf: 'posts/explore'
    };

    sections.import = {
        icon: 'fa fa-upload',
        text: 'Importar',
        class: '<?= $cl_nav_2['posts_import'] ?>',
        cf: 'posts/import'
    };

    sections.add = {
        icon: 'fa fa-plus',
        text: 'Nuevo',
        class: '<?= $cl_nav_2['posts_add'] ?>',
        cf: 'posts/add'
    };
    
    //Secciones para cada rol
    sections_role[1] = ['explore', 'import', 'add'];
    sections_role[2] = ['explore', 'import', 'add'];
    sections_role[3] = ['explore', 'add'];
    
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