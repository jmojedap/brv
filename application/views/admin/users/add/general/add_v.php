<div id="add_user_app">
    <div class="card center_box_750">
        <div class="card-body">        
            <form id="add_form" accept-charset="utf-8" @submit.prevent="validate_send">
                <div class="form-group row">
                    <label for="role" class="col-md-4 col-form-label text-right">Rol</label>
                    <div class="col-md-8">
                        <select name="role" v-model="form_values.role" class="form-control" required>
                            <option v-for="(option_role, key_role) in options_role" v-bind:value="key_role">{{ option_role }}</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="first_name" class="col-md-4 col-form-label text-right">Nombres y Apellidos</label>
                    <div class="col-md-4">
                        <input
                            name="first_name" class="form-control"
                            placeholder="Nombres" title="Nombres del usuario"
                            required autofocus
                            v-model="form_values.first_name"
                            >
                    </div>
                    <div class="col-md-4">
                        <input
                            name="last_name" class="form-control"
                            placeholder="Apellidos" title="Apellidos del usuario"
                            required
                            v-model="form_values.last_name"
                            >
                    </div>
                </div>
                
                <div class="form-group row" id="form-group_email">
                    <label for="email" class="col-md-4 col-form-label text-right">Correo electrónico</label>
                    <div class="col-md-8">
                        <input
                            name="email" type="email" class="form-control" title="Dirección de correo electrónico"
                            required
                            v-bind:class="{ 'is-invalid': validation.email_unique == 0, 'is-valid': validation.email_unique == 1 }"
                            v-model="form_values.email"
                            v-on:change="validate_form"
                            >
                        <span class="invalid-feedback">
                            El correo electrónico ya fue registrado, por favor escriba otro
                        </span>
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="password" class="col-md-4 col-form-label text-right">Contraseña</label>
                    <div class="col-md-8">
                        <input
                            name="password" class="form-control"
                            title="Debe tener al menos un número y una letra minúscula, y al menos 8 caractéres"
                            required pattern="(?=.*\d)(?=.*[a-z]).{8,}"
                            v-model="form_values.password"
                            >
                    </div>
                </div>

                <div class="form-group row">
                    <label for="gender" class="col-md-4 col-form-label text-right">Sexo</label>
                    <div class="col-md-8">
                        <select name="gender" v-model="form_values.gender" class="form-control">
                            <option v-for="(option_gender, key_gender) in options_gender" v-bind:value="key_gender">{{ option_gender }}</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="offset-4 col-md-8">
                        <button class="btn btn-success w120p" type="submit">Crear</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Modal -->
    <div class="modal fade" id="modal_created" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Usuario creado</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <i class="fa fa-check"></i> Usuario creado correctamente
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" v-on:click="go_created">Abrir usuario</button>
                    <button type="button" class="btn btn-secondary" v-on:click="clean_form" data-dismiss="modal">
                        <i class="fa fa-plus"></i> Crear otro
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->load->view($this->views_folder . 'add/general/add_vue_v');