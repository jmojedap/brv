<div id="responses_app">
    <div class="center_box_920">
        <div class="alert alert-info">
            Wompi ha enviado <strong>{{ responses.length }}</strong> respuestas a la URL de eventos:
        </div>
        <div class="card" v-for="(response, response_key) in responses">
            <div class="card-body">
                <h3 class="text-center">{{ response.data.transaction.id }}</h3>
                <h4 class="text-center text-muted">ID Transacción Wompi</h3>
                <p>
                    <span class="text-muted">Medio de pago:</span>
                    <span class="text-primary">{{ response.data.transaction.payment_method_type }}</span>
                    &middot;
                    <span class="text-muted">Respuesta Recibida:</span>
                    <span class="text-primary">{{ response.response_created_at }}</span>
                    <span class="">({{ response.response_created_at | ago }})</span>
                    &middot;
                    <span class="text-muted">Estado:</span>
                    <span class="text-primary">{{ response.data.transaction.status }}</span>
                    &middot;
                    <span class="text-muted">Mensaje Estado:</span>
                    <span class="text-primary">{{ response.data.transaction.status_message }}</span>
                    &middot;
                </p>
                <h5>Detalle Respuesta</h5>
                <pre style="border: 1px solid #CCC; padding: 1em; border-radius: 5px; color: #2196f3">{{ response }}</pre>
            </div>
        </div>
    </div>
</div>

<script>
// Filters
//-----------------------------------------------------------------------------
    Vue.filter('ago', function (date) {
        if (!date) return ''
        return moment(date, "YYYY-MM-DD HH:mm:ss").fromNow();
    });

    new Vue({
        el: '#responses_app',
        created: function(){
            //this.get_list();
        },
        data: {
            responses: <?= json_encode($responses) ?>
        },
        methods: {
            
        }
    });
</script>