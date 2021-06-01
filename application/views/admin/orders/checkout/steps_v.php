<?php
    $pct = number_format($step * 33.33, 0);
?>
<div class="center_box_750">
    <div class="progress mb-2">
        <div
            class="progress-bar"
            role="progressbar"
            style="width: <?= $pct ?>%;"
            aria-valuenow="<?= $pct ?>"
            aria-valuemin="0"
            aria-valuemax="100">
            Paso <?= $step ?>/3
        </div>
    </div>
</div>