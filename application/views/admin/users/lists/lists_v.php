<div id="user_lists">
    <div class="center_box_750">
        <table class="table bg-white">
            <thead>
                <th width="10px"></th>
                <th>Nombre</th>
                <th></th>
            </thead>
            <tbody>
                <tr v-for="(list, key) in user_lists">
                    <td>
                        <input type="checkbox" v-model="list.in_list" v-on:click="update_list(list.in_list, key)">
                    </td>
                    <td>{{ list.name }}</td>
                    <td>{{ list.in_list }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php $this->load->view($this->views_folder . 'lists/vue_v') ?>