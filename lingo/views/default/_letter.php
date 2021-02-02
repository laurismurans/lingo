<div class="col py-3 mx-1 border border-dark text-center h4 <?= $is_guess_completed ? 'bg-' . $lingoGame::BACKGROUND_COLORS[$lingoGame->wrong_guesses[$row][$letter_index]['status']] : '' ?>">
    <?= $is_guess_completed 
        ? ($lingoGame->wrong_guesses[$row][$letter_index]['letter']) 
        : (
            $letter_index === 0 && $row === $lingoGame->guess_count 
            ? $lingoGame->current_word[$letter_index]
            : '&nbsp;'
        ) 
    ?>
</div>
