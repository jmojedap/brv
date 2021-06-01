<div class="row">
    <div class="col-md-4">
        <table class="table bg-white">
            <tbody>
                <tr>
                    <td></td>
                    <td><a href="<?= URL_ADMIN . "posts/open/{$row->id}" ?>" class="btn btn-sm btn-light w120p" target="_blank">Abrir</a></td>
                </tr>
                <tr>
                    <td>ID</td>
                    <td><?= $row->id ?></td>
                </tr>
                <tr>
                    <td>Tipo</td>
                    <td><?= $row->type_id ?> &middot; <?= $this->Item_model->name(33, $row->type_id) ?> </td>
                </tr>
                <tr>
                    <td>Nombre post</td>
                    <td><?= $row->post_name ?></td>
                </tr>
                <tr>
                    <td>Status</td>
                    <td><?= $row->status ?></td>
                </tr>
                <tr>
                    <td>slug</td>
                    <td><?= $row->slug ?></td>
                </tr>
                <tr>
                    <td>ID imagen principal</td>
                    <td><?= $row->image_id ?></td>
                </tr>
            </tbody>
        </table>

        <table class="table bg-white">
            <tbody>
                <tr>
                    <td>qty comments</td>
                    <td><?= $row->qty_comments ?></td>
                </tr>
                <tr>
                    <td>Publicado</td>
                    <td><?= $row->published_at ?></td>
                </tr>
                <tr>
                    <td>Actualizado por</td>
                    <td><?= $row->updater_id ?> &middot; <?= $this->App_model->name_user($row->updater_id, 'u') ?></td>
                </tr>
                <tr>
                    <td>Actualizado</td>
                    <td><?= $row->updated_at ?> &middot; <?= $this->pml->ago($row->updated_at) ?></td>
                </tr>
                <tr>
                    <td>Creador</td>
                    <td><?= $row->creator_id ?> &middot; <?= $this->App_model->name_user($row->creator_id, 'u') ?></td>
                </tr>
                <tr>
                    <td>Creado</td>
                    <td><?= $row->created_at ?> &middot; <?= $this->pml->ago($row->created_at) ?></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <h2><?= $row->post_name ?></h2>
                <div>
                    <h4 class="text-muted">excerpt</h4>
                    <?= $row->excerpt ?>
                </div>
                <hr>
                <div>
                    <h4 class="text-muted">content</h4>
                    <?= $row->content ?>
                </div>
                <hr>
                <div>
                    <h4 class="text-muted">content json</h4>
                    <?= $row->content_json ?>
                </div>
                <hr>
                <div>
                    <h4 class="text-muted">keywords:</h4>
                    <?= $row->keywords ?>
                </div>
            </div>
        </div>
    </div>
</div>