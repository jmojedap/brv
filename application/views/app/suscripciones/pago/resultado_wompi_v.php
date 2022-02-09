<?php
    $elements['icon'] = 'fa fa-check-circle';
    $elements['class'] = 'text-success';

    if ( ! $success )
    {
        $success = FALSE;
        $elements['icon'] = 'fa fa-info-circle';
        $elements['class'] = 'text-info';
    }
?>

<div class="center_box_750 pt-2">
    <div class="text-center">
        <h1 class="head_title <?= $elements['class'] ?>">
            <i class="<?= $elements['icon'] ?>"></i>
            <?= $head_title ?>
        </h1>
    </div>

    <h3 class="text-center">Resultado transacción</h3>

    <table class="table bg-white">
        <tbody>
            <tr class="table-info">
                <td class="text-right" width="40%">Código compra</td>
                <td><?= $result->reference; ?></td>
            </tr>
            <tr>
                <td class="text-right">Fecha transacción</td>
                <td><?= $result->created_at; ?></td>
            </tr>
            <tr>
                <td class="text-right">Referencia Transacción Wompi</td>
                <td><?= $result->id; ?></td>
            </tr>
            <tr>
                <td class="text-right">Medio de pago</td>
                <td><?= $result->payment_method->type; ?></td>
            </tr>
            
            <tr>
                <td class="text-right">Valor</td>
                <td>
                    <?= $this->pml->money($result->amount_in_cents/100); ?>
                    <small>
                        <?= $result->currency; ?>
                    </small>
                </td>
            </tr>
        </tbody>
    </table>

    <hr>
    <!-- <?php if ( $order_code ) : ?>
        <div class="text-center">
            <a href="<?= URL_APP . "tienda/estado_compra/{$order_code}" ?>" class="btn btn-primary btn-lg">
                VER ESTADO COMPRA
            </a>
        </div>
    <?php endif; ?> -->
</div>
