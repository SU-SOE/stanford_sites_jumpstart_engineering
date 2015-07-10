<?php
/**
 * @file
 * Default Installation Class for JumpstartEngineering.
 * Child profile of JumpstartSites. Allows for additional configuration.
 *
 * @author Shea McKinney <sheamck@stanford.edu>
 * @author John Bickar <jbickar@stanford.edu>
 */

/**
 * JumpStart Installation Profile Class.
 */
class JumpstartSitesEngineering extends JumpstartSitesAcademic {

  /**
   * Returns all of the tree's install tasks in order from (first) top most
   * parent to (last) bottom most child.
   *
   * @return [array] [an array of installation tasks]
   */
  public function get_install_tasks(&$install_state) {

    // Get parent tasks.
    $parent_tasks = parent::get_install_tasks($install_state);

    // $tasks['stanford_sites_jumpstart_academic_delete_views'] = array(
    //   'display_name' => st('Delete default views from DB'),
    //   'display' => FALSE,
    //   'type' => 'normal',
    //   'function' => 'remove_all_default_views_from_db',
    //   'run' => INSTALL_TASK_RUN_IF_NOT_COMPLETED,
    // );

    $this->prepare_tasks($tasks, get_class());
    return array_merge($parent_tasks, $tasks);
  }

  /**
   * Implements hook_form_alter()
   * Modifies and alters the configuration form.
   *
   * @return array the form array
   */
  public function get_config_form(&$form, &$form_state) {

    // Get parent altered configuration first.
    $my_form = parent::get_config_form($form, $form_state);

    // $my_form['jumpstart_academic'] = array(
    //   '#type' => 'fieldset',
    //   '#title' => 'Jumpstart Academic Configuration',
    //   '#description' => 'Jumpstart Sites Academic Configuration Options',
    //   '#collapsible' => TRUE,
    //   '#collapsed' => FALSE,
    // );

    // $my_form['jumpstart_academic']['deploy_password'] = array(
    //   '#type' => 'textfield',
    //   '#title' => 'Content Deployment Password',
    //   '#description' => 'Stanford Sites Environment Only: Please enter the password for the content deployment.',
    //   '#default_value' => isset($form_state['values']['deploy_password']) ? $form_state['values']['deploy_password'] : '',
    // );

    // $my_form['jumpstart_academic']['fetch_endpoint'] = array(
    //   '#type' => 'textfield',
    //   '#title' => 'Content Endpoint',
    //   '#description' => 'Expert Only: If you are installing outside of the stanford sites environment the installation will attempt to fetch content from a remote server. To change the location of that remote server edit this field. Otherwise, leave this alone.',
    //   '#default_value' => isset($form_state['values']['fetch_endpoint']) ? $form_state['values']['fetch_endpoint'] : 'https://sites.stanford.edu/jsa-content/jsainstall',
    // );

    return $my_form;
  }

  /**
   * [get_config_form_submit description].
   *
   * @param [type] $form
   *   [description]
   * @param [type] $form_state
   *   [description]
   *
   * @return [type]             [description]
   */
  public function get_config_form_submit($form, &$form_state) {
    parent::get_config_form_submit($form, $form_state);
    // $vars = variable_get('stanford_jumpstart_install', array());
    // $vars['fetch_endpoint'] = check_plain($form['jumpstart_academic']['fetch_endpoint']['#value']);
    // variable_set('stanford_jumpstart_install', $vars);
  }

  // Install tasks below.
  // //////////////////////////////////////////////////////////////////////////


}
