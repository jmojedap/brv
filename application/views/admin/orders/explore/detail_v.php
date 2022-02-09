<!-- Modal -->
<div class="modal fade" id="detail_modal" tabindex="-1" role="dialog" aria-labelledby="detail_modal_label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detail_modal_label">{{ element.order_code }}</h5>
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
                        <td>Nombre comprador</td>
                        <td>
                            {{ element.buyer_name }}
                        </td>
                    </tr>
                    <tr>
                        <td>Estado</td>
                        <td>
                            <i class="fa fa-check-circle text-success" v-if="element.status == 1"></i>
                            <i class="fa fa-exclamation-triangle text-warning" v-if="element.status == 5"></i>
                            <i class="far fa-circle text-muted" v-if="element.status == 10"></i>
                            {{ element.status | status_name  }}
                        </td>
                    </tr>
                    <tr>
                        <td>Valor</td>
                        <td>
                            {{ element.amount | currency  }}
                        </td>
                    </tr>
                    <tr>
                        <td>Confirmado</td>
                        <td>
                            {{ element.confirmed_at }} - {{ element.confirmed_at | ago  }}
                        </td>
                    </tr>
                    <tr>
                        <td>Notas</td>
                        <td>
                            {{ element.notes_admin }}
                        </td>
                    </tr>
                    <tr>
                        <td>Actualizado</td>
                        <td>
                            {{ element.updated_at }} - {{ element.updated_at | ago  }}
                        </td>
                    </tr>
                    <tr>
                        <td>Creado</td>
                        <td>
                            {{ element.created_at }} - {{ element.created_at | ago  }}
                        </td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                    <a class="btn btn-primary w100p" v-bind:href="`<?= base_url("{$controller}/info/") ?>` + element.id">Abrir</a>
                    <button type="button" class="btn btn-secondary w100p" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>