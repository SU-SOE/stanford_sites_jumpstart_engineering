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

    // Remove some parent tasks.
    // JSE adds content to the site that is different from JSA. Lets
    // disable those modules and add in only the ones we want again.
   // unset($parent_tasks['stanford_sites_jumpstart_academic_configure_homepage']);


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

    $tasks['jse_configure_pical_homepage_layouts'] = array(
      'display_name' => st('Configure layouts for homepages'),
      'display' => FALSE,
      'type' => 'normal',
      'function' => 'configure_pical_homepage_layouts',
      'run' => INSTALL_TASK_RUN_IF_NOT_COMPLETED,
    );

    $tasks['jse_configure_jse_beans'] = array(
      'display_name' => st('Configure Beans'),
      'display' => FALSE,
      'type' => 'normal',
      'function' => 'configure_jse_beans',
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

    $this->fetch_jse_content_beans($endpoint);
    drush_log('JSE - Finished importing beans.', 'ok');

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
  /**
   * Enable a number of the home page layouts and set one to default on.
   * @param  [type] $install_state [description]
   * @return [type]                [description]
   */
  public function configure_pical_homepage_layouts (&$install_state) {
    $time = time();
    drush_log('JSE - Configuring PICAL homepage layouts.' . $time, 'ok');

    $default = 'stanford_jumpstart_home_morris';
    variable_set('stanford_jumpstart_home_active_body_class', 'stanford-jumpstart-home-morris');

    $context_status = variable_get('context_status', array());
    $homecontexts = stanford_jumpstart_home_context_default_contexts();

    $names = array_keys($homecontexts);

    // Enable these JSE layouts for use by site owners
    $enabled['stanford_jumpstart_home_hoover'] = 1;
    $enabled['stanford_jumpstart_home_morris'] = 1;
    unset($enabled['stanford_jumpstart_home_terman']);
    unset($enabled['stanford_jumpstart_home_pettit']);

    // Disable these layouts
     unset($enabled['stanford_jumpstart_home_lomita']);
     unset($enabled['stanford_jumpstart_home_mayfield_news_events']);
     unset($enabled['stanford_jumpstart_home_palm_news_events']);
     unset($enabled['stanford_jumpstart_home_panama_news_events']);
     unset($enabled['stanford_jumpstart_home_serra_news_events']);

    unset($context_status['']);

    foreach ($names as $context_name) {
      $context_status[$context_name] = TRUE;
      $settings = variable_get('sjh_' . $context_name, array());
      $settings['site_admin'] = isset($enabled[$context_name]);
      variable_set('sjh_' . $context_name, $settings);
    }

    $context_status[$default] = FALSE;
    unset($context_status['']);

    // Save settings
    variable_set('stanford_jumpstart_home_active', $default);
    variable_set('context_status', $context_status);

    $time_diff = time() - $time;
    drush_log('JSE - Finished configuring JSE homepage layouts: ' . $time_diff . ' seconds' , 'ok');
  }

  /**
   * Configure the beans used by the JSE layouts.
   * @param  [type] $install_state [description]
   * @return [type]                [description]
   */
  public function configure_jse_beans(&$install_state) {
    $time = time();
    drush_log('JSE - Configuring Beans.' . $time, 'ok');

    // Install default JSE block classes.
    $fields = array('module', 'delta', 'css_class');
    $values = array(
      array("bean","jumpstart-small-custom-block", "well"),
      array("bean","jumpstart-large-custom-block", "well"),
    );

    // Key all the values.
    $insert = db_insert('block_class')->fields($fields);
    foreach ($values as $k => $value) {
      $db_values = array_combine($fields, $value);
      $insert->values($db_values);
    }
    $insert->execute();

    // Install contextual block classes.
    $cbc_layouts = array();

    $cbc_layouts['stanford_jumpstart_home_hoover']['bean-homepage-about-block'][] = 'span4 well';
    $cbc_layouts['stanford_jumpstart_home_hoover']['bean-jumpstart-small-custom-block'][] = 'span4';
    $cbc_layouts['stanford_jumpstart_home_hoover']['bean-jumpstart-large-custom-block'][] = 'span8 well';

    $cbc_layouts['stanford_jumpstart_home_morris']['bean-homepage-about-block'][] = 'span4 well';
    $cbc_layouts['stanford_jumpstart_home_morris']['bean-jumpstart-small-custom-block'][] = 'span4';
    $cbc_layouts['stanford_jumpstart_home_morris']['views-f73ff55b085ea49217d347de6630cd5a'][] = 'span4 well';
    $cbc_layouts['stanford_jumpstart_home_morris']['views-stanford_events_views-block'][] = 'span4 well';

    $cbc_layouts['stanford_jumpstart_home_pettit']['bean-homepage-about-block'][] = 'span8 well';
    $cbc_layouts['stanford_jumpstart_home_pettit']['bean-jumpstart-large-custom-block'][] = 'span8';
    $cbc_layouts['stanford_jumpstart_home_pettit']['views-f73ff55b085ea49217d347de6630cd5a'][] = 'span4 well';
    $cbc_layouts['stanford_jumpstart_home_pettit']['views-stanford_events_views-block'][] = 'span4 well';

    $cbc_layouts['stanford_jumpstart_home_terman']['bean-jumpstart-about-block'][] = 'span4 well';
    $cbc_layouts['stanford_jumpstart_home_terman']['bean-jumpstart-large-custom-block'][] = 'span8';
    $cbc_layouts['stanford_jumpstart_home_terman']['bean-jumpstart-small-custom-block'][] = 'span4';
    $cbc_layouts['stanford_jumpstart_home_terman']['views-f73ff55b085ea49217d347de6630cd5a'][] = 'span4 well';
    $cbc_layouts['stanford_jumpstart_home_terman']['views-stanford_events_views-block'][] = 'span4 well';

  // Todo Tried:
    // $cbc_layouts['stanford_jumpstart_home_terman']['views-stanford_events_views-block'][] = 'span4 well';
    // $cbc_layouts['stanford_jumpstart_home_morris']['block-views-stanford-events-views-block'][] = 'span4 well';
    variable_set('contextual_block_class', $cbc_layouts);

    $time_diff = time() - $time;
    drush_log('JSE - Finished configuring Beans: ' . $time_diff . ' seconds' , 'ok');
  }

  /**
   * Fetches beans from jsa-content
   * @param  [type] $endpoint [description]
   * @return [type]           [description]
   */
  private function fetch_jse_content_beans($endpoint) {

    $uuids = array(
      '40cabca1-7d44-42bf-a012-db53fdccd350', // Jumpstart Large Custom Block.
      '7e510af6-c003-402d-91a4-7480dac1484a', // Jumpstart Small Custom Block.
    );

    $importer = new SitesContentImporter();
    $importer->set_endpoint($endpoint);
    $importer->set_bean_uuids($uuids);
    $importer->import_content_beans();

  }

}
