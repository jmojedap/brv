<div class="table-responsive">
    <table class="table bg-white">
        <thead>
            <th width="10px">
                <input type="checkbox" @change="select_all" v-model="all_selected">
            </th>
            <th width="10px"></th>
            <th>Ref. venta</th>
            <th>Estado</th>
            <th>Comprador</th>
            <th>Valor</th>
            <th>Ciudad</th>
            <th></th>
            
            <th width="50px"></th>
        </thead>
        <tbody>
            <tr v-for="(element, key) in list" v-bind:id="`row_` + element.id" v-bind:class="{'table-info': selected.includes(element.id) }">
                <td><input type="checkbox" v-bind:id="`check_` + element.id" v-model="selected" v-bind:value="element.id"></td>
                    
                <td>
                    <i class="fa fa-check-circle text-success" v-if="element.status == 1"></i>
                    <i class="fa fa-exclamation-triangle text-warning" v-if="element.status == 5"></i>
                    <i class="far fa-circle text-muted" v-if="element.status == 10"></i>
                </td>
                <td>
                    <a v-bind:href="`<?= URL_ADMIN . "orders/details/" ?>` + element.id">
                        {{ element.order_code }}
                    </a>
                </td>
                <td>
                    {{ element.status | status_name  }}
                </td>

                <td>
                    <a v-bind:href="`<?= URL_ADMIN . "users/profile/" ?>` + element.user_id">
                        {{ element.buyer_name }}
                    </a>
                    <br>
                    {{ element.email }}
                </td>
                <td>
                    {{ element.amount | currency }}
                </td>
                <td>{{ element.city }}</td>
                <td>
                    <b>A</b> <span v-bind:title="element.updated_at">{{ element.updated_at | ago }}</span>
                    <br>
                    <b>C</b> <span v-bind:title="element.created_at"> {{ element.created_at | ago }}</span>
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