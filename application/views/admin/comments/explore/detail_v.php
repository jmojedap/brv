<!-- Modal -->
<div class="modal fade" id="detail_modal" tabindex="-1" role="dialog" aria-labelledby="detail_modal_label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detail_modal_label">Comentario {{ element.id }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-borderless table-sm">
                    <tr>
                        <td>ID</td>
                        <td>{{ element.id }}</td>
                    </tr>
                    <tr>
                        <td>Tabla</td>
                        <td>
                            {{ element.table_id }}
                        </td>
                    </tr>
                    <tr>
                        <td>Creado</td>
                        <td>{{ element.created_at }}</td>
                    </tr>
                </table>
                <p>
                    {{ element.comment_text }}
                </p>
            </div>
            <div class="modal-footer">
                    <a class="btn btn-primary w100p" v-bind:href="`<?= URL_ADMIN . 'comments/info/' ?>` + element.id">Abrir</a>
                    <button type="button" class="btn btn-secondary w100p" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>