<?php

const RN = "\r\n";
const BR = "<br/>";

function console($val) {
    echo '<script>var test = `' . print_r($val, true) . '`; console.log(test);</script>';
}

function _print_r($val) {
    echo '<pre>'.print_r($val, true).'</pre>';
}

function _var_dump($val) {
    echo '<pre>';
    var_dump($val);
    echo '</pre>';
}

function devprint($obj) {
    if(GEHGen::getLoggedUser()->isDev()) {
        _print_r($obj);
    }
}

function devdump($obj) {
    if(GEHGen::getLoggedUser()->isDev()) {
        _var_dump($obj);
    }
}

function getPageParam() {
    if(!isset($_GET['page']) || !is_numeric($_GET['page'])) $pagenb = 0;
    else $pagenb = ($_GET['page'] ?? 1) - 1;
    if($pagenb < 0) $pagenb = 0;
    return $pagenb;
}

function dateago($time) {
    $now = new DateTime(date('Y-m-d H:i:s'));
    $then = new DateTime(date('Y-m-d H:i:s', $time));
    $diff = $now->diff($then);
    if($diff->y) return $diff->y.' an'.($diff->y > 1 ? 's' : '');
    elseif($diff->m) return $diff->m.' mois';
    elseif(($w = floor($diff->d / 7)) > 1) return $w.' semaine'.($w > 1 ? 's' : '');
    elseif($diff->d) return $diff->d.' jour'.($diff->d > 1 ? 's' : '');
    elseif($diff->h) return $diff->h.' heure'.($diff->h > 1 ? 's' : '');
    elseif($diff->i) return $diff->i.' minute'.($diff->i > 1 ? 's' : '');
    else return $diff->s.' seconde'.($diff->s > 1 ? 's' : '');
}

function dateToFrench($date, $format) {
    $english_days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
    $french_days = array('lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche');
    $english_months = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
    $french_months = array('janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre');
    return str_replace($english_months, $french_months, str_replace($english_days, $french_days, date($format, strtotime($date) ) ) );
}

function lorem_sentence($nb=1) {
    static $li = null;
    if(is_null($li)) $li = new LoremIpsum;
    return $nb > 1 ? $li->sentences($nb) : $li->sentence();
}

function lorem_word($nb=1) {
    static $li = null;
    if(is_null($li)) $li = new LoremIpsum;
    return $nb > 1 ? $li->words($nb) : $li->word();
}

function str_normalize($str) {
	if(class_exists('Normalizer')) {
		$txt = Normalizer::normalize($str, Normalizer::FORM_D);
		$txt = preg_replace('/[\x{0300}-\x{036f}]+/u', '', $txt);
	} else {
		$txt = preg_replace('/\pM+/u', '', $str);
		$txt = preg_replace('/[\x{0300}-\x{036f}]+/u', '', $txt);
		$txt = preg_replace('~&([a-z]{1,2})(acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml|caron);~i', '$1', htmlentities($txt, ENT_QUOTES, 'UTF-8'));
	}
    return $txt;
}

function str_slug($str) {
    $str = str_normalize($str);
    $str = strtolower($str);
    $str = preg_replace('#[^a-z0-9]+#i', '', $str);
    return $str;
}

function filemime($file){
	if(!is_file($file) || (!$file = realpath($file))) return;
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $info = finfo_file($finfo, $file);
    finfo_close($finfo);
	return $info;
}
