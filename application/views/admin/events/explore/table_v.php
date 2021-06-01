<div class="text-center mb-2" v-show="loading">
    <i class="fa fa-spin fa-spinner fa-3x text-muted"></i>
</div>

<div class="table-responsive" v-show="!loading">
    <table class="table bg-white">
        <thead>
            <th width="10px"><input type="checkbox" @change="select_all" v-model="all_selected"></th>
            <th width="10px">ID</th>
            
            <th width="200px">Tipo</th>
            <th>ID Usuario</th>
            <th>Usuario</th>
            <th>ID Elemento</th>
            <th>Relacionado 1</th>
            <th>Creado</th>
            <th>Hace</th>
            
            <th width="50px"></th>
        </thead>
        <tbody>
            <tr v-for="(element, key) in list" v-bind:id="`row_` + element.id" v-bind:class="{'table-info': selected.includes(element.id) }">
                <td><input type="checkbox" v-bind:id="`check_` + element.id" v-model="selected" v-bind:value="element.id"></td>
                <td class="text-muted">{{ element.id }}</td>
                
                <td>
                    <a v-bind:href="`<?= URL_ADMIN . "events/explore/1/?type=" ?>` + element.type_id">{{ element.type_id | type_name }}</a>
                </td>

                <td>
                    <a v-bind:href="`<?= URL_ADMIN . "events/explore/1/?u=" ?>` + element.user_id">{{ element.user_id }}</a>
                </td>
                <td>
                    <a v-bind:href="`<?= URL_ADMIN . "users/profile/" ?>` + element.user_id">
                        {{ element.user_display_name }}
                    </a>
                </td>
                <td>
                    <a v-bind:href="`<?= URL_ADMIN . "events/explore/1/?fe3=" ?>` + element.element_id">{{ element.element_id }}</a>
                </td>
                <td>
                    <a v-bind:href="`<?= URL_ADMIN . "events/explore/1/?fe1=" ?>` + element.related_1">{{ element.related_1 }}</a>
                </td>

                <td class="only-lg">
                    <span v-bind:title="`Creado en ` + element.created_at"> {{ element.created_at }}</span>
                </td>
                <td class="only-lg">
                    <span class="text-muted" v-bind:title="`Creado en ` + element.created_at"> {{ element.created_at | ago }}</span>
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