<?php
    $payu_data['state_pol'] = '4';
    $payu_data['reference_sale'] = $row->order_code;
    $payu_data['value'] = $row->amount;
    $payu_data['sign'] = rand(1000,9999);
    $payu_data['response_message_pol'] = 'Test confirmation';

    $options_response_code_pol = $this->Item_model->options('category_id = 10');
    $options_payment_method = $this->Item_model->options('category_id = 12');
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
                    <label for="response_code_pol" class="col-md-4 col-form-label text-right">response code pol</label>
                    <div class="col-md-8">
                        <?= form_dropdown('response_code_pol', $options_response_code_pol, '01', 'class="form-control"') ?>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="payment_method_id" class="col-md-4 col-form-label text-right">payment method</label>
                    <div class="col-md-8">
                        <?= form_dropdown('payment_method_id', $options_payment_method, '04', 'class="form-control"') ?>
                    </div>
                </div>

                <?php foreach ( $payu_data as $field => $field_value ) { ?>
                    

                    <div class="form-group row">
                        <label for="" class="col-md-4 col-form-label text-right"><?= $field ?></label>
                        <div class="col-md-8">
                            <input
                                type="text"
                                name="<?= $field ?>"
                                required
                                class="form-control"
                                value="<?= $field_value ?>"
                                >
                        </div>
                    </div>

                <?php } ?>
                
            </div>
        </div>
    </form>
</div>

<script>
    new Vue({
        el: '#test_confirmation',
        created: function(){
            //this.get_list();
        },
        data: {
            
        },
        methods: {
            send_form: function(){                
                axios.post(url_app + 'orders/confirmation_payu/', $('#confirmation_form').serialize())
                .then(response => {
                    toastr["success"]('confirmation_id: ' + response.data.confirmation_id);
                    //console.log(response.data.message);
                })
                .catch(function (error) {
                    console.log(error);
                });  
            },
        }
    });
</script>

