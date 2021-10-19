<script>
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

// Libraries
//-----------------------------------------------------------------------------
Vue.use(HighchartsVue.default)
Highcharts.theme = hc_brave_theme;
Highcharts.setOptions(Highcharts.theme);

// VueApp
//-----------------------------------------------------------------------------
var inbody_user_app = new Vue({
    el: '#inbody_user_app',
    created: function(){
        this.get_list()
    },
    data: {
        user: {
            id: <?= $row->id ?>, display_name: '<?= $row->display_name ?>'
        },
        list: [],
        inbody_id: <?= $inbody_id ?>,
        inbody: {
            id: <?= $inbody_id ?>,
        },
        loading: false,
        chartOptions: {
            chart: {
                zoomType: 'x'
            },
            title: {
                text: 'Índice de Masa Corporal'
            },
            subtitle: {
                text: '<?= $row->display_name ?>'
            },
            xAxis: {
                type: 'datetime'
            },
            yAxis: {
                title: {
                    text: 'IMC'
                }
            },
            legend: {
                enabled: true
            },
            plotOptions: {
                line: {
                    marker: {
                        radius: 5
                    },
                    lineWidth: 2,
                    states: {
                        hover: {
                            lineWidth: 3
                        }
                    },
                    threshold: null
                }
            },
            series: [
                { type: 'line', name: 'IMC', data: [] },
                { type: 'line', name: 'Límite inferior', data: [] },
                { type: 'line', name: 'Límite superior', data: [] }
            ]
        }
    },
    methods: {
        get_list: function(){
            axios.get(url_api + 'inbody/get/?u=' + this.user.id)
            .then(response => {
                this.list = response.data.list
                this.set_chart_data()
                this.get_inbody()
            })
            .catch(function(error) { console.log(error) })
        },
        set_chart_data: function(){
            //Limpiar
            this.chartOptions.series[0].data = []
            this.chartOptions.series[1].data = []
            this.chartOptions.series[2].data = []

            //Agregar
            this.list.forEach(inbody => {
                var momentValue = moment(inbody.test_date, 'YYYY-MM-DD HH:mm:ss').valueOf()
                this.chartOptions.series[0].data.push(
                    [momentValue, parseFloat(inbody.bmi_body_mass_index)]
                )
                this.chartOptions.series[1].data.push(
                    [momentValue, parseFloat(inbody.lower_limit_bmi_normal_range)]
                )
                this.chartOptions.series[2].data.push(
                    [momentValue, parseFloat(inbody.upper_limit_bmi_normal_range)]
                )
            })
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
            axios.get(url_api + 'inbody/get_info/' + this.inbody_id + '/' + this.user.id)
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
    },
    computed: {
        //Computed Chart Options
        compChartOptions() { 
            return this.chartOptions
        },
    }
})
</script>