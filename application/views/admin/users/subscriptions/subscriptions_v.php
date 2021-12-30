<div id="user_subscriptions_app">
    <div class="center_box_750">
        <div class="mb-2">
            <button class="btn btn-success w120p" data-toggle="modal" data-target="#modal_form">
                <i class="fa fa-plus"></i> Nueva
            </button>
        </div>
        <table class="table bg-white">
            <thead>
                <th width="40px">ID</th>
                <th>Desde</th>
                <th>Hasta</th>
                <th>Ref. Venta</th>
                <th></th>
                <th>Creada</th>
                <th width="40px"></th>
            </thead>
            <tbody>
                <tr v-for="(subscription, key) in list">
                    <td class="text-center">
                        {{ subscription.id }}
                    </td>
                    <td>
                        {{ subscription.start | date_format }}
                        <br>
                        <small class="text-muted">{{ subscription.start | ago }}</small>                        
                    </td>
                    <td>
                        {{ subscription.end | date_format }}
                        <br>
                        <small class="text-muted">{{ subscription.end | ago }}</small>                        
                    </td>
                    <td>
                        <a v-bind:href="`<?= URL_ADMIN . "orders/info/" ?>` + subscription.order_id">
                            {{ subscription.order_id }}
                        </a>
                    </td>

                    <td>
                        {{ subscription.amount | currency }}
                    </td>
                    <td>
                        <span v-bind:title="subscription.created_at"> {{ subscription.created_at | ago }}</span>
                    </td>
                    <td>
                        <button class="a4" v-on:click="set_current(key)" data-toggle="modal" data-target="#delete_modal">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <?php $this->load->view($this->views_folder . 'subscriptions/modal_form_v') ?>
    <?php $this->load->view('common/modal_single_delete_v') ?>
</div>

<?php $this->load->view($this->views_folder . 'subscriptions/vue_v') ?>