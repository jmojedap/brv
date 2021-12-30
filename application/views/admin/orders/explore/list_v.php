<div class="text-center mb-2" v-show="loading">
    <i class="fa fa-spin fa-spinner fa-3x text-muted"></i>
</div>

<div class="table-responsive" v-show="!loading">
    <table class="table bg-white">
        <thead>
            <th width="10px">
                <input type="checkbox" @change="select_all" v-model="all_selected">
            </th>
            <th>Cód. venta</th>
            <th>Comprador</th>
            <th>Canal</th>
            <th width="5px"></th>
            <th>Valor</th>
            <th></th>
            
            <th width="50px"></th>
        </thead>
        <tbody>
            <tr v-for="(element, key) in list" v-bind:id="`row_` + element.id" v-bind:class="{'table-info': selected.includes(element.id) }">
                <td><input type="checkbox" v-bind:id="`check_` + element.id" v-model="selected" v-bind:value="element.id"></td>
                
                <td>
                    <a v-bind:href="`<?= URL_ADMIN . "orders/info/" ?>` + element.id">
                        {{ element.order_code }}
                    </a>
                </td>
                <td>
                    <a v-bind:href="`<?= URL_ADMIN . "users/orders/" ?>` + element.user_id">
                        {{ element.buyer_name }}
                    </a>
                    <br>
                    {{ element.email }}
                </td>
                <td>
                    <div v-if="element.payment_channel > 0">
                        <i class="fa fa-circle" v-bind:class="`channel_` + element.payment_channel"></i>
                        {{ element.payment_channel | payment_channel_name }}
                    </div>
                </td>
                <td>
                    <i class="fa fa-check-circle text-success" v-if="element.status == 1" title="Pago confirmado"></i>
                    <i class="far fa-circle text-muted" v-if="element.status == 5" title="Pago no exitoso"></i>
                    <i class="far fa-circle text-muted" v-if="element.status == 10" title="Iniciado"></i>
                </td>

                <td>
                    {{ element.amount | currency }}
                </td>
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