<div id="followers_app">
    <h2>Seguido por {{ user.qty_followers }}</h2>
    <div class="center_box_750">
        <div class="bg-white">
            <div class="d-flex mb-2 border-bottom p-2" v-for="(user, uk) in users">
                <a v-bind:href="`<?= URL_ADMIN . "users/profile/" ?>` + user.id + `/` + user.username" class="">
                    <img
                        v-bind:src="user.url_thumbnail"
                        class="rounded rounded-circle w40p mr-3"
                        v-bind:alt="user.display_name"
                        onerror="this.src='<?= URL_IMG ?>users/sm_user.png'"
                    >
                </a>
                <div>
                    <a v-bind:href="`<?= URL_ADMIN . "users/profile/" ?>` + user.id + `/` + user.username" class="user_link">
                        <span class="username">{{ user.username }}</span><br>
                        <span class="display_name">{{ user.display_name }}</span>
                    </a>
                </div>
                <div class="ml-auto">
                    <button type="button" class="btn btn-primary btn-sm w100p" v-on:click="toggle_follow(uk)" v-show="user.follow_status == 1">Siguiendo</button>
                    <button type="button" class="btn btn-light btn-sm w100p" v-on:click="toggle_follow(uk)" v-show="user.follow_status == 2">Seguir</button>
                </div>
            </div>
        </div>
        <div class="text-center" v-show="user.qty_followers > users.length">
            <p> {{ user.qty_followers - users.length }} más</p>
            <button class="btn btn-light w120p" v-on:click="load_more" v-show="!loading">
                Cargar
            </button>
            <button class="btn btn-light w120p" v-show="loading">
                <span><i class="fa fa-spin fa-spinner"></i></span> Cargando
            </button>
        </div>
    </div>
</div>

<script>
var followers_app = new Vue({
    el: '#followers_app',
    created: function(){
        this.get_list()
    },
    data: {
        user: {
            id: <?= $row->id ?>,
            qty_followers: <?= $row->qty_followers ?>,
        },
        num_page: 1,
        per_page: <?= $per_page ?>,
        max_page: <?= $max_page ?>,
        users: [],
        loading: false,
    },
    methods: {
        get_list: function(){
            this.loading = true
            axios.get(url_api + 'follow/get_followers/' + this.user.id + '/' + this.num_page + '/' + this.per_page)
            .then(response => {
                this.users = this.users.concat(response.data.users)
                this.loading = false
            })
            .catch(function(error) { console.log(error) })
        },
        toggle_follow: function(user_key){
            var followed_id = this.users[user_key].id
            axios.get(url_api + 'follow/toggle/' + followed_id)
            .then(response => {
                this.users[user_key].follow_status = response.data.status
            })
            .catch(function(error) { console.log(error) })
        },
        load_more: function(){
            this.num_page++
            this.get_list()
        },
    }
})
</script>