<?php
    $hours = array();
    foreach ($query_hours->result() as $row_hour) {
        $hour['cod'] = $row_hour->cod;
        $hour['title'] = $row_hour->item_name . ' ' . $row_hour->abbreviation;
        $hour['group'] = $row_hour->item_group;
        $hour['selected'] = true;

        $hours[] = $hour;
    }
?>

<div id="schedule_generator_app">
    <div class="center_box_750">
        <div class="card">
            <div class="card-body">
                <form accept-charset="utf-8" method="POST" id="programacion_form" @submit.prevent="send_form">
                    <input type="hidden" name="str_hours" v-model="str_hours">
                    <fieldset v-bind:disabled="loading">
                        <div class="form-group row">
                            <label for="date_start" class="col-md-4 col-form-label text-right">Fecha</label>
                            <div class="col-md-8">
                                <input
                                    name="date_start" type="date" class="form-control"
                                    required
                                    v-model="form_values.date_start" v-on:change="set_hours_auto"
                                >
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="room_id" class="col-md-4 col-form-label text-right">Zona</label>
                            <div class="col-md-8">
                                <select name="room_id" v-model="form_values.room_id" class="form-control" required>
                                    <option v-for="(option_room, key_room) in rooms" v-bind:value="`0` + option_room.room_id">{{ option_room.name }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="total_spots" class="col-md-4 col-form-label text-right">Cupos habilitados</label>
                            <div class="col-md-8">
                                <input
                                    name="total_spots" type="number" class="form-control" min="1" max="60" value="10"
                                    required
                                    title="Cupo máximo" placeholder="Cupo máximo"
                                >
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="hours" class="col-md-4 col-form-label text-right">Horarios</label>
                            <div class="col-md-8">
                                <div v-for="(hour, kh) in hours">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value=""
                                            v-bind:id="`hour_` + kh"
                                            v-model="hours[kh].selected"
                                            v-on:change="set_selected_hours"
                                        >
                                        <label class="form-check-label" v-bind:for="`hour_` + kh">
                                            {{ hour.title }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- <div class="form-group row">
                            <label for="date_end" class="col-md-4 col-form-label text-right">Fecha hasta</label>
                            <div class="col-md-8">
                                <input
                                    name="date_end" type="date" class="form-control"
                                    required
                                    v-model="form_values.date_end"
                                >
                            </div>
                        </div> -->
                        
                        <div class="form-group row">
                            <div class="col-md-8 offset-md-4">
                                <button class="btn btn-primary w120p" type="submit">Programar</button>
                            </div>
                        </div>
                    <fieldset>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Variables
//-----------------------------------------------------------------------------

// VueApp
//-----------------------------------------------------------------------------
var schedule_generator_app = new Vue({
    el: '#schedule_generator_app',
    created: function(){
        this.set_hours_auto()
    },
    data: {
        form_values: {
            date_start: '<?= date('Y-m-d') ?>',
            date_end: '<?= date('Y-m-d') ?>',
        },
        hours: <?= json_encode($hours) ?>,
        str_hours: '',
        rooms: <?= json_encode($rooms->result()) ?>,
        loading: false,
    },
    methods: {
        send_form: function(){
            this.loading = true
            var form_data = new FormData(document.getElementById('programacion_form'))
            axios.post(url_api + 'trainings/schedule/', form_data)
            .then(response => {
                console.log(response.data)
                toastr['info'](response.data.message)
                this.loading = false
            })
            .catch( function(error) {console.log(error)} )
        },
        set_hours_auto: function(){
            var date1 = moment(this.form_values.date_start, 'YYYY-MM-DD')
            var week_day = parseInt(date1.format('d'))
            this.hours.forEach(hour => {
                hour.selected = false
                if ( week_day == 6 || week_day == 0 ) {
                    console.log('Es fin de semana', week_day)
                    if ( hour.group == 2 ) hour.selected = true
                } else {
                    console.log('Entre semana', week_day)
                    if ( hour.group == 1 ) hour.selected = true
                }
            });
            this.set_selected_hours()
        },
        set_selected_hours: function(){
            var arr_hours = [];
            this.hours.forEach(hour => {
                if ( hour.selected ) arr_hours.push(hour.cod)
            });
            
            this.str_hours = arr_hours.join(',')
            console.log(this.str_hours)
        },
    }
})
</script>