<div class="modal" tabindex="-1" role="dialog" id="modal_delete">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Eliminar registros</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>¿Confirma que desea eliminar los elementos seleccionados?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" v-on:click="delete_selected" data-dismiss="modal" id="btn-delete_selected">
                    Eliminar
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>