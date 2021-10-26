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
            <table class="table-calendar">
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
                        <td v-for="day in week.days" v-bind:class="day_class(day)" v-on:click="set_day(day)">
                            <div>
                                <div class="day-number">{{ day.day }}</div>
                                <div>
                                    <i class="fa fa-circle events-point text_evt203" v-show="day.qty_events_type[203] > 0"></i>
                                    <i class="fa fa-circle events-point text_evt221" v-show="day.qty_events_type[221] > 0"></i>
                                    <i class="fa fa-circle events-point text_evt223" v-show="day.qty_events_type[223] > 0"></i>
                                    <i class="fa fa-circle events-point text_evt225" v-show="day.qty_events_type[225] > 0"></i>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-md-8">
            <div class="text-center" v-show="active_day.id > 0">
                <h3>{{ active_day.start | date_format }}</h3>
                <h4 class="text-muted">{{ active_day.start | ago }}</h4>
            </div>
            <div class="mb-3">
                <ul class="nav nav-tabs justify-content-center">
                    <li class="nav-item">
                        <a class="nav-link" v-bind:class="{'active': section == 'trainings' }" href="#" v-on:click="set_section('trainings')">Entrenamientos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" v-bind:class="{'active': section == 'appointments' }" href="#" v-on:click="set_section('appointments')">Citas</a>
                    </li>
                </ul>
            </div>
            <div v-show="section == `trainings`">
                <?php $this->load->view('admin/calendar/calendar/trainings_v') ?>
            </div>
            <div v-show="section == `appointments`">
                <?php $this->load->view('admin/calendar/calendar/appointments_v') ?>
            </div>

        </div>
    </div>
    <?php $this->load->view('common/modal_single_delete_v') ?>
    <?php $this->load->view('admin/calendar/calendar/modal_delete_appointment_v') ?>
</div>

<?php $this->load->view($this->views_folder . 'calendar/vue_v') ?>