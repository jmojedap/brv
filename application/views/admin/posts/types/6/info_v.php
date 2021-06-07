<?php
    $creator = $this->Db_model->row_id('users', $row->creator_id);
?>

<div class="row">
    <div class="col-md-4">
        <div class="card mb-2">
            <div class="card-body">
            <div class="media">
                <a href="<?= URL_ADMIN . "users/profile/{$creator->id}" ?>">
                    <img src="<?= $creator->url_thumbnail ?>" class="w40p rounded rounded-circle mr-3" alt="...">
                </a>
                <div class="media-body">
                    <a href="<?= URL_ADMIN . "users/profile/{$creator->id}" ?>" class="link-bold">
                        <?= $creator->display_name ?>
                    </a>
                    <br>
                    <span class="text-muted"><?= $creator->username ?></span>
                </div>
                </div>
            </div>
            <img class="w100pc" src="<?= $row->url_image ?>" alt="Imagen publicación">
            <div class="card-body">
                <div>
                    <?= $row->excerpt ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <table class="table bg-white">
            <tbody>
                <tr>
                    <td class="td-title">ID</td>
                    <td><?= $row->id ?></td>
                </tr>
                <tr>
                    <td class="td-title">Tipo</td>
                    <td>Imagen</td>
                </tr>
                <tr>
                    <td class="td-title">Título</td>
                    <td><?= $row->post_name ?></td>
                </tr>
                <tr>
                    <td class="td-title">Status</td>
                    <td><?= $row->status ?></td>
                </tr>
            </tbody>
        </table>

        <table class="table bg-white">
            <tbody>
                <tr>
                    <td class="td-title">Comentarios</td>
                    <td><?= $row->qty_comments ?></td>
                </tr>
                <tr>
                    <td class="td-title">Publicada</td>
                    <td><?= $row->published_at ?></td>
                </tr>
                <tr>
                    <td class="td-title">Creada por</td>
                    <td>
                        <?= $row->updater_id ?> &middot;
                        <a href="<?= URL_ADMIN . "users/profile/{$row->creator_id}" ?>">
                            <?= $this->App_model->name_user($row->creator_id); ?>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td class="td-title">Creada en</td>
                    <td>
                        <?= $row->created_at ?> <br>
                        <?= $this->pml->ago($row->created_at); ?>
                    </td>
                </tr>
                <tr>
                    <td class="td-title">Actualizada por</td>
                    <td>
                        <?= $row->updater_id ?> &middot;
                        <a href="<?= URL_ADMIN . "users/profile/{$row->updater_id}" ?>">
                            <?= $this->App_model->name_user($row->updater_id); ?>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td class="td-title">Actualizada en</td>
                    <td>
                        <?= $row->updated_at ?> <br>
                        <?= $this->pml->ago($row->updated_at); ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>