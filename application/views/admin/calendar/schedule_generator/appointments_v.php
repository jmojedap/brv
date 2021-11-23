<div id="schedule_generator_app">
    <div class="center_box_750">
        <p class="d-none">{{ form_values.str_hours }}</p>
        <div class="card">
            <div class="card-body">
                <div v-show="result.status == -1">
                    <form accept-charset="utf-8" method="POST" id="schedule_form" @submit.prevent="send_form">
                        <fieldset v-bind:disabled="loading">
                            <div class="form-group row">
                                <label for="type_id" class="col-md-4 col-form-label text-right">
                                    <i class="fa fa-circle" v-bind:class="`text_evt` + parseInt(type_id)"></i>
                                    Tipo citas
                                </label>
                                <div class="col-md-8">
                                    <select name="type_id" v-model="type_id" class="form-control" required v-on:change="check_date_end">
                                        <option v-for="(option_type, key_type) in options_type" v-bind:value="key_type">{{ option_type }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="date_start" class="col-md-4 col-form-label text-right">Desde &middot; Hasta</label>
                                <div class="col-md-4">
                                    <input
                                        name="date_start" type="date" class="form-control"
                                        required
                                        v-on:change="check_date_end"
                                        v-model="form_values.date_start"
                                    >
                                </div>
                                <div class="col-md-4">
                                    <input
                                        name="date_end" type="date" class="form-control"
                                        required
                                        v-on:change="check_date_end"
                                        v-model="form_values.date_end"
                                    >
                                </div>
                            </div>
    
                            <hr>
    
                            <div class="form-group row">
                                <label for="hour_star" class="col-md-4 col-form-label text-right">Hora inicio</label>
                                <div class="col-md-4">
                                    <input
                                        name="hour_start" type="number" class="form-control" required
                                        v-on:change="check_hour_end" min=0 max="22"
                                        v-model="hour_start"
                                    >
                                </div>
                                <div class="col-md-4">
                                    <input
                                        name="minute_start" type="number" class="form-control" required
                                        v-on:change="check_hour_end" min="0" max="59" step="15"
                                        v-model="form_values.minute_start"
                                    >
                                </div>
                            </div>
    
                            <div class="form-group row">
                                <label for="hour_star" class="col-md-4 col-form-label text-right">Hora fin</label>
                                <div class="col-md-4">
                                    <input
                                        name="hour_end" type="number" class="form-control" required
                                        v-on:change="check_hour_end" min=0 max="23"
                                        v-model="hour_end"
                                    >
                                </div>
                                <div class="col-md-4">
                                    <input
                                        name="minute_end" type="number" class="form-control" required
                                        v-on:change="check_hour_end" min="0" max="59" step="15"
                                        v-model="form_values.minute_end"
                                    >
                                </div>
                            </div>
    
                            <div class="form-group row">
                                <label for="hour_star" class="col-md-4 col-form-label text-right">Duración cita</label>
                                <div class="col-md-4">
                                    <input
                                        name="duration" type="number" class="form-control" required min=10 max="60" step="10"
                                        v-model="duration" v-on:change="generate_appointments"
                                    >
                                </div>
                            </div>
    
                            <hr>
                            <div class="form-group row">
                                <div class="col-md-8 offset-md-4">
                                    <div v-for="(appointment, ka) in appointments">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value=""
                                                v-bind:id="`appointment_` + ka"
                                                v-model="appointments[ka].selected"
                                                v-on:change="set_selected_appointments"
                                            >
                                            <label class="form-check-label" v-bind:for="`appointment_` + ka">
                                                <span class="text-muted mr-3">[{{ ka + 1 }}]</span>
                                                {{ appointment.title }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
    
                            <hr>
                            <div class="form-group row">
                                <div class="col-md-4 text-right">
                                    Citas a programar
                                </div>
                                <div class="col-md-8">
                                    {{ qty_days() }} días x {{ selected_appointments.length }} citas =
                                    <strong class="text-primary">
                                        {{ qty_days() * selected_appointments.length }}
                                    </strong>
                                </div>
                            </div>
                            <div class="form-group row" v-show="qty_current_events > 0">
                                <div class="col-md-4 text-right">
                                    <i class="fa fa-info-circle text-warning"></i>
                                    Información
                                </div>
                                <div class="col-md-8">
                                    En las fechas seleccionadas ya están programadas
                                    <strong class="text-primary">{{ qty_current_events }}</strong> citas de <strong class="text-primary">{{ options_type[type_id] }}</strong>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <div class="col-md-8 offset-md-4">
                                    <button class="btn btn-primary w120p" type="submit">Programar</button>
                                </div>
                            </div>
                        <fieldset>
                    </form>
                </div>
                <!-- Si hay respuesta de la programación -->
                <div v-show="result.status >= 0">
                    <div class="alert alert-success" v-show="result.status == 1">
                        <i class="fa fa-check mr-2"></i>
                        {{ result.message }}
                    </div>
                    <div class="alert alert-warning" v-show="result.status == 0">
                        <i class="fa fa-exclamation-triangle"></i>
                        {{ result.message }}
                    </div>
                    <button class="btn btn-light w120p" type="button" v-on:click="reset_result">Volver</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// VueApp
//-----------------------------------------------------------------------------
var schedule_generator_app = new Vue({
    el: '#schedule_generator_app',
    created: function(){
        this.generate_appointments()
        this.get_qty_current_events()
    },
    data: {
        form_values: {
            date_start: '<?= $date_start ?>',
            date_end: '<?= $date_start ?>',
            minute_start: 0,
            minute_end: 0,
            str_hours: '',
        },
        options_type: <?= json_encode($options_type) ?>,
        type_id: '0221',
        hour_start: 8,
        hour_end: 12,
        duration: 30,
        appointments: [],
        selected_appointments: [],
        loading: false,
        result: {status: -1, message: ''},
        qty_current_events: 0,
    },
    methods: {
        get_qty_current_events: function(){
            this.loading = true
            var form_data = new FormData()
            form_data.append('type', parseInt(this.type_id))
            form_data.append('d1', this.form_values.date_start)
            form_data.append('d2', this.form_values.date_end)

            axios.post(url_api + 'events/qty_events/', form_data)
            .then(response => {
                this.qty_current_events = response.data.qty_events
                this.loading = false
            })
            .catch( function(error) {console.log(error)} )
        },
        send_form: function(){
            this.loading = true
            form_data = this.get_form_data();
            axios.post(url_api + 'calendar/schedule_appointments/', form_data)
            .then(response => {
                this.result = response.data
                if ( response.data.status == 1 ) {
                    toastr['success'](response.data.message)
                } else {
                    toastr['warning'](response.data.message)
                }
                this.loading = false
            })
            .catch( function(error) {console.log(error)} )
        },
        get_form_data: function(){
            var form_data = new FormData()
            form_data.append('type_id', this.type_id)
            form_data.append('date_start', this.form_values.date_start)
            form_data.append('date_end', this.form_values.date_end)
            form_data.append('str_hours', this.form_values.str_hours)
            return form_data
        },
        qty_days: function(){
            var date1 = moment(this.form_values.date_start, 'YYYY-MM-DD')
            var date2 = moment(this.form_values.date_end, 'YYYY-MM-DD')
            
            return moment.duration(date2.diff(date1)).asDays() + 1
        },
        set_selected_appointments: function(){
            this.selected_appointments = this.appointments.filter(appointment => appointment.selected == true)
            this.form_values.str_hours = JSON.stringify(this.selected_appointments)
        },
        check_date_end: function(){
            var date1 = moment(this.form_values.date_start, 'YYYY-MM-DD')
            var date2 = moment(this.form_values.date_end, 'YYYY-MM-DD')
            
            //Si fecha 2 es anterior
            if ( date2 < date1 ) {
                this.form_values.date_end = this.form_values.date_start
            }
            this.generate_appointments()
            this.get_qty_current_events()
        },
        check_hour_end: function(){
            //Si hora 2 es anterior
            if ( this.hour_end < this.hour_start ) {
                this.hour_end = this.hour_start + 1
            }
            this.generate_appointments()
        },
        generate_appointments: function(){
            this.appointments = []
            var hour1 = moment(this.form_values.date_start, 'YYYY-MM-DD');
            console.log('sumando ', this.hour_start)
            hour1.add(this.hour_start, 'hours');
            hour1.add(this.form_values.minute_start, 'minutes');

            var hour2 = moment(this.form_values.date_start, 'YYYY-MM-DD');
            hour2.add(this.hour_end, 'hours');
            hour2.add(this.form_values.minute_end, 'minutes');
            console.log(hour1.format('YYYY-MM-DD HH:mm:ss'));

            this.text = hour1.format('MMMM D YYYY, h:mm:ss a')
            this.text += ' --- '
            this.text += hour2.format('MMMM D YYYY, h:mm:ss a')
            this.text += ' --- '
            this.text += hour2.diff(hour1, 'minutes')

            var total_minutes = hour2.diff(hour1, 'minutes')
            var qty_appointments = Math.floor(total_minutes / this.duration)

            console.log('total minutos: ', total_minutes)
            console.log('qty_appointments: ', qty_appointments)
            
            var current_hour = hour1
            for (let index = 0; index < qty_appointments; index++) {
                var appointment = {
                    selected: true, 
                    title: hour1.format('hh:mm a'),
                    start: current_hour.format('HH:mm:ss'),
                    end: current_hour.add(this.duration, 'minutes').format('HH:mm:ss'), //Suma la duración para el inicio en el siguiente cislo
                }
                this.appointments.push(appointment)
            }

            this.set_selected_appointments()
        },
        reset_result: function(){
            this.result = {status: -1, message: ''}
        },
    }
})
</script>