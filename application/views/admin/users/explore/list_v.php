<div class="text-center mb-2" v-show="loading">
    <i class="fa fa-spin fa-spinner fa-3x text-muted"></i>
</div>

<div class="table-responsive" v-show="!loading">
    <table class="table bg-white">
        <thead>
            <th width="10px"><input type="checkbox" @change="select_all" v-model="all_selected"></th>
            <th width="50px"></th>
            <th>Nombre</th>
            <th>Rol</th>
            <th class="only-lg"></th>
            <th width="50px"></th>
        </thead>
        <tbody>
            <tr v-for="(element, key) in list" v-bind:id="`row_` + element.id" v-bind:class="{'table-info': selected.includes(element.id) }">
                <td><input type="checkbox" v-bind:id="`check_` + element.id" v-model="selected" v-bind:value="element.id"></td>
                
                <td>
                    <a v-bind:href="`<?= URL_ADMIN . "users/profile/" ?>` + element.id">
                        <img
                            v-bind:src="element.url_thumbnail"
                            class="rounded-circle w50p"
                            v-bind:alt="element.id"
                            onerror="this.src='<?= URL_IMG ?>users/sm_user.png'"
                        >
                    </a>
                </td>
                <td>
                    <a v-bind:href="url_app + `users/profile/` + element.id + `/` + element.username">
                        {{ element.display_name }}
                    </a>
                    <br>
                    <span class="text-muted">
                        {{ element.email }}
                    </span>
                </td>
                <td>
                    <i class="fa fa-check-circle text-success" v-if="element.status == 1"></i>
                    <i class="fa fa-check-circle text-warning" v-if="element.status == 2"></i>
                    <i class="far fa-circle text-danger" v-if="element.status == 0"></i>
                    {{ element.role | role_name }}
                </td>
                <td class="only-lg">
                    <b>L</b> <span v-bind:title="`Último login ` + element.last_login">{{ element.last_login | ago }}</span>
                    <br>
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