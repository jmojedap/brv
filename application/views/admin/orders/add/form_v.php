<form accept-charset="utf-8" method="POST" id="order_form" @submit.prevent="send_form">
    <fieldset v-bind:disabled="loading">
        <div class="form-group row">
            <label for="campo" class="col-md-4 text-right">Usuario</label>
            <div class="col-md-8 d-flex justify-content-between">
                <div>
                    <span class="text-primary">{{ user.display_name }}</span>
                    <br> {{ user.document_type | document_type_name }} {{ user.document_number }}
                    <br>
                    <span>Vencimiento: </span> {{ user.expiration_at | date_format }} &middot; {{ user.expiration_at | ago }}
                </div>
                <div>
                    <button class="btn btn-light btn-sm" type="button" v-on:click="set_step(1)">Cambiar</button>
                </div>
            </div>
        </div>
            
        <div v-show="step == 3">
            <!-- SI EL PRODUCTO TIENE BENEFICIO DE PAREJAS -->
            <div class="form-group row" v-show="product.for_partners == 1">
                <label for="partner" class="col-md-4 text-right"><i class="fa fa-user text-primary"></i> Beneficiario</label>
                <div class="col-md-8">
                    <span class="text-primary">
                        {{ partner.first_name }} {{ partner.last_name }}
                    </span>
                    <br>
                    <span class="text-muted">
                        {{ partner.document_type | document_type_name }}
                        {{ partner.document_number }}
                    </span>
                </div>
            </div>
            <div class="form-group form-check row" v-show="product.for_partners == 1">
                <div class="col-md-8 offset-md-4">
                    <input type="checkbox" class="form-check-input" id="pay_partner_value" v-model="pay_partner_value" v-on:change="set_quantity">
                    <label class="form-check-label" for="pay_partner_value">Incluir pago por beneficiario</label>
                </div>
            </div>
            <div class="form-group row">
                <label for="campo" class="col-md-4 text-right">Producto</label>
                <div class="col-md-8 d-flex justify-content-between">
                    <div>
                        <span class="text-muted">{{ product.code }}</span>
                        <br>{{ product.name }} 
                        <br>
                        <span v-show="quantity > 1">${{ product.price | currency }} x {{ quantity }} = </span> 
                        <span class="text-primary">${{ product.price * quantity | currency }}</span>

                    </div>
                    <div>
                        <button class="btn btn-light btn-sm" type="button" v-on:click="set_step(2)">Cambiar</button>
                    </div>
                </div>
            </div>
    
            <div class="form-group row">
                <label for="payment_channel" class="col-md-4 col-form-label text-right">Canal de pago</label>
                <div class="col-md-8">
                    <select name="payment_channel" v-model="payment.payment_channel" class="form-control" required>
                        <option v-for="(option_payment_channel, key_payment_channel) in options_payment_channel"
                            v-bind:value="key_payment_channel">{{ option_payment_channel }}
                        </option>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label for="confirmed_at" class="col-md-4 col-form-label text-right">Fecha de pago</label>
                <div class="col-md-8">
                    <input
                        name="confirmed_at" type="date" class="form-control"
                        title="Fecha de pago" placeholder="Fecha de pago"
                        required
                        v-model="payment.confirmed_at"
                    >
                </div>
            </div>

            <!-- <div class="form-group row">
                <label for="user_expiration_at" class="col-md-4 col-form-label text-right">Nuevo vencimiento</label>
                <div class="col-md-8">
                    <input
                        name="user_expiration_at" type="date" class="form-control"
                        required title="Nuevo vencimiento"
                        v-model="payment.user_expiration_at"
                    >
                    <small id="expirationHelp" class="form-text text-muted">
                        <span class="text-primary">{{ expirationAtDays }} días &middot;</span>
                        
                        Nueva fecha de vencimiento con motivo de este pago
                    </small>
                </div>
            </div> -->

            <div class="form-group row">
                <label for="bill" class="col-md-4 col-form-label text-right">Número factura</label>
                <div class="col-md-8">
                    <input name="bill" type="text" class="form-control" title="Número factura" v-model="payment.bill">
                </div>
            </div>

            <div class="form-group row">
                <label for="notes_admin" class="col-md-4 col-form-label text-right">Notas</label>
                <div class="col-md-8">
                    <textarea name="notes_admin" class="form-control" v-model="payment.notes_admin"></textarea>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-md-8 offset-md-4">
                    <button class="btn btn-success btn-lg w120p" type="submit">Crear pago</button>
                </div>
            </div>
        </div>
    <fieldset>
</form>