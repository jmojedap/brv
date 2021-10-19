<div id="schedule_generator_app">
    <div class="center_box_750">
        <h3 class="text-center">Citas de control nutricional</h3>
        <p class="d-none">{{ form_values.str_hours }}</p>
        <div class="card">
            <div class="card-body">
                <div v-show="result.status == -1">
                    <form accept-charset="utf-8" method="POST" id="schedule_form" @submit.prevent="send_form">
                        <fieldset v-bind:disabled="loading">
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
                            <div class="row">
                                <div class="col-md-8 offset-md-4">
                                    <p>
                                        {{ qty_days() }} días x {{ selected_appointments.length }} citas =
                                        {{ qty_days() * selected_appointments.length }}
                                    </p>
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
    },
    data: {
        form_values: {
            date_start: '<?= date('Y-m-d') ?>',
            date_end: '<?= date('Y-m-d') ?>',
            minute_start: 0,
            minute_end: 0,
            str_hours: '',
        },
        hour_start: 8,
        hour_end: 12,
        duration: 30,
        appointments: [],
        selected_appointments: [],
        loading: false,
        result: {status: -1, message: ''},
    },
    methods: {
        send_form: function(){
            this.loading = true
            var form_data = new FormData()
            form_data.append('date_start', this.form_values.date_start)
            form_data.append('date_end', this.form_values.date_end)
            form_data.append('str_hours', this.form_values.str_hours)
            axios.post(url_api + 'calendar/schedule_nutritional_control/', form_data)
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
            hour1.add(this.hour_start, 'hours');
            hour1.add(this.form_values.minute_start, 'minutes');

            var hour2 = moment(this.form_values.date_start, 'YYYY-MM-DD');
            hour2.add(this.hour_end, 'hours');
            hour2.add(this.form_values.minute_end, 'minutes');
            console.log(hour1.format('h'));

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
                    start: current_hour.format('hh:mm:ss'),
                    end: current_hour.add(this.duration, 'minutes').format('hh:mm:ss'), //Suma la duración para el inicio en el siguiente cislo
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