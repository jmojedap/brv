<div id="edit_app">
    <div class="card" style="max-width: 800px; margin: 0 auto;">
        <div class="card-body">
            <form id="edit_form" accept-charset="utf-8" @submit.prevent="validate_send">
                

                <div class="form-group row">
                    <label for="display_name" class="col-md-4 col-form-label text-right">Nombre y Apellidos</label>
                    <div class="col-md-8">
                        <input
                            name="display_name" class="form-control"
                            placeholder="Tu nombre"
                            required autofocus
                            v-model="form_values.display_name"
                            >
                    </div>
                </div>

                <div class="form-group row" id="form-group_document_number">
                    <label for="document_number" class="col-md-4 col-form-label text-right">No. Documento</label>
                    <div class="col-md-4">
                        <input
                            id="field-document_number"
                            name="document_number"
                            class="form-control"
                            v-bind:class="{ 'is-invalid': validation.document_number_unique == 0, 'is-valid': validation.document_number_unique == 1 }"
                            placeholder="Número de documento"
                            title="Solo números, sin puntos, debe tener al menos 5 dígitos"
                            pattern=".{5,}[0-9]"
                            v-model="form_values.document_number"
                            v-on:change="validate_form"
                            >
                        <span class="invalid-feedback">
                            El número de documento escrito ya fue registrado para otro usuario
                        </span>
                    </div>
                    <div class="col-md-4">
                        <select name="document_type" v-model="form_values.document_type" class="form-control" required>
                            <option v-for="(option_document_type, key_document_type) in options_document_type" v-bind:value="key_document_type">{{ option_document_type }}</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="city_id" class="col-md-4 col-form-label text-right">Ciudad residencia</label>
                    <div class="col-md-8">
                        <select name="city_id" v-model="form_values.city_id" class="form-control" required>
                            <option v-for="(option_city_id, key_city_id) in options_city_id" v-bind:value="key_city_id">{{ option_city_id }}</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="birth_date" class="col-md-4 col-form-label text-right">Fecha de nacimiento</label>
                    <div class="col-md-8">
                        <input
                            id="field-birth_date"
                            name="birth_date"
                            class="form-control bs_datepicker"
                            v-model="form_values.birth_date"
                            type="date"
                            >
                    </div>
                </div>

                <div class="form-group row">
                    <label for="gender" class="col-md-4 col-form-label text-right">Sexo</label>
                    <div class="col-md-8">
                        <select name="gender" v-model="form_values.gender" class="form-control" required>
                            <option v-for="(option_gender, key_gender) in options_gender" v-bind:value="key_gender">{{ option_gender }}</option>
                        </select>
                    </div>
                </div>


                <div class="form-group row">
                    <label for="celular" class="col-md-4 col-form-label text-right">No. celular</label>
                    <div class="col-md-8">
                        <input
                            id="field-phone_number"
                            name="phone_number"
                            class="form-control"
                            placeholder="Número celular"
                            title="Número celular"
                            v-model="form_values.phone_number"
                            >
                    </div>
                </div>

                <div class="form-group row">
                    <label for="privacy" class="col-md-4 col-form-label text-right">Privacidad de la cuenta</label>
                    <div class="col-md-8">
                        <select name="privacy" v-model="form_values.privacy" class="form-control" required>
                            <option v-for="(option_privacy, key_privacy) in options_privacy" v-bind:value="key_privacy">{{ option_privacy }}</option>
                        </select>
                        <small class="text-muted">Si tu cuenta es pública cualquier usuario podrá seguirte</small>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="offset-md-4 col-md-8">
                        <button class="btn btn-main w120p" type="submit">
                            Guardar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
var form_values = {
    display_name: '<?= $row->display_name ?>',
    document_number: '<?= $row->document_number ?>',
    document_type: '0<?= $row->document_type ?>',
    city_id: '0<?= $row->city_id ?>',
    birth_date: '<?= $row->birth_date ?>',
    gender: '0<?= $row->gender ?>',
    privacy: '0<?= $row->privacy ?>',
    phone_number: '<?= $row->phone_number ?>',
};

// VueApp
//-----------------------------------------------------------------------------
var edit_app = new Vue({
el: '#edit_app',
    data: {
        form_values: form_values,
        row_id: '<?= $row->id ?>',
        validation: {
            document_number_is_unique: true
        },
        options_city_id: <?= json_encode($options_city_id) ?>,
        options_document_type: <?= json_encode($options_document_type) ?>,
        options_gender: <?= json_encode($options_gender) ?>,
        options_privacy: <?= json_encode($options_privacy) ?>,
    },
    methods: {
        validate_form: function() {
            axios.post(url_api + 'accounts/validate_form/', $('#edit_form').serialize())
            .then(response => {
                this.validation = response.data.validation;
            })
            .catch(function (error) { console.log(error) })
        },
        validate_send: function () {
            axios.post(url_api + 'accounts/validate_form/' + this.row_id, $('#edit_form').serialize())
            .then(response => {
                if (response.data.status == 1) {
                this.send_form();
                } else {
                toastr['error']('Revise las casillas en rojo');
                }
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        send_form: function() {
            axios.post(url_api + 'accounts/update/', $('#edit_form').serialize())
                .then(response => {
                    console.log('status: ' + response.data.message);
                    if (response.data.status == 1)
                    {
                    toastr['success']('Datos actualizados');
                    }
                })
                .catch(function (error) {
                    console.log(error);
            });
        }
    }
});
</script>