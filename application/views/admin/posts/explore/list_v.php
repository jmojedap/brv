<div class="table-responsive">
    <table class="table bg-white">
        <thead>
            <th width="10px">
                <input type="checkbox" @change="select_all" v-model="all_selected">
            </th>
            <th width="50px"></th>
            <th>Título</th>
            <th width="150px">Tipo</th>
            <th class="only-lg">Descripción</th>

            <th width="50px"></th>
        </thead>
        <tbody>
            <tr v-for="(element, key) in list" v-bind:id="`row_` + element.id" v-bind:class="{'table-info': selected.includes(element.id) }">
                <td>
                    <input type="checkbox" v-bind:id="`check_` + element.id" v-model="selected" v-bind:value="element.id">
                </td>
                    

                <td>
                    <img
                        v-bind:src="element.url_thumbnail"
                        class="rounded w50p"
                        alt="imagen post"
                        onerror="this.src='<?= URL_IMG ?>app/sm_nd_square.png'"
                    >
                </td>
                <td>
                    <a v-bind:href="`<?= URL_ADMIN ?>posts/info/` + element.id">
                        {{ element.post_name }}
                    </a>
                </td>
                <td>{{ element.type_id | type_name  }}</td>
                <td class="only-lg">{{ element.excerpt }}</td>
                
                <td>
                    <button class="a4" data-toggle="modal" data-target="#detail_modal" @click="set_current(key)">
                        <i class="fa fa-info"></i>
                    </button>
                </td>
            </tr>
        </tbody>
    </table>
</div>