<div id="followers_app">
    <h2>Seguidores ({{ users.length }})</h2>
    <div class="center_box_750 bg-white">
        <div class="d-flex mb-2 border-bottom p-2" v-for="(user, uk) in users">
            <a v-bind:href="`<?= URL_ADMIN . "girls/instant/" ?>` + user.id + `/` + user.username" class="">
                <img
                    v-bind:src="user.url_thumbnail"
                    class="rounded rounded-circle w40p mr-3"
                    v-bind:alt="user.display_name"
                    onerror="this.src='<?= URL_IMG ?>users/sm_user.png'"
                >
            </a>
            <div>
                <a v-bind:href="`<?= URL_ADMIN . "girls/instant/" ?>` + user.id + `/` + user.username" class="">
                    {{ user.display_name }}
                </a>
            </div>
            <div class="ml-auto">
                <button type="button" class="btn btn-primary btn-sm w100p" v-on:click="toggle_follow(uk)" v-show="user.follow_status == 1">Siguiendo</button>
                <button type="button" class="btn btn-light btn-sm w100p" v-on:click="toggle_follow(uk)" v-show="user.follow_status == 2">Seguir</button>
            </div>
        </Siguiendo>
    </div>
</div>

<script>
var followers_app = new Vue({
    el: '#followers_app',
    created: function(){
        //this.get_list()
    },
    data: {
        users: <?= json_encode($users) ?>,
        loading: false,
    },
    methods: {
        toggle_follow: function(user_key){
            var user_id = this.users[user_key].id
            axios.get(url_api + 'follow/toggle/' + user_id)
            .then(response => {
                this.users[user_key].follow_status = response.data.status
            })
            .catch(function(error) { console.log(error) })
        },
    }
})
</script>