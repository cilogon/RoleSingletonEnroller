<?php

// This COmanage Registry enrollment plugin is intended to be used
// with a self-service enrollment flow (which may or may not require
// approval) configured with CO Person authorization that adds a
// CO Person Role in a COU. The plugin will inspect the enrollee
// CO Person record and if a CO Person Role already exists with
// the configured COU the flow will stop.
//
// The following enrollment steps are implemented:
//
// selectEnrollee:
//   - Check for an existing CO Person Role with the configured
//     COU and stop the flow if found.

App::uses('CoPetitionsController', 'Controller');
 
class RoleSingletonEnrollerCoPetitionsController extends CoPetitionsController {
  // Class name, used by Cake
  public $name = "RoleSingletonEnrollerCoPetitionsController";

  public $uses = array(
    "CoPetition",
    "RoleSingletonEnroller.RoleSingletonEnroller"
  );

  /**
   * Plugin functionality following start step
   *
   * @param Integer $id CO Petition ID
   * @param Array $onFinish URL, in Cake format
   */
  protected function execute_plugin_selectEnrollee($id, $onFinish) {
    // Use the petition ID to find the CO Person Roles.
    $args = array();
    $args['conditions']['CoPetition.id'] = $id;
    $args['contain']['EnrolleeCoPerson']['CoPersonRole'] = 'Cou';

    $petition = $this->CoPetition->find('first', $args);
    $this->log("Petitioner Attributes: Petition is " . print_r($petition, true));

    $coPersonId = $petition['CoPetition']['enrollee_co_person_id'];

    // Find the plugin configuration.
    $args = array();
    $args['conditions']['RoleSingletonEnroller.co_enrollment_flow_wedge_id'] = $this->params['named']['efwid'];
    $args['contain'] = false;

    $pluginCfg = $this->RoleSingletonEnroller->find('first', $args);
    if(empty($pluginCfg)) {
      throw new RuntimeException(_txt('pl.role_singleton_enroller.cfg.notfound'));
    }

    $couId = $pluginCfg['RoleSingletonEnroller']['cou_id'];

    if(!empty($petition['EnrolleeCoPerson']['CoPersonRole'])) {
      foreach($petition['EnrolleeCoPerson']['CoPersonRole'] as $r) {
        if($r['cou_id'] == $couId) {
          // Set the petition status to Duplicate.
          $this->CoPetition->id = $id;
          $this->CoPetition->saveField('status', PetitionStatusEnum::Duplicate, array('provision' => false));

          // Set the flash.
          $couName = $r['Cou']['name'];
          $this->Flash->set(_txt('pl.role_singleton_enroller.duplicate', array($couName)), array('key' => 'error'));

          // Redirect to the CO Person canvas view.
          $args = array();
          $args['plugin'] = null;
          $args['controller'] = 'co_people';
          $args['action'] = 'canvas';
          $args[] = $coPersonId;
          $this->redirect($args);
        }
      }
    }

    $this->redirect($onFinish);
  }
}
