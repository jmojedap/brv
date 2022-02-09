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

Vue.filter('ago', function (date) {
    if (!date) return ''
    return moment(date, 'YYYY-MM-DD HH:mm:ss').fromNow()
});

Vue.filter('date_format', function (date) {
    if (!date) return ''
    return moment(date).format('D MMM YYYY')
});

// VueApp
//-----------------------------------------------------------------------------
var user_subscriptions_app = new Vue({
    el: '#user_subscriptions_app',
    created: function(){
        this.get_list()
    },
    data: {
        form_values: {
            start: '', end: '', content: ''
        },
        user_id: <?= $row->id ?>,
        last_date: '',
        list: [],
        current_key: -1,
        loading: false,
    },
    methods: {
        get_list: function(){
            this.loading = true
            axios.get(url_api + 'subscriptions/get_user_subscriptions/' + this.user_id)
            .then(response => {
                this.list = response.data.list
                this.loading = false
            })
            .catch( function(error) {console.log(error)} )
        },
        set_current: function(key){
            this.current_key = key
        },
        send_form: function(){
            this.loading = true
            var form_data = new FormData(document.getElementById('subscription_form'))
            form_data.append('user_id', this.user_id)
            axios.post(url_api + 'subscriptions/save/', form_data)
            .then(response => {
                if ( response.data.saved_id > 0 ) {
                    this.get_list()
                    toastr['success']('Guardado')
                    this.clear_form()
                    $('#modal_form').modal('hide')
                }
                this.loading = false
            })
            .catch( function(error) {console.log(error)} )
        },
        set_end_date: function(){
            
            var end_date = moment(this.form_values.start)
            end_date = end_date.add(1, 'M')
            end_date = end_date.subtract(1,'days')
            
            this.form_values.end = end_date.format('YYYY-MM-DD')
        },
        delete_element: function(){
            var event_id = this.list[this.current_key].id
            this.loading = true
            axios.get(url_api + 'subscriptions/delete/' + event_id + '/' + this.user_id)
            .then(response => {
                if ( response.data.qty_deleted > 0 ) {
                    toastr['info']('Suscripción eliminada')
                    this.get_list()
                }
                this.loading = false
            })
            .catch( function(error) {console.log(error)} )
        },
        clear_form: function(){
            this.form_values = {start: '', end: '', content: ''}
        },
    },
    computed: {
        dates_difference: function(){
            var dates_difference = ''
            if ( this.form_values.start != '' && this.form_values.end != '' ) {
                start_date = moment(this.form_values.start)
                end_date = moment(this.form_values.end)
                dates_difference = parseInt(end_date.diff(start_date, 'days') + 1) + ' días'
            }
            return dates_difference
        },
    }
})
</script>