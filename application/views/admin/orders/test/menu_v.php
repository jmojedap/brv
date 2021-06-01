<?php
    $app_cf_index = $this->uri->segment(2) . '_' . $this->uri->segment(3);
    
    $cl_nav_3['test_confirmation'] = '';
    $cl_nav_3['test_result'] = '';;
    //$cl_nav_3['orders_import'] = '';
    
    $cl_nav_3[$app_cf_index] = 'active';
    //if ( $app_cf_index == 'orders_cropping' ) { $cl_nav_3['orders_test'] = 'active'; }
?>

<script>
    var sections = [];
    var nav_3 = [];
    var sections_role = [];
    var element_id = '<?= $row->id ?>';
    
    sections.confirmation = {
        icon: '',
        text: 'Confirmación',
        class: '<?= $cl_nav_3['test_confirmation'] ?>',
        cf: 'orders/test/confirmation/' + element_id,
        anchor: true

    };

    sections.result = {
        icon: '',
        text: 'Resultado',
        class: '<?= $cl_nav_3['test_result'] ?>',
        cf: 'orders/test/result/' + element_id,
        anchor: true
    };
    
    //Secciones para cada rol
    sections_role[1] = ['confirmation', 'result'];
    //sections_role[2] = ['explore', 'info', 'edit', 'test'];
    
    //Recorrer el sections del rol actual y cargarlos en el menú
    for ( key_section in sections_role[app_rid]) 
    {
        //console.log(sections_role[rol][key_section]);
        var key = sections_role[app_rid][key_section];   //Identificar elemento
        nav_3.push(sections[key]);    //Agregar el elemento correspondiente
    }
    
</script>

<?php
$this->load->view('common/nav_3_v');