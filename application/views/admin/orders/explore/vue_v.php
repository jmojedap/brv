
<script>
// Variables
//-----------------------------------------------------------------------------
var status_name = <?= json_encode($arr_status); ?>;
var arr_payment_channel = <?= json_encode($arr_payment_channel) ?>;

// Filters
//-----------------------------------------------------------------------------

Vue.filter('status_name', function (value) {
    if (!value) return '-';
    new_value = status_name[value];
    return new_value;
});

Vue.filter('currency', function (value) {
    if (!value) return '';
    value = '$ ' + new Intl.NumberFormat().format(value);
    return value;
});

Vue.filter('payment_channel_name', function (value) {
    if (!value) return '';
    value = arr_payment_channel[value];
    return value;
});

Vue.filter('ago', function (date) {
    if (!date) return ''
    return moment(date, "YYYY-MM-DD HH:mm:ss").fromNow();
});

// VueApp
//-----------------------------------------------------------------------------
var app_explore = new Vue({
    el: '#app_explore',
    data: {
        cf: '<?= $cf; ?>',
        controller: '<?= $controller; ?>',
        search_num_rows: <?= $search_num_rows ?>,
        num_page: 1,
        max_page: <?= $max_page; ?>,
        list: <?= json_encode($list) ?>,
        element: [],
        selected: [],
        all_selected: false,
        filters: <?= json_encode($filters) ?>,
        str_filters: '<?= $str_filters ?>',
        display_filters: false,
        active_filters: false,
        options_status: <?= json_encode($options_status) ?>,
        options_payment_channel: <?= json_encode($options_payment_channel) ?>,
        loading: false
    },
    methods: {
        get_list: function(){
            axios.post(url_app + this.controller + '/get/' + this.num_page, $('#search_form').serialize())
            .then(response => {
                this.list = response.data.list
                this.max_page = response.data.max_page;
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
            this.num_page = Pcrn.limit_between(this.num_page + sum, 1, this.max_page)
            this.get_list()
        },
        delete_selected: function(){
            var params = new FormData();
            params.append('selected', this.selected);
            
            axios.post(url_app + this.controller + '/delete_selected', params)
            .then(response => {
                this.hide_deleted();
                this.selected = [];
                if ( response.data.status == 1 )
                {
                    toastr_cl = 'info';
                    toastr_text = 'Registros eliminados';
                    toastr[toastr_cl](toastr_text);
                }
            })
            .catch(function (error) { console.log(error) })
        },
        hide_deleted: function(){
            for (let index = 0; index < this.selected.length; index++) {
                const element = this.selected[index];
                console.log('ocultando: row_' + element);
                $('#row_' + element).addClass('table-danger');
                $('#row_' + element).hide('slow');
            }
        },
        set_current: function(key){
            this.element = this.list[key];
        },
        toggle_filters: function(){
            this.display_filters = !this.display_filters
            $('#adv_filters').toggle('fast')
        },
        remove_filters: function(){
            this.filters.q = ''
            this.filters.status = ''
            this.filters.fe3 = ''
            this.filters.d1 = ''
            this.filters.d2 = ''
            this.display_filters = false
            setTimeout(() => { this.get_list() }, 100)
        },
        calculate_active_filters: function(){
            var calculated_active_filters = false
            if ( this.filters.q ) calculated_active_filters = true
            if ( this.filters.status ) calculated_active_filters = true
            if ( this.filters.fe3 ) calculated_active_filters = true
            if ( this.filters.d1 ) calculated_active_filters = true
            if ( this.filters.d2 ) calculated_active_filters = true

            this.active_filters = calculated_active_filters
        },
    }
});
</script>