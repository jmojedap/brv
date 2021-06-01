<?php
    //Cantidad de unidades
    $qty = 5;
    if ( $this->input->get('qty') ) { $qty = intval($this->input->get('qty')); }
    if ( $qty < 5 ) { $qty = 5; }
?>

<div id="product_app">
    <div class="product_detail center_box_750">
        <h1 class="title text-center"><?= $row->name ?></h1>

        <div class="d-flex mb-2">
            <button class="flex-fill btn btn-light" v-on:click="sum_qty(-1)">
                <i class="fa fa-minus"></i>
            </button>
            <div class="flex-fill p-2">
                <p class="price_1 text-center">
                    {{ price * qty | currency }}
                </p>
            </div>
            <button class="flex-fill btn btn-light" v-on:click="sum_qty(1)">
                <i class="fa fa-plus"></i>
            </button>
            
        </div>

        <div class="text-center">
            <?php if ( $this->session->userdata('logged') ) { ?>
                <button class="btn btn-main w120p btn-lg btn" v-on:click="add_product">
                    Comprar
                </button>
            <?php } else { ?>
                <button class="btn btn-main w120p btn-lg" data-toggle="modal" data-target="#modal_signup">
                    Comprar
                </button>
            <?php } ?>
        </div>

        <h2 class="mt-2">¿Cómo funciona?</h2>
        <p><?= $row->description ?></p>
    </div>

    <?php $this->load->view('accounts/modal_signup_v') ?>
</div>

<script>
// Filters
//-----------------------------------------------------------------------------
    Vue.filter('currency', function (value) {
        if (!value) return '';
        value = '$ ' + new Intl.NumberFormat().format(value);
        return value;
    });


// VueApp
//-----------------------------------------------------------------------------
    new Vue({
        el: '#product_app',
        created: function(){
            //this.get_list();
        },
        data: {
            user_id: '<?= $this->session->userdata('user_id'); ?>',
            product_id: '<?= $row->id; ?>',
            qty: <?= $qty ?>,
            price: <?= $row->price ?>
        },
        methods: {
            add_product: function(){
                axios.get(url_app + 'orders/add_product/' + this.product_id + '/' + this.qty)
                .then(response => {
                    if ( response.data.status == 1 ) {
                        window.location = url_app + 'orders/checkout';
                    }
                })
                .catch(function (error) {
                    console.log(error);
                });  
            },
            sum_qty: function(sum){
                this.qty += sum;
                if ( this.qty < 5 ) { this.qty = 5; }
            },
        }
    });
</script>