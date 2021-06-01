<?php
    $filters['d1'] = $d1;
?>

<div class="row">
    <div class="col-md-2">
        <a class="btn btn-light btn-block" href="<?= URL_ADMIN . 'statistics/users' ?>">
            Último año
        </a>
        <a class="btn btn-light btn-block" href="<?= URL_ADMIN . "statistics/users/?days_ago=7" ?>">
            Una semana
        </a>
        <a class="btn btn-light btn-block" href="<?= URL_ADMIN . "statistics/users/?days_ago=2" ?>">
            Dos días
        </a>
    </div>
    <div class="col-md-10">
        <table class="table bg-white">
            <thead>
                <th>Usuario</th>
                <th>Cant Login</th>
                <th>Cant Perfiles</th>
                <th>Cant Albumes</th>
            </thead>

            <tbody>
                <?php foreach ( $users->result() as $user ) { ?>
                    <?php
                        $filters['u'] = $user->id;
                        
                        //Cantidad login
                        $filters['tp'] = 101;
                        $qty_login = $this->Event_model->qty_events($filters);

                        //Cantidad perfiles
                        $filters['tp'] = 52;
                        $qty_profiles = $this->Event_model->qty_events($filters);

                        //Cantidad álbumes
                        $filters['tp'] = 51;
                        $qty_albums = $this->Event_model->qty_events($filters);

                        $cl_row = ( $qty_login > 0) ? '' : 'd-none';
                    ?>
                    <tr class="<?= $cl_row ?>">
                        <td>
                            <?= $user->display_name ?>
                        </td>
                        <td>
                            <?= $qty_login ?>
                        </td>
                        <td>
                            <?= $qty_profiles ?>
                        </td>
                        <td>
                            <?= $qty_albums ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>  
    </div>
</div>