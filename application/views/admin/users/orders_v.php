<div id="user_orders_app">
    <div class="center_box_750">
        <table class="table bg-white">
            <thead>
                <th>Cod. venta</th>
                <th>Canal</th>
                <th width="10px">Pagado</th>
                <th>Valor</th>
                <th></th>
            </thead>
            <tbody>
                <tr v-for="(order, key) in list">
                    <td>
                            <a v-bind:href="`<?= URL_ADMIN . "orders/info/" ?>` + order.id" class="">
                            {{ order.order_code }}
                        </a>
                    </td>
                    <td>
                        <div v-if="order.payment_channel > 0">
                            <i class="fa fa-circle" v-bind:class="`channel_` + order.payment_channel"></i>
                            {{ order.payment_channel | payment_channel_name }}
                        </div>
                    </td>
                    
                    <td class="text-center">
                        <i class="fa fa-check-circle text-success" v-if="order.status == 1"></i>
                        <i class="far fa-circle text-muted" v-if="order.status == 5"></i>
                        <i class="far fa-circle text-muted" v-if="order.status == 10"></i>
                    </td>
                    <td>
                        {{ order.amount | currency }}
                    </td>
                    <td>
                        <b>A</b> <span v-bind:title="order.updated_at">{{ order.updated_at | ago }}</span>
                        <br>
                        <b>C</b> <span v-bind:title="order.created_at"> {{ order.created_at | ago }}</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
// Variables
//-----------------------------------------------------------------------------
var arr_payment_channel = <?= json_encode($arr_payment_channel) ?>;

// Filters
//-----------------------------------------------------------------------------
Vue.filter('currency', function (value) {
    if (!value) return ''
    value = new Intl.NumberFormat().format(value)
    return value
});

Vue.filter('payment_channel_name', function (value) {
    if (!value) return '';
    value = arr_payment_channel[value];
    return value;
});

// VueApp
//-----------------------------------------------------------------------------
var user_orders_app = new Vue({
    el: '#user_orders_app',
    created: function(){
        this.get_list()
    },
    data: {
        //form_values: row,

        user_id: <?= $row->id ?>,
        list: [],
        loading: false,
    },
    methods: {
        get_list: function(){
            axios.get(url_api + 'orders/get/?&u=' + this.user_id)
            .then(response => {
                this.list = response.data.list
            })
            .catch(function(error) { console.log(error) })
        },
    }
})
</script>