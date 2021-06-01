<div class="table-responsive">
    <table class="table bg-white">
        <thead>
            <th width="10px"><input type="checkbox" @change="select_all" v-model="all_selected"></th>

            <th width="50px"></th>

            <th>Nombre</th>
            <th>Precio</th>
            <th>Disponibles</th>
            <th>Palabras clave</th>

            <th width="50px"></th>
        </thead>
        <tbody>
            <tr v-for="(element, key) in list" v-bind:id="`row_` + element.id" v-bind:class="{'table-info': selected.includes(element.id) }">
                <td><input type="checkbox" v-bind:id="`check_` + element.id" v-model="selected" v-bind:value="element.id"></td>

                <td>
                    <img
                        v-bind:src="element.url_thumbnail"
                        class="rounded w50p"
                        alt="imagen producto"
                        onerror="this.src='<?= URL_IMG ?>app/sm_nd_square.png'"
                    >
                </td>
                <td>
                    <a v-bind:href="`<?= URL_ADMIN . "products/info/" ?>` + element.id">
                        {{ element.name }}
                    </a>
                </td>
                <td class="text-right">{{ element.price | currency }}</td>

                <td>{{ element.keywords }}</td>
                <td>{{ element.stock }}</td>
                
                <td>
                    <button class="a4" data-toggle="modal" data-target="#detail_modal" @click="set_current(key)">
                        <i class="fa fa-info"></i>
                    </button>
                </td>
            </tr>
        </tbody>
    </table>
</div>