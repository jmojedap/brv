<div id="add_order_app">
    <div class="center_box_750">
        <div class="card mb-2">
            <div class="card-body">
                <div class="mb-2 d-flex justify-content-between">
                    <button class="btn btn-light w120p" type="button" v-on:click="set_step(1)" v-show="step == 2">
                        <i class="fa fa-arrow-left"></i>
                        Usuarios
                    </button>
                    <button class="btn btn-light w120p" type="button" v-on:click="set_step(2)" v-show="step == 3">
                        <i class="fa fa-arrow-left"></i>
                        Productos
                    </button>
                </div>

                <!-- 3: VERIFICACIÓN Y FORMULARIO DATOS ADICIONALES -->
                <div v-show="user.id > 0 && step < 4">
                    <?php $this->load->view($this->views_folder . 'add/form_v') ?>
                </div>

                <!-- 1: SELECCIONAR USUARIO -->
                <div v-show="step == 1">
                    <h3 class="text-center"><i class="fa fa-arrow-right text-success"></i> Seleccione el usuario</h3>
                    <div class="form-group row">
                        <label for="user_id" class="col-md-4 col-form-label text-right">Usuario</label>
                        <div class="col-md-8">
                            <input
                                name="q" type="text" class="form-control"
                                title="Buscar..." placeholder="Buscar..."
                                v-model="user_filters.q" v-on:change="get_users()"
                            >
                        </div>
                    </div>
                    <div class="alert alert-info" v-show="no_users">
                        No se encontraron usuarios que coincidan con "<strong>{{ user_filters.q }}</strong>"
                    </div>
                    <table class="table bg-white" v-show="users.length > 0">
                        <thead>
                            <th>Usuario</th>
                            <th></th>
                        </thead>
                        <tbody>
                            <tr v-for="(user, key) in users">
                                <td>
                                    {{ user.display_name }}
                                    <br>
                                    <span class="text-muted">{{ user.document_number }}</span>
                                </td>
                                <td width="50px">
                                    <button class="btn btn-light" v-on:click="set_user(key)">
                                        Seleccionar
                                    </button>                    
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- 2: SELECCIONAR PRODUCTO -->
                <div v-show="step == 2">
                    <h3 class="text-center"><i class="fa fa-arrow-right text-success"></i> Seleccione el producto</h3>
                    <div class="form-group row">
                        <label for="cat_1" class="col-md-4 col-form-label text-right">Categoría</label>
                        <div class="col-md-8">
                            <select name="cat_1" v-model="product_filters.cat_1" class="form-control" v-on:change="get_products()">
                                <option v-for="(option_cat_1, key_cat_1) in options_product_category" v-bind:value="key_cat_1">{{ option_cat_1 }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="q_producto" class="col-md-4 col-form-label text-right">Producto</label>
                        <div class="col-md-8">
                            <input
                                name="q_producto" type="text" class="form-control" title="Buscar producto" placeholder="Buscar..."
                                v-model="product_filters.q" v-on:change="get_products()"
                            >
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table bg-white">
                            <thead>
                                <th>Producto</th>
                                <th>Valor</th>
                                <th width="100px"></th>
                            </thead>
                            <tbody>
                                <tr v-for="(product, key) in products">
                                    <td>
                                        <span class="text-primary">{{ product.code }}</span>
                                        <br>
                                        {{ product.name }}
                                    </td>
                                    <td>
                                        <span class="text-primary">
                                            ${{ product.price | currency }}
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-light" v-on:click="set_product(key)">
                                            Seleccionar
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 4: CONFIRMACIÓN -->
                <div v-show="step == 4">
                    <h3 class="text-success text-center"><i class="fa fa-check text-success"></i> Pago guardado</h3>
                    <p class="text-center">Ref. venta: {{ order.order_code }}</p>
                    <div class="d-flex justify-content-around">
                        <a v-bind:href="`<?= URL_ADMIN . "orders/info/" ?>` + order.order_id" class="btn btn-primary w120p">
                            Abrir
                        </a>
                        <a href="<?= URL_ADMIN . "orders/add/" ?>" class="btn btn-light w120p">
                            Nuevo pago
                        </a>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>

<?php $this->load->view($this->views_folder . 'add/vue_v') ?>