<!-- Modal -->
<div class="modal fade" id="detail_modal" tabindex="-1" role="dialog" aria-labelledby="detail_modal_label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detail_modal_label">Evento: {{ element.id }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-borderless table-sm">
                    <tr>
                        <td class="text-right" width="25%">ID</td>
                        <td>{{ element.id }}</td>
                    </tr>
                    <tr>
                        <td class="text-right">Usuario</td>
                        <td>
                            {{ element.user_id }} &middot;
                            {{ element.user_display_name }}
                        </td>
                    </tr>
                    <tr>
                        <td class="text-right">Dirección IP</td>
                        <td>
                            {{ element.ip_address }}
                        </td>
                    </tr>
                    <tr>
                        <td class="text-right">Otros datos</td>
                        <td>
                            <span class="text-muted">start: </span> {{ element.start }} &middot;
                            <span class="text-muted">end: </span> {{ element.end }} &middot;
                            <span class="text-muted">seconds: </span> {{ element.seconds }} &middot;
                            <span class="text-muted">status: </span> {{ element.status }} &middot;
                            <span class="text-muted">type_id: </span> {{ element.type_id }} &middot;
                        </td>
                    </tr>
                    <tr>
                        <td class="text-right">Creado</td>
                        <td>
                            {{ element.created_at }} &middot; {{ element.created_at | ago }}
                        </td>
                    </tr>
                </table>
                <p>
                    {{ element.content }}
                </p>
            </div>
            <div class="modal-footer">
                <a class="btn btn-primary w100p" v-bind:href="`<?= URL_ADMIN . 'events/info/' ?>` + element.id">Abrir</a>
                <button type="button" class="btn btn-secondary w100p" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>