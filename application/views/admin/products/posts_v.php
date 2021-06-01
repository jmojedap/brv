<?php
    $arr_posts = array();

    foreach ($posts->result() as $post)
    {
        //$att_img = $this->File_model->att_img($post->imagen_id, '500px_');
        $post->img_src = URL_CONTENT . 'books/covers/' . $post->slug . '.jpg';    ;
        $post->disponible = $this->pml->ago($post->published_at);
        $post->published_at_nice = $this->pml->date_format($post->published_at, 'M-d');
        $arr_posts[] = $post;
    }
?>

<style>
    .cover_post{
        border: 1px solid #DDD;
        max-width: 120px;
        -webkit-border-radius: 2px;
        -moz-border-radius: 2px;
        border-radius: 2px;
        -webkit-box-shadow: 5px 5px 5px 0px rgba(227,227,227,1);
        -moz-box-shadow: 5px 5px 5px 0px rgba(227,227,227,1);
        box-shadow: 5px 5px 5px 0px rgba(227,227,227,1);
    }

    .cover_post:hover{
        border: 1px solid #AAA;
    }
</style>

<div id="product_posts" class="center_box_750">
    <?php if ( in_array($this->session->userdata('role'), array(1,2)) ) { ?>
        <div class="card mb-2">
            <div class="card-body">
                <form accept-charset="utf-8" method="POST" id="posts_form" @submit.prevent="add_post" clas="form-horizontal">
                    <div class="form-group row">
                        <label for="post_id" class="col-md-2 col-form-label text-right">Contenido</label>
                        <div class="col-md-8">
                            <input
                                name="post_id" id="field-post_id" type="text" class="form-control"
                                required
                                title="ID Post" placeholder="ID Post"
                                v-model="post_id"
                            >
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary btn-block" type="submit">
                                Agregar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <?php } ?>

    <div class="card mb-3" v-for="(post, key) in posts">
        <div class="d-flex flex-row">
            <div>
                <a v-bind:href="`<?= URL_ADMIN . "books/read/" ?>` + `/` + post.code + `/` + post.meta_id + `/` + post.slug">
                    <img v-bind:src="post.img_src" class="card-img" alt="Post cover" style="max-width: 120px;">
                </a>
            </div>
            <div>
                <div class="card-body">
                    <h5 class="card-title">{{ posts.id }} - {{ post.title }}</h5>
                    <p>
                        <a class="btn btn-success w75p" v-bind:href="`<?= URL_ADMIN . "posts/info/" ?>` + `/` + posts.id">
                            Abrir
                        </a>
                        <?php if ( in_array($this->session->userdata('role'), array(1,2)) ) { ?>
                            <button class="btn btn-warning w75p" v-on:click="remove_post(posts.id, post.meta_id)">
                                Quitar
                            </button>
                        <?php } ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    new Vue({
        el: '#product_posts',
        created: function(){
            //this.get_list();
        },
        data: {
            product_id: <?= $row->id ?>,
            posts: <?= json_encode($arr_posts) ?>,
            post_id: ''
        },
        methods: {
            add_post: function(){
                axios.get(url_app + 'products/add_post/' + this.product_id + '/' + this.post_id)
                .then(response => {
                    console.log(response.data)
                    window.location = url_app + 'products/posts/' + this.product_id;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            remove_post: function(post_id, meta_id){
                axios.get(url_app + 'products/delete_meta/' + this.product_id + '/' + meta_id)
                .then(response => {
                    console.log(response.data)
                    window.location = url_app + 'products/posts/' + this.product_id;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
        }
    });
</script>