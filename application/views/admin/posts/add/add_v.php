<?php 
    $options_type = $this->Item_model->options('category_id = 33', 'Tipo de post');
?>

<div id="add_post">
    <div class="card center_box_750">
        <div class="card-body">
            <form id="add_form" accept-charset="utf-8" @submit.prevent="send_form">
                <div class="form-group row">
                    <label for="post_name" class="col-md-4 col-form-label text-right">Nombre post</label>
                    <div class="col-md-8">
                        <input
                            id="field-post_name" class="form-control"
                            name="post_name"
                            required autofocus
                            v-model="form_values.post_name">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="type_id" class="col-md-4 col-form-label text-right">Tipo</label>
                    <div class="col-md-8">
                        <?= form_dropdown('type_id', $options_type, '', 'class="form-control" required v-model="form_values.type"') ?>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="offset-md-4 col-md-8 col-sm-12">
                        <button class="btn btn-success w120p" type="submit">
                            Crear
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modal_created" tabindex="-1" role="dialog"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Post creado</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <i class="fa fa-check"></i>
                    Post creado correctamente
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" v-on:click="go_created">
                        Abrir post
                    </button>
                    <button type="button" class="btn btn-secondary" v-on:click="clean_form" data-dismiss="modal">
                        <i class="fa fa-plus"></i>
                        Crear otro
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->load->view($this->views_folder . 'add/vue_v');