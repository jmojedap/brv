<?php
    $options_category = $this->Item_model->options('category_id = 21', 'Todos');
?>

<div class="card mb-2">
    <div class="card-body">
        

        <div class="form-group row">
            <label for="post_name" class="col-md-4 col-form-label text-right">Título</label>
            <div class="col-md-8">
                <input
                    id="field-post_name"
                    name="post_name"
                    autofocus
                    class="form-control"
                    placeholder="Título de la noticia"
                    title="Título de la noticia"
                    required
                    v-model="form_values.post_name">
            </div>
        </div>


        <div class="form-group row">
            <label for="cat_1" class="col-md-4 col-form-label text-right">Categoría</label>
            <div class="col-md-8">
                <?= form_dropdown('cat_1', $options_category, '', 'class="form-control" v-model="form_values.cat_1" title="Categoría"') ?>
            </div>
        </div>

        <div class="form-group row">
            <div class="offset-md-4 col-md-8 col-sm-12">
                <button class="btn btn-success w120p" type="submit">
                    Crear
                </button>
            </div>
        </div>
    </div>
</div>