<!-- Modal -->
<div class="modal fade" id="detail_modal" tabindex="-1" role="dialog" aria-labelledby="detail_modal_label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detail_modal_label">Lugar: {{ element.place_name }}</h5>
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
                        <td class="text-right">Lugar</td>
                        <td>
                            {{ element.place_name }} &middot;
                        </td>
                    </tr>
                    <tr>
                        <td class="text-right">Ubicación</td>
                        <td>
                            {{ element.country }} &middot; {{ element.region }}
                        </td>
                    </tr>
                    <tr>
                        <td class="text-right">Otros datos</td>
                        <td>
                            <span class="text-muted">Palabras clave: </span> {{ element.keywords }} &middot;
                            <span class="text-muted">Código: </span> {{ element.cod }} &middot;
                            <span class="text-muted">Código oficial (Dane): </span> {{ element.cod_official }} &middot;
                            <span class="text-muted">Población: </span> {{ element.population }} ({{ element.year_population }}) &middot;
                        </td>
                    </tr>
                    <tr>
                        <td class="text-right">Zona</td>
                        <td>
                            {{ element.zone }} ({{ element.zone_id }})
                        </td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <a class="btn btn-primary w100p" v-bind:href="`<?= URL_ADMIN . 'places/info/' ?>` + element.id">Abrir</a>
                <button type="button" class="btn btn-secondary w100p" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>