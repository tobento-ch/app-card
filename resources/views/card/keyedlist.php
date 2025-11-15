<?php if (!$card->empty()) { ?>
<div class="card">
    <?php if ($card->title()) { ?>
        <div class="card-head"><?= $view->esc($card->title()) ?></div>
    <?php } ?>
    <div class="card-body mt-s">
        <?php foreach($card->items() as $key => $value) { ?>
            <?php
            $key = $card->renderValue($view, $key);
            $value = $card->renderValue($view, $value);
            ?>
            <?php if ($key) { ?>
                <div><?= $key ?></div>
            <?php } ?>
            <div class="text-700 mt-xxs mb-s"><?= $value === '' ? '-' : $value ?></div>
        <?php } ?>
    </div>
</div>
<?php } ?>