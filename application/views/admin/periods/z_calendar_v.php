<?php
    $links = array();
    for ($i=1; $i <= 31; $i++) { 
        $links[$i] = "{$this->url_controller}calendar/{$year}/{$month}/{$i}";
    }

    $years = range(date('Y') - 2, date('Y') + 5);
    $months = array(
        '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril', '05' => 'Mayo', '06' => 'Junio',
        '07' => 'Julio', '08' => 'Agosto', '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre',
    );
?>

<div id="calendar_app">
    <div class="row">
        <div class="col-md-5">
            <div class="d-flex">
                <select name="year" class="form-control mr-1" v-model="year" v-on:change="set_month">
                    <option v-for="option_year in options_years" v-bind:value="option_year">{{ option_year }}</option>
                </select>
                <select name="month" v-model="month" class="form-control" v-on:change="set_month">
                    <option v-for="option_month in options_months" v-bind:value="option_month.month">{{ option_month.month_name }}</option>
                </select>
            </div>
            <br>
            <?= $this->calendar->generate($year, $month, $links); ?>
        </div>
        <div class="col-md-7">
            columna2
        </div>
    </div>
</div>

<script>
var calendar_app = new Vue({
    el: '#calendar_app',
    created: function(){
        //this.get_list()
    },
    data: {
        //form_values: row,
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
    },
    methods: {
        set_month: function(){
            window.location = url_app + 'periods/calendar/' + this.year + '/' + this.month
        },
        set_day: function(){
            console.log('hola')
        },
    }
})
</script>