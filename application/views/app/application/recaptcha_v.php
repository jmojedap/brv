<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ReCaptcha 3</title>
</head>
<body>
        <form accept-charset="utf-8" method="POST" id="form_id" action="<?= URL_ADMIN . 'app/test_recaptcha' ?>">
            <?php $this->load->view('assets/recaptcha') ?>
            <input
                type="text"
                id="field-campo"
                name="campo"
                required
                class="form-control"
                placeholder="titulo"
                title="titulo"
                value="mi nombre es"
                >
                <br>

            <input type="submit" value="enviar">
        </form>
</body>
</html>