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

    $tasks['jse_install_pps_menu_items'] = array(
      'display_name' => st('Install private pages section menu items.'),
      'display' => FALSE,
      'type' => 'normal',
      'function' => 'install_private_pages_section_menu_items',
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

  /**
   * Installs and configures the Private Pages Section menu for JSE
   * @param  [type] $install_state [description]
   */

  public function install_private_pages_section_menu_items(&$install_state) {
    $time = time();
    drush_log('JSE - starting create Private Pages menu items', 'ok');
    $items = array();

    // Rebuild the menu cache before starting this.
    drupal_static_reset();
    menu_cache_clear_all();
    menu_rebuild();

    // Private pages section landing page
    $items['private-pages'] = array(
      'link_path' => drupal_get_normal_path('private'),
      'link_title' => 'Private Pages',
      'menu_name' => 'menu-menu-private-pages',
      'weight' => -9,
    );

    // For Faculty
    $items['private/for-faculty'] = array(
      'link_path' => drupal_get_normal_path('private/for-faculty'),
      'link_title' => 'For Faculty',
      'menu_name' => 'menu-menu-private-pages',
      'weight' => -7,
      'parent' => 'private', // must be already saved.
    );

    // For Students
    $items['private/for-students'] = array(
      'link_path' => drupal_get_normal_path('private/for-students'),
      'link_title' => 'For Students',
      'menu_name' => 'menu-menu-private-pages',
      'weight' => -5,
      'parent' => 'private', // must be already saved.
    );

    // For Staff
    $items['private/for-staff'] = array(
      'link_path' => drupal_get_normal_path('private/for-staff'),
      'link_title' => 'For Staff',
      'menu_name' => 'menu-menu-private-pages',
      'weight' => -3,
      'parent' => 'private', // must be already saved.
    );

      // For Faculty / Sub-page
    $items['private/for-faculty/sub-page'] = array(
      'link_path' => drupal_get_normal_path('private/for-faculty/sub-page'),
      'link_title' => 'Faculty Sub-Page',
      'menu_name' => 'main-menu',
      'weight' => -9,
      'parent' => 'private/for-faculty', // must be already saved.
    );

    // Loop through each of the items and save them.
    foreach ($items as $k => $v) {

      // Check to see if there is a parent declaration. If there is then find
      // the mlid of the parent item and attach it to the menu item being saved.
      if (isset($v['parent'])) {
        $v['plid'] = $items[$v['parent']]['mlid'];
        unset($v['parent']); // Remove fluff before save.
      }
      // Save the menu item.
      $mlid = menu_link_save($v);
      $v['mlid'] = $mlid;
      $items[$k] = $v;
    }

    $time_diff = time() - $time;
    drush_log('JSE - Finished creating Private Pages menu items: ' . $time_diff . ' seconds' , 'ok');
  }
 }
