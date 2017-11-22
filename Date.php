<?php
namespace Harmony2;

use DateTime;

/**
 * Classe de gestion des dates
 *
 */
class Date extends DateTime
{
    function __construct($date=null) {
        if (!is_null($date))
            parent::__construct($date);
    }

    public static function getTabFerie($annee, $id_pays=0) {
        if($id_pays == 0 and defined('DOMAIN_ID_PAYS')) {
            $id_pays = DOMAIN_ID_PAYS;
        }

        $p = easter_date($annee);

        $lpk = mktime(0, 0, 0, date("m", $p), date("d", $p) +1, date("Y", $p) );
        $asc = mktime(0, 0, 0, date("m", $p), date("d", $p) + 39, date("Y", $p) );
        $lp = mktime(0, 0, 0, date("m", $p), date("d", $p) + 50, date("Y", $p) );

        switch($id_pays) {
            case 2:
                $tabFerie = array(
                    $annee.'-01-01',
                    $annee.'-05-01',
                    $annee.'-07-21',
                    $annee.'-08-15',
                    $annee.'-11-01',
                    $annee.'-11-11',
                    $annee.'-12-25',
                    date('Y-m-d', $lpk),
                    date('Y-m-d', $asc),
                    date('Y-m-d', $lp),
                    );
                break;
            case 6:
                $tabFerie = array(
                    $annee.'-01-01',
                    $annee.'-05-01',
                    $annee.'-05-25',
                    $annee.'-06-10',
                    $annee.'-08-15',
                    $annee.'-12-08',
                    $annee.'-12-25',
                    date('Y-m-d', $p),
                    date('Y-m-d', mktime(0, 0, 0, date("m", $p), date("d", $p) - 1, date("Y", $p) )),
                    date('Y-m-d', mktime(0, 0, 0, date("m", $p), date("d", $p) - 47 , date("Y", $p) )),
                    );        
                break;
            case 5:
                $tabFerie = array(
                    $annee.'-01-01',
                    $annee.'-08-01',
                    $annee.'-12-25',
                    date('Y-m-d', $lpk),
                    date('Y-m-d', $asc),
                    date('Y-m-d', $lp),
                );
                break;
            case 1:
            default :
                $tabFerie = array(
                    $annee.'-01-01',
                    $annee.'-05-01',
                    $annee.'-05-08',
                    $annee.'-07-14',
                    $annee.'-08-15',
                    $annee.'-11-01',
                    $annee.'-11-11',
                    $annee.'-12-25',
                    date('Y-m-d', $lpk),
                    date('Y-m-d', $asc),
                    date('Y-m-d', $lp),
                    );
                break;
        }

        sort($tabFerie);

        return $tabFerie;
    }

    function getNextBankHoliday($t = false, $nb = 3, $id_pays = 1)
    {
        if ($t == false) $t= strtotime(date('Y-m-d'));
        $tabFerie = $this->getTabFerie(date('Y'), $id_pays);


        $nbFerie = 0;
        $listFerie = array();

        foreach ($tabFerie as $date) {
            if(strtotime($date) >= $t) {
                $nbFerie++;
                $listFerie[] = $date;
            }
            if ($nbFerie == $nb) {
                return $listFerie;
            }
        }

        $annee = date('Y') + 1;
        $tabFerie = $this->getTabFerie($annee, $id_pays);
        foreach ($tabFerie as $date) {
            if(strtotime($date) >= $t) {
                $nbFerie++;
                $listFerie[] = $date;
            }
            if ($nbFerie == $nb) {
                return $listFerie;
            }
        }

        return $listFerie;
    }

    public function getDayName($date) {
        switch (date('N', strtotime($date))) {
            case 1 :
                return 'lundi';
                break;
            case 2 :
                return 'mardi';
                break;
            case 3 :
                return 'mercredi';
                break;
            case 4 :
                return 'jeudi';
                break;
            case 5 :
                return 'vendredi';
                break;
            case 6 :
                return 'samedi';
                break;
            case 7 :
                return 'dimanche';
                break;
            default :
                return '';
                break;
        }
    }

    public function getMonthName($date, $short=false) {
        switch (date('m', strtotime($date))) {
            case '01' :
                return ($short?'jan.':'janvier');
                break;
            case '02' :
                return ($short?'fév.':'février');
                break;
            case '03' :
                return 'mars';
                break;
            case '04' :
                return ($short?'avr.':'avril');
                break;
            case '05' :
                return 'mai';
                break;
            case '06' :
                return 'juin';
                break;
            case '07' :
                return ($short?'juil.':'juillet');
                break;
            case '08' :
                return 'août';
                break;
            case '09' :
                return ($short?'sep.':'septembre');
                break;
            case '10' :
                return ($short?'oct.':'octobre');
                break;
            case '11' :
                return ($short?'nov.':'novembre');
                break;
            case '12' :
                return ($short?'dec.':'décembre');
                break;
            default :
                return '';
                break;
        }
    }

    public function getLabelBankHoliday($date, $withPrefix = false) {
        $prefix = '';
        switch(date('m-d', strtotime($date))) {
            case '01-01' :
                if ($withPrefix)
                    $prefix = 'le ';
                return $prefix . $this->getDayName($date)." 1er janvier ".date('Y', strtotime($date))." (Jour de l'An)";
                break;
            case '05-01' :
                if ($withPrefix)
                    $prefix = 'le ';
                return $prefix . $this->getDayName($date)." 1er mai ".date('Y', strtotime($date))." (Fête du travail)";
                break;
            case '05-08' :
                if ($withPrefix)
                    $prefix = 'le ';
                return $prefix . $this->getDayName($date)." 8 mai ".date('Y', strtotime($date))." (Fête de la victoire)";
                break;
            case '07-14' :
                if ($withPrefix)
                    $prefix = 'le ';
                return $prefix . $this->getDayName($date)." 14 juillet ".date('Y', strtotime($date))." (Fête Nationale)";
                break;
            case '08-15' :
                if ($withPrefix)
                    $prefix = 'le ';
                return $prefix . $this->getDayName($date)." 15 août ".date('Y', strtotime($date))." (Assomption)";
                break;
            case '11-01' :
                if ($withPrefix)
                    $prefix = 'le ';
                return $prefix . $this->getDayName($date)." 1er novembre ".date('Y', strtotime($date))." (Toussaint)";
                break;
            case '11-11' :
                if ($withPrefix)
                    $prefix = 'le ';
                return $prefix . $this->getDayName($date)." 11 novembre ".date('Y', strtotime($date))." (Armistice)";
                break;
            case '12-25' :
                if ($withPrefix)
                    $prefix = 'le ';
                return $prefix . $this->getDayName($date)." 25 décembre ".date('Y', strtotime($date))." (Noel)";
                break;
            default :
                if ($withPrefix)
                    $prefix = 'le ';
                return $prefix . $this->getDayName($date) . " " . date('d', strtotime($date))." ".$this->getMonthName($date)." ".date('Y', strtotime($date));
                break;
        }
    }

    public function getFormatTextFr($date) {
        return date('d', strtotime($date)) . " " . $this->getMonthName($date) . " " . date('Y', strtotime($date));
    }

    public static function isBankHoliday($date, $id_pays=1) {
        return in_array($date, self::getTabFerie(date('Y', strtotime($date)), $id_pays));
    }
}
