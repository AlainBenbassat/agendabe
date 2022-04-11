<?php
/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2011
 * $Id$
 *
 */

require_once 'CRM/Admin/Form.php';

/**
 * This class generates.
 * 
 */
class CRM_Agendabe_Form_AgendaConfig extends CRM_Admin_Form
{
   protected $_roles = [];
   protected $_types = [];
   function preProcess( ) {
       
    parent::preProcess( );
    $session = CRM_Core_Session::singleton();
    $url = CRM_Utils_System::url('civicrm/event/agendabe/config');
    $session->pushUserContext( $url );
  }


function setDefaultValues() {
  $defaults = parent::setDefaultValues();
  $config = CRM_Core_Config::singleton();
  if(!empty($config->civicrm_events_agenda_types)) {
    foreach($config->civicrm_events_agenda_types as $key => $val) {
      $defaults[$key] = 1;
    }
  } 
  if(!empty($config->civicrm_events_agenda_statues)) {
   foreach($config->civicrm_events_agenda_statues as $key => $val) {
    $defaults[$key] = 1;
   }
  }
  return $defaults; 
}


/**
* Function to build the form
*
* @return None
* @access public
*/
public function buildQuickForm( ){
  parent::buildQuickForm( );
  $config = CRM_Core_Config::singleton();
  require_once 'CRM/Event/PseudoConstant.php';
  $original_events = [];
  $event_type = CRM_Core_OptionGroup::values('event_type', FALSE ,FALSE, FALSE, NULL, 'label', FALSE, TRUE);
  $original_events = $event_type;
  foreach($event_type as $key => $val) {
    	$val = str_replace(" ","_",$val);
    	$event_type[$key] = $val;
    }
  $event_types = [];
  foreach($event_type as $key => $val) {
    $this->addElement('checkbox', $key, ts($val) , NULL );
    $event_types[$key] = $val;
  }
  $params = [
    'option_group_id' => 361, // Hardcoded.
  ];
  $event_statues = civicrm_api3('OptionValue', 'get', $params);
	foreach( $event_statues['values'] as  $status => $status_val){
		$this->addElement('checkbox', $status, ts($status_val['label']) , NULL );
		$event_custom_statues[$status] = $status_val['label'];
	}
  $this->assign('event_type', $event_types);
  $this->assign('event_statues', $event_custom_statues);

}

/**
* Function to process the form
*
* @access public
* @return None
*/
public function postProcess() {    
  $params = $this->controller->exportValues($this->_name);
  $config = CRM_Core_Config::singleton(); 
  $configParams = [];
  $event_type = CRM_Core_OptionGroup::values('event_type', FALSE ,FALSE, FALSE, NULL, 'label', FALSE, TRUE);
  foreach($params as $key => $val) {
    if(is_int($key) &&  array_key_exists($key, $event_type)){
      $event_types[$key] = $event_type[$key];
    }
  }
  $custom_params = [
    'option_group_id' => 361, // Hardcoded.
  ];
  $event_statues = civicrm_api3('OptionValue', 'get', $custom_params);
  foreach($params as $key => $val) { 
    if(is_int($key) &&  array_key_exists($key, $event_statues['values'])){
      $event_custom_statues[$key] = $event_statues['values'][$key]['label'];
    }
  }
  $configParams['civicrm_events_agenda_types'] = $event_types;
  $configParams['civicrm_events_agenda_statues'] = $event_custom_statues;
  CRM_Core_BAO_ConfigSetting::create($configParams);
  CRM_Core_Session::setStatus(" ", ts('The value has been saved.'), "success" );
 }
}
