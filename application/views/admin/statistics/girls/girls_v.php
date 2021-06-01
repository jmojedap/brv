<?php
    $max_visits = 100;
    if ( $girls->num_rows() > 0 ) {
        $max_visits = $girls->row()->count_visits / 0.95;
    }
?>

<table class="table bg-white">
    <thead>
        <th width="50px;">
            Girl
        </th>
        <th>
            
        </th>
        <th width="70%">
            Visitas
        </th>
    </thead>
    <tbody>
        <?php foreach ( $girls->result() as $row_girl ) { ?>
            <?php
                $src_user = $this->App_model->src_img_user($row_girl, 'sm_');   
                $pct = $this->pml->percent($row_girl->count_visits, $max_visits);
            ?>
            <tr>
                <td>
                    <img class="rounded" src="<?= $src_user ?>" alt="Imagen Girl">
                </td>
                <td>
                    <?= $this->App_model->name_user($row_girl->girl_id) ?>
                </td>
                <td>
                    <div class="progress">
                        <div class="progress-bar"
                            role="progressbar"
                            style="width: <?= $pct ?>%;"
                            aria-valuenow="<?= $pct ?>"
                            aria-valuemin="0" aria-valuemax="100"
                            >
                            <?= $row_girl->count_visits ?>
                        </div>
                    </div>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>