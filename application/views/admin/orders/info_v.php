<div id="order_app">
    <div class="mb-2">
        <a class="btn btn-light" href="<?= base_url("orders/test_email/{$row->id}") ?>" target="_blank">
            <i class="fa fa-envelope"></i>
        </a>
    </div>
    <div class="row mb-2">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-user"></i> Cliente
                </div>
                <table class="table bg-white">
                    <tbody>
                        <tr>
                            <td class="td-title">Referencia</td>
                            <td>{{ order.order_code }}</td>
                        </tr>
                        <tr>
                            <td class="td-title">A nombre de</td>
                            <td>{{ order.buyer_name }}</td>
                        </tr>
                        <tr>
                            <td class="td-title">Correo electrónico</td>
                            <td>{{ order.email }}</td>
                        </tr>
                        <tr>
                            <td class="td-title">{{ order.document_type | document_type_name }}</td>
                            <td>
                                {{ order.document_number }}
                                
                            </td>
                        </tr>
                        <tr>
                            <td class="td-title">Total</td>
                            <td>
                                <strong class="text-primary">{{ order.amount | currency }}</strong> = 
                                ( {{ order.total_products | currency }} + {{ order.total_extras | currency }} )
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-map-marker"></i> Envío
                </div>
                <table class="table bg-white">
                    <tbody>
                        <tr>
                            <td class="td-title">Ciudad</td>
                            <td>{{ order.city }}</td>
                        </tr>
                        <tr>
                            <td class="td-title">Dirección</td>
                            <td>{{ order.address }}</td>
                        </tr>
                        <tr>
                            <td class="td-title">Teléfono</td>
                            <td>{{ order.phone_number }}</td>
                        </tr>
                        <tr>
                            <td class="td-title">Notas cliente</td>
                            <td>{{ order.notes }}</td>
                        </tr>
                        <tr>
                            <td class="td-title">Método envío</td>
                            <td>
                                {{ order.shipping_method_id | shipping_method_name }}
                                &middot; {{ order.total_weight }} kg
                            </td>
                        </tr>
                        <tr>
                            <td class="td-title">Estado envío</td>
                            <td>
                                {{ order.shipping_status | shipping_status_name }}
                                &middot; <span class="text-muted">Guía No.</span> <strong>{{ order.shipping_code }}</strong>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    Gestión
                </div>
                <table class="table bg-white">
                    <tbody>
                        <tr>
                            <td class="td-title">Estado de compra</td>
                            <td>{{ order.status | order_status_name }}</td>
                        </tr>
                        <tr>
                            <td class="td-title">Actualizado</td>
                            <td v-bind:title="order.updated_at">
                                {{ order.updated_at | date_format }} &middot; {{ order.updated_at | ago }}
                            </td>
                        </tr>
                        <tr>
                            <td class="td-title">Actualizado por</td>
                            <td>
                                <a href="<?= base_url("users/profile/{$row->updater_id}") ?>">
                                    <?= $this->App_model->name_user($row->updater_id); ?> &middot;
                                    <?= $row->updater_id; ?>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td class="td-title">Factura</td>
                            <td>{{ order.bill }}</td>
                        </tr>
                        <tr>
                            <td class="td-title">Anotación</td>
                            <td>{{ order.notes_admin }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <h3>Productos &middot; {{ products.length }}</h3>

    <table class="table bg-white">
        <thead>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>{{ order.total_products | currency }}</th>
        </thead>
        <tbody>
            <tr v-for="product in products">
                <td>
                    <a v-bind:href="`<?= base_url("products/info/") ?>` + product.product_id">
                        {{ product.name }}
                    </a>
                </td>
                <td>
                    {{ product.quantity }}
                </td>
                <td>{{ product.price | currency }}</td>
            </tr>
        </tbody>
    </table>

    <h3>Extras &middot; {{ extras.length }}</h3>

    <table class="table bg-white">
        <thead>
            <th>Extra</th>
            <th>Cantidad</th>
            <th>{{ order.total_extras | currency }}</th>
        </thead>
        <tbody>
            <tr v-for="extra in extras">
                <td>
                    {{ extra.extra_name }}
                </td>
                <td>
                    {{ extra.quantity }}
                </td>
                <td>{{ extra.price | currency }}</td>
            </tr>
        </tbody>
    </table>
    

</div>


<script>
// Variables
//-----------------------------------------------------------------------------
var order_status = <?= json_encode($arr_order_status) ?>;
var shipping_methods = <?= json_encode($arr_shipping_methods) ?>;
var shipping_status = <?= json_encode($arr_shipping_status) ?>;
var document_types = <?= json_encode($arr_document_types) ?>;

// Filters
//-----------------------------------------------------------------------------
Vue.filter('currency', function (value) {
    if (!value) return ''
    value = '' + new Intl.NumberFormat().format(value)
    return value
})

Vue.filter('order_status_name', function (value) {
    if (!value) return ''
    value = order_status[value]
    return value
})

Vue.filter('shipping_method_name', function (value) {
    if (!value) return ''
    value = shipping_methods[value]
    return value
})

Vue.filter('shipping_status_name', function (value) {
    if (!value) return ''
    value = shipping_status[value]
    return value
})

Vue.filter('document_type_name', function (value) {
    if (!value) return ''
    value = document_types[value]
    return value
})

Vue.filter('ago', function (date) {
    if (!date) return ''
    return moment(date, "YYYY-MM-DD HH:mm:ss").fromNow()
})

Vue.filter('date_format', function (date) {
    if (!date) return ''
    return moment().format('D MMM h:mm');
})

// VueApp
//-----------------------------------------------------------------------------
var order_app = new Vue({
    el: '#order_app',
    created: function(){
        //this.get_list()
    },
    data: {
        order: <?= json_encode($row) ?>,
        products: <?= json_encode($products->result()) ?>,
        extras: <?= json_encode($extras->result()) ?>,
        loading: false,
    },
    methods: {
        
    }
})
</script>