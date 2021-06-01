<?php
    $arr_products = array();

    foreach ($products->result() as $product)
    {
        $arr_products[] = $product;
    }
?>

<style>
    .cover_post{
        border: 1px solid #DDD;
        max-width: 120px;
        -webkit-border-radius: 2px;
        -moz-border-radius: 2px;
        border-radius: 2px;
        -webkit-box-shadow: 5px 5px 5px 0px rgba(227,227,227,1);
        -moz-box-shadow: 5px 5px 5px 0px rgba(227,227,227,1);
        box-shadow: 5px 5px 5px 0px rgba(227,227,227,1);
    }

    .cover_post:hover{
        border: 1px solid #AAA;
    }
</style>

<div id="user_products" class="center_box_750">
    <?php if ( in_array($this->session->userdata('role'), array(1,2)) ) { ?>
        <div class="card mb-2">
            <div class="card-body">
                <form accept-charset="utf-8" method="POST" id="products_form" @submit.prevent="add_post" clas="form-horizontal">
                    <div class="form-group row">
                        <label for="product_id" class="col-md-2 col-form-label text-right">Producto</label>
                        <div class="col-md-8">
                            <input
                                name="product_id" id="field-product_id" type="text" class="form-control"
                                required
                                title="ID Producto" placeholder="ID Producto"
                                v-model="product_id"
                            >
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary btn-block" type="submit">
                                Agregar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <?php } ?>

    <div class="card mb-3" v-for="(product, key) in products">
        <div class="card-body">
            <h5 class="card-title">{{ products.id }} - {{ products.name }}</h5>
            <p>
                {{ products.description }}
            </p>
            <p>
                Precio: {{ products.price }}
            </p>   
            <p>
                <a class="btn btn-success w75p" v-bind:href="`<?= URL_ADMIN . "products/info/" ?>` + `/` + products.id">
                    Info
                </a>
                <?php if ( in_array($this->session->userdata('role'), array(1,2)) ) { ?>
                    <button class="btn btn-warning w75p" v-on:click="remove_post(product.id, products.meta_id)">
                        Quitar
                    </button>
                <?php } ?>
            </p>
        </div>
    </div>
</div>

<script>
    new Vue({
        el: '#user_products',
        created: function(){
            //this.get_list();
        },
        data: {
            user_id: <?= $row->id ?>,
            products: <?= json_encode($arr_products) ?>,
            product_id: ''
        },
        methods: {
            add_post: function(){
                axios.get(url_app + 'users/add_product/' + this.user_id + '/' + this.product_id)
                .then(response => {
                    console.log(response.data)
                    window.location = url_app + 'users/products/' + this.user_id;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            remove_post: function(product_id, meta_id){
                axios.get(url_app + 'user/delete_meta/' + this.user_id + '/' + meta_id)
                .then(response => {
                    console.log(response.data)
                    window.location = url_app + 'users/products/' + this.product_id;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
        }
    });
</script>