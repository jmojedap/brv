<?php
    $years = range(date('Y') - 1, date('Y') + 2);
?>

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
        /*border-left: 2px solid #999999;*/
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
        <div class="col-md-5">
            <div class="d-flex mb-2 justify-content-between">
                <button class="btn btn-light w75p" v-on:click="sum_month(parseInt(month) - 1)">
                    <i class="fa fa-chevron-left"></i>
                </button>
                <div class="d-flex">
                    <select name="year" class="form-control mr-1" v-model="year" v-on:change="set_month">
                        <option v-for="option_year in options_years" v-bind:value="option_year">{{ option_year }}</option>
                    </select>
                    <select name="month" v-model="month" class="form-control" v-on:change="set_month">
                        <option v-for="option_month in options_months" v-bind:value="option_month.month">{{ option_month.month_name }}</option>
                    </select>
                </div>
                <button class="btn btn-light w75p" v-on:click="sum_month(parseInt(month) + 1)">
                    <i class="fa fa-chevron-right"></i>
                </button>
            </div>
            <table class="table bg-white text-center">
                <thead>
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
                        <td v-for="day in week.days" v-bind:class="day_class(day)" v-on:click="set_day(day)"
                        >
                            {{ day.day }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-md-7">
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
        options_years: <?= json_encode($years) ?>,
        options_months: [
            { month: '01', month_name: 'Enero' },
            { month: '02', month_name: 'Febrero' },
            { month: '03', month_name: 'Marzo' },
            { month: '04', month_name: 'Abril' },
            { month: '05', month_name: 'Mayo' },
            { month: '06', month_name: 'Junio' },
            { month: '07', month_name: 'Julio' },
            { month: '08', month_name: 'Agosto' },
            { month: '09', month_name: 'Septiembre' },
            { month: '10', month_name: 'Octubre' },
            { month: '11', month_name: 'Noviembre' },
            { month: '12', month_name: 'Diciembre' },
        ],
        year: <?= $year ?>,
        month: <?= $month ?>,
        day_start: '<?= $day_start ?>'
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
            axios.get(url_api + 'calendar/toggle_business_day/' + this.active_day.id)
            .then(response => {
                if ( response.data.saved_id > 0 ) {
                    this.active_day.qty_business_day = response.data.qty_bussines_day
                }
            })
            .catch(function(error) { console.log(error) })
        },
        set_month: function(){
            window.location = url_app + 'calendar/calendar/' + this.year + '/' + this.month
        },
        sum_month: function(month){
            this.month = month
            if ( month < 1 ) {
                this.year = this.year - 1
                this.month = 12
            }
            if ( month > 12 ) {
                this.year = this.year + 1
                this.month = 1
            }
            console.log(this.year, this.month)
            this.set_month()
        }
    },
    /*computed: {
        day_class: function(day){
            return 'wd_' + day.week_day 
        }
    }*/
})
</script>