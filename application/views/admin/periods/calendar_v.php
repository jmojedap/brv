<style>
    .day {
        cursor: pointer;
    }

    .day:hover {
        background-color: #03a9f4;
        color: white;
    }

    .first_month_day {
        border-left: 1px solid #e4e7ea;
    }


    .holyday{
        background-color: #ffecb3;
    }

    .wd_6 {
        background-color: #fff8e1;
    }

    .wd_7{
        background-color: #ffecb3;
        border-left: 2px solid #999999;
    }

    .day.active {
        font-weight: bold;
        background-color: #03a9f4;
        color: white;
    }

    .today {
        background-color: #e1f5fe;
        font-weight: bold;
    }
</style>

<div id="calendar_app">
    <div class="row">
        <div class="col-md-6">
            <table class="table bg-white text-center">
                <thead>
                    <th>Año</th>
                    <th>Sem</th>
                    <th>Mes</th>
                    <th class="wd_7">Do</th>
                    <th>Lu</th>
                    <th>Ma</th>
                    <th>Mi</th>
                    <th>Ju</th>
                    <th>Vi</th>
                    <th class="wd_6">Sa</th>
                </thead>
                <tbody>
                    <tr v-for="week in weeks">
                        <td>{{ week.year }}</td>
                        <td>{{ week.week_number }}</td>
                        <td>{{ week.first_day | month_name }}</td>
                        <td v-for="day in week.days" v-bind:class="day_class(day)" v-on:click="set_day(day)"
                        >
                            {{ day.day }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-md-6">
            <div v-show="active_day.id > 0">
                <h3>{{ active_day.start | date_format }}</h3>
                <h3 class="text-muted">{{ active_day.start | ago }}</h4>
                <button class="btn btn-success" v-on:click="toggle_business_day">
                    Marcar como festivo
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Filters
//-----------------------------------------------------------------------------
Vue.filter('month_name', function (date) {
    if (!date) return ''
    return moment(date).format('MMM')
});

Vue.filter('date_format', function (date) {
    if (!date) return ''
    return moment(date).format('dddd, D [de] MMMM / YYYY')
});

Vue.filter('ago', function (date) {
    if (!date) return ''
    return moment(date, 'YYYY-MM-DD HH:mm:ss').fromNow()
});

// VueApp
//-----------------------------------------------------------------------------

var calendar_app = new Vue({
    el: '#calendar_app',
    created: function(){
        //this.get_list()
    },
    data: {
        weeks: <?= json_encode($weeks) ?>,
        active_day: { id: 0 },
        loading: false,
    },
    methods: {
        set_day: function(day){
            this.active_day = day
        },
        day_class: function(day){
            var day_class = 'day'
            if ( day.id == this.active_day.id ) day_class += ' active'
            if ( day.day == 1 ) day_class += ' first_month_day'
            if ( day.qty_business_days == 0 ) day_class += ' holyday'
            if ( day.start == '<?= date('Y-m-d') ?>' ) day_class += ' today'
            day_class += ' wd_' + day.week_day
            return day_class
        },
        toggle_business_day: function(){
            axios.get(url_api + 'periods/toggle_business_day/' + this.active_day.id)
            .then(response => {
                if ( response.data.saved_id > 0 ) {
                    this.active_day.qty_business_day = response.data.qty_bussines_day
                }
            })
            .catch(function(error) { console.log(error) })
        },
    },
    /*computed: {
        day_class: function(day){
            return 'wd_' + day.week_day 
        }
    }*/
})
</script>