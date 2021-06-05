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
                                        <i class="fa fa-users fa-3x float-right text-primary"></i>
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
                                        <i class="fa fa-tags fa-3x float-right text-success"></i>
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
                                    </div>
                                    <div class="media-right media-middle">
                                        <i class="far fa-file-alt fa-3x float-right text-primary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-2">
                    <a href="<?= URL_ADMIN . "comments/explore" ?>">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media">
                                    <div class="media-body text-left w-100">
                                        <h3 class="text-color-2">{{ summary.posts.num_rows }}</h3>
                                        <span>Comentarios</span>
                                    </div>
                                    <div class="media-right media-middle">
                                        <i class="far fa-comment fa-3x float-right text-success"></i>
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