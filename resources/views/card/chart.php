<div class="card card-chart">
    <?php if ($card->title()) { ?>
        <div class="card-head"><?= $view->esc($card->title()) ?></div>
    <?php } ?>
    <div class="card-body mt-s"><?= $card->chart()->render() ?></div>
</div>