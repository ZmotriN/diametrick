<?php

class Quiz implements JsonSerializable {


    private $data = [];

    public function __construct(array $data) {
        if(!empty($data['sections']) && is_string($data['sections']))
            $data['sections'] = json_decode($data['sections']);
        if(!empty($data['comments']) && is_string($data['comments']))
            $data['comments'] = json_decode($data['comments']);
        // _print_r($data);
        $this->data = $data;
    }


    public function __get($key) {
        if($key == 'data') return $this->data;
        else return isset($this->data[$key]) ? $this->data[$key] : null;
    }


    public function __set($key, $val) {
        $this->data[$key] = $val;
    }


    public function jsonSerialize() {
        return $this->data;
    }


    public static function load($data) {
        if(is_object($data)) {
            $data = (array)$data;
        } elseif(is_numeric($data)) {
            if(!$obj = DB::getObject('quizzes', $data)) throw new QuizException("ID de questionnaire invalide.");
            $data = (array)$obj;
        } elseif(!is_array($data)) {
            throw new QuizException("Data invalide.");
        }
        return new self($data);
    }


    public function save() {
        if($this->id) $this->update();
        else $this->insert();
        return $this;
    }


    private function insert() {
        if(!$this->name) throw new QuizException("Nom de questionnaire invalide.");
        if(!$this->gabarit_id) throw new QuizException("ID de gabarit invalide.");
        if(!$this->verifyName()) {
            for($i = 1; !$this->verifyName($this->name.' ('.$i.')'); $i++);
            $this->name = $this->name.' ('.$i.')';
        }
        $this->user_id = SYS::getLoggedUser()->id;
        $this->prepare();
        $data = $this->data;
        $data['comments'] = '[]';
        $data['sections'] = json_encode($data['sections']);
        if(!$id = DB::insert('quizzes', $data)) throw new QuizException("Impossible d'ajouter le questionnaire.");
        $this->data['id'] = $id;
        return true;
    }


    private function update() {
        if(!$this->id) throw new QuizException("ID de questionnaire invalide.");
        if(!$this->name) throw new QuizException("Nom de questionnaire invalide.");
        if(!$this->verifyName()) throw new QuizException("Nom de questionnaire déjà utilisé.");
        $data = $this->data;
        unset($data['id']);
        $data['updated'] = date('Y-m-d H:i:s');
        $data['sections'] = json_encode($data['sections']);
        if(isset($data['comments'])) $data['comments'] = json_encode($data['comments']);
        if(!DB::update('quizzes', ['id' => $this->id], $data)) throw new QuizException("Impossible de mettre à jour le questionnaire.");
        else return true;
    }


    public function delete($free=0) {
        if(!$this->id) throw new QuizException("ID de questionnaire invalide.");
        if($this->approved && !SYS::getLoggedUser()->isAdmin()) throw new QuizException("Vous n'êtes pas autorisé à supprimer ce questionnaire.");
        if($free && $this->approved) {
            if(($qids = $this->getQuestionIds())) {
                DB::exec('UPDATE questions SET updated = updated, lastused = "0000-00-00 00:00:00" WHERE id IN ('.join(',',$qids).')');
            }
        } else {
            if(($qids = $this->getQuestionIds())) {
                DB::exec('DELETE FROM questions WHERE hidden = 1 AND id IN ('.join(',',$qids).')');
                DB::exec('UPDATE questions SET updated = updated, locked = 0 WHERE id IN ('.join(',',$qids).')');
            }
        }
        if(!DB::delete('quizzes', $this->id)) throw new QuizException("Impossible de supprimer le questionnaire.");
        return true;
    }


    private function verifyName($name=null) {
        if(!$name) $name = $this->name;
        if($this->id) return !DB::query('SELECT COUNT(*) as nb FROM quizzes WHERE name = "'.DB::escape($name).'" AND id <> ' . $this->id . ';', true, true);
        else return !DB::query('SELECT COUNT(*) as nb FROM quizzes WHERE name = "'.DB::escape($name).'";', true, true);
    }


    public function getQuestionIds() {
        foreach($this->sections as $section)
            foreach($section->blocks as $block)
                foreach($block->questions as $question)
                    if($question->id != 0)
                        $ids[] = $question->id;
        if(!empty($ids)) sort($ids);
        return $ids ?? [];
    }


    public function getIndexedQuestions() {
        if(!$ids = $this->getQuestionIds()) return [];
        foreach(DB::query("SELECT DISTINCT(questions.id), questions.question, questions.answer, questions.type_id, questions.user_id, questions.misc, questions.hidden, questions.approve, questions.updated FROM questions WHERE questions.id IN (".join(',', $ids).")") as $item){
            $question = Question::load($item);
            $questions[$question->id] = $question;
        }
        return $questions ?? [];
    }


    private function fillEmptyQuestions() {
        foreach($this->sections as $section) {
            $section->blocks = [];
            for($i = 0; $i < $section->block_number; $i++) {
                $block = new stdClass;
                for($j = 0; $j < $section->question_number; $j++)
                    $block->questions[] = (object)['id' => 0];
                $section->blocks[] = $block;
            }
        }
    }


    private function fillCategories() {
        foreach($this->sections as $section) {
            $q = 0;
            $categories = SYS::getRandCategories($section->theme_id, $section->block_number * $section->question_number);
            foreach($section->blocks as $block) {
                foreach($block->questions as $question) {
                    if(!$section->theme_id || $section->new_questions) $question->category_id = 0;
                    else $question->category_id = $categories[$q++ % count($categories)];
                }
            }
        }
    }


    private function fillFilters() {
        $default = (object)['level' => $this->level_id];
        foreach($this->sections as $section) {
            foreach($section->blocks as $block) {
                foreach($block->questions as $question) {
                    $question->filters = clone $default;
                    $question->filters->type = $section->type_id;
                    if($section->theme_id) $question->filters->theme = $section->theme_id;
                    if($question->category_id) $question->filters->category = $question->category_id;
                }
            }
        }
    }


    private function fillQuestions() {
        foreach($this->sections as $section) {
            if(in_array($section->type_id, Question::SKIP)) continue;
            if($section->new_questions) continue;
            foreach($section->blocks as $block) {
                foreach($block->questions as $question) {
                    if(!$quest = SYS::pickQuestion($question->filters)) continue;
                    $question->id = $quest->id;
                }
            }
        }
    }


    private function fillNewQuestions() {
        foreach($this->sections as $section) {
            if(in_array($section->type_id, Question::SKIP)) continue;
            foreach($section->blocks as $block) {
                foreach($block->questions as $question) {
                    if($question->id) continue;
                    if(!$quest = SYS::createEmptyQuestion($question->filters)) continue;
                    $question->id = $quest->id;
                }
            }
        }
    }


    private function prepare() {
        $this->sections = Gabarit::load($this->gabarit_id)->sections;
        $this->fillEmptyQuestions();
        $this->fillCategories();
    }


    public function generate() {
        if($this->generated) return $this;
        $this->fillFilters();
        $this->fillQuestions();
        $this->fillNewQuestions();
        $this->generated = 1;
        $this->save();
        return $this;
    }


    public function setTheme($section_idx, $block_idx, $theme) {
        $this->data['sections'][$section_idx]->blocks[$block_idx]->theme = $theme;
        return $this->save();
    }


    public function finish() {
        foreach($this->getIndexedQuestions() as $question) {
            $qids[] = $question->id;
            if($question->approve)
                throw new QuizException("Impossible de terminer le questionnaire. Des questions sont toujours en approbation.");
        }
        if(!empty($qids)) DB::exec('UPDATE questions SET hidden = 0, locked = 0, updated = updated, lastused = CURRENT_TIMESTAMP WHERE id IN ('.join(',', $qids).')');
        $this->edited = 1;
        $this->approved = 1;
        $this->save();
        return $this;
    }


    public function toApprove($root) {
        foreach($this->getIndexedQuestions() as $question)
            if($question->approve)
                throw new QuizException("Impossible de terminer le questionnaire. Des questions sont toujours en attente d'approbation.");
        SYS::sendMessage([
            'user_id' => -1,
            'subject' => 'Le questionnaire "'.$this->name.'" est en attente d\'approbation.',
            'body' => '<a href="'.$root.'questionnaires/modifier?id='.$this->id.'">'.$this->name.'</a>',
        ]);
        $this->edited = 1;
        $this->save();
        return $this;
    }


    public function approve() {
        foreach($this->getIndexedQuestions() as $question) {
            $qids[] = $question->id;
            if($question->approve)
                throw new QuizException("Impossible de terminer le questionnaire. Des questions sont toujours en approbation.");
        }
        if(!empty($qids)) DB::exec('UPDATE questions SET hidden = 0, locked = 0, updated = updated, lastused = CURRENT_TIMESTAMP WHERE id IN ('.join(',', $qids).')');
        SYS::sendMessage([
            'user_id' => $this->user_id,
            'subject' => 'Votre questionnaire "'.$this->name.'" a été approuvé.',
            'body' => 'Bravo!  👏',
        ]);
        $this->edited = 1;
        $this->approved = 1;
        $this->save();
        return $this;
    }


    public function disapprove($comment) {
        $this->data['comments'][] = [
            'user_id' => SYS::getLoggedUser()->id,
            'published' => date('Y-m-d H:i:i'),
            'comment' => strip_tags($comment, '<b><i><u><sub><sup><p><div><h3><h4><em><br>'),
        ];
        SYS::sendMessage([
            'from_id' => SYS::getLoggedUser()->id,
            'user_id' => $this->user_id,
            'subject' => 'Votre questionnaire "'.$this->name.'" a été rejeté.',
            'body' => $comment,
        ]);
        $this->edited = 0;
        $this->save();
        return $this;
    }


    public function newQuestion($data){
        $question = $this->data['sections'][$data->section_idx]->blocks[$data->block_idx]->questions[$data->question_idx];
        $newquestion = SYS::createEmptyQuestion($question->filters);
        DB::exec('UPDATE questions SET locked = 0, updated = updated WHERE id = '.$question->id);
        $question->id = $newquestion->id;
        $this->save();
        return $newquestion;
    }


    public function swapQuestion($data) {
        $question = $this->data['sections'][$data->section_idx]->blocks[$data->block_idx]->questions[$data->question_idx];
        if(!$newquestion = SYS::pickQuestion($question->filters)) throw new QuizException("Impossible d'échanger la question pour une nouvelle. Il n'y a plus de questions disponible pour cette catégorie.");
        DB::exec('UPDATE questions SET locked = 0, updated = updated WHERE id = '.$question->id);
        $question->id = $newquestion->id;
        $this->save();
        return $newquestion;
    }


    public function changeOwner($user_id) {
        DB::exec('UPDATE quizzes SET user_id = '.$user_id.', updated = updated WHERE id = '.$this->id);
    }

    public function rename($name) {
        DB::exec('UPDATE quizzes SET name = "'.DB::escape($name).'", updated = updated WHERE id = '.$this->id);
    }

}

class QuizException extends Exception { }