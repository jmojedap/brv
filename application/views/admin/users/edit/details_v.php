<?php
    $partner = $this->Db_model->row_id('users', $row->partner_id);
    $is_partner_of = $this->Db_model->exists('users', "partner_id = {$row->id}");
    $partner_of = null;
    if ( $is_partner_of > 0 ) {
        $partner_of = $this->Db_model->row_id('users', $is_partner_of);
    }
?>

<div id="app_edit">
    <div class="card center_box_750">
        <div class="card-body">
            <form id="edit_form" accept-charset="utf-8" @submit.prevent="validate_send">
                <fieldset v-bind:disabled="loading">
                    <input type="hidden" name="id" value="<?= $row->id ?>">

                    <div class="form-group row">
                        <label for="commercial_plan" class="col-md-4 col-form-label text-right">Plan suscripción</label>
                        <div class="col-md-8">
                            <select name="commercial_plan" v-model="form_values.commercial_plan" class="form-control">
                                <option v-for="(option_commercial_plan, key_commercial_plan) in options_commercial_plan" v-bind:value="key_commercial_plan">{{ option_commercial_plan }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="expiration_at" class="col-md-4 col-form-label text-right">Suscripción hasta</label>
                        <div class="col-md-8">
                            <input
                                name="expiration_at" class="form-control" type="date"
                                v-model="form_values.expiration_at"
                                >
                                <small class="form-text text-muted">Fecha hasta la cual el usuario puede reservar entrenamientos</small>
                        </div>
                    </div>

                    <?php if ( is_null($partner_of) ) : ?>
                        <div class="form-group row">
                            <label for="partner_id" class="col-md-4 col-form-label text-right">Beneficiario asociado</label>
                            <div class="col-md-8">
                                <select name="partner_id" v-model="form_values.partner_id" class="form-control">
                                    <option v-for="(option_partner_id, key_partner_id) in options_partner_id" v-bind:value="key_partner_id">{{ option_partner_id }}</option>
                                </select>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="form-group row">
                            <label for="partner_id" class="col-md-4 col-form-label text-right">Beneficiario de</label>
                            <div class="col-md-8">
                                <a href="<?= URL_ADMIN . "users/edit/{$partner_of->id}/details" ?>">
                                    <?= $partner_of->first_name . ' ' . $partner_of->last_name ?>
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <hr>

                    <div class="form-group row">
                        <label for="contract" class="col-md-4 col-form-label text-right">Contrato</label>
                        <div class="col-md-8">
                            <input
                                name="contract" type="text" class="form-control"
                                title="Contrato"
                                v-model="form_values.contract"
                            >
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="eps" class="col-md-4 col-form-label text-right">EPS</label>
                        <div class="col-md-8">
                            <input
                                name="eps" type="text" class="form-control" title="EPS"
                                v-model="form_values.eps"
                            >
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="shirt_size" class="col-md-4 col-form-label text-right">Talla camiseta</label>
                        <div class="col-md-8">
                            <input
                                name="shirt_size" type="text" class="form-control" title="Talla camiseta"
                                v-model="form_values.shirt_size"
                            >
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="job" class="col-md-4 col-form-label text-right">Ocupación</label>
                        <div class="col-md-8">
                            <input
                                name="job" type="text" class="form-control" title="Ocupación"
                                v-model="form_values.job"
                            >
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="offset-md-4 col-md-8">
                            <button class="btn btn-primary w120p" type="submit">
                                Guardar
                            </button>
                        </div>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>
</div>

<script>
// Variables
//-----------------------------------------------------------------------------
    var form_values = {
        contract: '<?= $row->contract ?>',
        eps: '<?= $row->eps ?>',
        shirt_size: '<?= $row->shirt_size ?>',
        job: '<?= $row->job ?>',
        expiration_at: '<?= $row->expiration_at ?>',
        commercial_plan: '0<?= $row->commercial_plan ?>',
        partner_id: '0<?= $row->partner_id ?>',
    };

// Vue App
//-----------------------------------------------------------------------------
    new Vue({
    el: '#app_edit',
        data: {
            form_values: form_values,
            row_id: '<?= $row->id ?>',
            validation: {
                document_number_unique: -1,
                username_unique: -1,
                email_unique: -1
            },
            options_commercial_plan: <?= json_encode($options_commercial_plan) ?>,
            options_partner_id: <?= json_encode($options_partner_id) ?>,
            loading: false
        },
        methods: {
            validate_form: function() {
                axios.post(url_app + 'users/validate/' + this.row_id, $('#edit_form').serialize())
                .then(response => {
                    this.validation = response.data.validation
                })
                .catch(function (error) { console.log(error) })
            },
            validate_send: function () {
                axios.post(url_app + 'users/validate/' + this.row_id, $('#edit_form').serialize())
                .then(response => {
                    if (response.data.status == 1) {
                        this.send_form()
                    } else {
                        toastr['error']('Hay casillas incompletas o incorrectas')
                        this.loading = false
                    }
                })
                .catch(function (error) { console.log(error) })
            },
            send_form: function() {
                this.loading = true
                axios.post(url_app + 'users/save/', $('#edit_form').serialize())
                .then(response => {
                    if (response.data.saved_id > 0) toastr['success']('Guardado')
                    this.loading = false
                })
                .catch(function (error) { console.log(error) })
            },
        }
    });
</script>