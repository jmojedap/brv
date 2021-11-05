<div id="app_edit">
    <div class="card center_box_750">
        <div class="card-body">
            <form id="edit_form" accept-charset="utf-8" @submit.prevent="validate_send">
                <fieldset v-bind:disabled="loading">
                    <input type="hidden" name="id" value="<?= $row->id ?>">

                    <div class="form-group row">
                        <label for="contract" class="col-md-4 col-form-label text-right">Contrato</label>
                        <div class="col-md-8">
                            <input
                                name="contract" type="text" class="form-control"
                                required title="Contrato"
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
            options_role: <?= json_encode($options_role) ?>,
            options_city: <?= json_encode($options_city) ?>,
            options_gender: <?= json_encode($options_gender) ?>,
            options_document_type: <?= json_encode($options_document_type) ?>,
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