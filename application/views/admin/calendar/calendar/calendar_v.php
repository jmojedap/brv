<link type="text/css" rel="stylesheet" href="<?= URL_RESOURCES ?>css/pml_calendar.css">

<div id="calendar_app">
    <div class="row">
        <div class="col-md-4">
            <div class="d-flex mb-2 justify-content-between">
                <button class="btn btn-light w75p" v-on:click="sum_month(parseInt(month) - 1)">
                    <i class="fa fa-chevron-left"></i>
                </button>
                <div class="d-flex">
                    <input
                        name="year" type="number" class="form-control" min="<?= date('Y') - 1 ?>" max="<?= date('Y') + 2 ?>"
                        v-model="year" v-on:change="set_month"
                    >
                    <select name="month" v-model="month" class="form-control mr-1" v-on:change="set_month">
                        <option v-for="option_month in options_months" v-bind:value="option_month.month">{{ option_month.month_name }}</option>
                    </select>
                    <a class="btn btn-primary" href="<?= URL_ADMIN . 'calendar/calendar/' . date('Ymd') ?>">
                        Hoy
                    </a>
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
        <div class="col-md-8">
            <div v-show="active_day.id > 0">
                <div class="text-center">
                    <h3>{{ active_day.start | date_format }}</h3>
                    <h4 class="text-muted">{{ active_day.start | ago }}</h4>
                    
                </div>

                <div class="d-flex mb-2 justify-content-center">
                    <button class="btn mr-2" v-bind:class="{'btn-light': room_id == 0}" v-on:click="set_room(0)">
                        Todas
                    </button>
                    <button class="btn mr-2" v-for="room in rooms" v-bind:class="{'btn-light': room_id == room.room_id }" v-on:click="set_room(room.room_id)">
                        <i class="fa fa-circle" v-bind:class="`text_z` + room.room_id"></i>
                        {{ room.short_name }}
                    </button>
                </div>

                <h5 class="text-center" v-show="room_id > 0"><i class="fa fa-circle" v-bind:class="`text_z` + room_id"></i> {{ parseInt(room_id) | room_name }}</h5>
                <h5 class="text-center" v-show="room_id == 0">Todas las zonas</h5>
                
                <table class="table bg-white" v-show="trainings.length > 0">
                    <thead>
                        <th width="100px">Hora</th>
                        <th>Zona</th>
                        <th>Cupos</th>
                        <th>Disponibles</th>
                        <th width="200px">Reservaciones</th>
                        <th width="80px"></th>
                        <th width="10px"></th>
                    </thead>
                    <tbody>
                        <tr v-for="(training, key_training) in trainings">
                            <td>{{ training.start | hour }}</td>
                            <td>
                                <i class="fa fa-circle mr-1" v-bind:class="`text_z` + training.room_id"></i>
                                <span v-show="room_id == 0">
                                    {{ training.room_id | room_name }}
                                </span>
                            </td>
                            <td>{{ training.total_spots }}</td>
                            <td>
                                {{ training.available_spots }}
                            </td>
                            <td>
                                <!-- <i class="fa fa-circle"
                                    v-for="n in parseInt(training.total_spots)"
                                    v-bind:class="{'available-spot': n > training.taken_spots, 'taken-spot': n <= training.taken_spots}"
                                    > -->
                                    <div class="progress">
                                        <div class="progress-bar bg-primary" role="progressbar" aria-valuenow="" aria-valuemin="0" aria-valuemax="100"
                                            v-bind:style="`width: ` + Pcrn.intPercent(training.taken_spots,training.total_spots) + `%;`"
                                            >
                                            {{ training.taken_spots }}
                                        </div>
                                    </div>
                            </td>
                            <td>
                                <a v-bind:href="`<?= URL_ADMIN . "trainings/info/" ?>` + training.id" class="btn btn-sm btn-light">Detalle</a>
                            </td>
                            <td>
                                <button class="a4" data-toggle="modal" data-target="#delete_modal" v-on:click="set_training(key_training)">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                
                <div v-show="trainings.length == 0" class="text-center">
                    <hr>
                    <p class="text-center">No hay sesiones programadas para este día y zona</p>
                    <a class="btn btn-light" href="<?= URL_ADMIN . "trainings/schedule_generator/" ?>">Programar</a>
                </div>

            </div>
        </div>
    </div>
    <?php $this->load->view('common/modal_single_delete_v') ?>
</div>

<?php $this->load->view($this->views_folder . 'calendar/vue_v') ?>