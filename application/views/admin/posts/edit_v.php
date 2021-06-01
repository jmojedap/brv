<?php $this->load->view('assets/summernote') ?>

<?php
    $arr_fields = array(
        'code',
        'status',
        'slug',
        'keywords',
        'parent_id',
        'place_id',
        'related_1',
        'text_1',
        'text_2',
        'integer_1',
        'integer_2',
    );
?>

<script>
    var post_id = <?= $row->id ?>;

    $(document).ready(function(){
        $('#field-content').summernote({
            lang: 'es-ES',
            height: 300
        });

        $('#post_form').submit(function(){
            update_post();
            return false;
        });

// Funciones
//-----------------------------------------------------------------------------
    function update_post(){
        $.ajax({        
            type: 'POST',
            url: url_app + 'posts/update/' + post_id,
            data: $('#post_form').serialize(),
            success: function(response){
                if ( response.status == 1 )
                {
                    toastr['success']('Guardado');
                }
            }
        });
    }
    });
</script>

<div id="edit_post">
    <form accept-charset="utf-8" method="POST" id="post_form">
        <div class="row">
            <div class="col-md-7">
                <div class="form-group">
                    <label for="excerpt">excerpt</label>
                    <textarea name="excerpt" id="field-excerpt" rows="3" class="form-control"><?= $row->excerpt ?></textarea>
                </div>


                <div class="form-group">
                    <label for="content" class="form-control-label">content</label>
                    <textarea name="content" id="field-content" class="form-control"><?= $row->content ?></textarea>
                </div>

                <div class="form-group">
                    <label for="content_json">content json</label>
                    <textarea name="content_json" id="field-content_json" rows="3" class="form-control"><?= $row->content_json ?></textarea>
                </div>


            </div>
            <div class="col-md-5">
                <div class="form-group row">
                    <div class="col-md-8 offset-md-4">
                        <button class="btn btn-success btn-block" type="submit">
                            Guardar
                        </button>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="post_name" class="col-md-4 col-form-label text-right">Post Name</label>
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
                    <label for="type_id" class="col-md-4 col-form-label text-right">Type</label>
                    <div class="col-md-8">
                        <?= form_dropdown('type_id', $options_type, $row->type_id, 'class="form-control"') ?>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="published_at" class="col-md-4 col-form-label text-right">Fecha publicación</label>
                    <div class="col-md-8">
                        <input
                            type="text"
                            id="field-published_at"
                            name="published_at"
                            required
                            class="form-control"
                            placeholder="Fecha publicación"
                            title="Fecha publicación"
                            value="<?= $row->published_at ?>"
                            >
                    </div>
                </div>

                <?php foreach ( $arr_fields as $field ) { ?>
                    <div class="form-group row">
                        <label for="<?= $field ?>" class="col-md-4 col-form-label text-right"><?= str_replace('_',' ',$field) ?></label>
                        <div class="col-md-8">
                            <input
                                type="text"
                                name="<?= $field ?>"
                                class="form-control"
                                title="<?= $field ?>"
                                value="<?= $row->$field ?>"
                                >
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </form>
</div>