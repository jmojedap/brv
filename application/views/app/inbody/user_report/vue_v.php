<script>
// Var
//-----------------------------------------------------------------------------
    var str_ik = '&ik=<?= $user->id . '-' . $user->userkey ?>';

// Filtros
//-----------------------------------------------------------------------------
Vue.filter('ago', function (date) {
    if (!date) return ''
    return moment(date, 'YYYY-MM-DD HH:mm:ss').fromNow()
});

Vue.filter('date_format', function (date) {
    if (!date) return ''
    return moment(date, 'YYYY-MM-DD HH:mm:ss').format('MMM DD / YYYY')
});

// VueApp
//-----------------------------------------------------------------------------
var user_report = new Vue({
    el: '#user_report',
    created: function(){
        this.get_list()
    },
    data: {
        user: {
            id: <?= $user->id ?>, display_name: '<?= $user->display_name ?>'
        },
        list: [],
        inbody_id: 0,
        inbody: {},
        loading: false,
    },
    methods: {
        get_list: function(){
            axios.get(url_eapi + 'inbody/get/?u=' + this.user.id + str_ik)
            .then(response => {
                this.list = response.data.list
                //this.get_inbody()
            })
            .catch(function(error) { console.log(error) })
        },
        unset_inbody: function(){
            this.inbody_id = 0;
        },
        set_inbody: function(inbody_id){
            this.inbody_id = inbody_id
            this.get_inbody()
        },
        get_inbody: function () {
            // Valor por defecto
            if ( this.inbody_id == 0 && this.list.length > 0 ) {
                this.inbody_id = this.list[0].id
            }
            this.loading = true
            axios.get(url_eapi + 'inbody/get_info/' + this.inbody_id + '/' + this.user.id)
            .then(response => {
                this.inbody = response.data
                this.loading = false
            })
            .catch(function(error) { console.log(error) })
        },
        is_lower: function(value, lower_limit){
            if ( parseFloat(value) < parseFloat(lower_limit) ) {
                return true
            } else {
                return false
            }
        },
        is_upper: function(value, upper_limit){
            if ( parseFloat(value) > parseFloat(upper_limit) ) {
                return true
            } else {
                return false
            }
        },
        in_range: function(value, lower_limit, upper_limit){
            if ( parseFloat(value) >= parseFloat(lower_limit) && parseFloat(value) <= parseFloat(upper_limit) ) {
                return true
            } else {
                return false
            }
        },
        bmi_class: function(bmi_value){
            var bmi_class = ''
            if ( bmi_value < 18.5 ) { bmi_class = 'bmi_1' }
            if ( bmi_value >= 18.5 && bmi_value <= 24.9 ) { bmi_class = 'bmi_2' }
            if ( bmi_value >= 25.0 && bmi_value <= 29.9 ) { bmi_class = 'bmi_3' }
            if ( bmi_value >= 30.0 && bmi_value <= 34.9 ) { bmi_class = 'bmi_4' }
            if ( bmi_value >= 35.0 && bmi_value <= 39.9 ) { bmi_class = 'bmi_5' }
            if ( bmi_value >= 40 ) { bmi_class = 'bmi_6' }
            return bmi_class
        },
        bmi_to_percent: function(bmi_value){
            var bmi_percent = 0;
            if ( bmi_value < 18.5 ) { bmi_percent = 25 }
            if ( bmi_value >= 18.5 && bmi_value <= 24.9 ) { bmi_percent = 50 }
            if ( bmi_value >= 25.0 && bmi_value <= 29.9 ) { bmi_percent = 70 }
            if ( bmi_value >= 30.0 && bmi_value <= 34.9 ) { bmi_percent = 80 }
            if ( bmi_value >= 35.0 && bmi_value <= 39.9 ) { bmi_percent = 90 }
            if ( bmi_value >= 40 ) { bmi_percent = 100 }
            return bmi_percent;
        }
    }
})
</script>