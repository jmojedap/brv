<?php
    $styles['main'] = "font-family: 'Trebuchet MS', Helvetica, sans-serif; text-align: center; background-color: #FAFAFA; padding: 20px;";
    $styles['h1'] = "color: #60C83C; margin-top: 50px;";
    $styles['h3'] = "";
    $styles['p'] = "";
    $styles['a'] = "";
    $styles['button'] = "padding: 10px; background-color: #60C83C; color: #FFF; text-decoration: none; display: block; max-width: 120px; margin: 0 auto;";
    $styles['footer'] = "margin-top: 50px; color: #AAAAAA; font-size: 0.7em";
?>
<?php
    $texts['title'] = 'Bienvenido a ' . APP_NAME;
    $texts['paragraph'] = 'Para activar tu cuenta haz clic en el siguiente enlace:';
    $texts['button'] = 'Activar';
    $texts['link'] = "accounts/activation/{$row_user->activation_key}";
    
    if ( $activation_type == 'recovery' ) 
    {
        $texts['title'] = APP_NAME;
        $texts['paragraph'] = 'Para reestablecer tu contraseña haz clic en el siguiente enlace:';
        $texts['button'] = 'Restaurar contraseña';
        $texts['link'] = "accounts/recover/{$row_user->activation_key}";
    }
?>
<div style="<?= $styles['main'] ?>">
    <h1 style="<?= $styles['h1'] ?>"><?= $texts['title'] ?></h1>
    <h3><?= $row_user->display_name ?></h3>
    <p><?= $texts['paragraph'] ?></p>
    <br>
    <a style="<?= $styles['button'] ?>" href="<?= URL_APP . $texts['link'] ?>" target="_blank">
        <?= $texts['button'] ?>
    </a>
    <footer style="<?= $styles['footer'] ?>">Powered by Pacarina Media Lab &middot; Colombia</footer>
</div>