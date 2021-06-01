<?php
    $sum_orders = 0;
    foreach ( $credit_orders->result() as $row_order ) { $sum_orders += $row_order->amount; }

    $sum_pays = 0;
    foreach ( $payed_posts->result() as $row_post ) { $sum_pays += $row_post->price; }

    $credit = $sum_orders - $sum_pays;
    if ( $credit < 0 ) $credit = 0;
?>


<h3>
    Su saldo actual es: <?= $this->pml->money($credit); ?>
</h3>

<div class="row">
    <div class="col-md-6">
        <table class="table bg-white">
            <thead>
                <th>Pagos</th>
                <th>Valor</th>
            </thead>
            <tbody>
                <tr class="table-info">
                    <td>Total</td>
                    <td><strong><?= $this->pml->money($sum_orders); ?></strong></td>
                </tr>
                <?php foreach ( $credit_orders->result() as $row_order ) { ?>
                    <td><?= $row_order->order_code ?></td>
                    <td><?= $this->pml->money($row_order->amount); ?></td>
                <?php } ?>
                
            </tbody>
        </table>
    </div>
    <div class="col-md-6">
        <table class="table bg-white">
            <thead>
                <th>Contenidos</th>
                <th>Precio</th>
            </thead>
            <tbody>
                <tr class="table-warning">
                    <td>Total</td>
                    <td><strong><?= $this->pml->money($sum_pays);  ?></strong></td>
                </tr>
                <?php foreach ( $payed_posts->result() as $row_post ) { ?>
                <tr>
                    <td><?= $row_post->title ?></td>
                    <td><?= $this->pml->money($row_post->price) ?></td>
                </tr>
                    
                <?php } ?>
            </tbody>

        </table>
    </div>
</div>

