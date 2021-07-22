<div class="text-center mb-2" v-show="loading">
    <i class="fa fa-spin fa-spinner fa-3x text-muted"></i>
</div>

<div class="table-responsive" v-show="!loading">
    <table class="table bg-white">
        <thead>
            <th width="10px"><input type="checkbox" @change="select_all" v-model="all_selected"></th>
            <th width="10px">ID</th>
            
            <th width="200px">Zona</th>
            <th></th>
            <th>Fecha</th>
            <th>Hora</th>
            <th>Cupos</th>
            <th>Disponibles</th>
            
            
            <th width="50px"></th>
        </thead>
        <tbody>
            <tr v-for="(element, key) in list" v-bind:id="`row_` + element.id" v-bind:class="{'table-info': selected.includes(element.id) }">
                <td><input type="checkbox" v-bind:id="`check_` + element.id" v-model="selected" v-bind:value="element.id"></td>
                <td class="text-muted">{{ element.id }}</td>
                
                <td>
                    <a v-bind:href="`<?= URL_ADMIN . "calendar/sesiones/1/?fe1=" ?>` + element.element_id">{{ element.element_id | room_name }}</a>
                </td>
                <td>
                    {{ element.start | ago }}
                </td>
                <td>
                    {{ element.start | day }} <br>

                </td>
                <td>
                    {{ element.start | hour }}
                </td>
                <td>{{ element.integer_1 }}</td>
                <td>{{ element.integer_2 }}</td>
                <td>
                    <button class="a4" data-toggle="modal" data-target="#detail_modal" @click="set_current(key)">
                        <i class="fa fa-info"></i>
                    </button>
                </td>
            </tr>
        </tbody>
    </table>
</div>