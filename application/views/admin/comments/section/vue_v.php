<script>
// Filtros
//-----------------------------------------------------------------------------
Vue.filter('ago', function (date) {
    if (!date) return ''
    return moment(date, "YYYY-MM-DD HH:mm:ss").fromNow()
})

// VueApp
//-----------------------------------------------------------------------------
var comments_section = new Vue({
    el: '#comments_section',
    created: function(){
        this.get_comments()
    },
    data: {
        table_id: <?= $table_id ?>,
        element_id: <?= $row->id ?>,
        form_values: {
            parent_id: 0,
            comment_text: '',
        },
        max_page: 1,
        num_page: 1,
        comments: [],
        current: {},
        current_key: 0,
        loading: false,
    },
    methods: {
        get_comments: function(){
            axios.get(url_api + 'comments/element_comments/' + this.table_id + '/' + this.element_id)
            .then(response => {
                this.comments = response.data.comments
                this.max_page = response.data.max_page
            }).catch(function(error) {console.log(error)})
        },
        save_comment: function(){
            this.loading = true
            var form_data = new FormData(document.getElementById('comment_form'))
            axios.post(url_api + 'comments/save/' + this.table_id + '/' + this.element_id, form_data)
            .then(response => {
                this.reset_form()
                this.get_comments()
                this.loading = false
            })
            .catch( function(error) {console.log(error)} )
        },
        reset_form: function(){
            this.form_values.comment_text = ''
        },
        get_more_comments: function(){
            this.num_page += 1
            axios.get(url_api + 'comments/element_comments/' + this.table_id + '/' + this.element_id + '/0/' + this.num_page)
            .then(response => {
                this.comments = this.comments.concat(response.data.comments)
            }).catch(function(error) {console.log(error)})
        },
        set_current: function(key){
            this.current_key = key
            this.current = this.comments[key]
        },
        delete_comment: function(){
            axios.get(url_api + 'comments/delete/' + this.current.id + '/' + this.element_id)
            .then(response => {
                if ( response.data.qty_deleted > 0 ) {
                    $('#comment_' + this.current.id).hide('slow')
                    console.log('ocultando: ', '#comment_' + this.current.id )
                }
            })
            .catch(function (error) { console.log(error) })
        },
        alt_like: function(key){
            axios.get(url_api + 'comments/alt_like/' + this.comments[key].id)
            .then(response => {
                this.comments[key].liked = response.data.like_status
                this.comments[key].score = parseInt(this.comments[key].score) + parseInt(response.data.qty_sum)
            })
            .catch(function(error) { console.log(error) })
        },
    }
})
</script>