<?php
    $options_category = $this->Item_model->options('category_id = 25', 'Todos');
?>

<div class="card mb-2">
    <div class="card-body">
        <div class="form-group row">
            <label for="post_name" class="col-md-4 col-form-label text-right">Título</label>
            <div class="col-md-8">
                <input
                    name="post_name" class="form-control"
                    title="Nombre del album"
                    required
                    v-model="form_values.post_name">
            </div>
        </div>

        <div class="form-group row">
            <label for="related_1" class="col-md-4 col-form-label text-right">Girl</label>
            <div class="col-md-8">
                <?= form_dropdown('related_1', $options_user, '', 'class="form-control" v-model="form_values.related_1" title="Usuario"') ?>
            </div>
        </div>

        <div class="form-group row">
            <label for="integer_1" class="col-md-4 col-form-label text-right">Tipo</label>
            <div class="col-md-8">
                <?= form_dropdown('integer_1', $options_type, '', 'class="form-control" v-model="form_values.integer_1" title="Tipo de álbum"') ?>
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