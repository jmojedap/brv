<?php
    $payu_data['transactionState'] = 4;
    $payu_data['referenceCode'] = $row->order_code;
    $payu_data['processingDate'] = date('Y-m-d H:i:s');
    $payu_data['cus'] = rand(100000,999999);
    $payu_data['TX_VALUE'] = $row->amount;
    $payu_data['currency'] = 'COP';
    $payu_data['pseBank'] = 'Bancolombia';
    $payu_data['lapPaymentMethod'] = 'VISA';
    $payu_data['reference_pol'] = rand(1000000,9999999);

    $options_pol_response_code = $this->Item_model->options('category_id = 10');
?>

<form action="<?= URL_ADMIN . 'orders/result/' ?>" accept-charset="utf-8" method="GET">
    <div class="card center_box_750">
        <div class="card-body">
            <div class="form-group row">
                <div class="col-md-8 offset-md-4">
                    <button class="btn btn-success w120p" type="submit">Enviar</button>
                </div>
            </div>

            <div class="form-group row">
                <label for="polResponseCode" class="col-md-4 col-form-label text-right">polResponseCode</label>
                <div class="col-md-8">
                    <?= form_dropdown('polResponseCode', $options_pol_response_code, '01', 'class="form-control"') ?>
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