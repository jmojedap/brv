
<div id="comments_info">
    <div class="row">
        <div class="col-md-4">
            <table class="table bg-white">
                <tbody>
                    <tr>
                        <td class="text-right text-muted" style="width: 25%">ID</td>
                        <td>{{ row.id }}</td>
                    </tr>
                    <tr>
                        <td class="text-right text-muted">Comentario</td>
                        <td>{{ row.comment_text }}</td>
                    </tr>
                    <tr>
                        <td class="text-right text-muted">Tabla</td>
                        <td>{{ row.table_id }}</td>
                    </tr>
                    <tr>
                        <td class="text-right text-muted">ID Elemento</td>
                        <td>{{ row.element_id }}</td>
                    </tr>
                    <tr>
                        <td class="text-right text-muted">ID Padre</td>
                        <td>{{ row.parent_id }}</td>
                    </tr>
                    <tr>
                        <td class="text-right text-muted">Puntaje</td>
                        <td>{{ row.score }}</td>
                    </tr>
                    <tr>
                        <td class="text-right text-muted">Sub comentarios</td>
                        <td>{{ row.qty_comments }}</td>
                    </tr>
                    <tr>
                        <td class="text-right text-muted">ID usuario creador</td>
                        <td>{{ row.creator_id }}</td>
                    </tr>
                    <tr>
                        <td class="text-right text-muted">Creado</td>
                        <td>{{ row.created_at }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-md-8">
            <table class="table bg-white">
                <thead>
                    <th>Sub comentarios ({{ row.qty_comments }})</th>
                    <th>Usuario</th>
                    <th>ID Usuario</th>
                </thead>
                <tbody>
                    <tr v-for="(element, key) in list">
                        <td>{{ element.comment_text }}</td>
                        <td>{{ element.display_name }}</td>
                        <td>{{ element.creator_id }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
var comments_info = new Vue({
    el: '#comments_info',
    created: function(){
        //this.get_list()
    },
    data: {
        row: <?= json_encode($row) ?>,
        list: <?= json_encode($subcomments) ?>
    },
    methods: {
        
    }
})
</script>