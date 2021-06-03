<div id="edit_post">
    hola
</div>

<script>
// Variables
//-----------------------------------------------------------------------------

var row = <?= json_encode($row) ?>;

// VueApp
//-----------------------------------------------------------------------------
var edit_post = new Vue({
    el: '#edit_post',
    created: function(){
        //this.get_list()
    },
    data: {
        form_values: row,
        loading: false,
    },
    methods: {
        
    }
})
</script>