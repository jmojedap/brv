<?php $this->load->view('assets/summernote') ?>
<?php $this->load->view('assets/bs4_chosen') ?>

<script>
    var noticia_id = <?= $row->id ?>;

    $(document).ready(function(){
        $('#field-content').summernote({
            lang: 'es-ES',
            height: 300
        });

        $('#noticia_form').submit(function(){
            update_post();
            return false;
        });

// Funciones
//-----------------------------------------------------------------------------
    function update_post(){
        $.ajax({        
            type: 'POST',
            url: url_app + 'noticias/update/' + noticia_id,
            data: $('#noticia_form').serialize(),
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

<div id="edit_post" style="max-width: 1500px; margin: 0px auto;">
    <form accept-charset="utf-8" method="POST" id="noticia_form">
        <div class="row">
            <div class="col-md-7">
                <div class="form-group">
                    <label for="excerpt">Título</label>
                    <input
                        type="text"
                        id="field-post_name"
                        name="post_name"
                        required
                        class="form-control"
                        placeholder="Título"
                        title="Título"
                        value="<?= $row->post_name ?>"
                        >
                </div>

                <div class="form-group">
                    <label for="excerpt">Resumen</label>
                    <textarea name="excerpt" id="field-excerpt" rows="4" class="form-control"><?= $row->excerpt ?></textarea>
                </div>

                <textarea name="content" id="field-content" class="form-control"><?= $row->content ?></textarea>

                <div class="form-group">
                    <label for="content_json">content json</label>
                    <textarea name="content_json" id="field-content_json" rows="3" class="form-control"><?= $row->content_json ?></textarea>
                </div>

            </div>
            <div class="col-md-5">
                <div class="form-group row">
                    <div class="col-md-8 offset-md-4">
                        <a href="<?= URL_APP . "noticias/leer/{$row->id}" ?>" class="btn btn-block btn-primary" title="Vista previa" target="_blank">
                            Vista previa
                        </a>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-8 offset-md-4">
                        <button class="btn btn-success btn-block" type="submit">
                            Guardar
                        </button>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="cat_1" class="col-md-4 col-form-label">Categoría</label>
                    <div class="col-md-8">
                        <?= form_dropdown('cat_1', $options_category, '0' . $row->cat_1, 'class="form-control" required') ?>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="status" class="col-md-4 col-form-label">Estado</label>
                    <div class="col-md-8">
                        <?= form_dropdown('status', $options_status, '0' . $row->status, 'class="form-control" required') ?>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="published_at" class="col-md-4 col-form-label">Fecha publicación</label>
                    <div class="col-md-8">
                        <input
                            type="date"
                            id="field-published_at"
                            name="published_at"
                            required
                            class="form-control"
                            value="<?= $this->pml->date_format($row->published_at, 'Y-m-d') ?>"
                            >
                    </div>
                </div>

                <div class="form-group row">
                    <label for="place_id" class="col-md-4 col-form-label">Lugar</label>
                    <div class="col-md-8">
                        <?= form_dropdown('place_id', $options_place, '0' . $row->place_id, 'class="form-control form-control-chosen" required') ?>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="keywords" class="col-md-4 col-form-label">Palabras clave</label>
                    <div class="col-md-8">
                        <input
                            type="text"
                            id="field-keywords"
                            name="keywords"
                            required
                            class="form-control"
                            placeholder="Palabras clave"
                            title="Palabras clave"
                            value="<?= $row->keywords ?>"
                            >
                    </div>
                </div>

                <div class="form-group row">
                    <label for="updater_id" class="col-md-4 col-form-label">Editor</label>
                    <div class="col-md-8">
                        <?= form_dropdown('updater_id', $options_user, '0' . $row->updater_id, 'class="form-control form-control-chosen" required') ?>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>