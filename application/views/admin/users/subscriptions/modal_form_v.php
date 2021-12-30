<!-- Modal -->
<form accept-charset="utf-8" method="POST" id="subscription_form" @submit.prevent="send_form">
    <fieldset v-bind:disabled="loading">
        <div class="modal fade" id="modal_form" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal_form">Suscripción</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <label for="start" class="col-md-4 col-form-label text-right">Desde</label>
                            <div class="col-md-8">
                                <input
                                    name="start" type="date" class="form-control"
                                    required
                                    title="Desde" placeholder="Desde"
                                    v-model="form_values.start" v-on:change="set_end_date"
                                >
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="end" class="col-md-4 col-form-label text-right">Hasta</label>
                            <div class="col-md-8">
                                <input
                                    name="end" type="date" class="form-control"
                                    required
                                    title="Hasta" placeholder="Hasta"
                                    v-model="form_values.end"
                                >
                                <small class="form-text text-muted">{{ dates_difference }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light w120p" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary w120p">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
    <fieldset>
</form>