<?php $this->load->view('assets/summernote') ?>

<script>
    $(document).ready(function(){
        $('#field-description').summernote({
            lang: 'es-ES',
            height: 300
        });
    });
</script>

<?php
    $arr_fields = array(
        'slug' => 'Slug',
        'status' => 'Estado',
        'type_id' => 'ID tipo',
        'keywords' => 'keywords',
        'price' => 'Precio',
        'cost' => 'Costo',
        'base_price' => 'Precio base',
        'tax_percent' => '% IVA',
        'tax' => 'IVA',
        'external_url' => 'URL externa',
        'url_image' => 'src imagen',
        'url_thumbnail' => 'src miniatura',
        'cat_1' => 'Categoría producto',
        'text_1' => 'Autor (text_1)',
        'text_2' => 'Editorial (text_2)',
        'integer_1' => 'Num. paginas (integer_1)',
        'priority' => 'Prioridad'
    );
?>

<div id="app_edit">
    <div class="card center_box_750">
        <div class="card-body">
            <form id="edit_form" accept-charset="utf-8" @submit.prevent="send_form">

                <div class="form-group row">
                    <label for="code" class="col-md-4 col-form-label text-right">Referencia</label>
                    <div class="col-md-8">
                        <input
                            type="text"
                            id="field-code"
                            name="code"
                            required
                            class="form-control"
                            placeholder="Referencia"
                            title="Referencia"
                            v-model="form_values.code"
                            >
                    </div>
                </div>

                <div class="form-group row">
                    <label for="name" class="col-md-4 col-form-label text-right">Nombre producto</label>
                    <div class="col-md-8">
                        <input
                            type="text"
                            id="field-name"
                            name="name"
                            required
                            class="form-control"
                            placeholder="Nombre producto"
                            title="Nombre producto"
                            v-model="form_values.name"
                            >
                    </div>
                </div>

                <div class="form-group row">
                    <label for="description" class="col-md-4 col-form-label text-right">Descripción</label>
                    <div class="col-md-8">
                        <textarea
                            id="field-description"
                            name="description"
                            required
                            class="form-control"
                            placeholder="Descripción"
                            title="Descripción"
                            v-model="form_values.description"
                            rows="3"
                            ></textarea>
                    </div>
                </div>

                <?php foreach ( $arr_fields as $field => $field_title ) { ?>
                    <div class="form-group row">
                        <label for="<?= $field ?>" class="col-md-4 col-form-label text-right"><?= $field_title ?></label>
                        <div class="col-md-8">
                            <input
                                type="text"
                                id="field-<?= $field ?>"
                                name="<?= $field ?>"
                                class="form-control"
                                placeholder="<?= $field_title ?>"
                                title="<?= $field_title ?>"
                                v-model="form_values.<?= $field ?>"
                                >
                        </div>
                    </div>
                <?php } ?>

                <div class="form-group row">
                    <div class="offset-md-4 col-md-8">
                        <button class="btn btn-success w120p" type="submit">
                            Guardar
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
    //Cargar valor en formulario
    var form_values = <?= json_encode($row) ?>;
    /*form_values.institution_id = '0<?= $row->institution_id ?>';
    form_values.level = '0<?= $row->level ?>';
    form_values.teacher_id = '0<?= $row->teacher_id ?>';
    form_values.schedule = '0<?= $row->schedule ?>';*/
    
    new Vue({
    el: '#app_edit',
        data: {
            form_values: form_values,
            row_id: '<?= $row->id ?>'
        },
        methods: {
            send_form: function() {
                axios.post(url_app + 'products/update/' + this.row_id, $('#edit_form').serialize())
                    .then(response => {
                        if (response.data.status == 1)
                        {
                            toastr['success']('El producto fue actualizado');
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                });
            }
        }
    });
</script>