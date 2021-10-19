<div class="text-center mb-2" v-show="loading">
    <i class="fa fa-spin fa-spinner fa-3x text-muted"></i>
</div>

<div class="table-responsive" v-show="!loading">
    <table class="table bg-white">
        <thead>
            <th width="10px"><input type="checkbox" @change="select_all" v-model="all_selected"></th>
            <th width="30px"></th>
            <th>Nombre</th>
            <th>Fecha medición</th>
            <th>Estatura (cm)</th>
            <th>Peso (kg)</th>
            <th>IMC</th>
            <th>Puntaje InBody</th>
            <th width="50px"></th>
        </thead>
        <tbody>
            <tr v-for="(element, key) in list" v-bind:id="`row_` + element.id" v-bind:class="{'table-info': selected.includes(element.id) }">
                <td><input type="checkbox" v-bind:id="`check_` + element.id" v-model="selected" v-bind:value="element.id"></td>
                
                <td>
                    <a v-bind:href="`<?= URL_ADMIN . "users/inbody/" ?>` + element.user_id">
                        <img
                            v-bind:src="element.url_thumbnail"
                            class="rounded-circle w30p"
                            v-bind:alt="element.id"
                            onerror="this.src='<?= URL_IMG ?>users/sm_user.png'"
                        >
                    </a>
                </td>
                <td>
                    <a v-bind:href="url_app + `users/inbody/` + element.user_id + `/` + element.id">
                        {{ element.display_name }}
                    </a>
                    <br>
                    <span class="text-muted">
                        {{ element.email }}
                    </span>
                </td>

                <td>
                    {{ element.test_date | date_format }}
                    <br>
                    <small class="text-muted">
                        {{ element.test_date | ago }}
                    </small>
                </td>
                <td>{{ element.height }}</td>
                <td>{{ element.weight }}</td>
                <td class="text-center" v-bind:class="bmi_class(element.bmi_body_mass_index)">
                    <strong>
                        {{ element.bmi_body_mass_index }}
                    </strong>
                </td>

                <td class="text-center">{{ element.inbody_score }}</td>
                
                <td>
                    <button class="a4" data-toggle="modal" data-target="#detail_modal" @click="set_current(key)">
                        <i class="fa fa-info"></i>
                    </button>
                </td>
            </tr>
        </tbody>
    </table>
</div>