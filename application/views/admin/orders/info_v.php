<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h2><?= $row->order_code ?></h2>
            </div>
            <table class="table">
                <tbody>
                    <tr>
                        <td>Valor</td>
                        <td><?= $this->pml->money($row->amount); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-md-8">
        <table class="table bg-white">
            <thead>
                <th>Producto</th>
                <th>Precio</th>
            </thead>
            <tbody>
                <?php foreach ( $products->result() as $row_product ) { ?>
                    <tr>
                        <td>
                            <a href="<?= URL_ADMIN . "products/info/{$row_product->product_id}" ?>">
                                <?= $row_product->name ?>
                            </a>
                        </td>
                        <td><?= $this->pml->money( $row_product->price) ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>