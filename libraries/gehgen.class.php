<?php

class GEHGen {

    const DEFAULT_FILTERS = [
        'type'         => 0,
        'level'        => 0,
        'theme'        => 0,
        'category'     => 0,
        'user'         => 0,
        'eligible'     => 0,
        'archive'      => 0,
        'approve'      => 0,
        'timesense'    => 0,
        'inverse'      => 0,
        'neverupdated' => 0,
        'myquestions'  => 0,
        'search'       => '',
    ];


    public static function session() {
        return PROJECT . '_user_id';
    }


    public static function updateLastSeen() {
        if(!empty($_SESSION[self::session()])) DB::exec('UPDATE users SET updated = updated, lastseen = CURRENT_TIMESTAMP WHERE id = '.$_SESSION[self::session()]);
    }


    public static function getLoggedUser() {
        static $user = null;
        if($user === null) {
            if(empty($_SESSION[self::session()])) return null;
            if(!$_SESSION[self::session()]) return null;
            $query = 'SELECT users.*, roles.slug AS role_slug, roles.name AS role_name FROM users INNER JOIN roles ON roles.id = users.role_id WHERE users.id = '.$_SESSION[self::session()].' AND users.active = 1 LIMIT 1';
            if(!$user = DB::query($query, true)) {
                unset($_SESSION[self::session()]);
                return null;
            } else $user = User::load($user);
        }
        return $user;
    }


    public static function login($email, $password) {
        $query = 'SELECT users.*, roles.slug AS role_slug FROM users INNER JOIN roles ON roles.id = users.role_id WHERE users.email = "'.DB::escape($email).'" AND users.pass = "'.DB::escape(sha1($password)).'" AND users.active = 1 LIMIT 1';
        if(!$user = DB::query($query, true)) throw new GEHGenException("Échec de connexion");
        $_SESSION[self::session()] = $user->id;
        return true;
    }


    public static function logout($root) {
        unset($_SESSION[self::session()]);
        header('location: '.$root.'connexion');
        die();
    }


    public static function getDefaultFilters() {
        return (object)self::DEFAULT_FILTERS;
    }


    public static function getFilters() {
        if(empty($_SESSION['filters'])){
            $_SESSION['filters'] = self::getDefaultFilters();
        } else {
            foreach(self::getDefaultFilters() as $k => $v)
                if(!isset($_SESSION['filters']->{$k}))
                    $_SESSION['filters']->{$k} = $v;
        }
        return $_SESSION['filters'];
    }


    public static function setFilters($filters) {
        $_SESSION['filters'] = $filters;
        foreach(self::getDefaultFilters() as $k => $v)
            if(!isset($_SESSION['filters']->{$k}))
                $_SESSION['filters']->{$k} = $v;
    }


    public static function getQuizzes($order='quizzes.updated DESC, quizzes.name ASC') {
        $query = 'SELECT quizzes.id, quizzes.level_id, levels.name AS level_name, quizzes.gabarit_id, gabarits.name AS gabarit_name, quizzes.user_id, users.name AS user_name, quizzes.name, quizzes.edited, quizzes.approved, quizzes.updated FROM quizzes INNER JOIN levels ON levels.id = quizzes.level_id INNER JOIN gabarits ON gabarits.id = quizzes.gabarit_id INNER JOIN users ON users.id = quizzes.user_id INNER JOIN seasons ON seasons.id = gabarits.season_id AND seasons.active = 1 ORDER BY '.$order;
        foreach(DB::query($query) as $obj) {
            $quizzes[] = Quiz::load($obj);
        }
        return isset($quizzes) ? $quizzes : [];
    }


    public static function getPageQuizzes($page=0, $order='quizzes.updated DESC, quizzes.name ASC') {
        $query = 'SELECT quizzes.id, quizzes.level_id, levels.name AS level_name, quizzes.gabarit_id, gabarits.name AS gabarit_name, quizzes.user_id, users.name AS user_name, quizzes.name, quizzes.edited, quizzes.approved, quizzes.updated FROM quizzes INNER JOIN levels ON levels.id = quizzes.level_id INNER JOIN gabarits ON gabarits.id = quizzes.gabarit_id INNER JOIN users ON users.id = quizzes.user_id INNER JOIN seasons ON seasons.id = gabarits.season_id AND seasons.active = 1';
        if(!self::getLoggedUser()->isAdmin()) $query .= ' AND users.id = '.self::getLoggedUser()->id;
        $query .= ' ORDER BY '.$order;
        $page = DB::getPage($query, $page);
        $page->quizzes = [];
        foreach($page->items as $item) $page->quizzes[] = Quiz::load($item);
        unset($page->items);
        return $page;
    }


    public static function getActiveSeason() {
        return DB::query("SELECT id FROM seasons WHERE active = 1 LIMIT 1", true, true);
    }


    public static function getSeasons($order='updated DESC, name ASC') {
        foreach(DB::query("SELECT * FROM seasons ORDER BY ".$order) as $obj) {
            $seasons[] = Season::load($obj);
        }
        return isset($seasons) ? $seasons : [];
    }


    public static function getPageSeason($page=0, $order='updated DESC, name ASC') {
        $query = "SELECT * FROM seasons ORDER BY ".$order;
        $page = DB::getPage($query, $page);
        $page->seasons = [];
        foreach($page->items as $item) $page->seasons[] = Season::load($item);
        unset($page->items);
        return $page;
    }


    public static function getGabarits($order='updated DESC, name ASC') {
        foreach(DB::query("SELECT gabarits.*, seasons.`name` AS season_name FROM gabarits INNER JOIN seasons ON seasons.id = gabarits.season_id ORDER BY ".$order) as $obj) {
            $gabarits[] = Gabarit::load($obj);
        }
        return isset($gabarits) ? $gabarits : [];
    }


    public static function getPageGabarits($page=0, $order='updated DESC, name ASC') {
        $query = "SELECT gabarits.*, seasons.`name` AS season_name FROM gabarits INNER JOIN seasons ON seasons.id = gabarits.season_id ORDER BY ".$order;
        $page = DB::getPage($query, $page);
        $page->gabarits = [];
        foreach($page->items as $item) $page->gabarits[] = Gabarit::load($item);
        unset($page->items);
        return $page;
    }


    public static function getAvaibleGabarits($order='name ASC') {
        foreach(DB::query('SELECT gabarits.*, seasons.name AS season_name FROM gabarits INNER JOIN seasons ON seasons.active = 1 AND seasons.id = gabarits.season_id WHERE gabarits.locked = 1 ORDER BY '.$order) as $obj){
            $gabarits[] = Gabarit::load($obj);
        }
        return isset($gabarits) ? $gabarits : [];
    }


    public static function getThemes($order='updated DESC, name ASC', $notemp=false) {
        foreach(DB::query("SELECT * FROM themes ORDER BY ".$order) as $obj) {
            if($notemp && $obj->temporary) continue;
            $themes[] = Theme::load($obj);
        }
        return isset($themes) ? $themes : [];
    }


    public static function getPageThemes($page=0, $order='updated DESC, name ASC') {
        $query = "SELECT * FROM themes ORDER BY ".$order;
        $page = DB::getPage($query, $page);
        $page->themes = [];
        foreach($page->items as $item) $page->themes[] = Theme::load($item);
        unset($page->items);
        return $page;
    }


    public static function getUsers($order='updated DESC, name ASC') {
        foreach(DB::query("SELECT users.*, roles.name AS role_name, users.lastseen FROM users INNER JOIN roles ON users.role_id = roles.id ORDER BY ".$order) as $obj) {
            $users[] = User::load($obj);
        }
        return isset($users) ? $users : [];
    }


    public static function getPageUsers($page=0, $order='updated DESC, name ASC') {
        $query = "SELECT users.*, roles.name AS role_name, users.lastseen FROM users INNER JOIN roles ON users.role_id = roles.id ORDER BY ".$order;
        $page = DB::getPage($query, $page);
        $page->users = [];
        foreach($page->items as $item) $page->users[] = User::load($item);
        unset($page->items);
        return $page;
    }


    public static function getRoles($order='id ASC') {
        return DB::query("SELECT * FROM roles ORDER BY ".$order);
    }


    public static function getLevels($order='updated DESC, name ASC') {
        foreach(DB::query("SELECT * FROM levels ORDER BY ".$order) as $obj) {
            $levels[] = Level::load($obj);
        }
        return isset($levels) ? $levels : [];
    }


    public static function getPageLevels($page=0, $order='updated DESC, name ASC') {
        $query = "SELECT * FROM levels ORDER BY ".$order;
        $page = DB::getPage($query, $page);
        $page->levels = [];
        foreach($page->items as $item) $page->levels[] = Level::load($item);
        unset($page->items);
        return $page;
    }


    public static function getModes($order='updated DESC, name ASC') {
        foreach(DB::query("SELECT * FROM modes ORDER BY ".$order) as $obj) {
            $modes[] = Mode::load($obj);
        }
        return isset($modes) ? $modes : [];
    }


    public static function getTypes($order='updated DESC, name ASC') {
        foreach(DB::query("SELECT * FROM types ORDER BY ".$order) as $obj) {
            $types[] = Type::load($obj);
        }
        return isset($types) ? $types : [];
    }


    public static function getRandCategories($theme_id=0, $limit=0) {
        foreach(DB::query('SELECT id FROM categories'.($theme_id ? ' WHERE theme_id = '.$theme_id : '').' ORDER BY RAND()'.($limit ? ' LIMIT '.$limit : '').';') as $obj) {
            $categories[] = $obj->id;
        }
        return $categories ?? [];
    }


    public static function getPageQuestions($page=0, $order='questions.updated DESC, questions.id DESC') {
        $filters = self::getFilters();
        $query = self::createFilteredQuestionsQuery($filters);
        if($filters->inverse) $order = 'questions.updated ASC, questions.id ASC';
        $query .= " ORDER BY ".$order;
        $page = DB::getPage($query, $page);
        $page->questions = [];
        foreach($page->items as $item) $page->questions[] = Question::load($item);
        unset($page->items);
        return $page;
    }


    public static function getQuestionWaitingCount() {
        return DB::query('SELECT COUNT(*) FROM questions WHERE hidden = 0 AND approve = 1' ,true, true);
    }


    public static function createFilteredQuestionsQuery($filters) {
        if(!$user = GEHGen::getLoggedUser()) throw new GEHGenException("Échec de connexion");

        $tables = [];
        $where = ['questions.hidden = 0'];

        $query = "SELECT DISTINCT(questions.id), questions.question, questions.type_id, questions.updated FROM questions";

        if($filters->type) $where[] = "questions.type_id = ".$filters->type;

        if($filters->eligible) {
            $where[] = 'questions.archive = 0';
            $where[] = 'questions.locked = 0';
            $where[] = 'questions.approve = 0';
            $where[] = 'YEAR(lastused) < '.date('Y',strtotime('now - '.QUEST_TTL));
        } elseif($filters->archive) {
            $where[] = 'questions.archive = 1';
        } else {
            $where[] = 'questions.archive = 0';
        }

        if($filters->approve) {
            $where[] = 'questions.approve = 1';
        } else {
            $where[] = '(questions.approve = 0 OR questions.user_id = '.$user->id.')';
        }

        if($filters->timesense) {
            $where[] = 'questions.timesense = 1';
        }

        if($filters->neverupdated) {
            $where[] = 'questions.updated = "0000=00-00 00:00:00"';
        }

        if($filters->myquestions) {
            $where[] = 'questions.user_id = '.$user->id;
        }

        if($filters->user) {
            $where[] = 'questions.user_id = '.$filters->user;
        }

        if($filters->level) {
            $tables[] = "INNER JOIN questions_levels ON questions_levels.question_id = questions.id AND questions_levels.level_id = ".$filters->level;
        }

        if($filters->theme || $filters->category) {
            $tables[] = "INNER JOIN questions_categories ON questions_categories.question_id = questions.id".($filters->category ? " AND questions_categories.category_id = ".$filters->category : '');
            if($filters->theme) $tables[] = "INNER JOIN categories ON questions_categories.category_id = categories.id".($filters->theme ? " AND categories.theme_id = ".$filters->theme : '');
        }

        if(trim($filters->search)) {
            $search = DB::escape('%'.str_replace(' ', '%', $filters->search).'%');
            $where[] = '(questions.question LIKE "'.$search.'" OR questions.answer LIKE "'.$search.'" OR questions.misc LIKE "'.$search.'")';
        }

        if(!empty($tables)) $query .= " ".join(' ',$tables);
        if(!empty($where)) $query .= " WHERE ".join(' AND ', $where);

        return $query;
    }


    public static function pickQuestion($filters) {
        if(is_array($filters)) $filters = (object)$filters;
        foreach(self::getDefaultFilters() as $k => $v)
            if(!isset($filters->{$k})) $filters->{$k} = $v;
        $filters->eligible = 1;
        $query = self::createFilteredQuestionsQuery($filters);
        if(!$results = DB::query($query.' ORDER BY RAND() LIMIT 1;', true)) return false;
        return Question::load($results)->lock();
    }


    public static function createEmptyQuestion($filters) {
        if(is_array($filters)) $filters = (object)$filters;
        foreach(self::getDefaultFilters() as $k => $v)
            if(!isset($filters->{$k})) $filters->{$k} = $v;
        $data = [
            'type_id' => $filters->type,
            'question' => LOREM_IPSUM ? lorem_sentence() : '',
            'answer' => LOREM_IPSUM ? lorem_word(3) : '',
            'hidden' => 1,
            'locked' => 1,
            'misc' => new stdClass,
        ];
        if(!empty($filters->level)) $data['levels'] = [$filters->level];
        if(!empty($filters->category)) $data['categories'] = [$filters->category];
        return Question::load($data)->save();
    }


    public static function sendMessage($data) {
        $data += [
            'user_id' => 0,
            'from_id' => 0,
            'subject' => '',
            'body' => '',
        ];
        if($data['user_id'] == -1) {
            foreach(DB::query('SELECT users.id, roles.slug AS role_slug FROM users INNER JOIN roles ON roles.id = users.role_id WHERE users.active = 1') as $user)
                if(User::load($user)->isAdmin())
                    self::sendMessage(['user_id' => $user->id] + $data);
        } elseif(!$data['user_id']) {
            foreach(DB::query('SELECT id FROM users WHERE active = 1') as $user)
                self::sendMessage(['user_id' => $user->id] + $data);
        } else Message::load($data)->save();
    }


    public static function backupDatabase() {
        $tempdir = str_replace('\\','/',sys_get_temp_dir()).'/gehgen/';
        $sqldest = $tempdir.'gehgen_'.date('YmdHis').'.sql';
        if(!is_dir($tempdir))
            if(!@mkdir($tempdir))
                throw new GEHGenException("Impossible de créer le fichier temporaire.");
        if(!DB::dump($sqldest)) throw new GEHGenException("Impossible de sauvegarder la base de données.");
        if(!is_file($sqldest) || !filesize($sqldest)) throw new GEHGenException("Sauvegarde de base de données invalide.");
        $database = gzencode(file_get_contents($sqldest),9);
        file_put_contents(($gzdest = $sqldest.'.gz'), $database);
        unlink($sqldest);
        return $gzdest;
    }


    public static function downloadFile($file) {
        if(!$tempfile = realpath(str_replace('\\','/',sys_get_temp_dir()).'/gehgen/'.pathinfo($file, PATHINFO_BASENAME)))
            throw new GEHGenException("Fichier invalide.");
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary");
        header('Content-Disposition: attachment; filename="' . basename($tempfile) . '"');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Expires: 0');
        header('Content-Length: '.filesize($tempfile));
        readfile($tempfile);
        unlink($tempfile);
        die();
    }

 }

class GEHGenException extends Exception { }