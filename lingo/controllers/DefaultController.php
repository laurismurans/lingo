<?php
namespace lingo\controllers;

use Yii;
use yii\web\Controller;
use lingo\forms\LingoGame;

class DefaultController extends Controller
{
    
    public function actionIndex() {
        $lingoGame = new LingoGame();
                
        if (Yii::$app->request->isPost) {
            $lingoGame = Yii::$app->session->get('LingoGame') ?: $lingoGame;
            
            if ($lingoGame->load(Yii::$app->request->post()) && $lingoGame->validateInput()) {
                $lingoGame->guess();
            }
        }
        
        if (!$lingoGame->current_word) {
            $lingoGame->resetGame();
        }
            
        return $this->render('index', [
            'lingoGame' => $lingoGame,
        ]);
    }   
}
