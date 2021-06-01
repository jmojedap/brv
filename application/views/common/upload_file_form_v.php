<form accept-charset="utf-8" method="POST" id="file_form" @submit.prevent="send_file_form">
    <div class="form-group row">
        <div class="col-md-8">
            <input
                type="file" id="field-file"
                ref="file_field" name="file_field"
                required class="form-control"
                v-on:change="handle_file_upload()"
                >
        </div>
        <div class="col-md-4">
            <button class="btn btn-success btn-block" type="submit">Cargar</button>
        </div>
    </div>
</form>

<div id="upload_response"></div>