<?php $this->load->view('assets/select2') ?>

<?php
    $options_country = $this->App_model->options_place('type_id = 2', 'cr', 'País');
    $options_city = $this->App_model->options_place('type_id = 4', 'cr', 'Ciudad');

    //Formulario destino
    $url_action = 'https://checkout.payulatam.com/ppp-web-gateway-payu/';
    if ( $form_data['test'] == 1 ) { $url_action = 'https://sandbox.checkout.payulatam.com/ppp-web-gateway-payu'; }
?>

<div class="px-3 py-1 mx-auto text-center">
    <h1 class="title">Verifica</h1>
</div>

<?php $this->load->view('orders/checkout/steps_v') ?>

<div class="center_box_750">
    <table class="table bg-white">
        <tbody>
            <tr>
                <td>Nombre</td>
                <td><?= $row->buyer_name ?></td>
            </tr>

            <tr>
                <td>Correo electrónico</td>
                <td><?= $row->email ?></td>
            </tr>

            <tr>
                <td>Ciudad</td>
                <td>
                    <?= $row->city; ?>
                </td>
            </tr>

            <tr>
                <td>Dirección</td>
                <td>
                    <?= $row->address; ?>
                </td>
            </tr>

            <tr>
                <td>Teléfono</td>
                <td><?= $row->phone_number ?></td>
            </tr>

            <tr>
                <td>Valor total</td>
                <td class="td_price">
                    <?= $this->pml->money($row->amount) ?>
                    <small>COP</small>
                </td>
            </tr>
        </tbody>
    </table>

    <form accept-charset="utf-8" method="POST" action="<?= $url_action ?>">
        <?php foreach ( $form_data as $field_name => $field_value ) { ?>
            <input type="hidden" name="<?= $field_name ?>" value="<?= $field_value ?>">
        <?php } ?>
        <button class="btn btn-main btn-block btn-lg" type="submit">IR A PAGAR</button>
        <p class="text-center my-3">
            <small>
                Para pago en efectivo se generará un <strong>CÓDIGO</strong> Efecty, Baloto, PagaTodo u otros.
            </small>  
        </p>
        <a class="btn btn-light btn-block mt-2" role="button" href="<?= URL_ADMIN . 'orders/checkout/1' ?>">
            <i class="fa fa-chevron-left"></i>
            Volver
        </a>
    </form>
    <hr>
    <?php $this->load->view('orders/checkout/products_v') ?>
</div>