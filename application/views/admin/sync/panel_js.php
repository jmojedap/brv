<script src="<?php //echo url_app('js/Math.uuid.js') ?>"></script>

<script>
//Variables 
//-----------------------------------------------------------------------------
        var url_sync = '<?= URL_SYNC ?>';
        
        var json_download = '';
        var json_tables_status = '';
        
        var table = '';             //Tabla actual
        var method_id_url = <?= $method_id ?>;      //Método de sincronización en URL
        var method_id = <?= $method_id ?>;          //Método de sincronización actual
        var since_id = 0;                   //ID since el cual se descargan los registros
        var limit = <?= $limit ?>;   //Número máximo de registros por cycle de descarga
        var quan_rows = 0;                  //Cantidad de registros en la table actual
        var quan_cycles = 1;                //Cantidad de cycles necesarios para descargar todos los datos
        var quan_inserted = 0;              //Cantidad de registros inserted actualmente
        var cycle = 0;                      //Número del cycle actual
        var percent = 0;                    //Porcentaje de registros inserted
        var user_id = '<?= $this->session->userdata('user_id') ?>';                   //ID Usuario en sesión local
        var username = '<?= $this->session->userdata('username') ?>';                  //Username en sesión local
        
        
        var message = '';

//Document Ready
//-----------------------------------------------------------------------------
    $(document).ready(function(){
        
        //Al hacer clic en el botón de sincronización de cada tabla
        $('.sincro').click(function(){

            table = $(this).data('table');          //Identificar la table
            since_id = $(this).data('since_id');    //Max ID de la table local
            
            method_id = $(this).data('method_id');  //Método automático definido para la table
            if ( method_id_url > 0 )
            {
                method_id = method_id_url;          //Método definido en la URL
            }
            
            start_sync();                       //Marcar inicio de sincronización en la table sis_table
            if ( method_id == 1 ) 
            {
                since_id = 0;       //Para importar todos los registros
                clean_table();
            }      //Eliminar todos los registros de la table local
            calculate_cycles();                      //Inicia todo el proceso de sincronización
        });
        
        //Al hacer clic en el botón de actualización de estado del servidor.
        $('#update_server_status').click(function(){
            update_server_status();
        });
    });

//Funciones
//-----------------------------------------------------------------------------
    
    //Actualiza el status de las tables del servidor, en la table local sis_table
    function update_server_status()
    {
        $.ajax({        
            type: 'POST',
            url: url_sync + 'tables_status',
            data: { user_id: user_id, username: username},
            beforeSend : function(){
                $('#update_server_status').html('Calculando');
            },
            success: function(response){
                json_tables_status = response;
                save_server_status();
            }
        });
    }
    
    /**
    * Si es exitosa la función update_server_status()
    * Se usan los datos JSON descargados para cargarlos
    * en la table local sis_table, campos: max_ids, quan_rows_server
    */
    function save_server_status()
    {
        $.ajax({
            type: 'POST',
            url: url_app + 'sync/save_server_status/',
            data: {
                json_tables_status : json_tables_status
            },
            beforeSend: function(){
                $('#update_server_status').html('<i class="fa fa-database"></i> Guardando');
            },
            success: function(response){
                console.log(response);
                window.location = url_app + 'sync/panel/';
                
            }
        });
    }
    
    /**
     * AJAX
     * En local, actualiza el campo sis_table.start_date
     */
    function start_sync(){

        $('#status_' + table).removeClass('table-success');

        $.ajax({       
            type: 'POST',
            url: url_app + 'sync/start_sync/' + table,
            success: function(response){
                console.log(response.message);
            }
        });
    }
    
    /**
     * AJAX
     * En local, elimina todos los registros de la table
     */
    function clean_table(){
        $.ajax({        
            type: 'POST',
            url: url_app + 'sync/clean_table/' + table,
            beforeSend : function(){
                $('#status_' + table).html('Limpiando tabla local...');
            },
            success: function(){
                $('#percent_bar_' + table).css("width", '0%'); 
            }
        });
    }
    
    /**
     * Conociendo el número de registros de una table se calcula el número de cycles
     * de descarga de registros para sincronización, según el límite (limit) de registros por cycle
     */
    function calculate_cycles()
    {
        $.ajax({        
            type: 'POST',
            url: url_sync + 'quan_rows/' + table + '/' + since_id,
            beforeSend : function()
            {
                $('#status_' + table).html('Calculando...');
            },
            success: function(response)
            {   
                quan_rows = response;
                quan_cycles = Math.ceil( quan_rows / limit);
                process_button();   //Pone el botón en formato status "En proceso"
                next_cycle();       //Iniciar el primer ciclo.
            }
        });
    }
    
    /**
     * Siguiente cycle en la sincronización
     * La sincronización se hace por cycles (paquetes de registros) por la 
     * imposibidad de descargar todos los registros en JSON en una sola transacción
     */
    function next_cycle()
    {
        if ( cycle < quan_cycles  ) //Faltan ciclos
        {
            console.log('Ciclo: ' + cycle);
            synchronize();
        } else {
            //Ciclos terminados
            restart_variables();    //Para siguiente table
            restart_button();       //Reestablecer botón de sincronización de la tabla
            update_sync_data();   //Actualizar finalización de sincronización
            show_result();          //Mostrar resultados finales.
        }
    }
    
    //Descargar los datos del Servidor y guardarlos en la BD local
    function synchronize()
    {
        $.ajax({
            type: 'POST',
            data: { user_id: user_id, username: username},
            url:  url_sync + 'get_rows/' +  table + '/' + limit + '/' + since_id,
            beforeSend : function(){
                var cycle_show = cycle + 1;
                $('#status_' + table).html('<i class="fa fa-download"></i> ' + cycle_show + '/' + quan_cycles);
            },
            success: function(response){
                json_download = response;
                insert_rows();
            }
        });
    }
    
    /**
    * Si es exitosa la función synchronize()
    * Se usan los datos JSON descargados para cargarlos
    * en la base de datos local
    */
    function insert_rows()
    {
        $.ajax({
            type: 'POST',
            url: url_app + 'sync/insert_rows/' + table,
            data: {
                json_download : JSON.stringify(json_download)
            },
            beforeSend: function(){
                var cycle_show = cycle + 1;
                $('#status_' + table).html('<i class="fa fa-save"></i> ' + cycle_show + '/' + quan_cycles);
            },
            success: function(response){
                cycle++;    //Aumenta para siguiente cycle
                since_id = response.max_id;
                quan_inserted += response.quan_rows;
                partial_result();
                next_cycle();
            }
        });
    }
    
    //Actualiza los datos del proceso de sincronización en la tabla local sis_table
    function update_sync_data()
    {
        $.ajax({
            type: 'POST',
            url: url_app + 'sync/update_sync_data/' + table,
            data: {
                quan_rows : quan_rows
            },
            success : function(){
                show_result();
            }
        });
    }
    
    /**
    * Si insert_rows() es exitosa
    * Se muestra en la vista el resultado parcial del proceso de sincronización
    */
    function partial_result()
    {
        percent = Math.ceil( 100 * ( quan_inserted / quan_rows ) );
        $('#quan_inserted_' + table).html(quan_inserted); 
        $('#percent_bar_' + table).css("width",  percent + '%'); 
    }
    
    function show_result()
    {
        $('#status_' + table).addClass('table-success');
        $('#status_' + table).html('<i class="fa fa-check"></i> Finalizado');
        $('#ago_' + table).html('1 min');
        $('#ago_' + table).removeClass('table-info');
        $('#ago_' + table).addClass('table-success');
        $('#quan_rows_' + table).html(quan_rows);
    }
    
    //Reinicia variables de sincronización
    function restart_variables()
    {
        quan_inserted = 0;
        quan_cycles = 0;
        cycle = 0;
        percent = 0;
    }
    
    //Actualiza el botón, status en proceso
    function process_button()
    {
        $('#sincro_' + table).html('<i class="fa fa-sync-alt fa-spin"></i>');
        $('#sincro_' + table).removeClass('btn-secondary');
        $('#sincro_' + table).addClass('btn-info');
    }
    
    //Reestablecer formato botón, al finalizar sincronización
    function restart_button()
    {
        $('#sincro_' + table).html('<i class="fa fa-sync-alt"></i>');
        $('#sincro_' + table).removeClass('btn-info');
        $('#sincro_' + table).addClass('btn-secondary');
    }
</script>