<style>
    .message-right{
        text-align: right;
    }

    .messages .user_img {
        width: 35px;
    }

    .messages .message {
        border: 1px solid blue;
        padding: 0.5em;
    }

    .message .globe{
        background-color: #e3f2fd;
        padding: 0.5em;
    }
</style>

<div class="page" id="conversation_app">
    <div class="row">
        <div class="col-md-4">
            <form class="mb-2">
                <input class="form-control" type="text" placeholder="Buscar" name="">
            </form>
            <ul class="list-group">
                <li class="list-group-item" v-for="(conversation, key) in conversations" v-on:click="set_current(key)"
                    v-bind:class="{active: conversation.id == conversation_id}">
                    <div class="media">
                        <img class="rounded-circle" src="<?= URL_IMG ?>users/sm_user.png" alt="...">
                        <div class="media-body">
                            <h5 class="mt-0 mb-5">{{ conversation.title }}</h5>
                            <span class="media-time">15 sec ago</span>
                        </div>
                        <div class="pl-20">
                            <span class="badge badge-pill badge-danger">3</span>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
        <div class="col-md-8">
            <div class="page-main">
                <!-- Chat Box -->
                <div class="bg-white">
                    <button type="button" id="historyBtn" class="btn btn-ligth">
                        Anteriores... {{ last_id }}
                    </button>
                    <div class="messages">
                        <div class="media message"
                            v-for="(message, key_message) in messages"
                            v-bind:class="{'message-right': message.user_id == user_id}"
                            v-bind:id="`message_` + message.id">
                            <img class="rounded-circle user_img mr-2" src="<?= URL_IMG ?>users/sm_user.png" alt="" style="width: 35px;" v-if="message.user_id != user_id">
                            
                            <div class="media-body" v-on:click="set_current_message(key_message)">
                                <div class="chat-content" v-bind:title="message.sent_at">
                                    <p>
                                        <span class="globe">{{ message.text }}</span>
                                    </p>
                                    <div class="d-flex" v-show="message.id == current_message.id">
                                        <div class="mr-3">
                                            {{ message.sent_at }}
                                        </div>
                                        <div class="mr-3">
                                            <button class="a4" v-on:click="delete_message(key_message)">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <img class="rounded-circle user_img ml-2" src="<?= $this->session->userdata('src_img') ?>" alt="" v-if="message.user_id == user_id">
                        </div>
                    </div>

                </div>
                <!-- End Chat Box -->

                <!-- Message Input-->
                <form class="mt-2" accept-charset="utf-8" method="POST" id="message_form" @submit.prevent="send_message">
                    <div class="message-input">
                        <input type="text" id="field-text" name="text" class="form-control" rows="1" required placeholder="Write a message here">
                    </div>
                    <button class="message-input-btn btn btn-primary" type="submit">SEND</button>
                </form>
                <!-- End Message Input-->

            </div>
        </div>
    </div>
</div>

<?php $this->load->view('messages/conversation/vue_v') ?>