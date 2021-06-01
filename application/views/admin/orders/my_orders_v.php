<table class="table bg-white">
    <thead>
        <th>Ref. venta</th>
        <th width="10px"></th>
        <th>Descripción</th>
    </thead>
    <tbody>
        <?php foreach ( $orders->result() as $row_order ) { ?>
            <?php
                $icon = '';
                $cl_text = '';
                if ( $row_order->status == 1 )
                {
                    $icon = '<i class="fa fa-check-circle text-success"></i>';
                    $cl_text = 'text-success';
                }
            ?>
            
            <tr>
                <td>
                    <a href="<?= URL_ADMIN . "orders/status/{$row_order->order_code}" ?>" class="">
                        <?= $row_order->order_code ?>
                    </a>
                </td>
                <td>
                    <?= $icon ?>
                </td>
                <td>
                    <b><?= $this->Item_model->name(7, $row_order->status); ?></b>
                    <br>
                    Fecha:
                    <b><?= $this->pml->date_format($row_order->updated_at); ?></b>
                    <br>
                    Valor:
                    <b class="<?= $cl_text ?>"><?= $this->pml->money($row_order->amount) ?></b>
                    <br>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>