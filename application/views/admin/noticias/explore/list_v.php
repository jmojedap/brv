<?php
    $cl_col['title'] = '';
    $cl_col['image'] = '';
    $cl_col['excerpt'] = 'only-lg';
    $cl_col['score_1'] = 'only-lg';
?>

<div class="table-responsive">
    <table class="table bg-white">
        <thead>
            <th width="10px">
                <input type="checkbox" @change="select_all" v-model="all_selected">
            </th>
            <th width="10px" class="<?= $cl_col['image'] ?>"></th>
            <th class="<?= $cl_col['title'] ?>">Título</th>
            <th class="<?= $cl_col['excerpt'] ?>">Descripción</th>
            <th class="<?= $cl_col['score_1'] ?>">Visitas</th>
            <th width="50px"></th>
        </thead>
        <tbody>
            <tr v-for="(element, key) in list" v-bind:id="`row_` + element.id" v-bind:class="{'table-info': selected.includes(element.id) }">
                <td>
                    <input type="checkbox" v-bind:id="`check_` + element.id" v-model="selected" v-bind:value="element.id">
                </td>
                <td class="<?= $cl_col['image'] ?>">
                    <a v-bind:href="`<?= URL_ADMIN . "noticias/edit/" ?>` + element.id">    
                        <img
                            v-bind:src="element.src_img"
                            width="60px"
                            class="rounded"
                            alt="miniatura noticia"
                            onerror="this.src='<?= URL_IMG ?>app/sm_nd_square.png'"
                        >
                    </a>
                </td>
                <td class="<?= $cl_col['title'] ?>">
                    <a v-bind:href="`<?= URL_ADMIN . "noticias/edit/" ?>` + element.id">
                        {{ element.post_name }}
                    </a>
                </td>
                <td class="<?= $cl_col['excerpt'] ?>">
                    {{ element.excerpt }}
                </td>
                <td class="<?= $cl_col['score_1'] ?>">
                    {{ element.score_1 }}
                </td>
                <td>
                    <button class="a4" data-toggle="modal" data-target="#detail_modal" @click="set_current(key)">
                        <i class="fa fa-info"></i>
                    </button>
                </td>
            </tr>
        </tbody>
    </table>
</div>