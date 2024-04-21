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
    $this->createCommand('refresh','Rafraichir','action','other');
    $this->createCommand('msg','message','info','string');
    $this->createCommand('status','status','info','string');
  }

  // Fonction exécutée automatiquement avant la suppression de l'équipement
  public function preRemove() {
  }

  // Fonction exécutée automatiquement après la suppression de l'équipement
  public function postRemove() {
  }

  private function createCommand($newcmd,$newname,$newtype,$newsubtype,$newunit = "",$newtemplate = 'default'){
    $newelement = $this->getCmd(null, $newcmd);
    if (!is_object($newelement)) {
      $newelement = new cubecodersCmd();
      $newelement->setName(__($newname, __FILE__));
    }
    $newelement->setEqLogic_id($this->getId());
    $newelement->setLogicalId($newcmd);
    $newelement->setType($newtype);
    $newelement->setSubType($newsubtype);
    $newelement->setTemplate('dashboard',$newtemplate);
    if ($newtype == 'info') {
      $newelement->setGeneric_type('GENERIC_INFO');
    } else {
      $newelement->setGeneric_type('GENERIC_ACTION');
    }
    if ($newunit != "") {
      $newelement->setUnite($newunit);
    }
    $newelement->save();
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
  private function _Login() {
    $adresse = $this->getConfiguration("adresse", "localhost");
    $port = $this->getConfiguration("port", "8080");
    $protocol = $this->getConfiguration("protocol", "http");
    $utilisateur = $this->getConfiguration("utilisateur", "admin");
    $password = $this->getConfiguration("password", "admin");
    $result = $this->_requestAPI($protocol . '://' . $adresse . ':' . $port . '/API/Core/Login', 'POST', '{"username":"' . $utilisateur . '","password":"' . $password . '", "rememberMe":true, "token":""}');
    log::add('cubecoders','debug',$result);
    if (!$result['success']) {
      return false;
    }
    $this->setConfiguration("token", $result['rememberMeToken']);
    $this->setConfiguration("SESSIONID", $result['sessionID']);
    $this->save();
    return true;
  }

  private function _getInstances($token,$sessionId) {
    $adresse = $this->getConfiguration("adresse", "localhost");
    $port = $this->getConfiguration("port", "8080");
    $protocol = $this->getConfiguration("protocol", "http");
    $instances = array();
    $result = $this->_requestAPI($protocol . '://' . $adresse . ':' . $port . '/API/ADSModule/GetInstances', 'POST', '{"token":"' . $token . '","SESSIONID":"' . $sessionId . '"}');
    log::add('cubecoders','debug',$result);
    if ((isset($result['Title'])) && ($result['Title'] == 'Unauthorized Access')) {
      return [false,$instances];
    }
    if ((!isset($result[0])) && (isset($result[0]['AvailableInstances']))) {
      $this->setConfiguration("token", "");
      $this->setConfiguration("SESSIONID", "");
      $this->save();
      return [false,$instances];
    }
    foreach ($result[0]['AvailableInstances'] as $instance) {
      $instances[$instance['InstanceID']] = [$instance['InstanceName'],$instance['FriendlyName'],$instance['Module'],$instance['AppState']];
    }
    return [true,$instances];
  }

  private function _addNewInstance($uuid,$instanceName,$friendlyName,$module) {
    $this->createCommand('fname-' . $uuid,'fname-' . $instanceName,'info','string');
    $this->createCommand('status-' . $uuid,'status-' . $instanceName,'info','string');
    $this->createCommand('module-' . $uuid,'module-' . $instanceName,'info','string');
    $this->createCommand('start-' . $uuid,'start-' . $instanceName,'action','other');
    $this->createCommand('stop-' . $uuid,'stop-' . $instanceName,'action','other');
    $this->createCommand('restart-' . $uuid,'restart-' . $instanceName,'action','other');
    $this->createCommand('kill-' . $uuid,'kill-' . $instanceName,'action','other');
    $this->createCommand('pause-' . $uuid,'pause-' . $instanceName,'action','other');
    $this->createCommand('resume-' . $uuid,'resume-' . $instanceName,'action','other');
    $this->checkAndUpdateCmd('module-' . $uuid, $module);
    $this->checkAndUpdateCmd('fname-' . $uuid, $friendlyName);
    log::add('cubecoders','debug','addNewInstance');
    log::add('cubecoders','debug',$uuid);
    log::add('cubecoders','debug',$instanceName);
  }

  private function _deleteInstance($uuid) {
    $this->getCmd(null, 'status-' . $uuid)->remove();
    $this->getCmd(null, 'start-' . $uuid)->remove();
    $this->getCmd(null, 'stop-' . $uuid)->remove();
    $this->getCmd(null, 'restart-' . $uuid)->remove();
    $this->getCmd(null, 'kill-' . $uuid)->remove();
    $this->getCmd(null, 'pause-' . $uuid)->remove();
    $this->getCmd(null, 'resume-' . $uuid)->remove();
    log::add('cubecoders','debug','deleteInstance');
    log::add('cubecoders','debug',$uuid);
  }

  private function _getExistingInstances() {
    $uuids = array();
    foreach ($this->getCmd('info') as $cmd) {
      if (strpos($cmd->getLogicalId(),'status-') !== false) {
        $uuids[] = str_replace('status-','',$cmd->getLogicalId());
      }
    }
    return $uuids;
  }

  public function refreshInstanceList() {
    $token = $this->getConfiguration("token", "");
    $sessionId = $this->getConfiguration("SESSIONID", "");
    $retry = 0;
    do {
      if ($token == "" || $sessionId == "") {
        if (!$this->_Login()) {
          return false;
        }
        sleep(0.3);
        $token = $this->getConfiguration("token", "");
        $sessionId = $this->getConfiguration("SESSIONID", "");
      }
      $instances = $this->_getInstances($token,$sessionId);
      $retry++;
    } while ($retry < 3 && !$instances[0]);
    if (!$instances[0]) {
      $this->setConfiguration("token", "");
      $this->setConfiguration("SESSIONID", "");
      $this->checkAndUpdateCmd('msg', 'Impossible de se connecter au serveur');
      $this->checkAndUpdateCmd('status', 'NOK');
      $this->save();
      return;
    }
    $existingInstances = $this->_getExistingInstances();
    foreach ($instances[1] as $uuid => $instanceInfo) {
      if (!in_array($uuid,$existingInstances)) {
        $this->_addNewInstance($uuid,$instanceInfo[0],$instanceInfo[1],$instanceInfo[2]);
      }
    }
    foreach ($existingInstances as $uuid) {
      if (!isset($instances[1][$uuid])) {
        $this->_deleteInstance($uuid);
      }
    }
    foreach ($instances[1] as $uuid => $instanceInfo) {
      $this->checkAndUpdateCmd('status-' . $uuid, $instanceInfo[3]);
    }
    /*
    50 --> sleep
    45 --> prepare sleep
    20 --> run
    10 --> starting
    40 --> stopping
    0 --> stop
    30 --> restarting
    -1 --> unknown */
    $this->checkAndUpdateCmd('msg', '');
    $this->checkAndUpdateCmd('status', 'OK');
  }

  private function _LoginInstance($uuid) {
    $adresse = $this->getConfiguration("adresse", "localhost");
    $port = $this->getConfiguration("port", "8080");
    $protocol = $this->getConfiguration("protocol", "http");
    $utilisateur = $this->getConfiguration("utilisateur", "admin");
    $password = $this->getConfiguration("password", "admin");
    $_url = $protocol . '://' . $adresse . ':' . $port . '/API/ADSModule/Servers/' . $uuid;
    $result = $this->_requestAPI($_url . '/API/Core/Login', 'POST', '{"username":"' . $utilisateur . '","password":"' . $password . '", "rememberMe":true, "token":""}');
    log::add('cubecoders','debug','test in instance');
    log::add('cubecoders','debug',$result['success']);
    if (!$result['success']) {
      return [false,'','',''];
    }
    log::add('cubecoders','debug','logged in instance');
    return [true,$result['rememberMeToken'],$result['sessionID'],$_url];
  }

  public function startInstance($uuid) {
    $instanceSession = $this->_LoginInstance($uuid);
    if (!$instanceSession[0]) {
      $this->checkAndUpdateCmd('msg', 'Impossible de se connecter à l\'instance');
      $this->checkAndUpdateCmd('status', 'NOK');
      return;
    }
    $result = $this->_requestAPI($instanceSession[3] . '/API/Core/Start', 'POST', '{"token":"' . $instanceSession[1] . '","SESSIONID":"' . $instanceSession[2] . '"}');
    log::add('cubecoders','debug',$result);
    if ((isset($result['Title'])) && ($result['Title'] == 'Unauthorized Access')) {
      $this->checkAndUpdateCmd('msg', 'Impossible de démarrer l\'instance');
      $this->checkAndUpdateCmd('status', 'NOK');
    }
    // no body if success
    if (empty($result)) {
      return;
    }
  }

  public function stopInstance($uuid) {
    $instanceSession = $this->_LoginInstance($uuid);
    if (!$instanceSession[0]) {
      $this->checkAndUpdateCmd('msg', 'Impossible de se connecter à l\'instance');
      $this->checkAndUpdateCmd('status', 'NOK');
      return;
    }
    $result = $this->_requestAPI($instanceSession[3] . '/API/Core/Stop', 'POST', '{"token":"' . $instanceSession[1] . '","SESSIONID":"' . $instanceSession[2] . '"}');
    if ((isset($result['Title'])) && ($result['Title'] == 'Unauthorized Access')) {
      $this->checkAndUpdateCmd('msg', 'Impossible d\'arrêter l\'instance');
      $this->checkAndUpdateCmd('status', 'NOK');
    }
    // no body if success
    if (empty($result)) {
      return;
    }
  }

  public function restartInstance($uuid) {
    $instanceSession = $this->_LoginInstance($uuid);
    if (!$instanceSession[0]) {
      $this->checkAndUpdateCmd('msg', 'Impossible de se connecter à l\'instance');
      $this->checkAndUpdateCmd('status', 'NOK');
      return;
    }
    $result = $this->_requestAPI($instanceSession[3] . '/API/Core/Restart', 'POST', '{"token":"' . $instanceSession[1] . '","SESSIONID":"' . $instanceSession[2] . '"}');
    if ((isset($result['Title'])) && ($result['Title'] == 'Unauthorized Access')) {
      $this->checkAndUpdateCmd('msg', 'Impossible de redémarrer l\'instance');
      $this->checkAndUpdateCmd('status', 'NOK');
    }
    // no body if success
    if (empty($result)) {
      return;
    }
  }

  public function killInstance($uuid) {
    $instanceSession = $this->_LoginInstance($uuid);
    if (!$instanceSession[0]) {
      $this->checkAndUpdateCmd('msg', 'Impossible de se connecter à l\'instance');
      $this->checkAndUpdateCmd('status', 'NOK');
      return;
    }
    $result = $this->_requestAPI($instanceSession[3] . '/API/Core/Kill', 'POST', '{"token":"' . $instanceSession[1] . '","SESSIONID":"' . $instanceSession[2] . '"}');
    if ((isset($result['Title'])) && ($result['Title'] == 'Unauthorized Access')) {
      $this->checkAndUpdateCmd('msg', 'Impossible de tuer l\'instance');
      $this->checkAndUpdateCmd('status', 'NOK');
    }
    // no body if success
    if (empty($result)) {
      return;
    }
  }

  public function pauseInstance($uuid) {
    $instanceSession = $this->_LoginInstance($uuid);
    if (!$instanceSession[0]) {
      $this->checkAndUpdateCmd('msg', 'Impossible de se connecter à l\'instance');
      $this->checkAndUpdateCmd('status', 'NOK');
      return;
    }
    $result = $this->_requestAPI($instanceSession[3] . '/API/Core/Pause', 'POST', '{"token":"' . $instanceSession[1] . '","SESSIONID":"' . $instanceSession[2] . '"}');
    if ((isset($result['Title'])) && ($result['Title'] == 'Unauthorized Access')) {
      $this->checkAndUpdateCmd('msg', 'Impossible de mettre en pause l\'instance');
      $this->checkAndUpdateCmd('status', 'NOK');
    }
    // no body if success
    if (empty($result)) {
      return;
    }
  }

  public function resumeInstance($uuid) {
    $instanceSession = $this->_LoginInstance($uuid);
    if (!$instanceSession[0]) {
      $this->checkAndUpdateCmd('msg', 'Impossible de se connecter à l\'instance');
      $this->checkAndUpdateCmd('status', 'NOK');
      return;
    }
    $result = $this->_requestAPI($instanceSession[3] . '/API/Core/Resume', 'POST', '{"token":"' . $instanceSession[1] . '","SESSIONID":"' . $instanceSession[2] . '"}');
    if ((isset($result['Title'])) && ($result['Title'] == 'Unauthorized Access')) {
      $this->checkAndUpdateCmd('msg', 'Impossible de reprendre l\'instance');
      $this->checkAndUpdateCmd('status', 'NOK');
    }
    // no body if success
    if (empty($result)) {
      return;
    }
  }


  private function _requestAPI($url, $method = 'GET', $data = null) {
    $request_http = new com_http($url);
    $request_http->setNoReportError(true);
    $request_http->setAllowEmptyReponse(true);
    $request_http->setHeader(array('Content-type: application/json','Accept: application/json','User-Agent: PostmanRuntime/7.37.3'));
    if ($method == 'POST') {
      $request_http->setPost($data);
    }
    $result=$request_http->exec();
    log::add('cubecoders','debug',$url);
    log::add('cubecoders','debug', $result);
    return json_decode($result,true);
  }

  public function toHtml($_version = 'dashboard') {
    $replace = $this->preToHtml($_version);
    if (!is_array($replace)) {
      return $replace;
    }
    $version = jeedom::versionAlias($_version);
    $replace['#tableInstance#'] = "";
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
    $parameters = $this->getDisplay('parameters');
    if (is_array($parameters)) {
      foreach ($parameters as $key => $value) {
        $replace['#' . $key . '#'] = $value;
      }
    }
    $widgetType = getTemplate('core', $version, 'cubecoders', __CLASS__);
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
    log::add('cubecoders','debug',$this->getLogicalId());
    switch ($this->getLogicalId()) {
      case 'refresh':
        $eqlogic->refreshInstanceList();
      break;
      default:
        if (strpos($this->getLogicalId(),'start-') !== false) {
          $eqlogic->startInstance(str_replace('start-','',$this->getLogicalId()));
        } elseif (strpos($this->getLogicalId(),'stop-') !== false) {
          $eqlogic->startInstance(str_replace('stop-','',$this->getLogicalId()));
        } elseif (strpos($this->getLogicalId(),'restart-') !== false) {
          $eqlogic->startInstance(str_replace('restart-','',$this->getLogicalId()));
        } elseif (strpos($this->getLogicalId(),'kill-') !== false) {
          $eqlogic->startInstance(str_replace('kill-','',$this->getLogicalId()));
        } elseif (strpos($this->getLogicalId(),'pause-') !== false) {
          $eqlogic->startInstance(str_replace('pause-','',$this->getLogicalId()));
        } elseif (strpos($this->getLogicalId(),'resume-') !== false) {
          $eqlogic->startInstance(str_replace('resume-','',$this->getLogicalId()));
        } else {
          log::add('cubecoders','debug','Commande inconnue');
          log::add('cubecoders','debug',$this->getLogicalId());
        }
      break;
    }
  }

  /*     * **********************Getteur Setteur*************************** */
}
