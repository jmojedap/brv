<div class="row">
    <div class="col-md-4">
        <table class="table bg-white">
            <tbody>
                <tr>
                    <td></td>
                    <td>
                        <a href="<?= URL_ADMIN . "posts/open/{$row->id}/0/{$row->slug}" ?>" class="btn btn-light w120p" target="_blank">
                            Abrir
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>ID</td>
                    <td><?= $row->id ?></td>
                </tr>
                <tr>
                    <td>Tipo</td>
                    <td>Libro virtual</td>
                </tr>
                <tr>
                    <td>Título</td>
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
            </tbody>
        </table>

        <table class="table bg-white">
            <tbody>
                <tr>
                    <td>count comments</td>
                    <td><?= $row->count_comments ?></td>
                </tr>
                <tr>
                    <td>published at</td>
                    <td><?= $row->published_at ?></td>
                </tr>
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
                <h2><?= $row->post_name ?></h2>
                <div>
                    <h4 class="text-muted">Descripción</h4>
                    <?= $row->excerpt ?>
                </div>
            </div>
        </div>
    </div>
</div>