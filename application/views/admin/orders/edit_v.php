<div id="edit_app">
    <div class="center_box_750">
        <div class="card">
            <div class="card-body">
                <form accept-charset="utf-8" method="POST" id="order_form" @submit.prevent="send_form">
                    <fieldset v-bind:disabled="loading">
                        <input type="hidden" name="id" value="<?= $row->id ?>">

                        <div class="form-group row">
                            <label for="phone_number" class="col-md-4 col-form-label text-right">Celular</label>
                            <div class="col-md-8">
                                <input
                                    name="phone_number" type="text" class="form-control"
                                    required
                                    title="Celular" placeholder="Celular"
                                    v-model="form_values.phone_number"
                                >
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="bill" class="col-md-4 col-form-label text-right">Número factura</label>
                            <div class="col-md-8">
                                <input
                                    name="bill" type="text" class="form-control" required
                                    title="No. factura" placeholder="No. factura"
                                    v-model="form_values.bill"
                                >
                            </div>
                        </div>

                        <?php if ( $row->total_weight > 0 ) : ?>
                            <div class="form-group row">
                                <label for="shipping_method_id" class="col-md-4 col-form-label text-right">Método de envío</label>
                                <div class="col-md-8">
                                    <select name="shipping_method_id" v-model="form_values.shipping_method_id" class="form-control" required>
                                        <option v-for="(option_shipping_method_id, key_shipping_method_id) in options_shipping_method_id" v-bind:value="key_shipping_method_id">{{ option_shipping_method_id }}</option>
                                    </select>
                                </div>
                            </div>
    
                            <div class="form-group row">
                                <label for="shipping_status" class="col-md-4 col-form-label text-right">Estado de envío</label>
                                <div class="col-md-8">
                                    <select name="shipping_status" v-model="form_values.shipping_status" class="form-control" required>
                                        <option v-for="(option_shipping_status, key_shipping_status) in options_shipping_status" v-bind:value="key_shipping_status">{{ option_shipping_status }}</option>
                                    </select>
                                </div>
                            </div>
    
                            <div class="form-group row">
                                <label for="shipping_code" class="col-md-4 col-form-label text-right">No. guía envío</label>
                                <div class="col-md-8">
                                    <input
                                        name="shipping_code" type="text" class="form-control"
                                        required
                                        title="No. guía envío" placeholder="No. guía envío"
                                        v-model="form_values.shipping_code"
                                    >
                                </div>
                            </div>
                        <?php endif; ?>


                        <div class="form-group row">
                            <label for="notes_admin" class="col-md-4 col-form-label text-right">Notas sobre la venta</label>
                            <div class="col-md-8">
                                <textarea
                                    name="notes_admin" class="form-control" rows="3"
                                    title="Notas internas" placeholder="Notas sobre la venta"
                                    v-model="form_values.notes_admin"
                                ></textarea>
                            </div>
                        </div>

                        <!-- <div class="form-group form-check border-top p-3">
                            <input type="checkbox" v-model="send_buyer_email" id="field-send_buyer_email">
                            <label class="form-check-label" for="field-send_buyer_email">Enviar E-mail de actualización de compra al comprador</label>
                        </div> -->
                        
                        <div class="form-group row">
                            <div class="col-md-8 offset-md-4">
                                <button class="btn btn-primary w120p" type="submit">Guardar</button>
                            </div>
                        </div>
                    <fieldset>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
var row = <?= json_encode($row) ?>;
row.shipping_status = '0<?= $row->shipping_status ?>';
row.shipping_method_id = '0<?= $row->shipping_method_id ?>';

// VueApp
//-----------------------------------------------------------------------------
var edit_app = new Vue({
    el: '#edit_app',
    data: {
        form_values: row,
        options_shipping_status: <?= json_encode($options_shipping_status) ?>,
        options_shipping_method_id: <?= json_encode($options_shipping_method_id) ?>,
        send_buyer_email: false,
        loading: false,
    },
    methods: {
        send_form: function(){
            this.loading = true
            var form_data = new FormData(document.getElementById('order_form'))
            axios.post(url_api + 'orders/admin_update/' + this.send_buyer_email, form_data)
            .then(response => {
                if ( response.data.saved_id > 0 ) toastr['success']('Guardado')
                if ( response.data.email_sent ) toastr['success']('Actualización enviada al comprador')
                
                this.loading = false
            })
            .catch( function(error) {console.log(error)} )
        },
    }
})
</script>