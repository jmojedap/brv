<script>
    var album_id = <?= $row->id ?>;

    $(document).ready(function(){
        $('#album_form').submit(function(){
            update_album();
            return false;
        });

// Funciones
//-----------------------------------------------------------------------------
    function update_album(){
        $.ajax({        
            type: 'POST',
            url: url_app + 'albums/update/' + album_id,
            data: $('#album_form').serialize(),
            success: function(response){
                if ( response.status == 1 )
                {
                    toastr['success'](response.message);
                }
            }
        });
    }
    });
</script>

<div id="edit_album" class="card center_box_750">
    <div class="card-body">
        <form accept-charset="utf-8" method="POST" id="album_form">
            <div class="form-group row">
                <label for="post_name" class="col-md-4 col-form-label text-right">Título álbum</label>
                <div class="col-md-8">
                    <input
                        type="text"
                        name="post_name"
                        required
                        class="form-control"
                        placeholder="post name"
                        title="post name"
                        value="<?= $row->post_name ?>"
                        >
                </div>
            </div>

            <div class="form-group row">
                <label for="excerpt" class="col-md-4 col-form-label text-right">Descripción</label>
                <div class="col-md-8">
                    <textarea
                        name="excerpt" class="form-control" rows="3"
                        title="Descripción" placeholder=""                        
                    ><?= $row->excerpt ?></textarea>
                </div>
            </div>


            <div class="form-group row">
                <label for="related_1" class="col-md-4 col-form-label text-right">Girl</label>
                <div class="col-md-8">
                    <?= form_dropdown('related_1', $options_user, '0' . $row->related_1, 'class="form-control" v-model="form_values.related_1" title="Usuario"') ?>
                </div>
            </div>

            <div class="form-group row">
                <label for="status" class="col-md-4 col-form-label text-right">Status</label>
                <div class="col-md-8">
                    <?= form_dropdown('status', $options_status, '0' . $row->status, 'class="form-control" v-model="form_values.status" title="Estado"') ?>
                </div>
            </div>

            <div class="form-group row">
                <label for="integer_1" class="col-md-4 col-form-label text-right">Tipo álbum</label>
                <div class="col-md-8">
                    <?= form_dropdown('integer_1', $options_type, '0' . $row->integer_1, 'class="form-control" title="Tipo"') ?>
                </div>
            </div>

            <div class="form-group row">
                <label for="score_1" class="col-md-4 col-form-label text-right">Prioridad</label>
                <div class="col-md-8">
                    <input
                        name="score_1" type="text" class="form-control"
                        required
                        title="Prioridad" placeholder="Prioridad"
                        value="<?= $row->score_1 ?>"
                    >
                </div>
            </div>

            <div class="form-group row">
                <label for="integer_1" class="col-md-4 col-form-label text-right">Clothes</label>
                <div class="col-md-8">
                    <?= form_dropdown('related_2', $options_related_2, '0' . $row->related_2, 'class="form-control" v-model="form_values.related_1" title="Clothes"') ?>
                </div>
            </div>
            <div class="form-group row">
                <label for="integer_2" class="col-md-4 col-form-label text-right">Precio</label>
                <div class="col-md-8">
                    <input
                        name="integer_2" type="text" class="form-control"
                        required
                        title="Precio" placeholder="Precio"
                        value="<?= $row->integer_2 ?>"
                    >
                </div>
            </div>

            <div class="form-group row">
                <label for="keywords" class="col-md-4 col-form-label text-right">Palabras clave</label>
                <div class="col-md-8">
                    <input
                        name="keywords" type="text" class="form-control"
                        required
                        title="Palabras clave" placeholder="Palabras clave"
                        value="<?= $row->keywords ?>"
                    >
                </div>
            </div>


            <div class="form-group row">
                <div class="col-md-8 offset-md-4">
                    <button class="btn btn-success w120p" type="submit">
                        Guardar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>