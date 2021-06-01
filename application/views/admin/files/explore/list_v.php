<div class="table-responsive">
    <table class="table bg-white">
        <thead>
            <th width="10px">
                <input  type="checkbox" id="checkbox_all_selected" @change="select_all" v-model="all_selected">
            </th>
            <th width="60px"></th>
            <th>Título</th>
            <th>Info</th>
            <th>Abrir</th>
            <th width="50px"></th>
        </thead>
        <tbody>
            <tr v-for="(element, key) in list" v-bind:id="`row_` + element.id" v-bind:class="{'table-info': selected.includes(element.id) }">
                <td>
                    <input type="checkbox" v-bind:id="`check_` + element.id" v-model="selected" v-bind:value="element.id">
                </td>
                
                <td>
                    <a v-bind:href="`<?= URL_ADMIN . "files/info/" ?>` + element.id">
                        <img
                            v-bind:src="element.url_thumbnail"
                            class="rounded w50p"
                            alt="imagen miniatura"
                            onerror="this.src='<?= URL_IMG ?>app/sm_nd_square.png'"
                        >
                    </a>
                </td>
                <td>
                    <a v-bind:href="`<?= URL_ADMIN . "files/info/" ?>` + element.id">{{ element.title }}</a>
                    <br>
                    {{ element.subtitle }}
                </td>

                <td>
                    {{ element.description }} &middot;
                    {{ element.keywords }} &middot;
                </td>

                <td>
                    <a v-bind:href="element.url" class="btn btn-sm btn-light" target="_blank">Abrir</a>
                    <a v-bind:href="element.url_thumbnail" class="btn btn-sm btn-light" target="_blank">Mini</a>
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