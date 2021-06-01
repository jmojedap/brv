<script>
// Variables
//-----------------------------------------------------------------------------
var user_id = '<?= $this->session->userdata('user_id'); ?>';
var product_id = '<?= $row->id; ?>';

// Document Ready
//-----------------------------------------------------------------------------
$(document).ready(function() {

    $('#btn_add_product').click(function()
    {
        add_product(product_id);
    });
});

// Functions
//-----------------------------------------------------------------------------

    /**
    Crear orden con producto */
    function add_product(product_id) {
        $.ajax({
            type: 'POST',
            url: url_app + 'orders/add_product/' + product_id,
            success: function(response) {
                console.log(response.message);
                if (response.status == 1) {
                    window.location = url_app + 'orders/checkout';
                }
            }
        });
    }
</script>

<div class="mb-2">
    <a href="<?= URL_ADMIN . "catalog/explore" ?>" class="btn btn-light">
        <i class="fa fa-arrow-left"></i>
        Volver
    </a>
</div>

<div class="row product_detail">
    <div class="col-md-4">
        <img class="rounded product_img w100pc" src="<?= $row->url_image ?>" alt="Imagen producto">
    </div>
    <div class="col-md-8">
        <h1><?= $row->name ?></h1>

        <p><?= $row->description ?></p>

        <div class="d-flex">
            <div class="flex-fill">
                <p class="price" style="font-size: 2em;">
                    <?= $this->pml->money($row->price); ?>
                </p>
            </div>
            <div class="flex-fill">
                <button class="btn btn-main btn-lg" id="btn_add_product">
                    AL CARRITO
                </button>
            </div>
        </div>

        <div class="d-flex bd-highlight">
            <div class="p-2 flex-fill bd-highlight">
                <small class="text-muted"></small><br>
                <?= $row->text_2 ?>
            </div>
            <div class="p-2 flex-fill bd-highlight">
                <small class="text-muted">Categoría</small><br>
                <?= $this->Item_model->name(25, $row->cat_1); ?>
            </div>
            <div class="p-2 flex-fill bd-highlight">
                <small class="text-muted"></small><br>
                <?= $row->text_1; ?>
            </div>
        </div>
        
        <hr>

        <h2>Información adicional</h2>
    </div>
</div>

<?php $this->load->view('accounts/modal_signup_v') ?>