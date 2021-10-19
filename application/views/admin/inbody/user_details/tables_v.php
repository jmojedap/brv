            <div v-show="inbody_id > 0">
                <h3>General</h3>
                <table class="table bg-white">
                    <thead>
                        <th>Variable</th>
                        <th class="text-center">Límite inferior</th>
                        <th class="text-center">Valor</th>
                        <th class="text-center">Límite superior</th>
                    </thead>
                    <tbody>
                        <tr v-for="(variable, key_variable) in inbody.body">
                            <td>{{ variable.title }}</td>
                            <td class="text-center">
                                <span>{{ variable.lower_limit }}</span>
                            </td>
                            <td class="text-center">
                                <i class="fa fa-arrow-circle-down value_is_lower" v-show="is_lower(variable.value, variable.lower_limit)"></i>
                                <i class="fa fa-arrow-circle-up value_is_upper" v-show="is_upper(variable.value, variable.upper_limit)"></i>
                                <i class="fa fa-check-circle text-success" v-show="in_range(variable.value, variable.lower_limit, variable.upper_limit)"></i>
                                <span class="ml-2">{{ variable.value }}</span>
                            </td>
                            <td class="text-center">
                                <span>{{ variable.upper_limit }}</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <h3>Tronco</h3>
                <table class="table bg-white">
                    <thead>
                        <th>Variable</th>
                        <th class="text-center">Límite inferior</th>
                        <th class="text-center">Valor</th>
                        <th class="text-center">Límite superior</th>
                    </thead>
                    <tbody>
                        <tr v-for="(variable, key_variable) in inbody.trunk">
                            <td>{{ variable.title }}</td>
                            <td class="text-center">
                                <span>{{ variable.lower_limit }}</span>
                            </td>
                            <td class="text-center">
                                <i class="fa fa-arrow-circle-down value_is_lower" v-show="variable.value < variable.lower_limit"></i>
                                <i class="fa fa-arrow-circle-up value_is_upper" v-show="variable.value > variable.upper_limit"></i>
                                <i class="fa fa-check-circle text-success" v-show="variable.value > variable.lower_limit && variable.value < variable.upper_limit"></i>
                                <span class="ml-2">{{ variable.value }}</span>
                            </td>
                            <td class="text-center">
                                <span>{{ variable.upper_limit }}</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <h3>Brazo derecho</h3>
                <table class="table bg-white">
                    <thead>
                        <th>Variable</th>
                        <th class="text-center">Límite inferior</th>
                        <th class="text-center">Valor</th>
                        <th class="text-center">Límite superior</th>
                    </thead>
                    <tbody>
                        <tr v-for="(variable, key_variable) in inbody.right_arm">
                            <td>{{ variable.title }}</td>
                            <td class="text-center">
                                <span>{{ variable.lower_limit }}</span>
                            </td>
                            <td class="text-center">
                                <i class="fa fa-arrow-circle-down value_is_lower" v-show="variable.value < variable.lower_limit"></i>
                                <i class="fa fa-arrow-circle-up value_is_upper" v-show="variable.value > variable.upper_limit"></i>
                                <i class="fa fa-check-circle text-success" v-show="variable.value > variable.lower_limit && variable.value < variable.upper_limit"></i>
                                <span class="ml-2">{{ variable.value }}</span>
                            </td>
                            <td class="text-center">
                                <span>{{ variable.upper_limit }}</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <h3>Brazo izquierdo</h3>
                <table class="table bg-white">
                    <thead>
                        <th>Variable</th>
                        <th class="text-center">Límite inferior</th>
                        <th class="text-center">Valor</th>
                        <th class="text-center">Límite superior</th>
                    </thead>
                    <tbody>
                        <tr v-for="(variable, key_variable) in inbody.left_arm">
                            <td>{{ variable.title }}</td>
                            <td class="text-center">
                                <span>{{ variable.lower_limit }}</span>
                            </td>
                            <td class="text-center">
                                <i class="fa fa-arrow-circle-down value_is_lower" v-show="variable.value < variable.lower_limit"></i>
                                <i class="fa fa-arrow-circle-up value_is_upper" v-show="variable.value > variable.upper_limit"></i>
                                <i class="fa fa-check-circle text-success" v-show="variable.value > variable.lower_limit && variable.value < variable.upper_limit"></i>
                                <span class="ml-2">{{ variable.value }}</span>
                            </td>
                            <td class="text-center">
                                <span>{{ variable.upper_limit }}</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <h3>Pierna derecha</h3>
                <table class="table bg-white">
                    <thead>
                        <th>Variable</th>
                        <th class="text-center">Límite inferior</th>
                        <th class="text-center">Valor</th>
                        <th class="text-center">Límite superior</th>
                    </thead>
                    <tbody>
                        <tr v-for="(variable, key_variable) in inbody.right_leg">
                            <td>{{ variable.title }}</td>
                            <td class="text-center">
                                <span>{{ variable.lower_limit }}</span>
                            </td>
                            <td class="text-center">
                                <i class="fa fa-arrow-circle-down value_is_lower" v-show="variable.value < variable.lower_limit"></i>
                                <i class="fa fa-arrow-circle-up value_is_upper" v-show="variable.value > variable.upper_limit"></i>
                                <i class="fa fa-check-circle text-success" v-show="variable.value > variable.lower_limit && variable.value < variable.upper_limit"></i>
                                <span class="ml-2">{{ variable.value }}</span>
                            </td>
                            <td class="text-center">
                                <span>{{ variable.upper_limit }}</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <h3>Pierna izquierda</h3>
                <table class="table bg-white">
                    <thead>
                        <th>Variable</th>
                        <th class="text-center">Límite inferior</th>
                        <th class="text-center">Valor</th>
                        <th class="text-center">Límite superior</th>
                    </thead>
                    <tbody>
                        <tr v-for="(variable, key_variable) in inbody.left_leg">
                            <td>{{ variable.title }}</td>
                            <td class="text-center">
                                <span>{{ variable.lower_limit }}</span>
                            </td>
                            <td class="text-center">
                                <i class="fa fa-arrow-circle-down value_is_lower" v-show="variable.value < variable.lower_limit"></i>
                                <i class="fa fa-arrow-circle-up value_is_upper" v-show="variable.value > variable.upper_limit"></i>
                                <i class="fa fa-check-circle text-success" v-show="variable.value > variable.lower_limit && variable.value < variable.upper_limit"></i>
                                <span class="ml-2">{{ variable.value }}</span>
                            </td>
                            <td class="text-center">
                                <span>{{ variable.upper_limit }}</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>