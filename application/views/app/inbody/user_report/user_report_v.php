<div id="user_report">
    <div class="text-center p-2" v-show="loading">
        <i class="fa fa-spin fa-spinner fa-3x"></i>
    </div>
    <div v-show="!loading">
        <table class="table bg-white" v-show="inbody_id == 0">
            <thead>
                <th>Mediciones ({{ list.length }})</th>
                <th width="30%">IMC</th>
                <th>InBody</th>
                <th width="10px"></th>
            </thead>
            <tbody>
                <tr v-for="(inbody, inbody_key) in list">
                    <td>
                        {{ inbody.test_date | ago }}
                        <br>
                        <small class="text-muted">{{ inbody.test_date | date_format }}</small>
                    </td>
                    <td>
                        {{ inbody.bmi_body_mass_index }}
                        <div class="progress" style="height: 3px;">
                            <div 
                                class="progress-bar"
                                role="progressbar"
                                v-bind:style="`width: ` + bmi_to_percent(inbody.bmi_body_mass_index) + `%;`"
                                aria-valuenow="25"
                                aria-valuemin="0" aria-valuemax="100" v-bind:class="bmi_class(inbody.bmi_body_mass_index)">
                            </div>
                        </div>
                    </td>
                    <td class="text-center">{{ parseInt(inbody.inbody_score) }}</td>
                    <td><button class="a4" v-on:click="set_inbody(inbody.id)"><i class="fa fa-arrow-right"></i></button></td>
                </tr>
            </tbody>
        </table>
    
        <div v-show="inbody_id > 0" class="p-1">
            <div class="mb-1">
                <button class="btn btn-light" v-on:click="unset_inbody()">
                    <i class="fa fa-arrow-left"></i>
                </button>
            </div>
            <?php $this->load->view('app/inbody/user_report/tables_v') ?>
        </div>
    </div>

    

</div>

<?php $this->load->view('app/inbody/user_report/vue_v') ?>