<div class="table-responsive">
    <table class="table bg-white">
        <thead>
            <th width="10px"><input type="checkbox" @change="select_all" v-model="all_selected"></th>
            <th class="table-warning" width="10px">ID</th>
            <th width="50px"></th>
            <th>Nombre</th>
            <th>Bonita</th>
            <th>Categoría</th>
            <th>Precio</th>
            <th>Leído</th>
            <th width="50px"></th>
        </thead>
        <tbody>
            <tr v-for="(element, key) in list" v-bind:id="`row_` + element.id" v-bind:class="{'table-info': selected.includes(element.id) }">
                <td><input type="checkbox" v-bind:id="`check_` + element.id" v-model="selected" v-bind:value="element.id"></td>
                <td class="table-warning">{{ element.id }}</td>
                
                <td>
                    <a v-bind:href="`<?= URL_ADMIN . "albums/pictures/" ?>` + element.id">
                        <img
                            v-bind:src="element.url_thumbnail"
                            class="rounded w50p"
                            v-bind:alt="element.id"
                            onerror="this.src='<?= URL_IMG ?>users/sm_user.png'"
                        >
                    </a>
                </td>
                <td>
                    <a v-bind:href="`<?= URL_ADMIN . "albums/pictures/" ?>` + element.id">
                        {{ element.title }}
                    </a>
                </td>
                <td>
                    {{ element.girl_name }}
                </td>
                <td>{{ element.cat_clothes | cat_clothes_name }}</td>
                <td>{{ element.price | currency }}</td>

                <td>{{ element.qty_read }}</td>
                
                <td>
                    <button class="a4" data-toggle="modal" data-target="#detail_modal" @click="set_current(key)">
                        <i class="fa fa-info"></i>
                    </button>
                </td>
            </tr>
        </tbody>
    </table>
</div>