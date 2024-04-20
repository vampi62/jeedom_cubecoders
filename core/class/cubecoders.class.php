<?php
/* This file is part of Jeedom.
*
* Jeedom is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* Jeedom is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
*/

/* * ***************************Includes********************************* */
require_once __DIR__  . '/../../../../core/php/core.inc.php';

class cubecoders extends eqLogic {
  /*     * *************************Attributs****************************** */

  /*
  * Permet de définir les possibilités de personnalisation du widget (en cas d'utilisation de la fonction 'toHtml' par exemple)
  * Tableau multidimensionnel - exemple: array('custom' => true, 'custom::layout' => false)
  public static $_widgetPossibility = array();
  */

  /*
  * Permet de crypter/décrypter automatiquement des champs de configuration du plugin
  * Exemple : "param1" & "param2" seront cryptés mais pas "param3"
  public static $_encryptConfigKey = array('param1', 'param2');
  */

  /*     * ***********************Methode static*************************** */

  /*
  * Fonction exécutée automatiquement toutes les minutes par Jeedom
  */
  public static function cron() {}
  

  /*
  * Fonction exécutée automatiquement toutes les 5 minutes par Jeedom
  */
  public static function cron5() {}
  

  /*
  * Fonction exécutée automatiquement toutes les 10 minutes par Jeedom
  */
  public static function cron10() {}
  

  /*
  * Fonction exécutée automatiquement toutes les 15 minutes par Jeedom
  */
  public static function cron15() {}
  

  /*
  * Fonction exécutée automatiquement toutes les 30 minutes par Jeedom
  */
  public static function cron30() {}
  

  /*
  * Fonction exécutée automatiquement toutes les heures par Jeedom
  */
  public static function cronHourly() {}
  

  /*
  * Fonction exécutée automatiquement tous les jours par Jeedom
  public static function cronDaily() {}
  */
  
  /*
  * Permet de déclencher une action avant modification d'une variable de configuration du plugin
  * Exemple avec la variable "param3"
  public static function preConfig_param3( $value ) {
    // do some checks or modify on $value
    return $value;
  }
  */

  /*
  * Permet de déclencher une action après modification d'une variable de configuration du plugin
  * Exemple avec la variable "param3"
  public static function postConfig_param3($value) {
    // no return value
  }
  */

  /*
   * Permet d'indiquer des éléments supplémentaires à remonter dans les informations de configuration
   * lors de la création semi-automatique d'un post sur le forum community
   public static function getConfigForCommunity() {
      return "les infos essentiel de mon plugin";
   }
   */

  /*     * *********************Méthodes d'instance************************* */

  // Fonction exécutée automatiquement avant la création de l'équipement
  public function preInsert() {
  }

  // Fonction exécutée automatiquement après la création de l'équipement
  public function postInsert() {
  }

  // Fonction exécutée automatiquement avant la mise à jour de l'équipement
  public function preUpdate() {
  }

  // Fonction exécutée automatiquement après la mise à jour de l'équipement
  public function postUpdate() {
  }

  // Fonction exécutée automatiquement avant la sauvegarde (création ou mise à jour) de l'équipement
  public function preSave() {
  }

  // Fonction exécutée automatiquement après la sauvegarde (création ou mise à jour) de l'équipement
  public function postSave() {
  }

  // Fonction exécutée automatiquement avant la suppression de l'équipement
  public function preRemove() {
  }

  // Fonction exécutée automatiquement après la suppression de l'équipement
  public function postRemove() {
  }

  /*
  * Permet de crypter/décrypter automatiquement des champs de configuration des équipements
  * Exemple avec le champ "Mot de passe" (password)
  public function decrypt() {
    $this->setConfiguration('password', utils::decrypt($this->getConfiguration('password')));
  }
  public function encrypt() {
    $this->setConfiguration('password', utils::encrypt($this->getConfiguration('password')));
  }
  */

  /*
  * Permet de modifier l'affichage du widget (également utilisable par les commandes)
  public function toHtml($_version = 'dashboard') {}
  */

  /*     * **********************Getteur Setteur*************************** */

  public function request($url) {
    $request_http = new com_http($url);
    $request_http->setNoReportError(true);
    $request_http->setHeader(array('Content-type: application/json','Accept: application/json'));
    $result=$request_http->exec();
    if ($result == "") {
      return "";
    }
    log::add('mcmyadmin','debug',$url);
    log::add('mcmyadmin','debug',$result);
    return json_decode($result,true);
  }
  public function toHtml($_version = 'dashboard') {
    $replace = $this->preToHtml($_version);
    if (!is_array($replace)) {
      return $replace;
    }
    $version = jeedom::versionAlias($_version);
    foreach ($this->getCmd('info') as $cmd) {
      if (!is_object($cmd)) {
        continue;
      }
      $replace['#' . $cmd->getLogicalId() . '#'] = $cmd->execCmd();
      $replace['#' . $cmd->getLogicalId() . '_id#'] = $cmd->getId();
      $replace['#' . $cmd->getLogicalId() . '_valueDate#']= date('d-m-Y H:i:s',strtotime($cmd->getValueDate()));
      $replace['#' . $cmd->getLogicalId() . '_collectDate#'] =date('d-m-Y H:i:s',strtotime($cmd->getCollectDate()));
      $replace['#' . $cmd->getLogicalId() . '_updatetime#'] =date('d-m-Y H:i:s',strtotime( $this->getConfiguration('updatetime')));
      $replace['#lastCommunication#'] =date('d-m-Y H:i:s',strtotime($this->getStatus('lastCommunication')));
      $replace['#numberTryWithoutSuccess#'] = $this->getStatus('numberTryWithoutSuccess', 0);
      if ($cmd->getIsHistorized() == 1) {
        $replace['#' . $cmd->getLogicalId() . '_history#'] = 'history cursor';
      }
    }
    foreach ($this->getCmd('action') as $cmd) {
      if (!is_object($cmd)) {
        continue;
      }
      $replace['#' . $cmd->getLogicalId() . '_id#'] = $cmd->getId();
      if ($cmd->getConfiguration('listValue', '') != '') {
        $listOption = '';
        $elements = explode(';', $cmd->getConfiguration('listValue', ''));
        $foundSelect = false;
        foreach ($elements as $element) {
          list($item_val, $item_text) = explode('|', $element);
          //$coupleArray = explode('|', $element);
          $cmdValue = $cmd->getCmdValue();
          if (is_object($cmdValue) && $cmdValue->getType() == 'info') {
            if ($cmdValue->execCmd() == $item_val) {
              $listOption .= '<option value="' . $item_val . '" selected>' . $item_text . '</option>';
              $foundSelect = true;
            } else {
              $listOption .= '<option value="' . $item_val . '">' . $item_text . '</option>';
            }
          } else {
            $listOption .= '<option value="' . $item_val . '">' . $item_text . '</option>';
          }
        }
        //if (!$foundSelect) {
        //	$listOption = '<option value="">Aucun</option>' . $listOption;
        //}
        //$replace['#listValue#'] = $listOption;
        $replace['#' . $cmd->getLogicalId() . '_id_listValue#'] = $listOption;
        $replace['#' . $cmd->getLogicalId() . '_listValue#'] = $listOption;
      }
    }
    $parameters = $this->getDisplay('parameters');
    if (is_array($parameters)) {
      foreach ($parameters as $key => $value) {
        $replace['#' . $key . '#'] = $value;
      }
    }
    $replace['#heightconfiglist#'] = strval(intval($replace['#height#'])-70);
    $replace['#widthconfiglist#'] = strval(intval($replace['#width#']));
    $replace['#heightbackuplist#'] = strval(intval($replace['#height#'])-70);
    $replace['#widthbackuplist#'] = strval(intval($replace['#width#']));
    $replace['#heightchat#'] = strval(intval($replace['#height#'])-150);
    $replace['#widthchat#'] = strval(intval($replace['#width#']));
    $replace['#heightuserlist#'] = strval(intval($replace['#height#'])-70);
    $replace['#widthuserlist#'] = strval(intval($replace['#width#']));
    $widgetType = getTemplate('core', $version, 'box', __CLASS__);
		return $this->postToHtml($_version, template_replace($replace, $widgetType));
	}
}

class cubecodersCmd extends cmd {
  /*     * *************************Attributs****************************** */

  /*
  public static $_widgetPossibility = array();
  */

  /*     * ***********************Methode static*************************** */


  /*     * *********************Methode d'instance************************* */

  /*
  * Permet d'empêcher la suppression des commandes même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
  public function dontRemoveCmd() {
    return true;
  }
  */

  // Exécution d'une commande
  public function execute($_options = array()) {
    $eqlogic = $this->getEqLogic(); //récupère l'éqlogic de la commande $this
    $adresse = $eqlogic->getConfiguration("adresse", "localhost");
    $port = $eqlogic->getConfiguration("port", "8080");
    $utilisateur = $eqlogic->getConfiguration("utilisateur", "admin");
    $password = $eqlogic->getConfiguration("password", "admin");
    $protocol = $eqlogic->getConfiguration("protocol", "http");
  }

  /*     * **********************Getteur Setteur*************************** */
}
