<body style="<?= $styles['body'] ?>">
    <p style="<?= $styles['text_center'] ?>">
        <a href="<?= URL_APP ?>accounts/login" target="_blank" title="Go to <?= APP_NAME ?>">
            <img src="<?= base_url() ?>resources/static/images/app/logo.png" alt="<?= APP_NAME ?>">
        </a>
    </p>
    <?php $this->load->view($view_a) ?>
    
    <footer style="<?= $styles['footer'] ?>">&copy; 2022 &middot; Creado por Pacarina Media Lab para Brave Functional Training &middot; Colombia</footer>
</body>