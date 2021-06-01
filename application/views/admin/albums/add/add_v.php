<div id="add_album">
    <form id="album_form" accept-charset="utf-8" @submit.prevent="send_form">
        <div style="max-width: 750px; margin: 0 auto;">
            <?php $this->load->view('albums/add/form_v.php') ?>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="modal_created" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Registro creado</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <i class="fa fa-check"></i>
                        Registro creado
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" v-on:click="go_created">
                            Editar álbum
                        </button>
                        <button type="button" class="btn btn-secondary" v-on:click="clean_form" data-dismiss="modal">
                            <i class="fa fa-plus"></i>
                            Crear otro
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>


<?php
$this->load->view('albums/add/vue_v');