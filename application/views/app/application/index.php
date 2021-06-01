<?php
    $url = 'https://www.vebonit.com/site/girls/';
    $seconds = 0;
?>

<html>
<head>
    <title>VeBonit | Cargando</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <?= "<meta http-equiv='Refresh' content='{$seconds};{$url}'>" ?>
    
    <link href="<?= URL_RESOURCES ?>css/style.css" rel="stylesheet" type="text/css" />
    
</head>

<body style="font-family: arial; font-size: 0.8em">
    <span class="suave">Procesando...</span>
    <br/>
    <span class="suave">
        <?= $msg_redirect; ?>
    </span>

</body>
</html>
