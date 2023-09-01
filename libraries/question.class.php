<?php

class Question implements JsonSerializable {

    const SKIP = [12];
    const TYPE = 'Question';
    const SLUG = 'question';

    const TYPES = [
        1 => Question_Thematique::TYPE,
        2 => Question_Association::TYPE,
        3 => Question_Indice::TYPE,
        5 => Question_PetitIndice::TYPE,
        7 => Question_Anagramme::TYPE,
        8 => Question_Orthographe::TYPE,
    ];

    protected $data = [];
    protected $levels = null;
    protected $categories = null;


    public function __construct(array $data) {
        if(isset($data['id']) && $data['id'] == 0) unset($data['id']);
        if(!empty($data['misc']) && is_string($data['misc'])) {
            $data['misc'] = json_decode($data['misc']);
        }
        if(isset($data['levels'])) {
            $this->levels = $data['levels'];
            unset($data['levels']);
        }
        if(isset($data['categories'])) {
            $this->categories = $data['categories'];
            unset($data['categories']);
        }

        if(empty($data['type_id'])) throw new QuestionException("Type de question invalide.");
        if(empty(self::TYPES[$data['type_id']])) throw new QuestionException("Type de question invalide.");

        $this->data = $data;
    }


    public function __get($key) {
        switch($key) {
            case 'data': return $this->data;
            case 'sanitized': return trim(preg_replace('#[\r\n\s\t]+#i',' ', strip_tags(str_replace('</p>',' </p>',$this->question))));
            case 'type_name': return self::TYPES[$this->data['type_id']];
            default:
                if(isset($this->misc->{$key})) return $this->misc->{$key};
                else return isset($this->data[$key]) ? $this->data[$key] : null;
        }
    }


    public function __set($key, $val) {
        $this->data[$key] = $val;
    }


    public function jsonSerialize() {
        $data = $this->data;
        $data['type_name'] = static::TYPE;
        if($data['user_id']) $data['user_name'] = User::load($data['user_id'])->name;
        $data['categories'] = $this->getCategories();
        $data['levels'] = $this->getLevels();
        $data['preview'] = $this->renderPreview();
        unset($data['slug']);
        return $data;
    }


    public static function load($data) {
        if(is_object($data)) {
            $data = (array)$data;
        } elseif(is_numeric($data)) {
            if(!$obj = DB::getObject('questions', $data)) throw new QuestionException("ID de niveau invalide.");
            $data = (array)$obj;
        } elseif(!is_array($data)) {
            throw new QuestionException("Data invalide.");
        }

        if(empty($data['type_id'])) throw new QuestionException("Type de question invalide.");

        switch($data['type_id']) {
            case 1: return new Question_Thematique($data);
            case 2: return new Question_Association($data);
            case 3: return new Question_Indice($data);
            case 5: return new Question_PetitIndice($data);
            // case 6: return new Question_Eclair($data);
            case 7: return new Question_Anagramme($data);
            case 8: return new Question_Orthographe($data);
            // case 9: return new Question_Liste($data);
            default: throw new QuestionException("Type de question invalide.");
        }
    }


    public static function getTypeName($id) {
        return self::TYPES[$id] ?? null;
    }


    public function save() {
        if(!$user = SYS::getLoggedUser()) throw new QuestionException("Utilisateur introuvable.");
        if(!in_array($user->role_slug, ['dev','admin'])) $this->approve = 1;
        $this->question = strip_tags(str_replace('</p>',' </p>',$this->question), '<b><i><u><sub><sup>');
        $this->answer = strip_tags(str_replace('</p>',' </p>',$this->answer), '<b><i><u><sub><sup>');
        $this->slug = str_slug(strip_tags($this->question));
        $this->user_id = $user->id;
        if($user->isAdmin()) $this->approve = 0;
        if($this->id) $this->update();
        else $this->insert();
        return $this;
    }


    private function saveLevels() {
        if(!is_array($this->levels)) return;
        if(count($this->levels)) {
            foreach($this->levels as $level)
                if(DB::insert('questions_levels', ['question_id' => $this->id, 'level_id' => $level], 'question_id = question_id') === false)
                    throw new QuestionException("Impossible d'ajouter les niveaux de la question.");
            if(!DB::exec("DELETE FROM questions_levels WHERE question_id = ".$this->id." AND level_id NOT IN (".join(',', $this->levels).");"))
                throw new QuestionException("Impossible de supprimer les niveaux de la question.");
        } else {
            if(!DB::exec("DELETE FROM questions_levels WHERE question_id = ".$this->id))
                throw new QuestionException("Impossible de supprimer les niveaux de la question.");
        }
    }


    private function saveCategories() {
        if(!is_array($this->categories)) return;
        if(count($this->categories)) {
            foreach($this->categories as $category)
                if(DB::insert('questions_categories', ['question_id' => $this->id, 'category_id' => $category], 'question_id = question_id') === false)
                    throw new QuestionException("Impossible d'ajouter les catégories de la question.");
            if(!DB::exec("DELETE FROM questions_categories WHERE question_id = ".$this->id." AND category_id NOT IN (".join(',', $this->categories).");"))
                throw new QuestionException("Impossible de supprimer les catégories de la question.");
        } else {
            if(!DB::exec("DELETE FROM questions_categories WHERE question_id = ".$this->id))
                throw new QuestionException("Impossible de supprimer les catégories de la question.");
        }
    }


    private function insert() {
        // if(!$this->question) throw new QuestionException("Question invalide.");
        $data = $this->data;
        if(isset($data['misc']) && !is_string($data['misc'])) $data['misc'] = json_encode($data['misc']);
        if(!$this->data['id'] = DB::insert('questions', $data)) throw new QuestionException("Impossible d'ajouter la thématique.");
        $this->saveLevels();
        $this->saveCategories();
        return true;
    }


    private function update() {
        if(!$this->id) throw new QuestionException("ID de question invalide.");
        $this->hidden = 0;
        $data = $this->data;
        unset($data['id']);
        $data['updated'] = date('Y-m-d H:i:s');
        if(isset($data['misc']) && !is_string($data['misc'])) $data['misc'] = json_encode($data['misc']);
        if(!DB::update('questions', ['id' => $this->id], $data)) throw new QuestionException("Impossible de mettre à jour la question.");
        $this->saveLevels();
        $this->saveCategories();
        return true;
    }


    public function delete() {
        if(!DB::exec("UPDATE questions SET hidden = 1, updated = updated WHERE id = ".$this->id)) throw new QuestionException("Impossible de supprimer la question.");
        return true;
    }


    public function archive() {
        if(!DB::exec("UPDATE questions SET archive = 1, updated = updated WHERE id = ".$this->id)) throw new QuestionException("Impossible d'archiver la question.");
        $this->archive = 1;
        return true;
    }


    public function unarchive() {
        if(!DB::exec("UPDATE questions SET archive = 0, updated = updated WHERE id = ".$this->id)) throw new QuestionException("Impossible de désarchiver la question.");
        $this->archive = 0;
        return true;
    }


    public function approve() {
        if(!DB::exec("UPDATE questions SET approve = 0, updated = updated WHERE id = ".$this->id)) throw new QuestionException("Impossible d'approuver la question.");
        if(!DB::query('SELECT COUNT(*) FROM questions WHERE hidden = 0 AND approve = 1 AND user_id = '.$this->user_id, true, true)) {
            SYS::sendMessage([
                'user_id' => $this->user_id,
                'subject' => 'Toutes vos questions ont été approuvées.',
                'body' => 'Super! 👍',
            ]);
        }
        $this->approve = 0;
        return true;
    }


    public function lock() {
        if(!DB::exec("UPDATE questions SET locked = 1, updated = updated WHERE id = ".$this->id)) throw new QuestionException("Impossible de réserver la question.");
        $this->locked = 1;
        return $this;
    }


    public function unlock() {
        if(!DB::exec("UPDATE questions SET locked = 0, updated = updated WHERE id = ".$this->id)) throw new QuestionException("Impossible de libérer la question.");
        $this->locked = 0;
        return $this;
    }


    public function getCategories() {
        return DB::query("SELECT categories.*, themes.name AS theme_name, themes.temporary FROM questions_categories INNER JOIN categories ON categories.id = questions_categories.category_id INNER JOIN themes ON themes.id = categories.theme_id WHERE questions_categories.question_id = ".$this->id." ORDER BY themes.name ASC, categories.name ASC");
    }


    public function getLevels() {
        return DB::query("SELECT levels.* FROM questions_levels INNER JOIN levels ON levels.id = questions_levels.level_id WHERE questions_levels.question_id = ".$this->id." ORDER BY levels.name ASC ;");
    }


    public function renderPreview() {
        $str = '<p><strong>Question</strong>:&nbsp;';
        $str .= $this->question.'</p>';
        $str .= '<details><summary data-id="'.$this->id.'" class="question_info">Détails</summary>';
        $str .= preg_replace('#<br>#i','',$this->renderInfoAnswer());
        $str .= preg_replace('#<br>#i','',$this->renderInfoFooter());
        $str .= '</details>';
        return $str;
    }


    public function renderInfo() {
        $str = $this->renderInfoHeader();
        $str .= $this->renderInfoAnswer();
        $str .= $this->renderInfoFooter();
        return $str;
    }


    protected function renderInfoHeader() {
        $str = '<h3>Type</h3>';
        $str .= '<p>'.static::TYPE.'</p>';
        $str .= '<br>';
        $str .= '<h3>Question</h3>';
        $str .= '<p>'.$this->question.'</p>';
        return $str;
    }


    protected function renderInfoAnswer() {
        $str = '<br>';
        $str .= '<h3>Réponse</h3>';
        $str .= '<p>'.$this->answer.'</p>';
        return $str;
    }


    protected function renderInfoFooter() {
        $str = '<br>';
        $str .= '<h3>Niveaux</h3>';
        foreach($this->getLevels() as $level) $levels[] = $level->name;
        $str .= '<p><div>'.join('</div><div>', $levels).'</div></p>';
        foreach($this->getCategories() as $category) $categories[] = '<span'.($category->temporary ? ' class="stroke"' : '').'>'.$category->theme_name.' / '.$category->name.'</span>';
        if(!empty($categories)) {
            $str .= '<br>';
            $str .= '<h3>Catégories</h3>';
            $str .= '<p><div>'.join('</div><div>', $categories).'</div></p>';
        }
        if($this->timesense) $options[] = 'Change avec le temps';
        if($this->archive) $options[] = 'Archive';
        if(!empty($options)) {
            $str .= '<br>';
            $str .= '<h3>Options</h3>';
            $str .= '<p><div>'.join('</div><div>', $options).'</div></p>';
        }
        if($this->user_id) {
            $str .= '<br>';
            $str .= '<h3>Dernière modification par:</h3>';
            $str .= '<p>'.User::load($this->user_id)->name.'<br>&nbsp;('.dateToFrench($this->updated, "l j F Y H:i").')</p>';
        }
        return $str;
    }


    public function render($num, $pts=[]) {
        $str = '<div class="view__section__question">';
        $str .= '<div class="view__section__question__num">'.$num.'.</div>';
        $str .= '<div class="view__section__question__text">'.$this->question.'</div>';
        $str .= '<div class="view__section__question__answer">'.$this->answer.'</div>';
        $str .= '</div>';
        return $str;
    }


}


class Question_Thematique extends Question {
    const TYPE = 'Thématique';
    const SLUG = 'thematique';
}


class Question_Eclair extends Question {
    const TYPE = 'Éclair';
    const SLUG =  'eclair';

}


class Question_Anagramme extends Question {
    const TYPE = 'Anagramme';
    const SLUG = 'anagramme';
}


class Question_Liste extends Question {
    const TYPE = 'Liste';
    const SLUG = 'liste';
}


class Question_Orthographe extends Question {
    const TYPE = 'Vocabulaire';
    const SLUG = 'orthographe';

    protected function renderInfoAnswer() {
        if(empty($this->misc->direction)) $this->misc->direction = 'fix';
        if(empty($this->misc->letters)) $this->misc->letters = '';
        switch($this->misc->direction){
            case 'starts-with': $direction = 'Commence par: '; break;
            case 'ends-with': $direction = 'Fini par: '; break;
            default: $direction = 'Contient: ';
        }
        $str = '<br>';
        $str .= '<h3>Particularité orthographique</h3>';
        $str .= '<p>'.$direction.$this->misc->letters.'</p>';
        $str .= '<br>';
        $str .= '<h3>Sous-questions</h3>';
        foreach($this->misc->subquestions as $item) $str .= '<p style="padding-bottom: 6px;">'.$item->question.' / <strong>'.$item->answer.'</strong></p>';
        return $str;
    }


    public function save() {
        if(!isset($this->data['misc']->subquestions)) $this->data['misc']->subquestions = [];
        foreach($this->data['misc']->subquestions as $item) {
            $item->question = strip_tags(str_replace('</p>',' </p>',$item->question), '<b><i><u><sub><sup>');
            $item->answer = strip_tags(str_replace('</p>',' </p>',$item->answer), '<b><i><u><sub><sup>');
        }
        return parent::save();
    }


    public function render($num, $pts=[]) {
        $str = '<div class="view__section__theme">' . $this->question . '</div>';
        foreach($this->misc->subquestions as $q => $question) {
            $str .= '<div class="view__section__question">';
            $str .= '<div class="view__section__question__num">'.($q+1).'.</div>';
            $str .= '<div class="view__section__question__text">'.$question->question.'</div>';
            $str .= '<div class="view__section__question__answer">'.$question->answer.'</div>';
            $str .= '</div>';
        }
        return $str;
    }

}


class Question_Association extends Question {
    const TYPE = 'Association';
    const SLUG = 'association';

    protected function renderInfoAnswer() {
        $str = '<br>';
        $str .= '<h3>Associations</h3>';
        foreach($this->associations as $association) $str .= '<p>'.$association->name.' / '.$association->value.'</p>';
        return $str;
    }

    public function render($num, $pts=[]) {
        mt_srand(crc32(md5($this->question)));
        $str = '<div class="view__section__theme">' . $this->question . '</div>';
        foreach($this->misc->associations as $association) $associations[$association->name] = $association->value;
        $associations = array_splice($associations, 0, count($pts));
        $shuffle = array_keys($associations);
        shuffle($shuffle);
        shuffle($shuffle);

        $i = 0;
        foreach($associations as $k => $v) {
            $answers[] = ($i + 1) . chr(array_search($k, $shuffle) + 65);
            $str .= '<div class="view__section__columns">';
            $str .= '<div class="view__section__columns__num">'.($i+1).'.</div>';
            $str .= '<div class="view__section__columns__column">'.$k.'</div>';
            $str .= '<div class="view__section__columns__num">'.chr($i+65).'.</div>';
            $str .= '<div class="view__section__columns__column">'.$associations[$shuffle[$i]].'</div>';
            $str .= '</div>';
            $i++;
        }

        $str .= '<div class="view__section__question">';
        $str .= '<div class="view__section__question__text">&nbsp;</div>';
        $str .= '<div class="view__section__question__answer">'.join(', ', $answers).'</div>';
        $str .= '</div>';

        return $str;
    }

}


class Question_Indice extends Question {
    const TYPE = 'Indice';
    const SLUG = 'indice';

    protected function renderInfoAnswer() {
        $str = '<br>';
        $str .= '<h3>Indices</h3>';
        foreach($this->misc->hints as $hint) $str .= '<p style="padding-bottom: 6px;">'.$hint.'</p>';
        $str .= '<br>';
        $str .= '<h3>Réponse</h3>';
        $str .= '<p>'.$this->answer.'</p>';
        return $str;
    }

    public function save() {
        if(!isset($this->data['misc']->hints)) $this->data['misc']->hints = [];
        foreach($this->data['misc']->hints as $k => $hint)
            $this->data['misc']->hints[$k] = strip_tags(str_replace('</p>',' </p>',$hint), '<b><i><u><sub><sup>');
        return parent::save();
    }


    public function render($num, $pts=[]) {
        $str = '<div class="view__section__question">';
        $str .= '<div class="view__section__question__num">'.($pts[0]).' p.</div>';
        $str .= '<div class="view__section__question__text">'.$this->question.'</div>';
        $str .= '</div>';
        foreach($this->misc->hints as $h => $hint) {
            if(empty($pts[$h+1])) break;
            $str .= '<div class="view__section__question">';
            $str .= '<div class="view__section__question__num">'.($pts[$h+1]).' p.</div>';
            $str .= '<div class="view__section__question__text">'.$hint.'</div>';
            $str .= '</div>';
        }
        $str .= '<div class="view__section__question">';
        $str .= '<div class="view__section__question__text">&nbsp;</div>';
        $str .= '<div class="view__section__question__answer">'.$this->answer.'.</div>';
        $str .= '</div>';
        return $str;
    }

}


class Question_PetitIndice extends Question {
    const TYPE = 'Petit Indice';
    const SLUG = 'petitindice';

    protected function renderInfoAnswer() {
        $str = '<br>';
        $str .= '<h3>Indices</h3>';
        foreach($this->misc->hints as $hint) $str .= '<p style="padding-bottom: 6px;">'.$hint.'</p>';
        $str .= '<br>';
        $str .= '<h3>Réponse</h3>';
        $str .= '<p>'.$this->answer.'</p>';
        return $str;
    }

    public function save() {
        if(!isset($this->data['misc']->hints)) $this->data['misc']->hints = [];
        foreach($this->data['misc']->hints as $k => $hint)
            $this->data['misc']->hints[$k] = strip_tags(str_replace('</p>',' </p>',$hint), '<b><i><u><sub><sup>');
        return parent::save();
    }


    public function render($num, $pts=[]) {
        $str = '<div class="view__section__question">';
        $str .= '<div class="view__section__question__num">'.($pts[0]).' p.</div>';
        $str .= '<div class="view__section__question__text">'.$this->question.'</div>';
        $str .= '</div>';
        foreach($this->misc->hints as $h => $hint) {
            if(empty($pts[$h+1])) break;
            $str .= '<div class="view__section__question">';
            $str .= '<div class="view__section__question__num">'.($pts[$h+1]).' p.</div>';
            $str .= '<div class="view__section__question__text">'.$hint.'</div>';
            $str .= '</div>';
        }
        $str .= '<div class="view__section__question">';
        $str .= '<div class="view__section__question__text">&nbsp;</div>';
        $str .= '<div class="view__section__question__answer">'.$this->answer.'.</div>';
        $str .= '</div>';
        return $str;
    }

}



class QuestionException extends Exception { }