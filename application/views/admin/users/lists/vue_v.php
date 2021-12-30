<script>
var user_lists = new Vue({
    el: '#user_lists',
    created: function(){
        //this.get_list()
    },
    data: {
        user_id: <?= $row->id ?>,
        user_lists: <?= json_encode($user_lists->result()) ?>,
        loading: false,
    },
    methods: {
        update_list: function(current_value, key_list){
            
            var add = 0
            if ( current_value == 0 ) { add = 1 }

            this.loading = true
            var form_data = new FormData()
            form_data.append('user_id', this.user_id)
            form_data.append('list_id', this.user_lists[key_list].id)
            form_data.append('add', add)
            axios.post(url_api + 'users/update_list/', form_data)
            .then(response => {
                if ( response.data.saved_id > 0 ) {
                    toastr['success']('Guardado')
                }
                if ( response.data.qty_deleted > 0 ) {
                    toastr['info']('Retirado de la lista')
                }
                this.loading = false
            })
            .catch( function(error) {console.log(error)} )
        },
    }
})
</script>