<div id="conversation_app" class="page">
    <div class="row">
        <div class="col-md-4">
        <div class="list-group">
            <a
                href="#"
                class="list-group-item list-group-item-action"
                v-for="(conversation, key) in conversations"
                v-on:click="set_current(key)"
                >
                {{ conversation.title }}
            </a>
        </div>
        </div>
        <div class="col-md-8">
            <p v-for="(message, key_message) in messages">
                {{ message.message_text }}
            </p>
            <form accept-charset="utf-8" method="POST" id="message_form" @submit.prevent="send_message">
                <input
                    type="text"
                    id="field-message_text"
                    name="message_text"
                    required
                    autofocus
                    class="form-control"
                    placeholder="Escribe un mensaje..."
                    title="Escribe un mensaje..."
                    v-model="form_values.message_text"
                    >
            </form>
        </div>
    </div>
</div>

<script>
    new Vue({
        el: '#conversation_app',
        created: function(){
            this.get_conversations();
        },
        data: {
            conversation_id: 0,
            conversations: [],
            messages: [],
            form_values: {
                message_text: ''
            },
        },
        methods: {
            get_conversations: function(){
                axios.get(url_app + 'messages/conversations/')
                .then(response => {
                    //console.log(response.data.message)
                    this.conversations = response.data.conversations;

                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            set_current: function(key){
                this.conversation_id = this.conversations[key].id;
                this.get_messages();
                console.log('Current: ' + this.conversation_id);
            },
            send_message: function(){
                axios.post(url_app + 'messages/send_message/' + this.conversation_id, $('#message_form').serialize())
                .then(response => {
                    console.log(response.data.message_id);
                    this.form_values.message_text = '';
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            get_messages: function(){
                axios.get(url_app + 'messages/get/' + this.conversation_id)
                .then(response => {
                    console.log('mensajes pedidos');
                    this.messages = response.data.messages;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
        }
    });
</script>