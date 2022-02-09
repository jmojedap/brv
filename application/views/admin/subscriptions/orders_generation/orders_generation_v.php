<div id="orders_generation_app">
    <div class="row">
        <div class="col-md-4">
            <form accept-charset="utf-8" method="POST" id="search_form" @submit.prevent="get_list">
                <input type="hidden" name="role" value="21">
                <input type="hidden" name="fe3" value="1">
                <fieldset v-bind:disabled="loading">
                    <div class="form-group row">
                        <label for="q" class="col-md-4 col-form-label text-right">Buscar</label>
                        <div class="col-md-8">
                            <input
                                name="q" type="text" class="form-control" title="Buscar" placeholder="Buscar"
                                v-model="users_filters.q"
                            >
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="d1" class="col-md-4 col-form-label text-right">Vencimiento desde</label>
                        <div class="col-md-4">
                            <input
                                name="d1" type="date" class="form-control" title="Vencimiento desde"
                                v-model="users_filters.d1"
                            >
                        </div>
                        <div class="col-md-4">
                            <input
                                name="d2" type="date" class="form-control" title="Vencimiento desde"
                                v-model="users_filters.d2"
                            >
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <div class="col-md-8 offset-md-4">
                            <button class="btn btn-primary w120p" type="submit">Filtrar</button>
                        </div>
                    </div>
                <fieldset>
            </form>
        </div>
        <div class="col-md-8">
            <table class="table bg-white">
                <thead>
                    <th width="10px"></th>
                    <th width="10px">No.</th>
                    <th>Usuario</th>
                    <th>Plan</th>
                    <th>Precio</th>
                    <th>Vencimiento</th>
                </thead>
                <tbody>
                    <tr v-for="(element, key) in users">
                        <td>
                            <input type="checkbox" v-model="element.selected">
                        </td>
                        <td class="text-center">{{ key + 1 }}</td>
                        <td>{{ element.first_name }} {{ element.last_name }}</td>
                        <td>
                            <a v-bind:href="`<?= URL_ADMIN . "products/info/" ?>` + element.product.id" class="">
                                {{ element.product.code }}
                            </a>
                            <br>
                            {{ element.product.name }}
                        </td>
                        <td>${{ element.product.price | currency }}</td>
                        <td>{{ element.expiration_at }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Filtros
//-----------------------------------------------------------------------------
Vue.filter('currency', function (value) {
    if (!value) return ''
    value = new Intl.NumberFormat().format(value)
    return value
});
// VueApp
//-----------------------------------------------------------------------------
var orders_generation_app = new Vue({
    el: '#orders_generation_app',
    created: function(){
        this.get_list()
    },
    data: {
        products: <?= json_encode($products->result()) ?>,
        users: [],
        users_filters: <?= json_encode($filters) ?>,
        loading: false,
    },
    methods: {
        get_list: function(){
            this.loading = true
            var form_data = new FormData(document.getElementById('search_form'))
            axios.post(url_api + 'users/get/1/1000', form_data)
            .then(response => {
                //this.users = response.data.list
                this.set_users_list(response.data.list)
                this.loading = false
            })
            .catch( function(error) {console.log(error)} )
        },
        set_users_list: function(users_list){
            users_list.forEach(element => {
                element.selected = true
                element.product = this.products.find(product => product.id == element.commercial_plan)
                this.users.push(element)
            });
        },
    }
})
</script>