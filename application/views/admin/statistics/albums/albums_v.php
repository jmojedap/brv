<?php
    $max_visits = $albums->row()->count_visits / 0.95;
?>

<h3>Visitas en los últimos 28 días</h3>

<table class="table bg-white">
    <thead>
        <th width="50px;">
            Álbum
        </th>
        <th>
            
        </th>
        <th width="70%">
            Visitas
        </th>
    </thead>
    <tbody>
        <?php foreach ( $albums->result() as $row_album ) { ?>
            <?php
                $att_img = $this->File_model->att_img($row_album->image_id, 'sm_');   
                $pct = $this->pml->percent($row_album->count_visits, $max_visits);
            ?>
            <tr>
                <td>
                    <a href="<?= URL_ADMIN . "albums/pictures/{$row_album->album_id}" ?>" class="">
                        <img class="rounded w50p" src="<?= $att_img['src'] ?>" alt="Imagen álbum">    
                    </a>
                </td>
                <td>
                    <?= $row_album->title; ?>
                    <br/>
                    <a href="<?= URL_ADMIN . "users/info/{$row_album->girl_id}" ?>" class="clase">
                        <?= $this->App_model->name_user($row_album->girl_id, 'd'); ?>
                    </a>
                </td>
                <td>
                    <div class="progress">
                        <div class="progress-bar"
                            role="progressbar"
                            style="width: <?= $pct ?>%;"
                            aria-valuenow="<?= $pct ?>"
                            aria-valuemin="0" aria-valuemax="100"
                            >
                            <?= $row_album->count_visits ?>
                        </div>
                    </div>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>