<?php

namespace lingo\forms;

use lingo\models\LingoWord;
use lingo\models\LingoSetting;
use Yii;
use yii\helpers\StringHelper;

class LingoGame extends \yii\base\Model {
    
    const STATUS_CORRECT = 'correct';
    const STATUS_MISPLACED = 'misplaced';
    const STATUS_MISSING = 'missing';
    
    const BACKGROUND_COLORS = [
        self::STATUS_CORRECT => 'success',
        self::STATUS_MISPLACED => 'warning',
        self::STATUS_MISSING => 'danger',
    ];
    

    public $allowed_guess_count;
    public $current_word;
    public $guessed_words = [];
    public $guess_count = 0;
    public $guess_word;
    public $word_length;
    public $points = 0;
    public $wrong_guesses = [];
    
    public function init() {
        $this->allowed_guess_count = LingoSetting::get('allowed_guess_count') ?: 5;
        $this->word_length = LingoSetting::get('word_length') ?: 5;
    }
        
    public function rules() {
        return [
            [['guess_word'], 'required'],
            [['current_word', 'guess_word'], 'string'],
            [['guess_count', 'allowed_guess_count', 'word_length'], 'integer'],
            [['points'], 'number'],
            [['guessed_words', 'wrong_guesses'], 'safe'],
        ];
    }
    
    public function attributeLabels() {
        return [
            'allowed_guess_count' => Yii::t('app', 'Allowed Guess Count'),
            'current_words' => Yii::t('app', 'Current Word'),
            'guessed_words' => Yii::t('app', 'Guessed Words'),
            'guess_count' => Yii::t('app', 'Guess Count'),
            'guess_word' => Yii::t('app', 'Guess'),
            'points' => Yii::t('app', 'Points'),
        ];
    }
    
    public function validateInput() {
        if (preg_match('~[0-9]+~', $this->guess_word)) {
            return false;
        } else if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $this->guess_word)) {
            return false;
        }
        
        return true;
    }
    
    public function isGuessed() {
        if (mb_strtolower($this->current_word) === mb_strtolower($this->guess_word)) {
            return true;
        }
        
        return false;
    }
    
    public static function str_split_unicode($str, $l = 0) { // From php.net 
        if ($l > 0) {
            $ret = array();
            $len = mb_strlen($str, "UTF-8");
            for ($i = 0; $i < $len; $i += $l) {
                $ret[] = mb_substr($str, $i, $l, "UTF-8");
            }
            return $ret;
        }
        return preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);
    }
    
    public function addWrongGuess() {
        $result = [];
        $current_word_array = self::str_split_unicode(mb_strtoupper($this->current_word));
        $guess_word_array = self::str_split_unicode(mb_strtoupper($this->guess_word));
        
        foreach ($guess_word_array as $key => $letter) {
            $result[$key] = [
                'letter' => $letter,
                'status' => self::STATUS_MISSING,
            ];
            if (in_array($letter, $current_word_array)) {
                $result[$key]['status'] = self::STATUS_MISPLACED;
                if ($letter === $current_word_array[$key]) {
                    $result[$key]['status'] = self::STATUS_CORRECT;
                }
            }
        }
        $this->wrong_guesses[] = $result;
    }
    
    public function guess() {
        if ($this->isGuessed()) {
            $this->addPoints();
            
            $this->wrong_guesses = [];
            $this->guess_count = 0;
            Yii::$app->session->setFlash('success', Yii::t('app', 'You have guessed the word {word}.', ['word' => mb_strtoupper($this->guess_word)]));
            
            $this->goToNextWord();
        } else {
            $this->guess_count++;
            
            $this->addWrongGuess();
            
            if ($this->guess_count >= $this->allowed_guess_count) {
                Yii::$app->session->setFlash('danger', Yii::t('app', 'You lost! The word was {word}. You earned {points} points!', ['word' => mb_strtoupper($this->guess_word), 'points' => $this->points]));
                $this->resetGame();
            }
        }
        
        Yii::$app->session->set('LingoGame', $this);
    }
    
    public function addPoints() {
        $lost_points_per_guess = LingoSetting::get('lost_points_per_guess');
        $max_points = $this->word_length * $lost_points_per_guess;
        $earned_points = $max_points - ($this->guess_count * $lost_points_per_guess);
        
        $this->points += $earned_points;
    }
    
    public function getWord() {
        $lingoWord = LingoWord::getRandomWord($this->word_length, $this->guessed_words);
        
        if ($lingoWord) {
            return mb_strtoupper($lingoWord->name);
        }
        
        return '';
    }
    
    public function goToNextWord() {
        $this->guessed_words[] = $this->current_word;
        $this->guess_count = 0;
        $this->current_word = $this->getWord();
        
        if (!$this->current_word) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'No more words in database. You have finished Lingo with {points} points!', ['points' => $this->points]));
            $this->resetGame();
        }
    }
    
    public function resetGame() {
        $this->points = 0;
        $this->guess_count = 0;
        $this->guessed_words = [];
        $this->wrong_guesses = [];
        $this->current_word = $this->getWord();
        
        Yii::$app->session->set('LingoGame', $this);
    }

}
