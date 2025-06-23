<?php if ($card->cards()) { ?>
<div class="card">
    <?php if ($card->title()) { ?>
        <div class="card-head"><?= $view->esc($card->title()) ?></div>
    <?php } ?>
    <div class="card-body">
        <div class="cards">
            <?php
            foreach($card->cards() as $gcard) {
                echo $gcard->render();
            }
            ?>
        </div>
    </div>
</div>
<?php } ?>