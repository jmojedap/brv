<div class="text-center mb-2" v-show="loading">
    <i class="fa fa-spin fa-spinner fa-3x text-muted"></i>
</div>

<div class="table-responsive" v-show="!loading">
    <table class="table bg-white">
        <thead>
            <th width="10px"><input type="checkbox" @change="select_all" v-model="all_selected"></th>
            <th>Tabla</th>
            <th title="ID del registro en la tabla asociada">ID Elemento</th>
            <th title="ID comentario padre">ID Padre</th>
            <th>Comentario</th>
            <th>Puntaje</th>
            <th>Sub Comentarios</th>
            <th class="only-lg"></th>
            <th width="50px"></th>
        </thead>
        <tbody>
            <tr v-for="(element, key) in list" v-bind:id="`row_` + element.id" v-bind:class="{'table-info': selected.includes(element.id) }">
                <td><input type="checkbox" v-bind:id="`check_` + element.id" v-model="selected" v-bind:value="element.id"></td>
                <td>
                    <a v-bind:href="`<?= URL_ADMIN . "comments/info/" ?>` + element.id">
                        {{ element.table_id }} &middot; {{ element.table_id | table_name }}
                    </a>
                </td>
                <td>{{ element.element_id }}</td>
                <td>{{ element.parent_id }}</td>
                <td>
                    {{ element.comment_text }}
                </td>
                <td>{{ element.score }}</td>
                <td>{{ element.qty_comments }}</td>
                <td class="only-lg">
                    <b>C</b> <span v-bind:title="`Creado en ` + element.created_at"> {{ element.created_at | ago }}</span>
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