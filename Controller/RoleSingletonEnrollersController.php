<?php

App::uses("SEWController", "Controller");

class RoleSingletonEnrollersController extends SEWController {
  // Class name, used by Cake
  public $name="RoleSingletonEnrollers";

  // Establish pagination parameters for HTML views
  public $paginate = array(
    'limit' => 25,
    'order' => array()
  );

  /**
   * Callback before views are rendered.
   *
   * @since  COmanage Registry v4.1.0
   */
  
  function beforeRender() {
    parent::beforeRender();
    
    // Pull the list of COUs
    $args = array();
    $args['conditions']['Cou.co_id'] = $this->cur_co['Co']['id'];
    $args['contain'] = false;

    $couModel = $this->RoleSingletonEnroller->CoEnrollmentFlowWedge->CoEnrollmentFlow->Co->Cou;

    $cous = $couModel->find('all', $args);

    $childCous = array();
    foreach($cous as $cou) {
      $childCous = array_unique($childCous + $couModel->childCousById($cou['Cou']['id'], true, true));
    }
    $this->set('vv_available_cous', $childCous); 
  }
  
  function isAuthorized() {
    $roles = $this->Role->calculateCMRoles();

    // Construct the permission set for this user, which will also be passed to the view.
    $p = array();
    
    // Delete an existing configuration?
    $p['delete'] = ($roles['cmadmin'] || $roles['coadmin']);
    
    // Edit an existing configuration?
    $p['edit'] = ($roles['cmadmin'] || $roles['coadmin']);
    
    // View the existing configuration?
    $p['index'] = ($roles['cmadmin'] || $roles['coadmin']);
    
    // View the existing confinguration?
    $p['view'] = ($roles['cmadmin'] || $roles['coadmin']);
    
    $this->set('permissions', $p);
    return $p[$this->action];
  }
}
