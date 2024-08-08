<?php
  // Determine if fields are editable
  $e = false;
  
  if(($this->action == "add" && $permissions['add']) || ($this->action == "edit" && $permissions['edit']))
    $e = true;
    
  // We shouldn't get here if we don't have at least read permission, but check just in case
  
  if(!$e && !$permissions['view'])
    return false;

  // Add breadcrumbs
  print $this->element("coCrumb");

  $args = array();
  $args['plugin'] = null;
  $args['controller'] = 'co_enrollment_flows';
  $args['action'] = 'index';
  $args['co'] = $cur_co['Co']['id'];
  $this->Html->addCrumb(_txt('ct.co_enrollment_flows.pl'), $args);

  $args = array();
  $args['plugin'] = null;
  $args['controller'] = 'co_enrollment_flows';
  $args['action'] = 'edit';
  $args[] = $role_singleton_enrollers[0]['CoEnrollmentFlowWedge']['co_enrollment_flow_id'];
  $this->Html->addCrumb($role_singleton_enrollers[0]['CoEnrollmentFlowWedge']['CoEnrollmentFlow']['name'], $args);

  $args = array();
  $args['plugin'] = null;
  $args['controller'] = 'co_enrollment_flow_wedges';
  $args['action'] = 'index';
  $args['coef'] = $role_singleton_enrollers[0]['CoEnrollmentFlowWedge']['co_enrollment_flow_id'];
  $args[] = $role_singleton_enrollers[0]['CoEnrollmentFlowWedge']['id'];
  $this->Html->addCrumb(_txt('ct.co_enrollment_flow_wedges.pl'), $args);

  $args = array();
  $args['plugin'] = null;
  $args['controller'] = 'co_enrollment_flow_wedges';
  $args['action'] = 'edit';
  $args[] = $role_singleton_enrollers[0]['CoEnrollmentFlowWedge']['id'];
  $this->Html->addCrumb($role_singleton_enrollers[0]['CoEnrollmentFlowWedge']['description'], $args);

  $crumbTxt = _txt('op.config');
  $this->Html->addCrumb($crumbTxt);
  
  print $this->Form->hidden('co_enrollment_flow_wedge_id', array('default' => $role_singleton_enrollers[0]['CoEnrollmentFlowWedge']['id'])) . "\n";
?>

<ul id="<?php print $this->action; ?>_role_singleton_enroller" class="fields form-list form-list-admin">
  <li>
    <div class="field-name">
      <div class="field-title">
        <?php print _txt('pl.role_singleton_enroller.cou'); ?>
      </div>
      <div class="field-desc"><?php print _txt('pl.role_singleton_enroller.cou.desc'); ?></div>
    </div>
    <div class="field-info">
      <?php
        $attrs['value'] = (isset($role_singleton_enrollers[0]['RoleSingletonEnroller']['cou_id'])
          ? $role_singleton_enrollers[0]['RoleSingletonEnroller']['cou_id']
          : 0);
        $attrs['empty'] = '';
        $attrs['required'] = 'required';
        $attrs['autofocus'] = true;

        if($e) {
          print $this->Form->select('cou_id', $vv_available_cous, $attrs);

          if($this->Form->isFieldError('cou_id')) {
            print $this->Form->error('cou_id');
          }
        } else {
          print filter_var($role_singleton_enrollers[0]['Cou']['name'],FILTER_SANITIZE_SPECIAL_CHARS);
        }
      ?>
    </div>
  </li>

  <?php if($e): ?>
    <li class="fields-submit">
      <div class="field-name">
        <span class="required"><?php print _txt('fd.req'); ?></span>
      </div>
      <div class="field-info">
        <?php print $this->Form->submit($submit_label); ?>
      </div>
    </li>
  <?php endif; ?>
</ul>
