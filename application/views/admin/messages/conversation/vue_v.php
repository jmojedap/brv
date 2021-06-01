<script>
    new Vue({
        el: '#conversation_app',
        created: function(){
            this.get_conversations();
            setInterval(() => {this.refresh_messages()}, 7000);
        },
        data: {
            user_id: <?= $this->session->userdata('user_id'); ?>,
            conversation_id: 0,
            conversations: [],
            messages: [],
            message: {
                id: 0,
                text: '',
                user_id: '<?= $this->session->userdata('user_id'); ?>',
                send_at: ''
            },
            current_message: {
                id: 0
            },
            last_id: 0
        },
        methods: {
            //Cargar listado de conversaciones en las que participa el usuario
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
            //Marcar una conversación como la actual
            set_current: function(key){
                this.conversation_id = this.conversations[key].id;
                this.get_messages();
                console.log('Current: ' + this.conversation_id);
            },
            //Envía el mensaje escrito
            send_message: function(){
                new_id = Math.floor((Math.random() * 10000000) + 1)
                this.display_message(new_id);
                axios.post(url_app + 'messages/send_message/' + this.conversation_id, $('#message_form').serialize())
                .then(response => {
                    this.last_id = response.data.message_id;
                    console.log(response.data.message_id);
                })
                .catch(function (error) {
                    console.log(error);
                });
                $('#field-text').val('');   //Reiniciar casilla de mensaje
            },
            //Mostrar el mensaje enviado
            display_message: function(new_id){
                var new_message = {
                    id: new_id,
                    user_id: this.user_id,
                    text: $('#field-text').val()
                }
                this.messages.push(new_message);
                this.go_to_bottom();
            },
            //Cargar mensajes iniciales de la conversación
            get_messages: function(){
                axios.get(url_app + 'messages/get/' + this.conversation_id)
                .then(response => {
                    this.messages = response.data.messages;
                    this.set_last();
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            //Cargar mensajes nuevos
            refresh_messages: function(){
                console.log('Buscando message_id > ' + this.last_id);
                axios.get(url_app + 'messages/get/' + this.conversation_id + '/' + this.last_id)
                .then(response => {
                    if ( response.data.qty_messages > 0 )
                    {
                        console.log('nuevos mensajes: ' + response.data.qty_messages );
                        //Agregando nuevos elementos
                        response.data.messages.forEach(element => { this.messages.push(element);});
                        this.set_last();
                    }
                    this.go_to_bottom();
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            //Establecer ID de último mensaje en la conversación (last_id)
            set_last: function(){
                if ( this.messages.length > 0 )
                {
                    var last_index = this.messages.length - 1;
                    console.log('last_index: ' + last_index);
                    this.last_id = this.messages[last_index].id;
                }
                console.log('mensajes: ' + this.messages.length);
                console.log('el último es: ' + this.last_id);
                this.go_to_bottom();
            },
            //Moverse a la parte inferior de la caja de mensajes
            go_to_bottom: function(){
                if ( this.messages.length > 0 )
                {
                    var pixeles = 200 * this.messages.length;
                    $("#app_message_chats").animate({scrollTop: pixeles}, 500);
                }
            },
            set_current_message: function(key_message){
                this.current_message = this.messages[key_message];
                console.log(this.message.id);
            },
            //Eliminar un mensaje
            delete_message: function(key_message){
                axios.get(url_app + 'messages/delete/' + this.current_message.id)
                .then(response => {
                    console.log(response.data.status)
                    if ( response.data.status )
                    {
                        this.messages.splice(key_message, 1);
                        toastr['success']('Mensaje eliminado');
                    }
                })
                .catch(function (error) {
                    console.log(error);
                });  
            },
        }
    });
</script>