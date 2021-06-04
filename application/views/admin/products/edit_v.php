<?php $this->load->view('assets/bs4_chosen') ?>

<div id="app_edit">
    <form id="edit_form" accept-charset="utf-8" @submit.prevent="sendForm">
        <input type="hidden" name="id" value="<?= $row->id ?>">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label for="code" class="col-md-4 col-form-label text-right">Referencia</label>
                            <div class="col-md-8">
                                <input
                                    type="text" name="code" required class="form-control"
                                    placeholder="Referencia" title="Referencia"
                                    v-model="form_values.code"
                                    >
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-right">Nombre producto</label>
                            <div class="col-md-8">
                                <input
                                    type="text" name="name" required class="form-control"
                                    placeholder="Nombre producto" title="Nombre producto"
                                    v-model="form_values.name"
                                    >
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="keywords" class="col-md-4 col-form-label text-right">Palabras clave</label>
                            <div class="col-md-8">
                                <input
                                    name="keywords" type="text" class="form-control"
                                    v-model="form_values.keywords"
                                >
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="description" class="col-md-4 col-form-label text-right">Descripción</label>
                            <div class="col-md-8">
                                <textarea
                                    name="description" required class="form-control" placeholder="Descripción" title="Descripción" rows="6"
                                    v-model="form_values.description"
                                    ></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label for="priority" class="col-md-4 col-form-label text-right">Prioridad [ {{ form_values.priority }} ]</label>
                            <div class="col-md-8">
                                <input
                                    name="priority" type="range" min="0" max="1000" class="w100pc"
                                    v-model="form_values.priority"
                                >
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="status" class="col-md-4 col-form-label text-right">Estado</label>
                            <div class="col-md-8">
                                <select name="status" v-model="form_values.status" class="form-control" required>
                                    <option v-for="(option_status, status_key) in options_status" v-bind:value="status_key">{{ option_status }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="cat_1" class="col-md-4 col-form-label text-right">Categoría</label>
                            <div class="col-md-8">
                                <select name="cat_1" v-model="form_values.cat_1" class="form-control" required v-on:change="getCat2">
                                    <option v-for="(option_cat_1, key_cat_1) in options_cat_1" v-bind:value="key_cat_1">{{ option_cat_1 }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="stock" class="col-md-4 col-form-label text-right">Cantidad existencias</label>
                            <div class="col-md-8">
                                <input name="stock" type="number" class="form-control" min="0" required v-model="form_values.stock">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="weight" class="col-md-4 col-form-label text-right">Peso (gramos)</label>
                            <div class="col-md-8">
                                <input
                                    name="weight" type="number" class="form-control"
                                    required min="0"
                                    title="Peso (gramos)" placeholder="Peso (gramos)"
                                    v-model="form_values.weight"
                                >
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="price" class="col-md-4 col-form-label text-right">Precio de venta</label>
                            <div class="col-md-8">
                                <input
                                    name="price" id="field-price" type="number" class="form-control"
                                    required min="1"
                                    v-model="form_values.price" v-on:change="updateDependents"
                                >
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="cost" class="col-md-4 col-form-label text-right">Costo</label>
                            <div class="col-md-8">
                                <input
                                    name="cost" id="field-cost" type="number" class="form-control"
                                    required min="1"
                                    title="" placeholder=""
                                    v-model="form_values.cost"
                                >
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="tax_percent" class="col-md-4 col-form-label text-right">% IVA</label>
                            <div class="col-md-8">
                                <input
                                    name="tax_percent" id="field-tax_percent" type="number" class="form-control"
                                    required min="0" max="50" step="0.01"
                                    title="" placeholder=""
                                    v-model="form_values.tax_percent" v-on:change="updateDependents"
                                >
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="tax" class="col-md-4 col-form-label text-right">Valor IVA</label>
                            <div class="col-md-8">
                                <input
                                    name="tax" id="field-tax" type="number" class="form-control"
                                    required step="0.01"
                                    v-model="form_values.tax" v-on:change="updateDependents"
                                >
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="base_prices" class="col-md-4 col-form-label text-right">Precio base</label>
                            <div class="col-md-8">
                                <input
                                    name="base_price" id="field-base_price" type="text" class="form-control"
                                    required step="0.01"
                                    title="" placeholder=""
                                    v-model="form_values.base_price" v-on:change="updateDependents"
                                >
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="offset-md-4 col-md-8">
                                <button class="btn btn-success w120p" type="submit">Guardar</button>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </form>
</div>

<script>
// Variables
//-----------------------------------------------------------------------------

//Cargar valor en formulario
var form_values = <?= json_encode($row) ?>;
form_values.status = '0' + '<?= $row->status ?>';
form_values.cat_1 = '0' + '<?= $row->cat_1 ?>';

// Vue App
//-----------------------------------------------------------------------------
var appEdit = new Vue({
el: '#app_edit',
    data: {
        form_values: form_values,
        row_id: '<?= $row->id ?>',
        options_status: <?= json_encode($options_status) ?>,
        options_cat_1: <?= json_encode($options_cat_1) ?>,
        options_cat_2: [],
    },
    methods: {
        sendForm: function() {
            axios.post(url_api + 'products/save/', $('#edit_form').serialize())
                .then(response => {
                    if (response.data.saved_id > 0)
                    {
                        toastr['success']('El producto fue actualizado')
                    }
                })
                .catch(function (error) { console.log(error) })
        },
        updateDependents: function(){
            var base_price = this.form_values.price / ( 1 + this.form_values.tax_percent/100)
            var tax = parseFloat(this.form_values.price) - base_price
            this.form_values.tax = tax.toFixed(2)
            this.form_values.base_price = base_price.toFixed(2)
        },
        getCat2: function(){            
            var form_data = new FormData
            form_data.append('condition', 'category_id = 25 AND level = 1 AND parent_id = ' + parseInt(this.form_values.cat_1))
            form_data.append('empty_value', 'Seleccione la categoría 2')
            axios.post(url_api + 'items/get_options/', form_data)
            .then(response => {
                this.options_cat_2 = response.data.options
            })
            .catch( function(error) {console.log(error)} )
        },

    }
});
</script>