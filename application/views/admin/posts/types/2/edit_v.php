<?php $this->load->view('assets/summernote') ?>
<?php $this->load->view('assets/bs4_chosen') ?>

<?php
    $options_category = $this->Item_model->options('category_id = 21', 'Categoría');
    $options_status = $this->Item_model->options('category_id = 42', 'Estado');
    $options_place = $this->App_model->options_place('id > 0', 'full_name');
    $options_user = $this->App_model->options_user('role < 20');
?>

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
            url: url_app + 'noticias/update/',
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

<div id="edit_post">
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

            </div>
            <div class="col-md-5">
                <div class="form-group row">
                    <div class="col-md-8 offset-md-4">
                        <button class="btn btn-success w120p" type="submit">Guardar</button>
                        <a href="<?= URL_ADMIN . "noticias/leer/{$row->id}" ?>" class="btn btn-primary w120p" title="Vista previa" target="_blank">
                            Vista previa
                        </a>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="cat_1" class="col-md-4 col-form-label text-right">Categoría</label>
                    <div class="col-md-8">
                        <?= form_dropdown('cat_1', $options_category, '0' . $row->cat_1, 'class="form-control" required') ?>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="status" class="col-md-4 col-form-label text-right">Estado</label>
                    <div class="col-md-8">
                        <?= form_dropdown('status', $options_status, '0' . $row->status, 'class="form-control" required') ?>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="published_at" class="col-md-4 col-form-label text-right">Fecha publicación</label>
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
                    <label for="place_id" class="col-md-4 col-form-label text-right">Lugar</label>
                    <div class="col-md-8">
                        <?= form_dropdown('place_id', $options_place, '0' . $row->place_id, 'class="form-control form-control-chosen" required') ?>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="keywords" class="col-md-4 col-form-label text-right">Palabras clave</label>
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
                    <label for="updater_id" class="col-md-4 col-form-label text-right">Editor</label>
                    <div class="col-md-8">
                        <?= form_dropdown('updater_id', $options_user, '0' . $row->updater_id, 'class="form-control form-control-chosen" required') ?>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>