<?php
    $app_cf_index = $this->uri->segment(2) . '_' . $this->uri->segment(3);
    
    $cl_nav_2['users_explore'] = '';
    $cl_nav_2['users_profile'] = '';
    $cl_nav_2['users_edit'] = '';
    $cl_nav_2['users_albums'] = '';
    $cl_nav_2['users_products'] = '';
    
    $cl_nav_2[$app_cf_index] = 'active';
    //if ( $app_cf == 'users/explore' ) { $cl_nav_2['users_explore'] = 'active'; }
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

    sections.albums = {
        icon: 'fa fa-images',
        text: 'Álbums',
        class: '<?= $cl_nav_2['users_albums'] ?>',
        cf: 'users/albums/' + element_id
    };

    sections.followers = {
        icon: '',
        text: 'Seguidores',
        class: '<?= $cl_nav_2['users_followers'] ?>',
        cf: 'users/followers/' + element_id
    };

    sections.following = {
        icon: '',
        text: 'Seguidos',
        class: '<?= $cl_nav_2['users_folloging'] ?>',
        cf: 'users/following/' + element_id
    };

    sections.edit = {
        icon: 'fa fa-pencil-alt',
        text: 'Editar',
        class: '<?= $cl_nav_2['users_edit'] ?>',
        cf: 'users/edit/' + element_id
    };
    
    //Secciones para cada rol
    sections_role[1] = ['profile', 'albums', 'followers', 'following', 'edit'];
    sections_role[2] = ['profile'];
    sections_role[13] = ['profile', 'edit'];
    
    //Recorrer el sections del rol actual y cargarlos en el menú
    for ( key_section in sections_role[app_rid]) 
    {
        var key = sections_role[app_rid][key_section];   //Identificar elemento
        nav_2.push(sections[key]);    //Agregar el elemento correspondiente
    }
</script>

<?php
$this->load->view('common/nav_2_v');