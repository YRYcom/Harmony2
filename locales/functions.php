<?php
/**
 * pgettext
 * Effectue la traduction dans un contexte
 *
 * @param string $context Contexte dans lequel se situe la traduction
 * @param string $msgid Message d''identification à traduire
 * @return string
 */
if (!function_exists('pgettext')) {
    function pgettext($context, $msgid) {
        $contextString = "{$context}\004{$msgid}";
        $translation = _($contextString);
        if ($translation === $contextString) return $msgid;
        else  return $translation;
    }
}

/**
 * npgettext
 * Effectue la traduction dans un contexte en tenant compte du pluriel
 *
 * @param string $context Contexte dans lequel se situe la traduction
 * @param string $msgid Message d''identification à traduire
 * @param string $msgid_plural Message d''identification à traduire au pluriel
 * @param int $num 1 ou 2 pour l'utilisation d'un message ou d'un autre
 * @return string
 */
if (!function_exists('npgettext')) {
    function npgettext($context, $msgid, $msgid_plural, $num) {
        if ( ($num < -1) or (1 < $num) )  {
            $num=2;
        } else {
            $num=1;
        }
        $contextString = "{$context}\004{$msgid}";
        $contextStringp = "{$context}\004{$msgid_plural}";
        $translation = ngettext($contextString, $contextStringp, $num);
        if ($translation === $contextString) {
            return $msgid;
        } else if ($translation === $contextStringp) {
            return $msgid_plural;
        } else {
            return $translation;
        }
    }
}

/**
 * __
 * retourne la chaine traduite
 *
 * @param string $message Message d''identification à traduire
 * @param string $context Contexte dans lequel se situe la traduction
 * @return string
 */
function __($message, $context ="") {

    //Si le message est null on renvoi la chaine vide
    if (empty($message))
        return '';

    // A cause de la class template
    // On decode les caracteres HTML
    $encoder = false;
    $messageDecode = html_entity_decode ($message, ENT_QUOTES | ENT_HTML401, 'UTF-8');
    if ($messageDecode != $message) {
        $encoder = true;
        $message = $messageDecode;
    }
    if($context != "") {
        $trad = pgettext($context, $message);
    } else {
        $trad = _($message);
    }

    $estTraduit = false;
    if($trad != $message) {
        $estTraduit = true;
    }

    if($encoder == true) {
        $trad = htmlentities($trad, ENT_QUOTES | ENT_HTML401,'UTF-8');
    }
    if( isset($_COOKIE['FRTSURB']) AND $_COOKIE['FRTSURB'] == "surb" ) {
        if ($estTraduit == true)
            return "<s>".str_replace('\"', '"', $trad).'</s>';
        else
            return "<del>".str_replace('\"', '"', $trad).'</del>';
    }

    return str_replace(['\"', '"', "'", '<' , '>'], ['&quot;','&quot;', '&#39;', '&lt;', '&gt;'], $trad);
}

/**
 * _e
 * affiche la chaine traduite
 * @param $message
 * @param string $context
 */
function _e($message, $context ="") {
    echo __($message, $context) ;
}


/**
 * _n
 *  retourne la chaine traduite en tenant compte du pluriel
 *
 * @param string $message Message d''identification à traduire
 * @param string $message_plural Message d''identification à traduire au pluriel
 * @param int $num 1 ou 2 pour l'utilisation d'un message ou d'un autre
 * @param string $context Contexte dans lequel se situe la traduction
 * @return string
 */
function _n($message, $message_plural, $num, $context ="") {
    if($context != "") {
        return npgettext($context, $message, $message_plural, $num);
    } else {
        return ngettext($message, $message_plural, $num);
    }
}