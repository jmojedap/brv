<div id="order_app">
    <div class="row mb-2">
        <div class="col-md-4">
            <div class="card mb-2">
                <div class="card-header">Cliente</div>
                <table class="table bg-white">
                    <tbody>
                        <tr>
                            <td class="td-title">Cód. venta</td>
                            <td>{{ order.order_code }}</td>
                        </tr>
                        <tr>
                            <td class="td-title">A nombre de</td>
                            <td>
                                <a v-bind:href="`<?= URL_ADMIN . "users/orders/" ?>` + order.user_id" class="">
                                    {{ order.buyer_name }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td class="td-title">Correo electrónico</td>
                            <td>{{ order.email }}</td>
                        </tr>
                        <tr>
                            <td class="td-title">{{ order.document_type | document_type_name }}</td>
                            <td>
                                {{ order.document_number }}
                                
                            </td>
                        </tr>
                        <tr>
                            <td class="td-title">Total</td>
                            <td>
                                <strong class="text-primary">{{ order.amount | currency }}</strong>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-2">
                <div class="card-header">Administración</div>
                <table class="table bg-white">
                    <tbody>
                        <tr>
                            <td class="td-title">Pagado</td>
                            <td>
                                <div class="d-flex">
                                    <div class="mr-2">
                                        <span v-if="order.payed == 1">
                                            <i class="fa fa-check-circle text-success"></i>
                                            <strong class="text-success">Sí</strong>
                                        </span>
                                        <span class="text-muted" v-else="order.payed">
                                            <i class="fa fa-info-circle text-warning"></i>
                                            No
                                        </span>
                                    </div>
                                    <div>
                                        <?php if ( $row->payment_channel ) : ?>
                                            <i class="fa fa-circle channel_<?= $row->payment_channel ?>"></i>
                                            <?= $this->Item_model->name(106, $row->payment_channel) ?>    
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="order.payed == 1">
                            <td class="td-title">Fecha pago</td>
                            <td v-bind:title="order.confirmed_at">
                                {{ order.confirmed_at | ago }} &middot; <span class="text-muted"><?= $this->pml->date_format($row->confirmed_at, 'Y-M-d H:i') ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="td-title">Estado venta</td>
                            <td>{{ order.status | order_status_name }}</td>
                        </tr>
                        <tr>
                            <td class="td-title">Actualizado</td>
                            <td v-bind:title="order.updated_at">
                                {{ order.updated_at | date_format }} &middot; {{ order.updated_at | ago }}
                            </td>
                        </tr>
                        <tr>
                            <td class="td-title">Actualizado por</td>
                            <td>
                                <a href="<?= base_url("users/profile/{$row->updater_id}") ?>">
                                    <?= $this->App_model->name_user($row->updater_id); ?> &middot;
                                    <?= $row->updater_id; ?>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td class="td-title">Factura</td>
                            <td>{{ order.bill }}</td>
                        </tr>
                        <tr>
                            <td class="td-title">Anotación</td>
                            <td>{{ order.notes_admin }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card" v-if="order.weigth > 0">
                <div class="card-header">Envío</div>
                <table class="table bg-white">
                    <tbody>
                        <tr>
                            <td class="td-title">Ciudad</td>
                            <td>{{ order.city }}</td>
                        </tr>
                        <tr>
                            <td class="td-title">Dirección</td>
                            <td>{{ order.address }}</td>
                        </tr>
                        <tr>
                            <td class="td-title">Teléfono</td>
                            <td>{{ order.phone_number }}</td>
                        </tr>
                        <tr>
                            <td class="td-title">Notas cliente</td>
                            <td>{{ order.notes }}</td>
                        </tr>
                        <tr>
                            <td class="td-title">Método envío</td>
                            <td>
                                {{ order.shipping_method_id | shipping_method_name }}
                                &middot; {{ order.total_weight }} kg
                            </td>
                        </tr>
                        <tr>
                            <td class="td-title">Estado envío</td>
                            <td>
                                {{ order.shipping_status | shipping_status_name }}
                                &middot; <span class="text-muted">Guía No.</span> <strong>{{ order.shipping_code }}</strong>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php $this->load->view('admin/orders/info/products_v') ?>
</div>


<?php $this->load->view('admin/orders/info/vue_v') ?>