<div class="row">
    <div class="col-md-4">
        <table class="table bg-white">
            <tbody>
                <tr>
                    <td></td>
                    <td>
                        <a href="<?= URL_ADMIN . "products/info/{$row->id}/{$row->slug}" ?>" class="btn btn-light">
                            Vista previa
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>ID</td>
                    <td><?= $row->id ?></td>
                </tr>
                <tr>
                    <td>Referencia</td>
                    <td><?= $row->code ?></td>
                </tr>
                <tr>
                    <td>Precio de venta</td>
                    <td><?= $this->pml->money($row->price) ?></td>
                </tr>
                <tr>
                    <td>Nombre</td>
                    <td><?= $row->name ?></td>
                </tr>
                <tr>
                    <td>Estado</td>
                    <td><?= $row->status ?></td>
                </tr>
                <tr>
                    <td>Slug</td>
                    <td><?= $row->slug ?></td>
                </tr>
            </tbody>
        </table>

        <table class="table bg-white">
            <tbody>
                <tr>
                    <td>Actualizado por</td>
                    <td><?= $row->updater_id ?></td>
                </tr>
                <tr>
                    <td>Actualizado</td>
                    <td><?= $row->updated_at ?></td>
                </tr>
                <tr>
                    <td>Creado por</td>
                    <td><?= $row->creator_id ?></td>
                </tr>
                <tr>
                    <td>Creado</td>
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
                    <h4 class="text-muted">Descripción:</h4>
                    <?= $row->description ?>
                </div>
                <hr>
                <div>
                    <h4 class="text-muted">Palabras clave:</h4>
                    <?= $row->keywords ?>
                </div>
            </div>
        </div>
    </div>
</div>