<div id="app_explore">
    <div class="row">
        <div class="col-md-5">
            <?php $this->load->view($views_folder . 'search_form_v'); ?>
        </div>

        <div class="col-md-3">
            <a href="<?= URL_ADMIN . "{$controller}/export/?{$str_filters}" ?>" class="btn btn-light only-lg" title="Exportar registros encontrados a Excel">
                <i class="fa fa-download"></i>
            </a>
            <button class="btn btn-light"
                title="Eliminar elementos seleccionados"
                data-toggle="modal"
                data-target="#modal_delete"
                v-show="selected.length > 0"
                >
                <i class="fa fa-trash"></i>
            </button>
        </div>
        
        <div class="col-md-4 d-flex justify-content-between">
            <a class="btn text-muted">
                <strong class="text-primary">{{ search_num_rows }}</strong> resultados &middot;
                <strong class="text-primary">Pág. {{ num_page }}</strong>/{{ max_page }}
            </a>
            <div>
                <?php $this->load->view('common/vue_pagination_v'); ?>
            </div>
        </div>
    </div>

    <div id="elements_table">
        <?php $this->load->view($views_folder . 'list_v'); ?>
        <?php $this->load->view($views_folder . 'detail_v'); ?>
    </div>

    <?php $this->load->view('common/modal_delete_v'); ?>
</div>

<?php $this->load->view($views_folder . 'vue_v') ?>