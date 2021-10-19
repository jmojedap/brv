<div v-show="active_day.id > 0">
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