<div class="text-center mb-2" v-show="loading">
    <i class="fa fa-spin fa-spinner fa-3x text-muted"></i>
</div>

<div class="table-responsive" v-show="!loading">
    <table class="table bg-white">
        <thead>
            <th width="10px"><input type="checkbox" @change="select_all" v-model="all_selected"></th>
            <th width="10px">ID</th>
            <th>Nombre</th>
            <th>Inicio</th>
            <th>Hace</th>
            <th width="150px">Tipo</th>
            <th>Día de la semana</th>
            <th>Cantidad de días</th>
            <th>Días hábiles</th>
            <th>Día hábil</th>
            <th width="50px"></th>
        </thead>
        <tbody>
            <tr v-for="(element, key) in list" v-bind:id="`row_` + element.id" v-bind:class="{'table-info': selected.includes(element.id) }">
                <td><input type="checkbox" v-bind:id="`check_` + element.id" v-model="selected" v-bind:value="element.id"></td>
                <td class="text-muted">{{ element.id }}</td>

                <td>
                    <a v-bind:href="`<?= URL_ADMIN . "periods/edit/" ?>` + element.id">
                        {{ element.period_name }}
                    </a>
                </td>
                <td>
                    {{ element.start | date_format }}
                </td>
                <td>
                    {{ element.start | ago }}
                </td>
                <td>
                    <a v-bind:href="`<?= URL_ADMIN . "periods/explore/1/?type=0" ?>` + element.type_id">{{ element.type_id | type_name }}</a>
                </td>

                <td>{{ element.week_day }} </td>
                <td>{{ element.qty_days }} </td>
                <td>{{ element.qty_business_days }} </td>
                <td>
                    <div v-show="element.type_id == 9">
                        <button class="btn btn-sm btn-light w50p" v-show="element.qty_business_days > 0" type="button" v-on:click="toggle_business_days(key)">
                            Sí
                        </button>
                        <button class="btn btn-sm btn-warning w50p" v-show="element.qty_business_days == 0" type="button" v-on:click="toggle_business_days(key)">
                            No
                        </button>
                    </div>
                </td>
                
                <td>
                    <button class="a4" data-toggle="modal" data-target="#detail_modal" @click="set_current(key)">
                        <i class="fa fa-info"></i>
                    </button>
                </td>
            </tr>
        </tbody>
    </table>
</div>