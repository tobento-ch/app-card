<?php if ($card->html()) { ?>
<div class="card">
    <?php if ($card->title()) { ?>
        <div class="card-head"><?= $view->esc($card->title()) ?></div>
    <?php } ?>
    <div class="card-body mt-s"><?= $card->html() ?></div>
</div>
<?php } ?>