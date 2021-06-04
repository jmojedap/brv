<?php
    $wompi_data['reference'] = $row->order_code;
    $wompi_data['amount_in_cents'] = $row->amount * 100;
    $wompi_data['costumer_email'] = $row->email;
?>

<div id="test_confirmation">
    <form accept-charset="utf-8" method="POST" id="confirmation_form" @submit.prevent="send_form">
        <div class="card center_box_750">
            <div class="card-body">
                <div class="form-group row">
                    <div class="col-md-8 offset-md-4">
                        <button class="btn btn-success w120p" type="submit">Enviar</button>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="transaction_status" class="col-md-4 col-form-label text-right">transaction_status</label>
                    <div class="col-md-8">
                        <input
                            name="transacion_status" type="text" class="form-control"
                            required
                            title="transaction_status" placeholder="transaction_status"
                            v-model="transaction_status"
                        >
                    </div>
                </div>

                <?php foreach ( $wompi_data as $field => $field_value ) { ?>
                    <div class="form-group row">
                        <label for="" class="col-md-4 col-form-label text-right"><?= $field ?></label>
                        <div class="col-md-8">
                            <input
                                type="text" name="<?= $field ?>" required
                                class="form-control"
                                v-model="<?= $field ?>"
                                >
                        </div>
                    </div>

                <?php } ?>
                
            </div>
        </div>
    </form>
</div>

<script>
var test_confirmation = new Vue({
    el: '#test_confirmation',
    data: {
        reference: '<?= $row->order_code ?>',
        amount_in_cents: '<?= $row->amount * 100 ?>',
        costumer_email: '<?= $row->email ?>',
        transaction_status: 'APPROVED'
    },
    methods: {
        send_form: function(){
            var wompi_response = {
                "event": "transaction.updated",
                "data": {
                    "transaction": {
                        "id": "151557-1611811166-90040",
                        "created_at": "<?= date('Y-m-d') ?>T05:19:26.425Z",
                        "amount_in_cents": this.amount_in_cents,
                        "reference": this.reference,
                        "customer_email": this.costumer_email,
                        "currency": "COP",
                        "payment_method_type": "PSE",
                        "payment_method": {
                            "type": "PSE",
                            "extra": {
                            "ticket_id": "557161181116690040",
                            "vat_value": "0",
                            "entity_code": "9005199119",
                            "return_code": "SUCCESS",
                            "request_date": "<?= date('Y-m-d') ?>",
                            "async_payment_url": "https://registro.pse.com.co/PSEUserRegister/StartTransaction.htm?enc=tnPcJHMKlSnmRpHM8fAbu334nZ2%2fGxhetfsCRMMDsBlpMQA0yTafeArXsvpPtbGb",
                            "traceability_code": "874151427",
                            "transaction_cycle": "1",
                            "transaction_state": "OK",
                            "transaction_value": "180000",
                            "external_identifier": "874151427",
                            "bank_processing_date": "<?= date('Y-m-d') ?>"
                        },
                        "user_type": 0,
                        "user_legal_id": "1017176890",
                        "user_legal_id_type": "CC",
                        "payment_description": "Pago a <?= APP_NAME ?>, ref: " + this.reference,
                        "financial_institution_code": "1007"
                    },
                    "status": this.transaction_status,
                    "status_message": null,
                    "shipping_address": null,
                    "redirect_url": "https://www.plataformaenlinea.com/2017/orders/result",
                    "payment_source_id": null,
                    "payment_link_id": null,
                    "customer_data": {
                            "full_name": "<?= $row->buyer_name ?>",
                            "phone_number": "+573105088305"
                        }
                    }
                },
                "sent_at": "<?= date('Y-m-d') ?>T05:22:10.236Z",
                "timestamp": 1611811330,
                "signature": {
                    "checksum": "b9357bcbcb6cc54dd1711850e954a13419213144a9325914bb277659164217eb",
                    "properties": [
                    "transaction.id",
                    "transaction.status",
                    "transaction.amount_in_cents"
                    ]
                },
                "environment": "prod",
                "ip_address": "3.93.201.250",
                "response_id": "23397",
                "response_created_at": "<?= date('Y-m-d H:i:s') ?>"
            }
            axios.post(url_app + 'orders/confirmation_wompi/', wompi_response)
            .then(response => {
                toastr["success"]('confirmation_id: ' + response.data.confirmation_id);
            })
            .catch(function (error) { console.log(error) })
        },
    }
});
</script>

