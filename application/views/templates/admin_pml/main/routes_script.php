<script>
    //Set New CF (Controller Function), and load sections
    function load_cf(new_cf)
    {
        //console.log('CF: ' + new_cf);
        app_cf = new_cf;
        load_sections('nav_1'); //Global function in routes_scripts_v
    }
    
    //Update document sections
    function load_sections(menu_type)
    {
        $.ajax({
            url: url_app + app_cf + '/?json=' + menu_type,
            beforeSend: function(){
                before_send_load_sections(menu_type);
            },
            success: function(result){
                success_load_sections(result, menu_type);
            }
        });
    }
    
    function before_send_load_sections(menu_type)
    {
        $('#view_a').html('Cargando...');
        $('#view_b').html('');
        
        if ( menu_type === 'nav_1' ) { $('#nav_2').html(''); }
        if ( menu_type === 'nav_2' ) { $('#nav_3').html(''); }

        $('.popover').remove(); //Especial, para quitar elementos de herramienta de edición enriquecida, summernote
    }
    
    function success_load_sections(result, menu_type)
    {
        //console.log('CF Respuesta: ' + result.head_title);
        document.title = result.head_title;
        history.pushState(null, null, url_app + app_cf);
        
        $('#head_title').html(result.head_title);
        $('#head_subtitle').html(result.head_subtitle);
        $('#view_a').html(result.view_a);
        
        if ( menu_type === 'nav_1')
        {
            $('#nav_2').html(result.nav_2);
            $('#nav_3').html(result.nav_3);
        }
        
        if ( menu_type === 'nav_2' )
        {
            $('#nav_3').html(result.nav_3);
        }
    }
</script>