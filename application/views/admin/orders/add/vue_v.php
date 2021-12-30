<script>
// Variables
//-----------------------------------------------------------------------------
var document_type_names = <?= json_encode($arr_document_types) ?>;
var today = moment()

// Filters
//-----------------------------------------------------------------------------
Vue.filter('currency', function (value) {
    if (!value) return ''
    value = new Intl.NumberFormat().format(value)
    return value
});

Vue.filter('document_type_name', function (value) {
    if (!value) return ''
    value = document_type_names['0' + value]
    return value
})

Vue.filter('ago', function (date) {
    if (!date) return ''
    return moment(date, 'YYYY-MM-DD HH:mm:ss').fromNow()
});

Vue.filter('diff', function (date1, date2) {
    if (!date1 || !date2) return ''
    return moment(date, 'YYYY-MM-DD HH:mm:ss').fromNow()
});

Vue.filter('date_format', function (date) {
    if (!date) return ''
    return moment(date).format('D MMM YYYY');
})

// VueApp
//-----------------------------------------------------------------------------
var add_order_app = new Vue({
    el: '#add_order_app',
    created: function(){
        //this.get_users()
        this.get_products()
    },
    data: {
        step: 1,
        payment: {
            payment_channel: '020',
            bill: '',
            confirmed_at: '<?= date('Y-m-d') ?>',
            notes_admin: '',
            user_expiration_at: ''
        },
        user_filters: {q: ''},
        users: [],
        user: {
            id: 0, display_name: '', document_number: '', document_type: '', 
            expiration_at: '',
        },
        key_user: -1,
        no_users: false,
        product_filters: {
            q: '',
            cat_1: 2110, //Suscripción a entrenamiento
        },
        products: [],
        product: {
            id: 0, name: '', price: 0
        },
        order: {
            id: 0, order_code: ''
        },
        options_product_category: <?= json_encode($options_product_category) ?>,
        options_payment_channel: <?= json_encode($options_payment_channel) ?>,
        loading: false,
    },
    methods: {
        set_step: function(new_step){
            this.step = new_step
        },
        // Usuarios
        //-----------------------------------------------------------------------------
        get_users: function(){
            this.loading = true
            var form_data = new FormData()
            form_data.append('role', 21)
            form_data.append('q', this.user_filters.q)
            axios.post(url_api + 'users/get/1/10', form_data)
            .then(response => {
                this.users = response.data.list
                this.loading = false
                if ( this.users.length == 0 ) this.no_users = true
                if ( this.users.length > 0 ) this.no_users = false
            })
            .catch( function(error) {console.log(error)} )
        },
        set_user: function(key){
            this.key_user = key
            this.user = this.users[key]
            this.step = 2
        },
        // Productos
        //-----------------------------------------------------------------------------
        get_products: function(){
            this.loading = true
            var form_data = new FormData()
            form_data.append('q', this.product_filters.q)
            form_data.append('cat_1', this.product_filters.cat_1)
            axios.post(url_api + 'products/get/1/30', form_data)
            .then(response => {
                this.products = response.data.list
                this.loading = false
            })
            .catch( function(error) {console.log(error)} )
        },
        set_product: function(key){
            this.key_product = key
            this.product = this.products[key]
            this.step = 3
            this.set_new_expiration_at()
        },
        set_new_expiration_at: function(){
            console.log('Fecha Exp')
            var newExpirationDay = today.add(1,'months')
            this.payment.user_expiration_at = newExpirationDay.format('YYYY-MM-DD')
        },
        // General
        //-----------------------------------------------------------------------------
        send_form: function(){
            this.loading = true

            axios.post(url_api + 'orders/create/', this.form_data())
            .then(response => {
                if ( response.data.saved_id > 0 ) {
                    this.step = 4
                    this.order = response.data.order
                    console.log(response.data.order)
                    toastr['success']('Pago guardado')
                } else {
                    toastr['error']('El pago no fue creado')
                    this.loading = false
                }
            })
            .catch( function(error) {console.log(error)} )
        },
        form_data: function(){
            var form_data = new FormData()
            form_data.append('user_id', this.user.id)
            form_data.append('product_id', this.product.id)
            form_data.append('quantity', 1)

            form_data.append('payed', '01')
            form_data.append('payment_channel', this.payment.payment_channel)
            form_data.append('notes_admin', this.payment.notes_admin)

            return form_data
        },
    },
    /*computed: {
        expirationAtDays: function(){
            var newExpirationAt = moment(this.payment.user_expiration_at)
            var expirationAtDays = today.diff(newExpirationAt, 'days')
            return expirationAtDays
        },
    }*/
})
</script>