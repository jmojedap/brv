<div class="row">
    <div class="col-md-4">
        <table class="table bg-white">
            <tbody>
                <tr>
                    <td></td>
                    <td>
                        <a href="<?= URL_ADMIN . "products/details/{$row->id}" ?>" class="btn btn-light" target="_blank">
                            Vista previa
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>ID</td>
                    <td><?= $row->id ?></td>
                </tr>
                <tr>
                    <td>type_id</td>
                    <td><?= $row->type_id ?></td>
                </tr>
                <tr>
                    <td>name</td>
                    <td><?= $row->name ?></td>
                </tr>
                <tr>
                    <td>status</td>
                    <td><?= $row->status ?></td>
                </tr>
                <tr>
                    <td>slug</td>
                    <td><?= $row->slug ?></td>
                </tr>
                <tr>
                    <td>image_id</td>
                    <td><?= $row->image_id ?></td>
                </tr>
            </tbody>
        </table>

        <table class="table bg-white">
            <tbody>
                <tr>
                    <td>updater_id</td>
                    <td><?= $row->updater_id ?></td>
                </tr>
                <tr>
                    <td>updated_at</td>
                    <td><?= $row->updated_at ?></td>
                </tr>
                <tr>
                    <td>creator_id</td>
                    <td><?= $row->creator_id ?></td>
                </tr>
                <tr>
                    <td>created_at</td>
                    <td><?= $row->created_at ?></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <h2><?= $row->name ?></h2>
                <div>
                    <h4 class="text-muted">description:</h4>
                    <?= $row->description ?>
                </div>
                <hr>
                <div>
                    <h4 class="text-muted">meta:</h4>
                    <?= $row->meta ?>
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