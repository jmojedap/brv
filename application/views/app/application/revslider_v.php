<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <?php $this->load->view('assets/bootstrap') ?>

    <script src="<?= URL_RESOURCES ?>assets/slidr/slidr.min.js"></script>

    
</head>

<body>
    <div class="container">
        <h1>Hola</h1>

        <div id="slidr-img" style="display: inline-block">
            <img data-slidr="one" src="<?= URL_CONTENT ?>galleries/200081/public/00375-00290.jpg">
            <img data-slidr="two" src="<?= URL_CONTENT ?>galleries/200081/public/00375-00280.jpg">
            <img data-slidr="tres" src="<?= URL_CONTENT ?>galleries/200081/public/00375-00270.jpg">
        </div>
    </div>

    <script>
        slidr.create('slidr-img', {
            after: function(e) { console.log('in: ' + e.in.slidr); },
            before: function(e) { console.log('out: ' + e.out.slidr); },
            breadcrumbs: true,
            controls: 'corner',
            direction: 'horizontal',
            fade: true,
            keyboard: true,
            overflow: true,
            pause: false,
            theme: '#FAFE79',
            timing: { 'fade': '0.5s ease-in' },
            touch: true,
            transition: 'fade'
        }).start();
    </script>
</body>

</html>