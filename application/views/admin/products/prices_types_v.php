<div id="prices_types_app">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <table class="table bg-white">
                    <thead>
                        <th>Tipo</th>
                        <th class="text-center">Precio</th>
                        <th width="90px"></th>
                    </thead>
                    <tbody>
                        <tr v-for="(price_type, key) in prices_types" v-bind:class="{'table-info': form_values.related_1 == '0' + price_type.price_type }">
                            <td>{{ price_type.price_type_name }}</td>
                            <td class="text-center">{{ price_type.price | currency }}</td>
                            <td>
                                <div v-if="price_type.price_type > 10">
                                    <button class="a4" v-on:click="setCurrent(key)">
                                        <i class="fa fa-pencil-alt"></i>
                                    </button>
                                    <button class="a4" v-on:click="setCurrent(key)" data-toggle="modal" data-target="#delete_modal">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <form accept-charset="utf-8" method="POST" id="price_type_form" @submit.prevent="send_form">
                            <fieldset v-bind:disabled="loading">
                                <input type="hidden" name="product_id" value="<?= $row->id ?>">
                                <input type="hidden" name="type_id" value="189">

                                <div class="form-group row">
                                    <label for="related_1" class="col-md-4 col-form-label text-right">Tipo de precio</label>
                                    <div class="col-md-8">
                                        <select name="related_1" v-model="form_values.related_1" class="form-control" required>
                                            <option v-for="(option_related_1, key_related_1) in options_price_type" v-bind:value="key_related_1">{{ option_related_1 }}</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="decimal_1" class="col-md-4 col-form-label text-right">Precio</label>
                                    <div class="col-md-8">
                                        <input
                                            name="decimal_1" type="number" class="form-control"
                                            required min="1000" title="Precio"
                                            v-model="form_values.price"
                                        >
                                    </div>
                                </div>
                                
                                <div class="form-group row">
                                    <div class="col-md-8 offset-md-4">
                                        <button class="btn btn-primary w120p" type="submit">Guardar</button>
                                    </div>
                                </div>
                            <fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        
    </div>    

    <?php $this->load->view('common/modal_single_delete_v') ?>
</div>

<script>
// Filteres
//-----------------------------------------------------------------------------
Vue.filter('currency', function (value) {
    if (!value) return ''
    value = new Intl.NumberFormat().format(value)
    return value
});

// VueApp
//-----------------------------------------------------------------------------
var prices_types_app = new Vue({
    el: '#prices_types_app',
    created: function(){
        this.get_list()
    },
    data: {
        product_id: <?= $row->id ?>,
        prices_types: [],
        form_values: {meta_id: 0, related_1: '', decimal_1: 0},
        options_price_type: <?= json_encode($options_price_type) ?>,
        loading: false,
    },
    methods: {
        setCurrent: function(key){
            this.form_values.meta_id = this.prices_types[key].meta_id
            this.form_values.related_1 = '0' + this.prices_types[key].price_type
            this.form_values.price = this.prices_types[key].price
        },
        clearForm: function(){
            this.form_values = {meta_id: 0, related_1: '', decimal_1: 0}
        },
        get_list: function(){
            axios.get(url_api + 'products/get_prices_types/' + this.product_id)
            .then(response => {
                this.prices_types = response.data.prices_types
            })
            .catch(function(error) { console.log(error) })
        },
        send_form: function(){
            this.loading = true
            var form_data = new FormData(document.getElementById('price_type_form'))
            axios.post(url_api + 'products/save_meta/', form_data)
            .then(response => {
                if ( response.data.saved_id > 0 ) {
                    toastr['success']('Guardado')
                    this.get_list()
                    this.clearForm()
                }
                this.loading = false
            })
            .catch( function(error) {console.log(error)} )
        },
        delete_element: function(){
            var meta_id = this.form_values.meta_id
            axios.get(url_api + 'products/delete_meta/' + this.product_id + '/' + meta_id)
            .then(response => {
                if ( response.data.qty_deleted > 0 ) {
                    toastr['info']('Tipo de precio eliminado')
                    this.get_list()
                    this.clearForm()
                }
            })
            .catch(function(error) { console.log(error) })
        },
    }
})
</script>