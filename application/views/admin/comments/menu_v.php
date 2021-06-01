<?php
    $app_cf_index = $this->uri->segment(2) . '_' . $this->uri->segment(3);
    
    $cl_nav_2['comments_info'] = '';
    $cl_nav_2['comments_edit'] = '';
    //$cl_nav_2['comments_import'] = '';
    
    $cl_nav_2[$app_cf_index] = 'active';
    //if ( $app_cf == 'comments/explore' ) { $cl_nav_2['comments_explore'] = 'active'; }
?>

<script>
    var sections = [];
    var nav_2 = [];
    var sections_role = [];
    var element_id = '<?= $row->id ?>';
    
    sections.explore = {
        icon: 'fa fa-arrow-left',
        text: 'Explorar',
        class: '',
        cf: 'comments/explore/',
        anchor: true
    };

    sections.info = {
        icon: '',
        text: 'Información',
        class: '<?= $cl_nav_2['comments_info'] ?>',
        cf: 'comments/info/' + element_id
    };

    sections.edit = {
        icon: '',
        text: 'Editar',
        class: '<?= $cl_nav_2['comments_edit'] ?>',
        cf: 'comments/edit/' + element_id,
        anchor: true
    };
    
    //Secciones para cada rol
    sections_role[1] = ['explore', 'info'];
    sections_role[2] = ['explore', 'info'];
    
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