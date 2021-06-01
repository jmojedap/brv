

<table class="table bg-white">
    <thead>
        <th>Cód Suscripción</th>
        <th>Desde</th>
        <th>Hasta</th>
        <th>Vencimiento</th>
        <th>Valor</th>
        <th>Estado</th>
    </thead>
    <tbody>
        <?php foreach ( $suscriptions->result() as $row_suscription ) { ?>
            <?php
                $row_order = $this->Db_model->row('orders', $row_suscription->order_id);

                $date_status = 0;
                if ( $row_suscription->date_2 >= date('Y-m-d H:i:s') ) { $date_status = 1; }

                $date_status = ( $row_suscription->date_2 >= date('Y-m-d H:i:s') ) ? 1 : 0 ;
                $pay_status = ( $row_suscription->status == 1 ) ? 1 : 0 ;

                //General status
                $status = ( $date_status + $pay_status >= 2 ) ? 1 : 0 ;

                $remaining = '';
                if ( $row_suscription->date_2 > date('Y-m-d H:i:s') ) { $remaining = $this->pml->ago($row_suscription->date_2); }
                $remaining = $this->pml->ago($row_suscription->date_2);
            ?>
            <tr>
                <td><?= $row_order->order_code ?></td>
                <td><?= $this->pml->date_format($row_suscription->date_1, 'Y/M/d'); ?></td>
                <td><?= $this->pml->date_format($row_suscription->date_2, 'Y/M/d'); ?></td>
                <td>
                    <?= $remaining ?>
                </td>
                <td><?= $this->pml->money($row_order->amount); ?></td>
                <td>
                    <?php if ( $status ) { ?>
                        <i class="fa fa-check-circle text-success"></i>
                        <b class="text-success">
                            Activa
                        </b>
                    <?php } else { ?>
                        Vencida
                    <?php } ?>

                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>