<div class="my-2">
    <h3>Productos &middot; {{ products.length }}</h3>

    <table class="table bg-white">
        <thead>
            <th width="50px">Referencia</th>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>{{ order.total_products | currency }}</th>
        </thead>
        <tbody>
            <tr v-for="product in products">
                <td>{{ product.code }}</td>
                <td>
                    <a v-bind:href="`<?= URL_ADMIN . "products/info/" ?>` + product.product_id">
                        {{ product.name }}
                    </a>
                </td>
                <td>
                    {{ product.quantity }}
                </td>
                <td>{{ product.price | currency }}</td>
            </tr>
        </tbody>
    </table>

    <div v-show="extras.length > 0">
        <h3>Extras &middot; {{ extras.length }}</h3>
    
        <table class="table bg-white">
            <thead>
                <th>Extra</th>
                <th>Cantidad</th>
                <th>{{ order.total_extras | currency }}</th>
            </thead>
            <tbody>
                <tr v-for="extra in extras">
                    <td>
                        {{ extra.extra_name }}
                    </td>
                    <td>
                        {{ extra.quantity }}
                    </td>
                    <td>{{ extra.price | currency }}</td>
                </tr>
            </tbody>
        </table>
    </div>

</div>