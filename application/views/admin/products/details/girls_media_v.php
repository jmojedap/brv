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

<?php
    $post = $posts->row();
?>

<div class="row product_detail vbn_post">
    <div class="col-md-4">
        <img class="w100pc rounded" src="<?= $row->url_image ?>" alt="Imagen contenido">
    </div>
    <div class="col-md-8">
        <h1><?= $row->name ?></h1>
        <h2><?= $row->text_1 ?></h2>

        <div class="d-flex bd-highlight">
            <div class="p-2 flex-fill bd-highlight">
                <small class="text-muted">Categoría</small><br>
                <?= $this->Item_model->name(25, $row->cat_1); ?>
            </div>
            <div class="p-2 flex-fill bd-highlight">
                <small class="text-muted">Tipo</small><br>
                <?= $row->text_2 ?>
            </div>
        </div>
        
        <hr>
        <div class="d-flex">
            <div class="flex-fill">
                <p class="price">
                    <?= $this->pml->money($row->price); ?>
                </p>
            </div>
            <div class="flex-fill">
                <a class="btn btn-light btn-lg" href="<?= URL_ADMIN . "girls/album/{$post->related_1}/{$post->id}" ?>">
                    Vista previa
                </a>
            </div>
            <div class="flex-fill">
                <?php if ( $this->session->userdata('logged') ) { ?>
                    <?php if ( strlen($row->external_url) == 0 ) { ?>
                        <button class="btn btn-main w120p btn-lg" id="btn_add_product">
                            Comprar
                        </button>
                    <?php } else { ?>
                        <a class="btn btn-main btn-lg w120p" href="<?= $row->external_url ?>" target="_blank">
                            Comprar
                        </a>
                    <?php } ?>
                <?php } else { ?>
                    <button class="btn btn-main w120p btn-lg" data-toggle="modal" data-target="#modal_signup">
                        Comprar
                    </button>
                <?php } ?>
            </div>
            
        </div>

        <hr>

        
        <h2 class="mt-2">Álbumes</h2>
        <ul>
            <?php foreach ( $posts->result() as $row_post ) { ?>
                <li>
                    <a href="<?= URL_ADMIN . "cf" ?>" class="clase">
                        contenido
                    </a>
                    <?= $row_post->title ?>
                </li>
            <?php } ?>
        </ul>

        <h2 class="mt-2">Descripción del contenido</h2>
        <p><?= $row->description ?></p>
    </div>
</div>

<?php $this->load->view('accounts/modal_signup_v') ?>