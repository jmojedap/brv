<script>
// Variables
//-----------------------------------------------------------------------------
    file_id = '<?= $row->id ?>';

// Document Ready
//-----------------------------------------------------------------------------
    $(document).ready(function(){

        //Al submit formulario, prevenir evento por defecto y ejecutar función ajax
        $('#change_file_form').submit(function()
        {
            send_form();
            return false;
        });
    });

// Functions
//-----------------------------------------------------------------------------

    /* Función AJAX para envío de archivo JSON a plataforma */
    function send_form()
    {
        var form = $('#change_file_form')[0];
        var form_data = new FormData(form);

        $.ajax({        
            type: 'POST',
            enctype: 'multipart/form-data', //Para incluir archivos en POST
            processData: false,  // Important!
            contentType: false,
            cache: false,
            url: url_api + 'files/change_e/' + file_id,
            data: form_data,
            beforeSend: function(){
                $('#status_text').html('Enviando archivo');
            },
            success: function(response){
                console.log(response.message);
                ///*$('#status_text').html(response.message);
                if ( response.status == 1 )
                {
                    window.location = url_app + 'files/cropping/' + response.row.id;
                }
            }
        });
    }
</script>

<div class="alert alert-warning">
    <i class="fa fa-info-circle"></i> 
    Al cargar un nuevo archivo, el archivo actual se eliminará. Los datos de descripción y asignación del archivo se conservarán sin cambios.
</div>

<div class="card" id="change_file" style="max-width: 600px; margin: 0 auto;">
    <div class="card-body">
        <form enctype="multipart/form-data" method="post" accept-charset="utf-8" id="change_file_form">
            <div class="form-group row">
                <label for="file_field" class="col-md-3 col-form-label ">Archivo</label>
                <div class="col-md-9">
                    <input
                        type="file"
                        name="file_field"
                        required
                        class="form-control"
                        placeholder="Archivo"
                        title="Arcivo a cargar"
                        >
                </div>
            </div>

            <div class="form-group row">
                <div class="col-md-9 offset-md-3">
                    <button class="btn btn-success btn-block" type="submit">
                        Cargar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>