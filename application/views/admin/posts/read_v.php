<div id="read_app">
    <div class="card center_box_750">
        <div class="card-body">
            <h1><?= $head_title ?></h1> 
            <span class="text-muted"><?= $row->published_at ?></span>
            <p><?= $row->excerpt ?></p>
            <div>
                <?= $row->content ?>
            </div>
        </div>
    </div>
</div>