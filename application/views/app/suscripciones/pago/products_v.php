<div class="d-flex justify-content-between">
    <div ><h3 class="text-primary">Productos</h3></div>
</div>
<table class="table bg-white">
    <thead>
        <th>Producto</th>
        <th class="text-right">{{ order.total_products | currency }}</th>
    </thead>
    <tbody>
        <tr v-for="(product, product_key) in products">
            <td>
                <a v-bind:href="`<?php echo URL_APP . "tienda/producto/" ?>` + product.product_id" class="">
                    {{ product.name }}
                </a>
                <br>
                {{ product.quantity }} x {{ product.price | currency }}
            </td>
            <td class="text-right">{{ product.price * product.quantity | currency }}</td>
        </tr>
    </tbody>
</table>