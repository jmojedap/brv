<div class="text-center mb-2" v-show="loading">
    <i class="fa fa-spin fa-spinner fa-3x text-muted"></i>
</div>

<div class="table-responsive" v-show="!loading">
    <table class="table bg-white">
        <thead>
            <th width="10px"><input type="checkbox" @change="select_all" v-model="all_selected"></th>
            <th width="50px"></th>
            <th>Usuario</th>
            <th>Nombre completo</th>
            <th>No. Documento</th>
            <th>Suscripción hasta</th>
            <th>Plan</th>
            <th width="50px"></th>
        </thead>
        <tbody>
            <tr v-for="(element, key) in list" v-bind:id="`row_` + element.id" v-bind:class="{'table-info': selected.includes(element.id) }">
                <td><input type="checkbox" v-bind:id="`check_` + element.id" v-model="selected" v-bind:value="element.id"></td>
                
                <td>
                    <a v-bind:href="`<?= URL_ADMIN . "users/profile/" ?>` + element.id">
                        <img
                            v-bind:src="element.url_thumbnail"
                            class="rounded-circle w40p"
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
                    {{ element.first_name }} <strong>{{ element.last_name }}</strong>
                    <br>
                    <i class="far fa-circle text-danger" v-if="element.status == 0"></i>
                    {{ element.role | role_name }}
                </td>
                <td>
                    {{ element.document_type | document_type_name }} &middot;
                    {{ element.document_number }}
                </td>
                
                <td>
                    <span v-html="expiration_icon(element.expiration_at)"></span>
                    
                    <span>
                        {{ element.expiration_at | expiration }}
                    </span>
                    <br>
                    <span class="text-muted">
                        {{ element.expiration_at }}
                    </span>
                </td>

                <td>
                    <div v-if="element.role >= 20">
                        <a v-bind:href="`<?= URL_ADMIN . 'users/edit/' ?>` + element.id + `/details`" class="btn" title="Editar plan">
                            <i class="fa fa-circle" v-bind:class="`prtp_` + element.commercial_plan"></i>
                            {{ element.commercial_plan | commercial_plan_name }}
                        </a>
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