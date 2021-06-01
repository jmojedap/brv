<?php
    //$link_status = URL_APP . ) . "orders/estado/?order_code={$row_order->order_code}";
    $link_status = URL_APP . "orders/my_suscriptions";
?>


<body>
    <div style="<?= $style->body ?>">
        
        <table>
            <tr>
                <td style="width: 33%;">
                    <b style="<?= $style->text_info ?>"><?= $this->pml->money($row_order->amount) ?></b>
                    <span style="<?= $style->text_muted ?>">
                        <?= $this->Item_model->name(7, $row_order->status) ?>
                    </span>
                </td>
                <td style="text-align: left;">
                    <h4 style="<?= $style->h4 ?>"></h4>
                </td>
                <td style="text-align: right;">
                    <a href="<?= $link_status ?>" style="<?= $style->btn ?>" title="Ver compra en la página" target="_blank">
                        Ver compra
                    </a>
                </td>
            </tr>

            <tr>
                <td colspan="3" style="<?= $style->text_center ?>">

                    <h1 style="<?= $style->h1 ?>">
                        <?= $row_order->buyer_name ?>
                    </h1>
                </td>
            </tr>

            <tr style="<?= $style->text_center ?>">
                <td colspan="3">
                    <span style="<?= $style->text_muted?>">
                        Cód. compra:
                    </span>
                    <span style="<?= $style->text_danger ?>">
                        <?= $row_order->order_code ?>
                    </span>

                    <span style="<?= $style->text_muted?>">
                        |
                    </span>

                    <span style="<?= $style->text_muted?>">
                        Actualizado:
                    </span>
                    <span style="<?= $style->text_danger ?>">
                        <?= $this->pml->date_format($row_order->updated_at, 'Y-M-d H:i') ?>
                    </span>
                    <span style="<?= $style->text_muted?>">
                        |
                    </span>
                </td>
            </tr>
        </table>

        <h2 style="<?= $style->h2 ?>">Detalle de la compra</h2>

        <table style="<?= $style->table ?>">
            <thead style="<?= $style->thead ?>">
                <tr style="">
                    <td style="<?= $style->td ?>">Producto</td>
                    <td style="<?= $style->td ?>">Precio</td>
                    <td style="<?= $style->td ?>">Cantidad</td>
                    <td style="<?= $style->td ?>">
                        <?= $this->pml->money($row_order->amount) ?>
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
                            <?= $row_product->description ?>
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

        <h2 style="<?= $style->h2 ?>">Datos de entrega</h2>

        <p>
            <span style="<?= $style->text_muted ?>">
                No. documento
            </span>
            <span style="<?= $style->text_danger ?>">
                <?= $row_order->document_number ?>
            </span>

            |

            <span style="<?= $style->text_muted ?>">E-mail</span>
            <span style="<?= $style->text_danger ?>"><?= $row_order->email ?></span>

            |

            <span style="<?= $style->text_muted ?>">
                Ciudad
            </span>
            <span style="<?= $style->text_danger ?>">
                <?= $row_order->city ?>
            </span>

            |

            <span style="<?= $style->text_muted ?>">Dirección</span>
            <span style="<?= $style->text_danger ?>">
                <?= $row_order->address ?>
            </span>            

            |

            <span style="<?= $style->text_muted ?>">
                Teléfono
            </span>
            <span style="<?= $style->text_danger ?>">
                <?= $row_order->phone_number ?>
            </span>
        </p>

        <hr>

        <div style="<?= $style->text_center ?>">
            <h3>
                <?= APP_NAME ?><br>
            </h3>
            <p sytle="<?= $style->text_muted ?>">
                &copy; <?= date('Y') ?>
            </p>
        </div>

    </div>
</body>