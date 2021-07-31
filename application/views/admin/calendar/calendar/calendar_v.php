<style>
    .taken-spot{ color: #DDD; }
    .available-spot{ color: #60c83c; }

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

    .text_z10 {color: #FFC400;}
    .text_z20 {color: #F02555;}
    .text_z30 {color: #3080FF;}
    .text_z40 {color: #BB15EE;}
    .text_z50 {color: #39B44A;}
</style>

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
                    <button class="btn mr-2" v-for="room in rooms" v-bind:class="{'btn-light': room_id == room.room_id }" v-on:click="set_room(room.room_id)">
                        <i class="fa fa-circle" v-bind:class="`text_z` + room.room_id"></i>
                        {{ room.short_name }}
                    </button>
                </div>

                <h5 class="text-center"><i class="fa fa-circle" v-bind:class="`text_z` + room_id"></i> {{ parseInt(room_id) | room_name }}</h5>
                
                <table class="table bg-white">
                    <thead>
                        <th>Hora</th>
                        <th>Cupos</th>
                        <th>Disponibles</th>
                        <th></th>
                        <th width="80px"></th>
                    </thead>
                    <tbody>
                        <tr v-for="(training, key_training) in trainings">
                            <td>{{ training.start | hour }}</td>
                            <td>{{ training.total_spots }}</td>
                            <td>
                                {{ training.available_spots }}
                            </td>
                            <td>
                                <i class="fa fa-circle"
                                    v-for="n in parseInt(training.total_spots)"
                                    v-bind:class="{'available-spot': n > training.taken_spots, 'taken-spot': n <= training.taken_spots}"
                                    >
                            </td>
                            <td>
                                <a v-bind:href="`<?= URL_ADMIN . "calendar/training/" ?>` + training.id" class="btn btn-sm btn-light">Detalle</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view($this->views_folder . 'calendar/vue_v') ?>