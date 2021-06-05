<?php
    $general_colors = array(
        array('name' => 'info','background' => '#00c0ef', 'font_color' => '#FFFFFF'),
        array('name' => 'info hover','background' => '#0ab6e0', 'font_color' => '#FFFFFF'),
        array('name' => 'primary','background' => '#3E8EF7', 'font_color' => '#FFFFFF'),
        array('name' => 'primary hover','background' => '#589FFC', 'font_color' => '#FFFFFF'),
        array('name' => 'success','background' => '#11c26d', 'font_color' => '#FFFFFF'),
        array('name' => 'success hover','background' => '#28d17c', 'font_color' => '#FFFFFF'),
        array('name' => 'warning','background' => '#fdd835', 'font_color' => '#FFFFFF'),
        array('name' => 'warning hover','background' => '#f1cd2d', 'font_color' => '#FFFFFF'),
        array('name' => 'danger','background' => '#FF4C52', 'font_color' => '#FFFFFF'),
        array('name' => 'danger hover','background' => '#FF666B', 'font_color' => '#FFFFFF')
    );

    $app_colors = array(
        array('name' => 'main','background' => '#60C83C', 'font_color' => '#FFFFFF'),
        array('name' => 'light','background' => '#D8DF9E', 'font_color' => '#000'),
        array('name' => 'dark','background' => '#39B44A', 'font_color' => '#FFF'),
        array('name' => 'darker','background' => '#458F2E', 'font_color' => '#FFF'),
        array('name' => 'secondary','background' => '#A81493', 'font_color' => '#FFFFFF'),
        array('name' => 'color-2','background' => '#3E8EF7', 'font_color' => '#FFFFFF'),
        array('name' => 'color-3','background' => '#11C26D', 'font_color' => '#FFFFFF'),
        array('name' => 'color-4','background' => '#FDD835', 'font_color' => '#FFFFFF'),
        array('name' => 'color-5','background' => '#00D9FF', 'font_color' => '#FFFFFF'),
        array('name' => 'color-6','background' => '#FF7B33', 'font_color' => '#FFFFFF'),
        array('name' => 'color-7','background' => '#F066FF', 'font_color' => '#FFFFFF'),
        array('name' => 'color-8','background' => '#FC3F71', 'font_color' => '#FFFFFF'),
    );

    $arr_classes = array(
        'light',
        'info',
        'primary',
        'success',
        'warning',
        'danger',
        'secondary',
    );
?>

<script>
    $(document).ready(function(){
        $('.btn').click(function(){
            console.log('mostrando')
            toastr['success']('El mensaje se guardó correctamenbe');
            toastr['error']('Ocurrió un error');
            toastr['info']('Estamos informando algo');
            toastr['warning']('Estamos informando algo');
        })
    })
</script>

<div class="row">
    <div class="col-md-4">
        <table class="table bg-white">
            <thead>
                <th>Color</th>
                <th></th>
            </thead>
            <?php foreach ( $general_colors as $color ) { ?>
                <tr>
                    <td><?= $color['name'] ?></td>
                    <td style="background-color: <?= $color['background'] ?>; color: <?= $color['font_color'] ?>"><?= $color['background'] ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>
    <div class="col-md-4">
        <h3>Colores aplicación</h3>
        <p>
        [
        <?php foreach ( $app_colors as $color ) : ?>
            '<?php echo $color['background'] ?>',
        <?php endforeach ?>
        ]
        </p>
        <table class="table bg-white">
            <thead>
                <th>Color</th>
                <th></th>
            </thead>
            <?php foreach ( $app_colors as $color ) { ?>
                <tr>
                    <td><?= $color['name'] ?></td>
                    <td style="background-color: <?= $color['background'] ?>; color: <?= $color['font_color'] ?>"><?= $color['background'] ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>
    <div class="col-md-4">
        <?php foreach ( $arr_classes as $class ) { ?>
            <button class="btn btn-<?= $class ?> btn-block"><?= $class ?></button>
        <?php } ?>
    </div>
</div>