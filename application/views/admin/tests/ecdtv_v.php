<div id="screens_app">
    <h1>Preguntas</h1>
    <table class="table bg-white">
        <thead>
            <th>id</th>
            <th>Texto</th>
        </thead>
        <tbody>
            <tr v-for="(pregunta, pregunta_key) in preguntas">
                <td>{{ pregunta.id }}</td>
                <td>{{ pregunta.texto }}</td>
            </tr>
        </tbody>
    </table>

    <hr>

    <h1>Opciones</h1>
    <table class="table bg-white">
        <thead>
            <th>id</th>
            <th>Pregunta</th>
            <th>key</th>
            <th>Respuesta</th>
        </thead>
        <tbody>
            <tr v-for="(option, option_key) in options">
                <td>{{ option.id }}</td>
                <td>{{ option.question }}</td>
                <td>{{ option.key }}</td>
                <td>{{ option.text }}</td>
            </tr>
        </tbody>
    </table>
</div>

<script>
    new Vue({
        el: '#screens_app',
        created: function(){
            //this.get_list();
        },
        data: {
            options: <?= json_encode($options) ?>,
            preguntas: <?= json_encode($preguntas) ?>,
        },
        methods: {
            
        }
    });
</script>