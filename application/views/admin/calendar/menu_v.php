<?php
    $app_cf_index = $this->uri->segment(2) . '_' . $this->uri->segment(3);
    
    $cl_nav_2['calendar_calendar'] = '';
    $cl_nav_2['calendar_programacion_automatica'] = '';
    $cl_nav_2['calendar_trainings'] = '';
    
    $cl_nav_2[$app_cf_index] = 'active';
    //if ( $app_cf_index == 'calendar_import_e' ) { $cl_nav_2['calendar_import'] = 'active'; }
?>

<script>
    var sections = [];
    var nav_2 = [];
    var sections_role = [];
    
    sections.calendar = {
        icon: '',
        text: 'Calendario',
        class: '<?= $cl_nav_2['calendar_calendar'] ?>',
        cf: 'calendar/calendar'
    };

    sections.programacion_automatica = {
        icon: '',
        text: 'Programar',
        class: '<?= $cl_nav_2['calendar_programacion_automatica'] ?>',
        cf: 'calendar/programacion_automatica'
    };

    sections.trainings = {
        icon: '',
        text: 'Entrenamientos',
        class: '<?= $cl_nav_2['calendar_trainings'] ?>',
        cf: 'calendar/trainings'
    };
    
    //Secciones para cada rol
    sections_role[1] = ['calendar', 'programacion_automatica', 'trainings'];
    sections_role[2] = ['calendar', 'programacion_automatica', 'trainings'];
    
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