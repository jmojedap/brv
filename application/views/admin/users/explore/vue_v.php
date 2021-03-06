
<script>
// Variables
//-----------------------------------------------------------------------------
var role_names = <?= json_encode($arr_roles) ?>;
var document_type_names = <?= json_encode($arr_document_types) ?>;
var commercial_plans = <?= json_encode($arr_commercial_plans) ?>;
var status_icons = {
    "0":'<i class="far fa-circle text-danger" title="Inactivo"></i>',
    "1":'<i class="fa fa-check-circle text-success" title="Activo"></i>'
};

// Filters
//-----------------------------------------------------------------------------

Vue.filter('status_icon', function (value) {
    if (!value) return ''
    value = status_icons[value]
    return value
})

Vue.filter('role_name', function (value) {
    if (!value) return ''
    value = role_names[value]
    return value
})

Vue.filter('commercial_plan_name', function (value) {
    if (!value) return ''
    value = commercial_plans[value]
    return value
})

Vue.filter('document_type_name', function (value) {
    if (!value) return ''
    value = document_type_names['0' + value]
    return value
})

Vue.filter('ago', function (date) {
    if (!date) return ''
    return moment(date, "YYYY-MM-DD HH:mm:ss").fromNow()
})

Vue.filter('expiration', function (date) {
    if (!date) return ''
    return moment(date, "YYYY-MM-DD HH:mm:ss").fromNow()
})

Vue.filter('age', function (date) {
    if (!date) return ''
    return moment().diff(date, 'years',false)
})

// App
//-----------------------------------------------------------------------------

var app_explore = new Vue({
    el: '#app_explore',
    created: function(){
        this.calculate_active_filters()
    },
    data: {
        cf: '<?= $cf ?>',
        controller: '<?= $controller ?>',
        search_num_rows: <?= $search_num_rows ?>,
        num_page: <?= $num_page ?>,
        max_page: <?= $max_page ?>,
        list: <?= json_encode($list) ?>,
        element: [],
        selected: [],
        all_selected: false,
        filters: <?= json_encode($filters) ?>,
        str_filters: '<?= $str_filters ?>',
        display_filters: false,
        loading: false,
        active_filters: false,
        options_role: <?= json_encode($options_role) ?>,
        options_commercial_plan: <?= json_encode($options_commercial_plan) ?>,
        options_expiration: {
            '0':'Sin fecha definida',
            '1':'Vigente',
            '2':'Vencida', 
        },
        today: '<?= date('Y-m-d') ?>',
    },
    methods: {
        get_list: function(e, num_page = 1){
            this.loading = true
            axios.post(url_app + this.controller + '/get/' + num_page, $('#search_form').serialize())
            .then(response => {
                this.num_page = num_page
                this.list = response.data.list
                this.max_page = response.data.max_page
                this.search_num_rows = response.data.search_num_rows
                this.str_filters = response.data.str_filters
                history.pushState(null, null, url_app + this.cf + this.num_page +'/?' + response.data.str_filters)
                this.all_selected = false
                this.selected = []
                this.loading = false

                this.calculate_active_filters()
            })
            .catch(function (error) { console.log(error) })
        },
        select_all: function() {
            if ( this.all_selected )
            { this.selected = this.list.map(function(element){ return element.id }) }
            else
            { this.selected = [] }
        },
        sum_page: function(sum){
            var new_num_page = Pcrn.limit_between(this.num_page + sum, 1, this.max_page)
            this.get_list(null, new_num_page)
        },
        delete_selected: function(){
            var params = new FormData()
            params.append('selected', this.selected)
            
            axios.post(url_app + this.controller + '/delete_selected', params)
            .then(response => {
                this.hide_deleted()
                this.selected = []
                if ( response.data.qty_deleted > 0 )
                {
                    toastr['info']('Registros eliminados: ' + response.data.qty_deleted)
                }
            })
            .catch(function (error) { console.log(error) })
        },
        hide_deleted: function(){
            for ( let index = 0; index < this.selected.length; index++ )
            {
                const element = this.selected[index]
                console.log('ocultando: row_' + element)
                $('#row_' + element).addClass('table-danger')
                $('#row_' + element).hide('slow')
            }
        },
        set_current: function(key){
            this.element = this.list[key]
        },
        toggle_filters: function(){
            this.display_filters = !this.display_filters
            $('#adv_filters').toggle('fast')
        },
        remove_filters: function(){
            this.filters.q = ''
            this.filters.role = ''
            this.filters.fe1 = ''
            this.filters.fe2 = ''
            this.filters.d1 = ''
            this.filters.d2 = ''
            this.display_filters = false
            //$('#adv_filters').hide()
            setTimeout(() => { this.get_list() }, 100)
        },
        calculate_active_filters: function(){
            var calculated_active_filters = false
            if ( this.filters.q ) calculated_active_filters = true
            if ( this.filters.role ) calculated_active_filters = true
            if ( this.filters.fe1 ) calculated_active_filters = true
            if ( this.filters.fe2 ) calculated_active_filters = true
            if ( this.filters.d1 ) calculated_active_filters = true
            if ( this.filters.d2 ) calculated_active_filters = true

            this.active_filters = calculated_active_filters
        },
        // Especiales Brave
        //-----------------------------------------------------------------------------
        expiration_icon: function(date){
            var date_user = new Date(date)
            var date_today = new Date(this.today)

            var expiration_icon = '<i class="far fa-circle text-muted mr-2"></i>'
            if ( date != null ) {
                if ( date_user >= date_today ) {
                    expiration_icon = '<i class="fa fa-check-circle text-success mr-2"></i>'
                } else {
                    expiration_icon = '<i class="fa fa-exclamation-triangle text-warning mr-2"></i>'
                }
            }
            return expiration_icon
        },
    }
})
</script>