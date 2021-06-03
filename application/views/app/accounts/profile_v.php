<div class="center_box_750 text-center">
    <img src="<?= $row->url_image ?>" alt="Imagen de perfil del usuario" class="w120p rounded rounded-circle border mb-2" onerror="this.src='<?= URL_IMG ?>users/user.png'">
    <table class="table bg-white text-left">
        <tbody>
            <tr>
                <td width="25%"><span class="text-muted">No. Documento</span></td>
                <td>
                    <?= $row->document_number ?>
                </td>
            </tr>

            <tr>
                <td><span class="text-muted">Nombre</span></td>
                <td><?= $row->display_name ?></td>
            </tr>

            <tr>
                <td><span class="text-muted">Nombre de usuario</span></td>
                <td><?= $row->username ?></td>
            </tr>

            <tr>
                <td><span class="text-muted">Correo electrónico</span></td>
                <td><?= $row->email ?></td>
            </tr>

            <tr>
                <td><span class="text-muted">Sexo</span></td>
                <td><?= $this->Item_model->name(59, $row->gender) ?></td>
            </tr>

            <tr>
                <td><span class="text-muted">Rol de usuario</span></td>
                <td><?= $this->Item_model->name(58, $row->role) ?></td>
            </tr>

            <tr>
                <td><span class="text-muted">Fecha de nacimiento</span></td>
                <td><?= $this->pml->date_format($row->birth_date, 'Y-M-d') ?></td>
            </tr>
        </tbody>
    </table>
</div>