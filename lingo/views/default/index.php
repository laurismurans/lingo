<?php 

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\widgets\Pjax;

$this->registerJs("
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
    
    $(document).on('beforeSubmit', '#lingo_form', function(e) {
        $('.btn-lingo-submit').attr('disabled', true);
    });
");

?>

<?php Pjax::begin(); ?>
    <section class="container" id="lingo_container" style="max-width: 350px;">
        <div class="row">
            <div class="col text-right pr-0 h5 font-weight-bold">
                <?= Yii::t('app', 'Points') ?>: <?= $lingoGame->points ?>
            </div>
        </div>
        <?php for ($row = 0; $row < $lingoGame->allowed_guess_count; $row++) { 
            $is_guess_completed = false;
            if ($row < $lingoGame->guess_count) {
                $is_guess_completed = true;
            } ?>
            <?= $this->render('_row', [
                'lingoGame' => $lingoGame,
                'is_guess_completed' => $is_guess_completed,
                'row' => $row,
            ]) ?>
        <?php } ?>
        <?php $form = ActiveForm::begin([
            'id' => 'lingo_form',
            'options' => [
                'data-pjax' => 1,
            ],
        ]); ?>
            <div class="row">
                <div class="col">
                    <?= $form->field($lingoGame, 'guess_word')->textInput([
                        'pattern' => '[\p{L}]+', 
                        'maxlength' => 5, 
                        'minlength' => 5, 
                        'placeholder' => Yii::t('app', 'Enter your guess'),
                    ])->label(false) ?>
                </div>
                <div class="col-auto">
                    <?= Html::submitButton(Yii::t('app', 'Guess'), ['class' => 'btn btn-success btn-lingo-submit', 'data-pjax' => 1]) ?>
                </div>
            </div>
            <div class="row">
                <div class="col text-center">
                    *<?= Yii::t('app', 'Please enter 5 letters from A to Z. Both lowercase and uppercase are valid') ?>
                </div>
            </div>
        <?php $form->end(); ?>
    </section>
<?php Pjax::end(); ?>
