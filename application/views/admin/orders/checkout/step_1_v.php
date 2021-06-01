<?php
    $options_country = $this->App_model->options_place('type_id = 2 AND status = 1', 'full_name', 'País');
    $options_city = $this->App_model->options_place('type_id = 4', 'cr', 'Ciudad');
?>

<script>
// Variables
//-----------------------------------------------------------------------------
    var order_id = <?= $row->id; ?>;

// Document Ready
//-----------------------------------------------------------------------------

    $(document).ready(function(){
        $('#checkout_form').submit(function(){
            update_order();
            return false;
        });
    });

// Functions
//-----------------------------------------------------------------------------
    function update_order(){
        $.ajax({        
            type: 'POST',
            url: url_app + 'orders/update/' + order_id,
            data: $('#checkout_form').serialize(),
            success: function(response){
                console.log(response.message);
                if ( response.status == 1 ) { window.location = url_app + 'orders/checkout/2'; }
            }
        });
    }
</script>

<div class="mb-3 mx-auto text-center">
    <h1 class="title">Tus datos</h1>
    <p class="lead">
        Completa los datos requeridos por
        <a href="https://www.payulatam.com/co/compradores/" target="_blank">PayU</a>
        para realizar la compra
    </p>
</div>

<?php $this->load->view('orders/checkout/steps_v') ?>

<div class="row_no center_box_750">
    

    

    <div class="col-md-6_no">

        <form accept-charset="utf-8" method="POST" id="checkout_form">
            

            <div class="form-group row">
                <label for="country_id" class="col-md-3 col-form-label">País</label>
                <div class="col-md-9">
                    <?= form_dropdown('country_id', $options_country, $row->country_id, 'id="field-country_id" class="form-control" required') ?>
                </div>
            </div>

            <div class="form-group row">
                <label for="city_id" class="col-md-3 col-form-label">Tu ciudad</label>
                <div class="col-md-9">
                    <input
                        type="text"
                        id="field-city"
                        name="city"
                        required
                        value="<?= $row->city ?>"
                        class="form-control"
                        placeholder=""
                        title=""
                        >
                </div>
            </div>

            <div class="form-group row">
                <label for="phone_number" class="col-md-3 col-form-label">Teléfono</label>
                <div class="col-md-9">
                    <input
                        id="field-phone_number"
                        name="phone_number"
                        class="form-control"
                        required
                        minlength="7"
                        value="<?= $row->phone_number ?>"
                        type="text"
                        title="Escribe tu número de teléfono"
                        >
                </div>
            </div>

            <div class="form-group row">
                <label for="address" class="col-md-3 col-form-label">Dirección</label>
                <div class="col-md-9">
                    <input
                        id="field-address"
                        name="address"
                        class="form-control"
                        required
                        value="<?= $row->address ?>"
                        type="text"
                        title="Escribe tu dirección"
                        >
                </div>
            </div>
            <div class="form-group row">
                <div class="col-md-9 offset-md-3 text-center">
                    <button class="btn btn-main btn-lg btn-block" type="submit">
                        CONTINUAR
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="col-md-6_no">
        
        <h3 class="section_title">Datos de la compra</h3>
        <table class="table bg-white">
            <tbody>
                <tr>
                    <td>Tu nombre</td>
                    <td><?= $row->buyer_name ?></td>
                </tr>

                <tr>
                    <td>Correo electrónico</td>
                    <td><?= $row->email ?></td>
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

        <?php $this->load->view('orders/checkout/products_v') ?>
    </div>
    
</div>