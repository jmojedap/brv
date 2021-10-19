<div id="add_place">
    <div class="center_box_750">
        <div class="card">
            <div class="card-body">
                <form accept-charset="utf-8" method="POST" id="place_form" @submit.prevent="send_form">
                    <div class="form-group row">
                        <label for="type_id" class="col-md-4 col-form-label text-right">Tipo</label>
                        <div class="col-md-8">
                            <select name="type_id" v-model="form_values.type_id" class="form-control" required>
                                <option v-for="(option_type, key_type) in options_type" v-bind:value="key_type">{{ option_type }}</option>
                            </select>
                        </div>
                    </div>

                    <!-- UBICACIÓN ASCENDENCIA -->
                    <div class="form-group row">
                        <label for="country_id" class="col-md-4 col-form-label text-right">País * </label>
                        <div class="col-md-8">
                            <select name="country_id" v-model="form_values.country_id" class="form-control" required v-on:change="get_regions">
                                <option v-for="(option_country, key_country) in options_country" v-bind:value="key_country">{{ option_country }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="region_id" class="col-md-4 col-form-label text-right">Departamento/Provincia</label>
                        <div class="col-md-8">
                            <select name="region_id" v-model="form_values.region_id" class="form-control">
                                <option v-for="(option_region, key_region) in options_region" v-bind:value="key_region">{{ option_region }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="place_name" class="col-md-4 col-form-label text-right"><strong>Nombre *</strong></label>
                        <div class="col-md-8">
                            <input name="place_name" type="text" class="form-control" required v-model="form_values.place_name">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="full_name" class="col-md-4 col-form-label text-right">Nombre completo</label>
                        <div class="col-md-8">
                            <input name="full_name" type="text" class="form-control" v-model="form_values.full_name">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="keywords" class="col-md-4 col-form-label text-right">Palabras clave</label>
                        <div class="col-md-8">
                            <input name="keywords" type="text" class="form-control" v-model="form_values.keywords">
                        </div>
                    </div>

                    <!-- CÓDIGOS -->
                    <div class="form-group row">
                        <label for="cod" class="col-md-4 col-form-label text-right">Código</label>
                        <div class="col-md-8">
                            <input name="cod" type="text" class="form-control" v-model="form_values.cod">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="cod_full" class="col-md-4 col-form-label text-right">Código completo</label>
                        <div class="col-md-8">
                            <input name="cod_full" type="text" class="form-control" v-model="form_values.cod_full">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="cod_official" class="col-md-4 col-form-label text-right">Código oficial (DANE)</label>
                        <div class="col-md-8">
                            <input name="cod_official" type="text" class="form-control" v-model="form_values.cod_official">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="population" class="col-md-4 col-form-label text-right">Población / Año</label>
                        <div class="col-md-4">
                            <input name="population" type="number" class="form-control" required v-model="form_values.population">
                        </div>
                        <div class="col-md-4">
                            <input name="population_year" type="number" class="form-control" required v-model="form_values.population_year">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-8 offset-md-4">
                            <button class="btn btn-primary w120p" type="submit">
                                Crear
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php $this->load->view('common/modal_created_v') ?>
</div>

<script>
var add_place = new Vue({
    el: '#add_place',
    created: function(){
        //this.get_list()
    },
    data: {
        row_id: 0,
        form_values: {
            type_id: '04',
            place_name: '',
            full_name: '',
            keywords: '',
            cod: '',
            cod_full: '',
            cod_official: '',
            country_id: '051',
            region_id: '0285',
            population: 0,
            population_year: '<?= date('Y') ?>',
        },
        options_type: <?= json_encode($options_type) ?>,
        options_country: <?= json_encode($options_country) ?>,
        options_region: <?= json_encode($options_region) ?>,
    },
    methods: {
        send_form: function(){
            axios.post(url_api + 'places/save/', $('#place_form').serialize())
            .then(response => {
                console.log(response.data)
                if ( response.data.saved_id > 0 )
                {
                    this.row_id = response.data.saved_id
                    this.clean_form()
                    $('#modal_created').modal()
                }
            }).catch(function(error) {console.log(error)})  
        },
        clean_form: function() {
            this.form_values.place_name = ''
        },
        go_created: function() {
            window.location = url_app + 'places/info/' + this.row_id;
        },
        get_regions: function(){
            var form_data = new FormData
            form_data.append('type', 3)
            form_data.append('fe1', this.form_values.country_id)
            axios.post(url_api + 'places/get_options', form_data)
            .then(response => {
                this.options_region = response.data
                this.form_values.region_id = ''
            }).catch(function(error) {console.log(error)})
        },
    }
})
</script>