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
        //this.get_products()
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
        partner: {id: 0, last_name: '(NO ASIGNADO)'},
        quantity: 1,
        pay_partner_value: false,
        key_user: -1,
        no_users: false,
        product_filters: {
            q: '',
            cat_1: 2110, //Suscripción a entrenamiento
        },
        products: <?= json_encode($products->result()) ?>,
        product: {
            id: 0, code: '', name: '', price: 0
        },
        order: {
            id: 0, order_code: '', period_id: ''
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
            if ( this.user.commercial_plan > 0 ) {    
                this.set_product(this.user.commercial_plan)
                this.check_partner()
                this.step = 3
            } else {
                this.step = 2
            }
        },
        //Verificar si tiene beneficiario y obtener sus datos.
        check_partner: function(){
            if ( this.user.partner_id > 0 ) {
                axios.get(url_api + 'users/get_info/' + this.user.partner_id)
                .then(response => {
                    this.partner = response.data.user
                    console.log(this.partner)
                })
                .catch(function(error) { console.log(error) })
            }
        },
        // Productos
        //-----------------------------------------------------------------------------
        get_products: function(){
            this.loading = true
            var form_data = new FormData()
            form_data.append('q', this.product_filters.q)
            form_data.append('cat_1', this.product_filters.cat_1)
            form_data.append('sf', 'subscriptions')     //Select format
            axios.post(url_api + 'products/get/1/30', form_data)
            .then(response => {
                this.products = response.data.list
                this.loading = false
            })
            .catch( function(error) {console.log(error)} )
        },
        set_product: function(product_id){
            this.product = this.products.find(element => element.id == product_id)
            this.step = 3
            //this.set_new_expiration_at()
        },
        // Establecer la cantidad de productos, dependiendo si va a pagar el valor
        // del beneficiario
        set_quantity: function(){
            if ( this.pay_partner_value ) {
                this.quantity = 2
            } else {
                this.quantity = 1
            }
        },
        /*set_new_expiration_at: function(){
            console.log('Fecha Exp')
            var newExpirationDay = today.add(1,'months')
            this.payment.user_expiration_at = newExpirationDay.format('YYYY-MM-DD')
        },*/
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
            form_data.append('quantity', this.quantity)

            form_data.append('payed', '01')
            form_data.append('payment_channel', this.payment.payment_channel)
            form_data.append('notes_admin', this.payment.notes_admin)

            return form_data
        },
    },
})
</script>