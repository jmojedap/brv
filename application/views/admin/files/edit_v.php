<script>
// Variables
//-----------------------------------------------------------------------------
    var file_id = '<?= $row->id ?>';

// Document ready
//-----------------------------------------------------------------------------
    $(document).ready(function(){
        $('#file_form').submit(function(){
            send_form();
            return false;
        });
    });

// Enviar formulario
//-----------------------------------------------------------------------------

    function send_form(){
        $.ajax({        
            type: 'POST',
            url: url_app + 'files/update/' + file_id,
            data: $('#file_form').serialize(),
            success: function(response){
                if ( response.status == 1) {
                    toastr['success']('Guardado');
                } else {
                    toastr['error']('Los cambios no se guardaron');
                }
            }
        });
    }
</script>

<div class="row">
    <div class="col-md-6 col-sm-12">
        <div class="card">
            <div class="card-body">
                <form accept-charset="utf-8" method="POST" id="file_form">
                    <div class="form-group row">
                        <label for="title" class="col-sm-3 col-form-label text-right">Título archivo *</label>
                        <div class="col-sm-9">
                            <input
                                type="text"
                                name="title"
                                required
                                class="form-control"
                                placeholder="Título archivo"
                                title="Título archivo"
                                value="<?= $row->title ?>"
                                >
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label for="subtitle" class="col-sm-3 col-form-label text-right">Subtítulo</label>
                        <div class="col-sm-9">
                            <input
                                type="text" name="subtitle"
                                class="form-control"
                                title="Subtítulo archivo"
                                value="<?= $row->subtitle ?>"
                                >
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label for="keywords" class="col-sm-3 col-form-label text-right">Palabras clave *</label>
                        <div class="col-sm-9">
                            <input
                                type="text" name="keywords" required
                                class="form-control"
                                title="Palabras clave"
                                value="<?= $row->keywords ?>"
                                >
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label for="descripcion" class="col-sm-3 col-form-label text-right">Descripción</label>
                        <div class="col-sm-9">
                            <textarea
                                type="text"
                                id="field-description"
                                name="description"
                                class="form-control"    
                                title="Descripción"
                                ><?= $row->description ?></textarea>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label for="link" class="col-sm-3 col-form-label text-right">Link</label>
                        <div class="col-sm-9">
                            <input
                                type="url"
                                id="field-external_link"
                                name="external_link"
                                class="form-control"
                                placeholder="Link que se abre al hacer clic en el archivo"
                                title="Link que se abre al hacer clic en el archivo"
                                value="<?= $row->external_link ?>"
                                >
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="offset-sm-3 col-sm-9">
                            <button class="btn btn-success w120p">
                                Guardar
                            </button>
                        </div>
                    </div>
                </form>
                
            </div>
        </div>    
    </div>
    
    <div class="col-md-6 col-sm-12">
        <img src="<?= $src ?>" alt="Imagen archivo" class="rounded mb-2" style="width: 100%; max-width: 500px;">
        <br/>
        <a href="<?= URL_ADMIN . "files/change/{$row->id}" ?>" class="btn btn-primary" title="Cambiar esta imagen">
            Cambiar
        </a>
    </div>
</div>



