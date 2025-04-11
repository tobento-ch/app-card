<?php if (!$card->empty()) { ?>
<div class="card">
    <?php if ($card->title()) { ?>
        <div class="card-head"><?= $view->esc($card->title()) ?></div>
    <?php } ?>
    <div class="card-body content mt-xs">
        <table>
            <?php if ($card->headers()) { ?>
            <tr>
            <?php foreach($card->headers() as $heading) { ?>
                <th><?= $card->renderValue($view, $heading, $heading) ?></th>
            <?php } ?>
            </tr>
            <?php } ?>
            <?php if ($card->rows()) { ?>
                <?php foreach($card->rows() as $row) { ?>
                    <tr>
                    <?php foreach($card->verifyRow($row) as $name => $value) { ?>
                        <td><?= $card->renderValue($view, $value, $name) ?></td>
                    <?php } ?>
                    </tr>
                <?php } ?>
            <?php } ?>
        </table>
    </div>
</div>
<?php } ?>