<div class="row">
    <div class="col-md-3">
        <?php if ( $row->is_image ) { ?>
            <img class="rounded mb-2 w100pc" alt="imagen archivo" src="<?= $row->url ?>">
        <?php } ?>

        
    </div>
    <div class="col-md-9">
        <h3 class="mb-2"><?= $row->title ?></h3>
        <table class="table bg-white">
            <tbody>
                <tr>
                    <td class="td-title">ID</td>
                    <td><?= $row->id ?></td>
                </tr>
                <tr>
                    <td class="td-title">Título</td>
                    <td><?= $row->title ?></td>
                </tr>
                <tr>
                    <td class="td-title">Subtítulo</td>
                    <td><?= $row->subtitle ?></td>
                </tr>
                <tr>
                    <td class="td-title">Descripción</td>
                    <td><?= $row->description ?></td>
                </tr>
                <tr>
                    <td class="td-title">Palabras clave</td>
                    <td><?= $row->keywords ?></td>
                </tr>
                <tr>
                    <td class="td-title">Type</td>
                    <td><?= $row->type_id ?></td>
                </tr>
                <tr>
                    <td class="td-title">Carpeta upload</td>
                    <td><?= $row->folder ?></td>
                </tr>
                <tr>
                    <td class="td-title">Nombre archivo</td>
                    <td><?= $row->file_name ?></td>
                </tr>
                <tr>
                    <td class="td-title">Es imagen</td>
                    <td>
                        <?php if ( $row->is_image ) : ?>
                            Sí
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td class="td-title">Tamaño</td>
                    <td><?= $row->size ?> KB</td>
                </tr>
                <tr>
                    <td class="td-title">Dimensiones (px)</td>
                    <td><?= $row->width ?> x <?= $row->height ?></td>
                </tr>
                <tr>
                    <td class="td-title">Table ID &middot; Related ID</td>
                    <td><?= $row->table_id ?> <i class="fa fa-caret-right"></i> <?= $row->related_1 ?> </td>
                </tr>
            </tbody>
        </table>

        <table class="table bg-white">
            <tbody>
                <tr>
                    <td class="td-title">Actualizado por</td>
                    <td><?= $this->App_model->name_user($row->updater_id) ?></td>
                </tr>
                <tr>
                    <td class="td-title">Actualizado en</td>
                    <td>
                        <?= $row->updated_at ?>
                        &middot;
                        <?= $this->pml->ago($row->updated_at) ?>
                    </td>
                </tr>
                <tr>
                    <td class="td-title">Cargado por</td>
                    <td><?= $this->App_model->name_user($row->creator_id) ?></td>
                </tr>
                <tr>
                    <td class="td-title">Cargado en</td>
                    <td>
                        <?= $row->created_at ?>
                        &middot;
                        <?= $this->pml->ago($row->created_at) ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>