<?php
    $link_status = URL_APP . "tienda/estado_compra/$row_order->order_code";
    //$link_status = base_url("orders/my_suscriptions");
?>

<div>
    <table>
        <tr>
            <td width="50%">
                <b style="<?= $style->text_main ?>; font-size: 1.2em;"><?= $this->pml->money($row_order->amount) ?></b>
                <span style="<?= $style->text_muted ?>">
                    <?= $this->Item_model->name(7, $row_order->status) ?>
                </span>
            </td>
            <td></td>
            <td style="text-align: right;" width="50%">
                <!-- <a href="<?= $link_status ?>" style="<?= $style->btn ?>" title="Ver compra en la página" target="_blank">
                    Ver compra
                </a> -->
            </td>
        </tr>
    </table>

    <h2 style="<?= $style->h2 ?>">Venta <?= $row_order->order_code ?></h2>

    <table style="<?= $style->table ?>">
        <tbody>
            <tr>
                <td style="<?= $style->td ?>" width="35%">Cód. venta</td>
                <td style="<?= $style->td ?>"><?= $row_order->order_code ?></td>
            </tr>
            <tr>
                <td style="<?= $style->td ?>">Estado pago</td>
                <td style="<?= $style->td ?>"><?= $this->Item_model->name(7, $row_order->status) ?></td>
            </tr>
            <tr>
                <td style="<?= $style->td ?>">Canal de pago</td>
                <td style="<?= $style->td ?>"><?= $this->Item_model->name(106, $row_order->payment_channel) ?></td>
            </tr>
            <tr>
                <td style="<?= $style->td ?>">Fecha confirmado</td>
                <td style="<?= $style->td ?>"><?= $this->pml->date_format($row_order->confirmed_at, 'd/M/Y') ?></td>
            </tr>
            <tr>
                <td style="<?= $style->td ?>">Valor total</td>
                <td style="<?= $style->td ?>"><?= $this->pml->money($row_order->amount) ?></td>
            </tr>
            <?php if ( strlen($row_order->bill) ) : ?>
                <tr>
                    <td style="<?= $style->td ?>">No. factura</td>
                    <td style="<?= $style->td ?>"><?= $row_order->bill ?></td>
                </tr>
            <?php endif; ?>
            <?php if ( $row_order->total_weight > 0 ) : ?>
                <tr>
                    <td style="<?= $style->td ?>">Estado de envío</td>
                    <td style="<?= $style->td ?>"><?= $this->Item_model->name(187, $row_order->shipping_status) ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <h2 style="<?= $style->h2 ?>">Productos</h2>

    <table style="<?= $style->table ?>">
        <thead style="<?= $style->thead ?>">
            <tr style="">
                <td style="<?= $style->td ?>">Producto</td>
                <td style="<?= $style->td ?>">Precio</td>
                <td style="<?= $style->td ?>">Cantidad</td>
                <td style="<?= $style->td ?>">
                    <?= $this->pml->money($row_order->total_products) ?>
                </td>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products->result() as $row_product) : ?>
                <?php
                    $precio_detalle = $row_product->quantity * $row_product->price;
                ?>
                <tr>
                    <td style="<?= $style->td ?>">
                        <?= $row_product->name ?>
                    </td>
                    <td style="<?= $style->td ?>">
                        <p>
                            <?= $this->pml->money($row_product->price) ?>
                        </p>
                    </td>
                    <td style="<?= $style->td ?>">
                        <p>
                            <?= $row_product->quantity ?>
                        </p>
                    </td>
                    <td style="<?= $style->td ?>">
                        <?= $this->pml->money($precio_detalle) ?>
                    </td>

                </tr>
            <?php endforeach ?>
        </tbody>
    </table>

    <?php if ( $extras->num_rows() > 0 ) : ?>
        <h2 style="<?= $style->h2 ?>">Otros</h2>

        <table style="<?= $style->table ?>">
            <thead style="<?= $style->thead ?>">
                <tr style="">
                    <td style="<?= $style->td ?>">Total extras</td>
                    <td style="<?= $style->td ?>">
                        <?= $this->pml->money($row_order->total_extras) ?>
                    </td>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($extras->result() as $row_extra) : ?>
                    <?php
                        $precio_extra = $row_extra->quantity * $row_extra->price;
                    ?>
                    <tr>
                        <td style="<?= $style->td ?>">
                            <?= $row_extra->extra_name ?>
                        </td>
                        <td style="<?= $style->td ?>">
                            <?= $this->pml->money($precio_extra) ?>
                        </td>

                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>        
    <?php endif; ?>

    <p>
        <span style="<?= $style->text_muted ?>">
            Comprador
        </span>
        <span style="<?= $style->text_main ?>">
            <?= $row_order->buyer_name ?>
        </span>

        &middot;
        <span style="<?= $style->text_muted ?>">
            No. documento
        </span>
        <span style="<?= $style->text_main ?>">
            <?= $row_order->document_number ?>
        </span>

        &middot;

        <span style="<?= $style->text_muted ?>">E-mail</span>
        <span style="<?= $style->text_main ?>"><?= $row_order->email ?></span>

        &middot;

        <span style="<?= $style->text_muted ?>">
            Ciudad
        </span>
        <span style="<?= $style->text_main ?>">
            <?= $row_order->city ?>
        </span>

        &middot;

        <span style="<?= $style->text_muted ?>">Dirección</span>
        <span style="<?= $style->text_main ?>">
            <?= $row_order->address ?>
        </span>            

        &middot;

        <span style="<?= $style->text_muted ?>">
            Teléfono
        </span>
        <span style="<?= $style->text_main ?>">
            <?= $row_order->phone_number ?>
        </span>


    </p>
</div>