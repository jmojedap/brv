<!-- Modal -->
<div class="modal fade" id="detail_modal" tabindex="-1" role="dialog" aria-labelledby="detail_modal_label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detail_modal_label">{{ element.display_name }}</h5>
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
                        <td>Nombre</td>
                        <td>
                            {{ element.display_name }}
                        </td>
                    </tr>
                    <tr>
                        <td>Correo electrónico</td>
                        <td>{{ element.email }}</td>
                    </tr>
                    <tr>
                        <td>Edad</td>
                        <td>
                        <div v-if="element.birth_date">
                            {{ element.birth_date | age }} &middot;
                            <small class="text-muted">{{ element.birth_date }}</small>
                        </div>
                        </td>
                    </tr>
                    <tr>
                        <td>Creado</td>
                        <td v-bind:title="element.created_at">{{ element.created_at | ago }}</td>
                    </tr>
                    <tr>
                        <td>Actualizado</td>
                        <td v-bind:title="element.updated_at">{{ element.updated_at | ago }}</td>
                    </tr>
                </table>
                <hr>
                <p>
                    {{ element.admin_notes }}
                </p>
            </div>
            <div class="modal-footer">
                    <a class="btn btn-primary w100p" v-bind:href="`<?= URL_ADMIN . 'users/profile/' ?>` + element.id">Abrir</a>
                    <button type="button" class="btn btn-secondary w100p" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>