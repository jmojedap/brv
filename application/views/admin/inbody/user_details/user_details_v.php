<?php $this->load->view('assets/highcharts') ?>

<div id="inbody_user_app">
    <div class="card mb-2 center_box_920">
        <div class="card-body">
            <highcharts :options="compChartOptions" style="height: 500px;"></highcharts>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-4">
            <table class="table bg-white">
                <thead>
                    <th>Mediciones ({{ list.length }})</th>
                    <th></th>
                    <th width="10"></th>
                </thead>
                <tbody>
                    <tr v-for="(element, key) in list">
                        <td>{{ element.test_date | date_format }}</td>
                        <td>{{ element.test_date | ago }}</td>
                        <td>
                            <button class="btn btn-sm"
                                v-bind:class="{'btn-light': element.id != inbody_id, 'btn-primary': element.id == inbody_id }"
                                v-on:click="set_inbody(element.id)"
                                >
                                <i class="fa fa-arrow-right"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- DETALLE -->
        <div class="col-md-8">
            <?php $this->load->view('admin/inbody/user_details/tables_v') ?>
        </div>
    </div>
</div>

<?php $this->load->view('admin/inbody/user_details/vue_v') ?>