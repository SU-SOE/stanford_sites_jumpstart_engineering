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

    $tasks['jse_install_content'] = array(
      'display_name' => st('Install JSE specific content'),
      'display' => FALSE,
      'type' => 'normal',
      'function' => 'install_content',
      'run' => INSTALL_TASK_RUN_IF_NOT_COMPLETED,
    );

    $this->prepare_tasks($tasks, get_class());
    return array_merge($parent_tasks, $tasks);
  }

  // Install tasks below.
  // //////////////////////////////////////////////////////////////////////////

  /**
   * [install_content description]
   * @param  [type] &$install_state [description]
   * @return [type]                 [description]
   */
  public function install_content(&$install_state) {

    $time = time();
    drush_log('JSE - Starting Content Import. Time: ' . $time, 'ok');

    $endpoint = 'https://sites.stanford.edu/jsa-content/jsainstall';

    // Load up library.
    $this->load_sites_content_importer_files($install_state);

    $filters = array('sites_products' => array('55'));
    $view_importer = new SitesContentImporterViews();
    $view_importer->set_endpoint($endpoint);
    $view_importer->set_resource('content');
    $view_importer->set_filters($filters);
    $view_importer->import_content_by_views_and_filters();

    $time_diff = time() - $time;
    drush_log('JSE - Finished importing content. Import took: ' . $time_diff . ' seconds' , 'ok');

  }






}
