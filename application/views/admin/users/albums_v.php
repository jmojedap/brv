<div id="albums_app" class="mb-2">
    <div class="card-deck">
        <div class="card" v-for="album in list" style="max-width: 19%;">
            <a v-bind:href="`<?= URL_ADMIN . "girls/album/{$row->id}/" ?>` + album.id" class="">
                <img
                    v-bind:src="album.src_cover"
                    class="card-img-top" alt="Album cover"
                    onerror="this.src='<?= URL_IMG ?>app/nd.png'"
                    >
            </a>
            <div class="card-body">
                <h5 class="card-title">{{ album.title }}</h5>
            </div>
        </div>
    </div>

    <div class="alert alert-info" v-show="quan_albums == 0">
        <strong><?= $row->first_name ?></strong> no tiene álbums creados todavía.
    </div>
</div>

<script>
new Vue({
    el: '#albums_app',
    created: function() {
        //this.get_list();
    },
    data: {
        list: <?= json_encode($albums) ?>,
        quan_albums: <?= count($albums) ?>
    },
    methods: {

    }
});
</script>