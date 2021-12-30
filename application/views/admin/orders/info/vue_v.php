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
    return moment().format('D MMM HH:mm');
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