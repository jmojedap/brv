<div id="pictures_app">
    <div class="row mb-2">
        <div class="col-md-4">
            <a href="<?= URL_ADMIN . "girls/album/{$row->related_1}/{$row->id}" ?>" class="btn btn-light w120p">
                Abrir
            </a>
            <button class="btn btn-light w120p" v-on:click="import_pictures">
                Importar
            </button>
        </div>
        <div class="col-md-8">
            <?php $this->load->view('common/upload_file_form_v') ?>
        </div>
    </div>


    <div class="grid-general">
        <div class="card" v-for="(picture, key) in pictures">
            <img v-bind:src="folder_galleries + picture.file_name" class="card-img" alt="Miniatura">
            <div class="card-body">
                <button class="btn btn-light" v-on:click="set_private_picture(key)" v-show="picture.status == 1">
                    <i class="fa fa-lock"></i>
                </button>
                <button class="btn btn-warning" v-on:click="set_current(key)" data-toggle="modal" data-target="#delete_modal">
                    <i class="fa fa-trash"></i>
                </button>
            </div>
        </div>
    </div>

    <?php $this->load->view('common/modal_single_delete_v') ?>
</div>

<?php $this->load->view('albums/pictures/vue_v') ?>