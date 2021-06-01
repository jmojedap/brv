<!-- Modal -->
<div class="modal fade" id="modal_signup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">¿Ya tienes cuenta de usuario?</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="text-center">Debes iniciar sesión para continuar</p>
            </div>
            <div class="modal-footer">
                <a href="<?= URL_ADMIN . "accounts/signup" ?>" class="btn btn-light">
                    Registrarme
                </a>
                <a href="<?= URL_ADMIN . "accounts/login" ?>" class="btn btn-main">
                    Iniciar sesión
                </a>
            </div>
        </div>
    </div>
</div>