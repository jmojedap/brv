<?php
    $app_cf_index = $this->uri->segment(2) . '_' . $this->uri->segment(3);

    $cl_nav_2 = [
        'orders_info' => '',
        'orders_details' => '',
        'orders_responses' => '',
        'orders_payment' => '',
        'orders_edit' => '',
        'orders_test' => '',
    ];
    
    $cl_nav_2[$app_cf_index] = 'active';
    if ( $app_cf_index == 'orders_cropping' ) { $cl_nav_2['orders_test'] = 'active'; }
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
        cf: 'orders/explore/',
        anchor: true
    };

    sections.info = {
        icon: '',
        text: 'Información',
        class: '<?= $cl_nav_2['orders_info'] ?>',
        cf: 'orders/info/' + element_id
    };

    sections.details = {
        icon: '',
        text: 'Detalles',
        class: '<?= $cl_nav_2['orders_details'] ?>',
        cf: 'orders/details/' + element_id
    };

    sections.responses = {
        icon: '',
        text: 'Wompi',
        class: '<?= $cl_nav_2['orders_responses'] ?>',
        cf: 'orders/responses/' + element_id
    };

    sections.payment = {
        icon: '',
        text: 'Pago',
        class: '<?= $cl_nav_2['orders_payment'] ?>',
        cf: 'orders/payment/' + element_id
    };

    sections.edit = {
        icon: '',
        text: 'Editar',
        class: '<?= $cl_nav_2['orders_edit'] ?>',
        cf: 'orders/edit/' + element_id
    };
    
    sections.test = {
        icon: 'fa fa-test',
        text: 'Test',
        class: '<?= $cl_nav_2['orders_test'] ?>',
        cf: 'orders/test/' + element_id + '/confirmation'
    };
    
    //Secciones para cada rol
    sections_role[1] = ['info', 'payment', 'details', 'responses', 'edit', 'test'];
    sections_role[2] = ['info', 'payment', 'details', 'responses', 'edit'];
    
    //Recorrer el sections del rol actual y cargarlos en el menú
    for ( key_section in sections_role[app_rid]) 
    {
        var key = sections_role[app_rid][key_section];   //Identificar elemento
        nav_2.push(sections[key]);    //Agregar el elemento correspondiente
    }
    
</script>

<?php
$this->load->view('common/nav_2_v');