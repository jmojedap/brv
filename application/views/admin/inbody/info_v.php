<div class="center_box_750">
    <table class="table bg-white">
        <tbody>
            <?php foreach ( $row as $field_name => $field_value ) : ?>
                <tr>
                    <td class="td-title"><?= $field_name ?></td>
                    <td><?= $field_value ?></td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>