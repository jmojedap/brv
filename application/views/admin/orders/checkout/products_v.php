<h3 class="section_title">Detalle productos</h3>
<table class="table bg-white">
    <thead>
        <th>Producto</th>
        <th>Precio</th>
    </thead>
    <tbody>
        <?php foreach ( $products->result() as $product ) { ?>
            <tr>
                <td>
                    <a href="<?= URL_ADMIN . "products/details/{$product->product_id}" ?>" class="">
                        <?= $product->name ?>
                    </a>
                </td>
                <td><?= $this->pml->money($product->price * $product->quantity) ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>