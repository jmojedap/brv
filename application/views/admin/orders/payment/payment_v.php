<div id="payment_app">
    <div class="center_box_750">
        <!-- PEDIDO CON USUARIO ASIGNADO -->
        <div v-if="order.user_id > 0">
            <!-- FALTAN DATOS -->
            <div v-if="missing_data.length > 0">
                <div class="alert alert-info">
                    <h4>Datos incompletos</h4>
                    <p>Para actualizar datos de pago debe completar los siguientes datos del pedido:</p>
                    <ul>
                        <li v-for="missing_field in missing_data">{{ missing_field }}</li>
                    </ul>
                </div>
                <p>
                    <a class="btn btn-primary" href="<?= URL_ADMIN . "orders/edit/{$row->id}" ?>">Completar datos</a>
                </p>
            </div>
            <!-- DATOS DE PEDIDO COMPLETOS -->
            <div class="card" v-else>
                <div class="card-body">
                    <div v-if="order.payment_channel == 0 || order.payment_channel >= 20">
                        <form accept-charset="utf-8" method="POST" id="payment_form" @submit.prevent="send_form">
                            <fieldset v-bind:disabled="loading">
                                <input type="hidden" name="id" value="<?= $row->id ?>">

                                <div class="form-group row">
                                    <label for="payed" class="col-md-4 col-form-label text-right">Pedido pagado</label>
                                    <div class="col-md-8">
                                        <select name="payed" v-model="order.payed" class="form-control" required>
                                            <option v-for="(option_payed, key_payed) in options_payed" v-bind:value="key_payed">{{ option_payed }}</option>
                                        </select>
                                    </div>
                                </div>

                                <div v-show="order.payed == '01'">
                                    <div class="form-group row">
                                        <label for="payment_channel" class="col-md-4 col-form-label text-right">Canal de pago</label>
                                        <div class="col-md-8">
                                            <select name="payment_channel" v-model="order.payment_channel" class="form-control" v-bind:required="order.payed == '01'">
                                                <option v-for="(option_payment_channel, key_payment_channel) in options_payment_channel" v-bind:value="key_payment_channel">{{ option_payment_channel }}</option>
                                            </select>
                                        </div>
                                    </div>
    
                                    <div class="form-group row">
                                        <label for="confirmed_at" class="col-md-4 col-form-label text-right">Fecha de pago</label>
                                        <div class="col-md-8">
                                            <input
                                                name="confirmed_at" type="date" class="form-control"
                                                title="Fecha de pago" placeholder="Fecha de pago"
                                                v-bind:required="order.payed == '01'"
                                                v-model="order.confirmed_at"
                                            >
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="bill" class="col-md-4 col-form-label text-right">Número de factura</label>
                                        <div class="col-md-8">
                                            <input name="bill" type="text" class="form-control" title="Número factura" v-model="order.bill" v-bind:required="order.payed == '01'">
                                        </div>
                                    </div>

    
                                    <div class="form-group row">
                                        <label for="notes_admin" class="col-md-4 col-form-label text-right">Notas sobre pago</label>
                                        <div class="col-md-8">
                                            <textarea name="notes_admin" class="form-control" v-model="order.notes_admin"></textarea>
                                        </div>
                                    </div>
                                </div>
        
                                
                                <div class="form-group row">
                                    <div class="col-md-8 offset-md-4">
                                        <button class="btn btn-primary w120p" type="submit">Guardar</button>
                                    </div>
                                </div>
                            <fieldset>
                        </form>
                    </div>

                    <!-- PEDIDO PAGADO A TRAVÉS DE PAYU -->
                    <div v-if="order.payment_channel == 10">
                        <h3>Pedido procesado por PayU</h3>
                        <p>
                            <a class="btn btn-primary" href="<?= URL_ADMIN . "pedidos/payu/{$row->id}" ?>">Ver detalles</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- PEDIDO SIN USUARIO ASIGNADO -->
        <div v-else>
            <div class="alert alert-info">
                <h4>Sin usuario</h4>
                <p>El pedido no tiene un usuario identificado, no puede actualizarse su pago.</p>
            </div>
        </div>
    </div>
</div>

<script>
// Variables
//-----------------------------------------------------------------------------
var order = <?= json_encode($row) ?>;
    order.payed = '0<?= $row->payed ?>';
    order.payment_channel = '0<?= $row->payment_channel ?>';
    order.confirmed_at = '<?= substr($row->confirmed_at,0,10) ?>';

// VueApp
//-----------------------------------------------------------------------------
var payment_app = new Vue({
    el: '#payment_app',
    data: {
        order: order,
        loading: false,
        options_payed: {'00':'No','01':'Sí'},
        options_payment_channel: <?= json_encode($options_payment_channel) ?>,
        missing_data: <?= json_encode($missing_data) ?>,
    },
    methods: {
        send_form: function(){
            this.loading = true
            var form_data = new FormData(document.getElementById('payment_form'))
            axios.post(url_api + 'orders/update_payment/', form_data)
            .then(response => {
                var toastr_type = 'success'
                if ( order.payed == '00' ) toastr_type = 'info'

                if ( response.data.saved_id > 0 ) {
                    toastr[toastr_type](response.data.message)
                }
                this.loading = false
            })
            .catch( function(error) {console.log(error)} )
        }
    }
})
</script>