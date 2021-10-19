<?php
    $period_id = date('Ymd');
?>

<div id="summary_app">
    <div class="center_box_750">
        <div class="row">
            <div class="col-md-6">
                <a href="<?= URL_ADMIN . "users/explore" ?>">
                    <div class="card mb-2">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media">
                                    <div class="media-body text-left w-100">
                                        <h3 class="text-color-2">{{ summary.users.num_rows }}</h3>
                                        <span>Usuarios</span>
                                    </div>
                                    <div class="media-right media-middle">
                                        <i class="fa fa-users fa-3x float-right color-text-1"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-6">
                <div class="card mb-2">
                    <a href="<?= URL_ADMIN . "products/explore" ?>">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media">
                                    <div class="media-body text-left w-100">
                                        <h3 class="text-color-2">{{ summary.products.num_rows }}</h3>
                                        <span>Productos activos</span>
                                    </div>
                                    <div class="media-right media-middle">
                                        <i class="fa fa-tags fa-3x float-right color-text-2"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <!-- ENTRENAMIENTOS -->
            <div class="col-md-6">
                <div class="card mb-2">
                    <a href="<?= URL_ADMIN . "calendar/calendar" ?>">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media">
                                    <div class="media-body text-left w-100">
                                        <h3 class="text-color-2">{{ summary.trainings.num_rows }}</h3>
                                        <span>Sesiones de entrenamiento</span>
                                        <p class="text-muted">Programados en los próximos 7 días</p>
                                    </div>
                                    <div class="media-right media-middle">
                                        <i class="fas fa-dumbbell fa-3x float-right color-text-3"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <!-- RESERVACIONES -->
            <div class="col-md-6">
                <div class="card mb-2">
                    <a href="<?= URL_ADMIN . "calendar/calendar" ?>">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media">
                                    <div class="media-body text-left w-100">
                                        <h3 class="text-color-2">{{ summary.reservations.num_rows }}</h3>
                                        <span>Reservas de entrenamiento</span>
                                        <p class="text-muted">En los próximos 7 días</p>
                                    </div>
                                    <div class="media-right media-middle">
                                        <i class="far fa-calendar-check fa-3x float-right color-text-4"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- CITAS PROGRAMADAS -->
            <div class="col-md-6">
                <div class="card mb-2">
                    <a href="<?= URL_ADMIN . "calendar/calendar/{$period_id}/appointments" ?>">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media">
                                    <div class="media-body text-left w-100">
                                        <h3 class="text-color-2">{{ summary.appointments.qty_scheduled }}</h3>
                                        <span>Citas programadas</span>
                                        <p class="text-muted">Programados en los próximos 7 días</p>
                                    </div>
                                    <div class="media-right media-middle">
                                        <i class="fas fa-user-md fa-3x float-right color-text-5"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <!-- RESERVACIONES -->
            <div class="col-md-6">
                <div class="card mb-2">
                    <a href="<?= URL_ADMIN . "calendar/calendar/{$period_id}/appointments" ?>">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media">
                                    <div class="media-body text-left w-100">
                                        <h3 class="text-color-2">{{ summary.appointments.qty_reserved }}</h3>
                                        <span>Citas reservadas</span>
                                        <p class="text-muted">En los próximos 7 días</p>
                                    </div>
                                    <div class="media-right media-middle">
                                        <i class="far fa-calendar-check fa-3x float-right color-text-6"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-2">
                    <a href="<?= URL_ADMIN . "inbody/explore" ?>">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media">
                                    <div class="media-body text-left w-100">
                                        <h3 class="text-color-2">{{ summary.inbody.num_rows }}</h3>
                                        <span>Mediciones InBody</span>
                                        <p class="text-muted">Total mediciones de InBody cargadas</p>
                                    </div>
                                    <div class="media-right media-middle">
                                        <i class="fa fa-weight fa-3x float-right color-text-1"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-2">
                    <a href="<?= URL_ADMIN . "posts/explore" ?>">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media">
                                    <div class="media-body text-left w-100">
                                        <h3 class="text-color-2">{{ summary.posts.num_rows }}</h3>
                                        <span>Publicaciones</span>
                                        <p class="text-muted">Publicaciones de usuarios y Brave</p>
                                    </div>
                                    <div class="media-right media-middle">
                                        <i class="far fa-file-alt fa-3x float-right color-text-4"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>    
</div>

<script>
var summary_app = new Vue({
    el: '#summary_app',
    created: function(){
        //this.get_list()
    },
    data: {
        summary: <?= json_encode($summary) ?>,
        loading: false,
    },
    methods: {
        
    }
})
</script>