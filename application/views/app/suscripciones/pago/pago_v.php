<div class="px-3 py-1 mx-auto text-center">
    <?php if ( $redirect != 1 ) : ?>
        <h1 class="head_title text-main">Verifica</h1>
    <?php endif; ?>
</div>

<?php //$this->load->view('app/tienda/pago/steps_v') ?>

<div class="center_box_750" id="step_verifica">
    <!-- INDICAR LOADING SI HAY REDIRECCIONAMIENTO A PLATAFORMA DE PAGOS  -->
    <div class="text-center my-3" v-if="redirect == 1">
        <i class="fa fa-spin fa-spinner fa-3x text-muted"></i>
    </div>

    <!-- MOSTRAR INFO PARA VERIFICAR DATOS Y VALORES -->
    <div v-else>
        <hr>
        
        <h3 class="text-primary">Valores</h3>
        <table class="table table-sm bg-white">
            <tbody>
                <tr>
                    <td>Total productos</td>
                    <td class="text-right">
                        {{ order.total_products | currency }}
                    </td>
                </tr>
                <tr v-for="(extra, ek) in extras">
                    <td>{{ extra.extra_name }}</td>
                    <td class="text-right">
                        {{ extra.price | currency }}
                    </td>
                </tr>
                <tr v-show="order.total_extras >= 0">
                    <td>Total a pagar</td>
                    <td class="text-right">
                        <strong class="text-main">
                            {{ order.amount | currency }}
                        </strong>
                    </td>
                </tr>
            </tbody>
        </table>

        

        <form method="GET" action="<?= $form_destination ?>" id="wompi_form">
            <?php foreach ( $form_data as $field_name => $field_value ) { ?>
                <input type="hidden" name="<?= $field_name ?>" value="<?= $field_value ?>">
            <?php } ?>
            <div class="text-center center_box_320">
                <button class="btn btn-success btn-block btn-lg" type="submit">IR A PAGAR</button>
            </div>
        </form>

        <br>
        
        <div class="d-flex justify-content-between">
            <div><h3 class="text-primary">Datos del usuario</h3></div>
        </div>
        
        <table class="table bg-white">
            <tbody>
                <tr>
                    <td class="text-muted">Nombre</td>
                    <td><?= $order->buyer_name ?></td>
                </tr>

                <tr>
                    <td class="text-muted"><?= $this->Item_model->name(53, $order->document_type, 'abbreviation'); ?></td>
                    <td>
                        <?= $order->document_number ?>
                    </td>
                </tr>
                <tr>
                    <td class="text-muted">Correo electrónico</td>
                    <td><?= $order->email ?></td>
                </tr>

                <!-- <tr>
                    <td class="text-muted">Ciudad</td>
                    <td>
                        <?= $order->city; ?>
                    </td>
                </tr> -->

                <tr>
                    <td class="text-muted">Dirección</td>
                    <td>
                        <?= $order->address; ?>
                    </td>
                </tr>

                <tr>
                    <td class="text-muted">Teléfono</td>
                    <td><?= $order->phone_number ?></td>
                </tr>

            </tbody>
        </table>

        <?php $this->load->view('app/suscripciones/pago/products_v') ?>
    </div>
</div>

<script>
// Filters
//-----------------------------------------------------------------------------
Vue.filter('currency', function (value) {
    if (!value) return '';
    value = '$' + new Intl.NumberFormat().format(value);
    return value;
});

// VueApp
//-----------------------------------------------------------------------------
var step_verifica = new Vue({
    el: '#step_verifica',
    created: function(){
        this.goToWompi()
    },
    data: {
        order: <?= json_encode($order) ?>,
        products: <?= json_encode($products->result()) ?>,
        extras: <?= json_encode($extras->result()) ?>,
        redirect: <?= $redirect ?>,
    },
    methods: {
        goToWompi: function(){
            if ( this.redirect == 1 ) {
                document.getElementById("wompi_form").submit();
            }
        },
    }
})
</script>