<?php
    $max_qty_events = 0;
    foreach ($events->result() as $row)
    {
        if ( $row->qty_events > $max_qty_events) {
            $max_qty_events = $row->qty_events;
        }
    }

    $arr_lapses = array(
        7 => '7 d',
        28 => '28 d',
        90 => '90 d',
        365 => '1 a',
    );
?>

<div class="center_box_750">
    <ul class="nav nav-pills justify-content-center mb-2">
        <?php foreach ( $arr_lapses as $lapse_days => $lapse_name ) { ?>
            <?php
                $cl_link = $this->pml->active_class($lapse_days, $qty_days, 'active');
            ?>
            <li class="nav-item">
                <a href="<?= URL_ADMIN . "events/summary/{$lapse_days}" ?>" class="nav-link <?= $cl_link ?> w2">
                    <?= $lapse_name ?>
                </a>
            </li>
        <?php } ?>
    </ul>

    <table class="table bg-white">
        <thead>
            <th>Tipo</th>
            <th>Cantidad</th>
        </thead>

        <tbody>
            <?php foreach ( $events->result() as $row ) { ?>
            <?php
                    $pct = $this->pml->percent($row->qty_events, $max_qty_events);    
                ?>
            <tr>
                <td><?= $row->event_type ?></td>
                <td>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width: <?= $pct ?>%"
                            aria-valuenow="<?= $pct ?>" aria-valuemin="0" aria-valuemax="100">
                            <?= $row->qty_events ?>
                        </div>
                    </div>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>