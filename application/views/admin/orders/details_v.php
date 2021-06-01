<div class="center_box_750">
    <h3>General</h3>
    <table class="table bg-white">
        <tbody>
            <tr>
                <td>Ref Venta</td>
                <td><?= $row->order_code; ?></td>
            </tr>
            <tr>
                <td>Estado compra</td>
                <td>
                    <?php if ( $row->status == 1) : ?>
                        <i class="fa fa-check-circle text-success"></i>
                    <?php endif; ?>
                    <?= $this->Item_model->name(7, $row->status) ?>
                    [<?= $row->status ?>]
                </td>
            </tr>
            <tr>
                <td>Usuario ID</td>
                <td>
                    <?= $row->user_id ?> &middot;
                    <a href="<?= URL_ADMIN . "usuarios/actividad/{$row->user_id}" ?>" class="">
                        <?= $this->App_model->name_user($row->user_id); ?>
                    </a>
                </td>
            </tr>
            <tr>
                <td>Comprador</td>
                <td>
                    <?= $row->buyer_name ?>
                </td>
            </tr>
            <tr>
                <td>No. documento</td>
                <td>
                    <?= $row->document_number ?>
                </td>
            </tr>
            <tr>
                <td>E-mail</td>
                <td>
                    <?= $row->email ?>
                </td>
            </tr>
        </tbody>
    </table>

    <h3>Valores</h3>

    <table class="table bg-white">
        <tbody>
            <tr>
                <td>Valor total</td>
                <td><strong class="text-success"><?= $this->pml->money($row->amount); ?></strong></td>
            </tr>
            <tr>
                <td>Total productos</td>
                <td><?= $this->pml->money($row->total_products); ?></td>
            </tr>
            <tr>
                <td>Impuestos (IVA)</td>
                <td><?= $this->pml->money($row->total_tax); ?></td>
            </tr>
            <tr>
                <td>Valores extra</td>
                <td><?= $this->pml->money($row->total_extras); ?></td>
            </tr>
        </tbody>
    </table>


    <h3>Envío/Ubicación</h3>
    <table class="table bg-white">
        <tbody>
            <tr>
                <td>Ciudad</td>
                <td><?= $row->city ?></td>
            </tr>
            <tr>
                <td>Dirección</td>
                <td><?= $row->address ?></td>
            </tr>
            <tr>
                <td>Teléfono</td>
                <td><?= $row->phone_number ?></td>
            </tr>
            <tr>
                <td>Peso</td>
                <td><?= $row->total_weight ?> kg</td>
            </tr>
        </tbody>
    </table>
    <h3>Gestión</h3>
    <table class="table bg-white">
        <tbody>
            <tr>
                <td>Código Respuesta PayU</td>
                <td><?=  $row->response_code_pol ?></td>
            </tr>
            <tr>
                <td>No. factura</td>
                <td><?= $row->bill ?></td>
            </tr>
            <tr>
                <td>No. guía</td>
                <td><?= $row->shipping_code ?></td>
            </tr>
            <tr>
                <td>Notas internas</td>
                <td><?= $row->notes_admin ?></td>
            </tr>
        </tbody>
    </table>

    <h3>Productos</h3>

    <table class="table bg-white">
        <thead>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Precio</th>
            <th>Subtotal</th>
        </thead>
        <tbody>
            <?php foreach ( $products->result() as $row_product ) { ?>
                <tr>
                    <td>
                        <a href="<?= URL_ADMIN . "products/info/{$row_product->product_id}" ?>">
                            <?= $row_product->name ?>
                        </a>
                    </td>
                    <td><?= $row_product->quantity ?></td>
                    <td><?= $this->pml->money($row_product->price) ?></td>
                    <td><?= $this->pml->money($row_product->price * $row_product->quantity) ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>