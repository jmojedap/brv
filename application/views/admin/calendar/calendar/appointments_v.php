<div v-show="active_day.id > 0">
    <div class="d-flex mb-2 justify-content-center">
        <button class="btn mr-2" v-bind:class="{'btn-light': appointment_type_id == 0}" v-on:click="set_appointment_type(0)">
            Todas
        </button>
        <button class="btn mr-2"
            v-for="(appointment_type, atk) in appointment_types"
            v-bind:class="{'btn-light': appointment_type_id == atk }" v-on:click="set_appointment_type(atk)">
            <i class="fa fa-circle" v-bind:class="`text_evt` + atk"></i> {{ appointment_type }}
        </button>
    </div>
    
    <table class="table bg-white" v-show="appointments.length > 0">
        <thead>
            <th width="100px">Hora</th>
            <th>Tipo cita</th>
            <th width="40px"></th>
            <th>Usuario</th>
            <th width="87px"></th>
        </thead>
        <tbody>
            <tr v-for="(appointment, key_appointment) in appointments">
                <td>{{ appointment.start | hour }}</td>
                <td>
                    <i class="fa fa-circle mr-1" v-bind:class="`text_evt` + appointment.type_id"></i>
                    <span v-show="appointment_type_id == 0">
                        {{ appointment.event_type }}
                    </span>
                </td>
                <td>
                    <a v-if="appointment.user_id > 0" v-bind:href="`<?= URL_ADMIN . "users/appointments/" ?>` + appointment.user_id">
                        <img
                            v-bind:src="appointment.user_thumbnail"
                            class="rounded rounded-circle w30p"
                            alt="Imagen de usuario"
                            onerror="this.src='<?= URL_IMG ?>users/sm_user.png'"
                        >
                    </a>
                </td>
                <td>
                    <span v-if="appointment.user_id > 0">
                        <a v-bind:href="`<?= URL_ADMIN . "users/appointments/" ?>` + appointment.user_id">
                            {{ appointment.user_display_name }}
                        </a>
                    </span>
                    <span class="text-muted" v-else>-</span>
                </td>
                <td>
                    <a v-bind:href="`<?= URL_ADMIN . "events/edit/" ?>` + appointment.id" class="a4"><i class="fa fa-pencil-alt"></i></a>
                    <button class="a4" data-toggle="modal" data-target="#delete_appointment_modal" v-on:click="set_appointment(key_appointment)">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
        </tbody>
    </table>

    
    <div v-show="appointments.length == 0" class="text-center">
        <hr>
        <p class="text-center">No hay citas programadas para este día</p>
        <a class="btn btn-light" v-bind:href="`<?= URL_ADMIN . "calendar/schedule_generator/appointments/" ?>` + active_day.start">Programar</a>
    </div>

</div>