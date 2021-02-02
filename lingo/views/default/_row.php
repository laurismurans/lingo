<div class="row">
    <?php for ($letter_index = 0; $letter_index < $lingoGame->word_length; $letter_index++) { ?>
        <?= $this->render('_letter', [
            'lingoGame' => $lingoGame,
            'is_guess_completed' => $is_guess_completed,
            'row' => $row,
            'letter_index' => $letter_index,
        ]) ?>
    <?php } ?>
</div>
