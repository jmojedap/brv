<div class="text-center mb-2" v-show="loading">
    <i class="fa fa-spin fa-spinner fa-3x text-muted"></i>
</div>

<div class="table-responsive" v-show="!loading">
    <table class="table bg-white">
        <thead>
            <th width="10px"><input type="checkbox" @change="select_all" v-model="all_selected"></th>
            <th width="10px">ID</th>
            <th>Nombre</th>
            <th></th>
            <th width="150px">Tipo</th>
            <th>País</th>
            <th>Departamento</th>
            <th>Población</th>
            <th width="50px"></th>
        </thead>
        <tbody>
            <tr v-for="(element, key) in list" v-bind:id="`row_` + element.id" v-bind:class="{'table-info': selected.includes(element.id) }">
                <td><input type="checkbox" v-bind:id="`check_` + element.id" v-model="selected" v-bind:value="element.id"></td>
                <td class="text-muted">{{ element.id }}</td>

                <td>
                    <a v-bind:href="`<?= URL_ADMIN . "admin/places/index/" ?>` + element.id">
                        {{ element.place_name }}
                    </a>
                </td>
                <td>
                    <span v-show="element.status == 1"><i class="fa fa-check-circle text-success"></i> Activo</span>
                    <span v-show="element.status == 0"><i class="far fa-circle text-warning"></i> Inactivo</span>
                </td>
                <td>
                    <a v-bind:href="`<?= URL_ADMIN . "admin/places/explore/1/?type=" ?>` + element.type_id">{{ element.type_id | type_name }}</a>
                </td>

                <td>{{ element.country }}</td>
                <td>{{ element.region }}</td>
                <td>{{ element.population }} <span class="text-muted">({{ element.population_year }})</span></td>
                
                <td>
                    <button class="a4" data-toggle="modal" data-target="#detail_modal" @click="set_current(key)">
                        <i class="fa fa-info"></i>
                    </button>
                </td>
            </tr>
        </tbody>
    </table>
</div>