<script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.6/chosen.jquery.min.js"></script>
<link href="<?= URL_RESOURCES ?>assets/bs4_chosen/dist/css/component-chosen.css" rel="stylesheet">

<script>
    $(document).ready(function(){
        $('.form-control-chosen').chosen({});
        $('.form-control-chosen-required').chosen({
            allow_single_deselect: false,
            width: '100%'
        });
    });
</script>