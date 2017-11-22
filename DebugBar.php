<?php
namespace Harmony2;
use Harmony2\Http\Request;

/**
 * Barre de debug
 *
 */
class DebugBar
{
  /**
   * Evite le retour de debug en mode Ajax
   *
   * @var boolean
   */
  static private $AjaxMode = false;
  /**
   * Si la barre de debug est activée
   *
   * @var boolean
   */
  static private $init = false;
  /**
   * Les messages de debug
   *
   * @var array
   */
  static private $timedebugaff = array();
  /**
   * Les requêtes exécutées
   *
   * @var array
   */
  static private $requete = array();
  /**
   * Le temps au lancement du debug
   *
   * @var float
   */
  static private $timedebugaffdebut;
  /**
   * Le temps à la fin du debug
   *
   * @var float
   */
  static private $timedebugafffin;
  /**
   * Le titre de la fenêtre de debug
   *
   * @var string
   */
  static private $titre_fenetre;
  /**
   * Commentaire sur le debug
   *
   * @var string
   */
  static private $commentaire = '';

  static private $securekey = '';

  static private $domain = '';

  /** @var  Request $request */
  static private $request;

  /**
   * Permet de savoir si l'utilisateur à activé le debug
	 * @return bool
	 */
  static public function getStatut(){
    if (self::isInit()==false)
      return false;
		if(self::$request->issetCookie('ADMINISTRATEUR') && self::$request->cookie('ADMINISTRATEUR') == self::$securekey) {
      return true;
    } else {
      return false;
    }
  }

  /**
   * Active ou desactive le debug
	 *
	 * @return bool
	 */
  static public function switchStatut(){
    if (self::isInit()==false)
      return false;
    if(self::getStatut() == true) {
			self::$request->setcookie('ADMINISTRATEUR', null, mktime(0, 0, 0, 12, 31, 2037), '/', self::$domain);
			self::$request->setcookie('ADMINISTRATEUR', null);
    } else {
			self::$request->setcookie('ADMINISTRATEUR', self::$securekey, mktime(0, 0, 0, 12, 31, 2037), '/', self::$domain);
			self::$request->setcookie('ADMINISTRATEUR', self::$securekey);
    }
    return true;
  }

  /**
   * Initialisation de les parametre du debug
   *
	 * @param Request $request
	 * @param string $titreFenetre
	 * @param $securekey
	 * @param $domain
	 */
  static public function start(Request $request, $titreFenetre = '', $securekey, $domain)
  {
    self::$init = true;
    self::$timedebugaffdebut = self::microtime_ms();
    self::$titre_fenetre = $titreFenetre;
    self::$securekey = $securekey;
    self::$domain = $domain;
    self::$request = $request;
  }

  /**
   * Ajoute un message de debug
   *
   * @param string $message
   */
  static public function ajouterMessage($message)
  {
    if (self::isInit())
      self::$timedebugaff[] = array($message, self::microtime_ms(), round(memory_get_usage() / 1024, 2));
  }

  /**
   * Ajoute une requête et ses informations au debug
   *
   * @param string $requete La requête
   * @param float $temps Le temps de la requête
   * @param string $erreur Le message d'erreur
   */
  static public function ajouterRequete($requete, $temps, $erreur)
  {
    if (self::isInit())
      self::$requete[] = array($requete, $temps, $erreur);
  }

  static public function getCountRequeste(){
    if(is_array(self::$requete)){
      return count(self::$requete);
    } else {
      return 0;
    }
  }

  /**
   * Ajoute un commentaire de debug
   *
   * @param string $commentaire
   */
  static public function ajouterCommentaire($commentaire)
  {
    if (self::isInit())
      self::$commentaire = $commentaire;
  }

  /**
   * AjaxMode evite le retour du debug en mode ajax
   *
   */
  static public function setAjaxMode()
  {
    self::$AjaxMode = true;
  }

  /**
   * Affiche le code javascript afin de lancer la fenêtre de debug
	 * @return bool
	 */
  static public function parse()
  {
    if(self::isInit()==false or  self::getStatut() == false)
      return false;

    if (self::isInit()) {
      self::$timedebugafffin = self::microtime_ms();

      $contenu = '';
      if (is_array(self::$requete)) {
        $i = 0;
        $contenu .= '<b>Script</b> : ' . self::$request->server('PHP_SELF') . '<br/>';
        $contenu .= '<b>URI</b> : ' . self::$request->server('REQUEST_URI') . '<br/>';
        $contenu .= '<b>Date</b> : Le ' . date('d/m/Y') . ' à ' . date('H:i:s') . '<br/>';
        if (self::$commentaire != '') {
          $contenu .= '<b>Commentaire :</b> <br/>' . self::$commentaire . '<br />';
        }

        // Les messages de debug
        $contenu .= '<b>Le cours d execution :</b> <br/><table border="1" cellspacing="0" cellpadding="0" width="100%">';
        $contenu .= '<tr><td width="40" align="center"><b>Num</b></td><td align="center"><b>Designation</b></td><td align="center" width="60"><b>Temps (ms)</b></td><td align="center" width="60"><b>Memoire (Ko)</b></td></tr>';
        foreach (self::$timedebugaff as $v) {
          $i++;
          $contenu .= '<tr><td width="40" align="center">' . $i . '</td><td align="left">' . $v[0] . '</td><td align="center" width="60">' . round(($v[1] - self::$timedebugaffdebut) * 1000, 2) . '</td><td align=center>' . $v[2] . '</td></tr>';
        }
        $contenu .= '<tr><td valign="top" align="right" colspan="2"><b>Temps total d\'execution :</b></td><td valign="top" align="center"><b>' . round((self::$timedebugafffin - self::$timedebugaffdebut) * 1000, 2) . '</b></td><td width="150" align="center">' . round(memory_get_usage() / 1024, 2) . '&nbsp;<b>/</b>&nbsp;' . round(memory_get_peak_usage() / 1024, 2) . '</td></tr>';
        $contenu .= '</table><br/>';

        // les requêtes
        $contenu .= '<b>Les requetes :</b> <br/><table border="1" cellspacing="0" cellpadding="0" width="100%">';
        $contenu .= '<tr><td width="40" align="center"><b>Num</b></td><td align="center"><b>Requete</b></td><td align="center" width="60"><b>Temps (ms)</b></td><td width="20%"  align="center"><b>Erreur</b></td></tr>';
        $temps = 0;

        $i=0;
        foreach (self::$requete as $value) {
          $i++;
          $temps += round($value[1] * 1000, 2);
          $contenu .= '<tr><td valign="top" align="center">' . $i . '</td><td valign="top">' . htmlentities($value[0]) . '</td><td valign="top" align="center">' . round($value[1] * 1000, 2) . ' ms</td><td valign="top" style="color:red;">' . ($value[2] == '' ? '&nbsp;' : $value[2]) . '</td></tr>';

        }
        $contenu .= '<tr><td valign="top" align="right" colspan="2"><b>Temps total d\'execution : </b></td><td valign="top" align="center"><b>' . $temps . '</b></td><td valign="top" style="color:red;"></td></tr>';
        $contenu .= '</table><br/>';


      }

      $contenu = str_replace(array("\n", "\r", "\\", "'"), array(" ", " ", "\\\\", "\'"), $contenu);

      /**echo '
       * <script type="application/javascript">
       * console.info("Script : '.self::$request->server('PHP_SELF').'");
       * console.info("URI : '.self::$request->server('REQUEST_URI').'");
       * console.info("Date : Le '.date('d/m/Y').' à '.date('H:i:s').'");
       * console.info("Le cours d execution :");
       * console.table('.$logArrayTime.');
       * console.info("Temps total d\'execution des scripts : '.round((self::$timedebugafffin-self::$timedebugaffdebut)*1000,2).' ms");
       * console.info("Memoire total consommée des scripts : '.round(memory_get_usage()/1024,2).' / '.round(memory_get_peak_usage()/1024,2).' Ko");
       * console.info("Les requetes : ");
       * console.table('.$logArraySql.');
       * console.info("Temps total d\'execution des requetes : '.$temps.' ms");
       * </script>
       * ';**/

      $javascript = '<script>
var fendebug = window.open(\'\',\'fenetredebug\'); var titre = \'DEBUG ' . self::$titre_fenetre . '\'; var objTitre = fendebug.document.getElementById("titre");
if (!objTitre) { fendebug.document.clear(); fendebug.document.write(\'<html><heade><style>table{ font-family:arial; font-size:11px;} #titre{font-weight:bold;font-size:16px;} body {font-family:arial;font-size:11px;}</style><title>\'+titre+\'</title></head><body></div><div id="titre">\'+titre+\'</div><br/><div id="contenu"></div></body></html>\'); }
fendebug.document.getElementById("contenu").innerHTML = \'' . $contenu . '\' + fendebug.document.getElementById("contenu").innerHTML;							
</script>';

      echo $javascript;

      return true;
    }
    return false;
  }


  /**
   * Permet de savoir si oui ou non la barre de debug est active
   *
   * @return boolean
   */
  static public function isInit()
  {
    return (bool)self::$init;
  }

  /**
   * Retourne un microtime formaté
   *
   * @return float
   */
  static private function microtime_ms()
  {
    list($usec, $sec) = explode(" ", microtime());

    return ((float)$usec + (float)$sec);
  }
}
