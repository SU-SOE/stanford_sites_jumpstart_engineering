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

    // The private files directory setting in the final task causes issues in
    // JSE unstall. Lets pop it off and place it back on the end.
    $keys = array_keys($parent_tasks);
    $finish = array_pop($parent_tasks);
    $finish_key = array_pop($keys);

    $tasks['jse_install_content'] = array(
      'display_name' => st('Install JSE specific content'),
      'display' => FALSE,
      'type' => 'normal',
      'function' => 'install_content',
      'run' => INSTALL_TASK_RUN_IF_NOT_COMPLETED,
    );

    $tasks['jse_set_jse_variables'] = array(
      'display_name' => st('Install JSE needed variables'),
      'display' => FALSE,
      'type' => 'normal',
      'function' => 'set_jse_variables',
      'run' => INSTALL_TASK_RUN_IF_NOT_COMPLETED,
    );

    $tasks['jse_install_pps_menu_items'] = array(
      'display_name' => st('Install private pages section menu items.'),
      'display' => FALSE,
      'type' => 'normal',
      'function' => 'install_private_pages_section_menu_items',
      'run' => INSTALL_TASK_RUN_IF_NOT_COMPLETED,
    );

    $tasks['jse_install_main_menu_items'] = array(
      'display_name' => st('Install JSE main menu items.'),
      'display' => FALSE,
      'type' => 'normal',
      'function' => 'jse_install_main_menu_items',
      'run' => INSTALL_TASK_RUN_IF_NOT_COMPLETED,
    );

    $tasks['jse_configure_capx'] = array(
      'display_name' => st('Create CAPx default configuration.'),
      'display' => FALSE,
      'type' => 'normal',
      'function' => 'capx_default_configuration',
      'run' => INSTALL_TASK_RUN_IF_NOT_COMPLETED,
    );

    $tasks['jse_configure_pical_homepage_layouts'] = array(
      'display_name' => st('Configure layouts for homepages'),
      'display' => FALSE,
      'type' => 'normal',
      'function' => 'configure_pical_homepage_layouts',
      'run' => INSTALL_TASK_RUN_IF_NOT_COMPLETED,
    );

    $tasks['jse_configure_sitewide_layout'] = array(
      'display_name' => st('Configure site-wide layout'),
      'display' => FALSE,
      'type' => 'normal',
      'function' => 'configure_sitewide_layout',
      'run' => INSTALL_TASK_RUN_IF_NOT_COMPLETED,
    );

    $tasks['jse_configure_jse_beans'] = array(
      'display_name' => st('Configure Beans'),
      'display' => FALSE,
      'type' => 'normal',
      'function' => 'configure_jse_beans',
      'run' => INSTALL_TASK_RUN_IF_NOT_COMPLETED,
    );

    $tasks['jse_install_jumpstart_users'] = array(
      'display_name' => st('Create Jumpstart Users.'),
      'display' => FALSE,
      'type' => 'normal',
      'function' => 'jse_install_jumpstart_users',
      'run' => INSTALL_TASK_RUN_IF_NOT_COMPLETED,
    );

    // Do the prefixing stuff.
    $this->prepare_tasks($tasks, get_class());

    // Add the finish task back.
    $tasks[$finish_key] = $finish;

    return array_merge($parent_tasks, $tasks);
  }

  // Set Jumpstart Engineering Specific variables and settings
  //
  //

  public function set_jse_variables(&$install_state) {
    drush_log('JSE - Setting JSE Specific Variables.', 'ok');


    // Variables.
    variable_set('stanford_jumpstart_engineering', TRUE);
    variable_del('stanford_jumpstart_academic');
    drush_log('JSE - Finished Setting JSE Variables.', 'ok');
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

    drupal_set_time_limit(600);

    if (lock_acquire('jumpstart_sites_engineering_install_content', 600.0)) {

      $endpoint = 'https://sites.stanford.edu/jsa-content/jsainstall';

      $filters = array('tid_raw' => array('55'));
      $view_importer = new SitesContentImporterViews();
      $view_importer->set_endpoint($endpoint);
      $view_importer->set_resource('content');
      $view_importer->set_filters($filters);
      $view_importer->import_content_by_views_and_filters();

      // Load up library.
      $this->load_sites_content_importer_files($install_state);

      $this->fetch_jse_content_beans($endpoint);
      drush_log('JSE - Finished importing beans.', 'ok');

      lock_release('jumpstart_sites_engineering_install_content');
      $time_diff = time() - $time;
      drush_log('JSE - Finished importing content. Import took: ' . $time_diff . ' seconds', 'ok');
    }
    else {
      drush_log('JSE - Lock not acquired; no content imported', 'error');
    }

  }

  /**
   * Installs and configures the Main menu items for JSE.
   *
   * @param [type] $install_state
   *   Description.
   */
  public function jse_install_main_menu_items(&$install_state) {
    $time = time();
    drush_log('JSE - Start creating Main menu items', 'ok');
    $items = array();

    // Rebuild the menu cache before starting this.
    drupal_static_reset();
    menu_cache_clear_all();
    menu_rebuild();

    // Get the parent link id for the "About" menu item
    $plid = array();
    $parent = 'node/51';
    $menu_name = 'main-menu';
    $menu_info = db_select('menu_links', 'ml')
      ->condition('ml.link_path', $parent)
      ->condition('ml.menu_name', $menu_name)
      ->fields('ml', array('mlid', 'plid'))
      ->execute()
      ->fetchAll();

    foreach ($menu_info as $key => $value) {
      $plid[] = $menu_info[$key]->mlid;
    }

    // About / affiliate-organizations
    $items['about/affiliate-organization'] = array(
      'link_path' => drupal_get_normal_path('about/affiliate-organizations'),
      'link_title' => 'Affiliate Organizations',
      'menu_name' => 'main-menu',
      'weight' => -5,
      'plid' => $plid[0], // must be saved prior to contact item.
    );

    // Loop through each of the items and save them.
    foreach ($items as $k => $v) {

      // Check to see if there is a parent declaration. If there is then find
      // the mlid of the parent item and attach it to the menu item being saved.

      if (!isset($v['plid'])) {
        if (isset($v['parent'])) {
          $v['plid'] = $items[$v['parent']]['mlid'];
          unset($v['parent']); // Remove fluff before save.
        }
      }

      // Save the menu item.
      $mlid = menu_link_save($v);
      $v['mlid'] = $mlid;
      $items[$k] = $v;
    }

    $time_diff = time() - $time;
    drush_log('JSE - Finished creating Main Menu items: ' . $time_diff . ' seconds', 'ok');
  }

  /**
   * Installs and configures the Private Pages Section menu for JSE.
   *
   * @param [type] $install_state
   *   Description.
   */
  public function install_private_pages_section_menu_items(&$install_state) {
    $time = time();
    drush_log('JSE - starting create Private Pages menu items', 'ok');
    $items = array();

    // Rebuild the menu cache before starting this.
    drupal_static_reset();
    menu_cache_clear_all();
    menu_rebuild();

    // Private pages section landing page.
    $items['private'] = array(
      'link_path' => drupal_get_normal_path('private'),
      'link_title' => 'Private Pages',
      'menu_name' => 'menu-menu-private-pages',
      'weight' => -9,
    );

    // For Faculty.
    $items['private/for-faculty'] = array(
      'link_path' => drupal_get_normal_path('private/for-faculty'),
      'link_title' => 'For Faculty',
      'menu_name' => 'menu-menu-private-pages',
      'weight' => -7,
    );

    // For Students.
    $items['private/for-students'] = array(
      'link_path' => drupal_get_normal_path('private/for-students'),
      'link_title' => 'For Students',
      'menu_name' => 'menu-menu-private-pages',
      'weight' => -5,
    );

    // For Staff.
    $items['private/for-staff'] = array(
      'link_path' => drupal_get_normal_path('private/for-staff'),
      'link_title' => 'For Staff',
      'menu_name' => 'menu-menu-private-pages',
      'weight' => -3,
    );

    // For Faculty / Sub-page.
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
    drush_log('JSE - Finished creating Private Pages menu items: ' . $time_diff . ' seconds', 'ok');
  }

  /**
   * Installs default settings and creates an importer and a mapper.
   *
   * @param [type] &$install_state
   *   Description.
   */
  public function capx_default_configuration(&$install_state) {
    drush_log('JSE - Configuring CAPx Defaults', 'status');

    // Loop through the mapper settings and save to the DB.
    $mappers = $this->get_capx_mappers();
    foreach ($mappers as $machine_name => $settings) {

      $mapper = entity_create('capx_cfe', array());

      $mapper->uid = 1;
      $mapper->title = $settings['title'];
      $mapper->machine_name = $machine_name;
      $mapper->type = "mapper";
      $mapper->settings = $settings;

      // Do the save.
      capx_mapper_save($mapper);
    }

    // Loop through the importer configurations and save them to the db.
    $importers = $this->get_capx_importers();
    foreach ($importers as $machine_name => $info) {
      $settings = $info['settings'];
      $meta = $info['meta'];

      $importer = entity_create('capx_cfe', array());
      $importer->type = "importer";
      $importer->machine_name = $machine_name;
      $importer->title = $info['title'];
      $importer->uid = 1;
      $importer->settings = $settings;
      $importer->meta = $meta;

      // Do the save.
      capx_importer_save($importer);
    }

    drush_log('JSE - Finished Configuring CAPx', 'status');
  }


  /**
   * Returns an array of mapper information to be used to save to the db.
   *
   * @return array
   *   An array of mapper information.
   */
  private function get_capx_mappers() {
    $mappers = array();

    // Default Mapper.
    $mappers['default'] = array(
      'fields' => array(
        'field_s_person_affiliation' => array(
          'tid' => '',
        ),
        'field_s_person_cohort' => array(
          'value' => '',
        ),
        'field_s_person_dissertatn_title' => array(
          'value' => '',
        ),
        'field_s_person_education' => array(
          'value' => '$.education.*.label.text',
        ),
        'field_s_person_email' => array(
          'email' => '$.primaryContact.email',
        ),
        'field_s_person_faculty_title' => array(
          'value' => '$.longTitle[0]',
        ),
        'field_s_person_faculty_type' => array(
          'tid' => '',
        ),
        'field_s_person_fax_display' => array(
          'value' => '$.primaryContact.fax',
        ),
        'field_s_person_file' => array(
          0 => '',
        ),
        'field_s_person_first_name' => array(
          'value' => '$.names.preferred.firstName',
        ),
        'field_s_person_graduation_year' => array(
          'value' => '',
        ),
        'field_s_person_info_links' => array(
          'url' => '',
          'title' => '',
        ),
        'field_s_person_interests' => array(
          'tid' => '',
        ),
        'field_s_person_last_name' => array(
          'value' => '$.names.preferred.lastName',
        ),
        'field_s_person_mail_address_dspl' => array(
          'value' => '$.primaryContact.address',
        ),
        'field_s_person_mail_code' => array(
          'value' => '',
        ),
        'field_s_person_middle_name' => array(
          'value' => '',
        ),
        'field_s_person_office_hours' => array(
          'value' => '',
        ),
        'field_s_person_office_location' => array(
          'value' => '',
        ),
        'field_s_person_phone_display' => array(
          'value' => '',
        ),
        'field_s_person_profile_picture' => array(
          0 => '',
        ),
        'field_s_person_staff_type' => array(
          'tid' => '',
        ),
        'field_s_person_student_type' => array(
          'tid' => '',
        ),
        'field_s_person_study' => array(
          'tid' => '',
        ),
        'field_s_person_weight' => array(
          'value' => '',
        ),
        'body' => array(
          'value' => '',
          'summary' => '',
        ),
      ),
      'properties' => array(
        'title' => '$.displayName',
      ),
      'collections' => array(),
      'entity_type' => 'node',
      'bundle_type' => 'stanford_person',
      'title' => t("Default"),
    );

    // JSE Default.
    $mappers["jse_default"] = array(
      'fields' => array(
        'field_s_person_affiliation' => array(
          'tid' => '',
        ),
        'field_s_person_cohort' => array(
          'value' => '',
        ),
        'field_s_person_dissertatn_title' => array(
          'value' => '',
        ),
        'field_s_person_education' => array(
          'value' => '$.education.*.label.text',
        ),
        'field_s_person_email' => array(
          'email' => '$.primaryContact.email',
        ),
        'field_s_person_faculty_title' => array(
          'value' => '$.titles.*.label.text',
        ),
        'field_s_person_faculty_type' => array(
          'tid' => '',
        ),
        'field_s_person_fax_display' => array(
          'value' => '$.primaryContact.fax',
        ),
        'field_s_person_file' => array(
          0 => '',
        ),
        'field_s_person_first_name' => array(
          'value' => '$.names.preferred.firstName',
        ),
        'field_s_person_graduation_year' => array(
          'value' => '',
        ),
        'field_s_person_info_links' => array(
          'url' => '$.internetLinks.*.url',
          'title' => '$.internetLinks.*.label.text',
        ),
        'field_s_person_interests' => array(
          'tid' => '',
        ),
        'field_s_person_last_name' => array(
          'value' => '$.names.preferred.lastName',
        ),
        'field_s_person_mail_address_dspl' => array(
          'value' => '$.primaryContact.address',
        ),
        'field_s_person_mail_code' => array(
          'value' => '',
        ),
        'field_s_person_middle_name' => array(
          'value' => '$.names.preferred.middleName',
        ),
        'field_s_person_office_hours' => array(
          'value' => '',
        ),
        'field_s_person_office_location' => array(
          'value' => '',
        ),
        'field_s_person_phone_display' => array(
          'value' => '$.primaryContact.phoneNumbers.*',
        ),
        'field_s_person_profile_picture' => array(
          0 => '$.profilePhotos.bigger',
        ),
        'field_s_person_staff_type' => array(
          'tid' => '',
        ),
        'field_s_person_student_type' => array(
          'tid' => '',
        ),
        'field_s_person_study' => array(
          'tid' => '',
        ),
        'field_s_person_weight' => array(
          'value' => '',
        ),
        'body' => array(
          'value' => '$.bio.text',
          'summary' => '',
        ),
      ),
      'properties' => array(
        'title' => '$.displayName',
      ),
      'collections' => array(),
      'entity_type' => 'node',
      'bundle_type' => 'stanford_person',
      'title' => t("JSE Default"),
    );

    return $mappers;
  }


  /**
   * Returns an array of importer configuration information.
   *
   * @return array
   *   An array of importer configuration information.
   */
  private function get_capx_importers() {
    $importers = array();

    // Default.
    $importers['default'] = array();
    $importers['default']['title'] = t("Default");
    $importers['default']['settings'] = array(
      'mapper' => 'jse_default',
      'organization' => '',
      'child_orgs' => 0,
      'workgroup' => '',
      'sunet_id' => '',
      'cron_option' => 'none',
      'cron_option_day_number' => '1',
      'cron_option_day_week' => 'monday',
      'cron_option_month' => '0',
      'cron_option_hour' => '2:00',
      'orphan_action' => 'unpublish',
    );
    $importers['default']['meta'] = array();

    return $importers;
  }

  /**
   * Enable a number of the home page layouts and set one to default on.
   * @param  [type] $install_state [description]
   * @return [type]                [description]
   */
  public function configure_pical_homepage_layouts(&$install_state) {
    $time = time();
    drush_log('JSE - Configuring PICAL homepage layouts.' . $time, 'ok');

    $default = 'stanford_jumpstart_home_morris';
    variable_set('stanford_jumpstart_home_active_body_class', 'stanford-jumpstart-home-morris');

    $context_status = variable_get('context_status', array());
    $homecontexts = stanford_jumpstart_home_context_default_contexts();

    $names = array_keys($homecontexts);

    // Enable these JSE layouts for use by site owners.
    $enabled['stanford_jumpstart_home_hoover'] = 1;
    $enabled['stanford_jumpstart_home_morris'] = 1;
    $enabled['stanford_jumpstart_home_terman'] = 1;
    $enabled['stanford_jumpstart_home_pettit'] = 1;

    // Disable these layouts.
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

    // Save settings.
    variable_set('stanford_jumpstart_home_active', $default);
    variable_set('context_status', $context_status);

    $time_diff = time() - $time;
    drush_log('JSE - Finished configuring JSE homepage layouts: ' . $time_diff . ' seconds', 'ok');
  }

  /**
   * Configure the sitewide layout.
   * @param  [type] $install_state [description]
   * @return [type]                [description]
   */
  public function configure_sitewide_layout(&$install_state) {
    $time = time();
    drush_log('JSE - Configuring site-wide layout.', 'ok');

    $context_status = variable_get('context_status', array());

    $context_status['sitewide_jse'] = FALSE;
    $context_status['sitewide_jsa'] = TRUE;

    variable_set('context_status', $context_status);

    $time_diff = time() - $time;
    drush_log('JSE - Finished configuring JSE site-wide layout: ' . $time_diff . ' seconds', 'ok');

  }

  /**
   * Configure the beans used by the JSE layouts.
   * @param  [type] $install_state [description]
   * @return [type]                [description]
   */
  public function configure_jse_beans(&$install_state) {
    $time = time();
    drush_log('JSE - Configuring Beans.', 'ok');

    // Install default JSE block classes.
    $fields = array('module', 'delta', 'css_class');
    $values = array(
      array("bean", "jumpstart-small-custom-block", "well"),
      array("bean", "jumpstart-large-custom-block", "well"),
    );

    // Key all the values.
    $insert = db_insert('block_class')->fields($fields);
    foreach ($values as $value) {
      $db_values = array_combine($fields, $value);
      $insert->values($db_values);
    }
    $insert->execute();

    // Install contextual block classes.
    $cbc_layouts = array();

    $cbc_layouts['stanford_jumpstart_home_hoover']['bean-jumpstart-home-page-banner---no-'][] = 'span8';
    $cbc_layouts['stanford_jumpstart_home_hoover']['bean-homepage-about-block'][] = 'span4 well';
    $cbc_layouts['stanford_jumpstart_home_hoover']['bean-jumpstart-small-custom-block'][] = 'span4';
    $cbc_layouts['stanford_jumpstart_home_hoover']['bean-jumpstart-large-custom-block'][] = 'span8 well';
    $cbc_layouts['stanford_jumpstart_home_hoover']['views-46f3a22e00be75cb8fe3bc16de17162a'][] = 'span4 well'; // Affiliates two-stacked


    $cbc_layouts['stanford_jumpstart_home_morris']['bean-jumpstart-home-page-banner---no-'][] = 'span8';
    $cbc_layouts['stanford_jumpstart_home_morris']['bean-homepage-about-block'][] = 'span4 well';
    $cbc_layouts['stanford_jumpstart_home_morris']['bean-jumpstart-small-custom-block'][] = 'span4';
    $cbc_layouts['stanford_jumpstart_home_morris']['views-f73ff55b085ea49217d347de6630cd5a'][] = 'span4 well';
    $cbc_layouts['stanford_jumpstart_home_morris']['views-stanford_events_views-block'][] = 'span4 well';
    $cbc_layouts['stanford_jumpstart_home_morris']['views-46f3a22e00be75cb8fe3bc16de17162a'][] = 'span4 well'; // Affiliates two-stacked

    $cbc_layouts['stanford_jumpstart_home_pettit']['bean-jumpstart-home-page-full-width-b'][] = 'span12';
    $cbc_layouts['stanford_jumpstart_home_pettit']['bean-homepage-about-block'][] = 'span8 well';
    $cbc_layouts['stanford_jumpstart_home_pettit']['bean-jumpstart-large-custom-block'][] = 'span8';
    $cbc_layouts['stanford_jumpstart_home_pettit']['views-f73ff55b085ea49217d347de6630cd5a'][] = 'span4 well';
    $cbc_layouts['stanford_jumpstart_home_pettit']['views-stanford_events_views-block'][] = 'span4 well';
    $cbc_layouts['stanford_jumpstart_home_pettit']['views-46f3a22e00be75cb8fe3bc16de17162a'][] = 'span4 well'; // Affiliates two-stacked

    $cbc_layouts['stanford_jumpstart_home_terman']['bean-jumpstart-home-page-full-width-b'][] = 'span12';
    $cbc_layouts['stanford_jumpstart_home_terman']['bean-jumpstart-about-block'][] = 'span4 well';
    $cbc_layouts['stanford_jumpstart_home_terman']['bean-jumpstart-large-custom-block'][] = 'span8';
    $cbc_layouts['stanford_jumpstart_home_terman']['bean-jumpstart-small-custom-block'][] = 'span4';
    $cbc_layouts['stanford_jumpstart_home_terman']['views-f73ff55b085ea49217d347de6630cd5a'][] = 'span4 well';
    $cbc_layouts['stanford_jumpstart_home_terman']['views-stanford_events_views-block'][] = 'span4 well';
    $cbc_layouts['stanford_jumpstart_home_terman']['views-46f3a22e00be75cb8fe3bc16de17162a'][] = 'span4 well'; // Affiliates two-stacked

    $cbc_layouts['sitewide_jse']['bean-jse-linked-logo-block'][] = 'span4';
    $cbc_layouts['sitewide_jse']['bean-jse-logo-block'][] = 'span4';
    $cbc_layouts['sitewide_jse']['bean-jumpstart-footer-contact-block'][] = 'span2';
    $cbc_layouts['sitewide_jse']['bean-jumpstart-footer-social-media--0'][] = 'span2';
    $cbc_layouts['sitewide_jse']['bean-jumpstart-custom-footer-block'][] = 'span2';
    $cbc_layouts['sitewide_jse']['stanford_private_page-stanford_internal_login'][] = 'span2';

    variable_set('contextual_block_class', $cbc_layouts);

    $time_diff = time() - $time;
    drush_log('JSE - Finished configuring Beans: ' . $time_diff . ' seconds', 'ok');
  }

  /**
   * Fetches beans from jsa-content
   * @param  [type] $endpoint [description]
   * @return [type]           [description]
   */
  private function fetch_jse_content_beans($endpoint) {

    $uuids = array(
      '04cef32d-aa4b-477c-850e-e9efd331fa4c',
      // Jumpstart Home Page Banner - No Caption.
      '40cabca1-7d44-42bf-a012-db53fdccd350',
      // Jumpstart Large Custom Block.
      '7e510af6-c003-402d-91a4-7480dac1484a',
      // Jumpstart Small Custom Block.
      '2c570a0a-d52a-4e8b-bf36-ec01b2777932',
      // JSE Logo Block.
      '593aed4a-653e-4bea-8129-9733f4b2bd4b',
      // JSE Linked Logo Block
      '87527e6a-1f9e-4b39-a999-c138851b3a47',
      // Jumpstart Custom Footer Block.
      'afb406ad-c08f-4c91-a179-e703a8afc6ca',
      // Jumpstart Home Page Full-Width Banner - No Caption
    );

    $importer = new SitesContentImporter();
    $importer->set_endpoint($endpoint);
    $importer->set_bean_uuids($uuids);
    $importer->import_content_beans();

  }

  /**
   * Installs and configures the default users for jumpstart
   * @param  [array] $install_state [the current installation state]
   */
  public function jse_install_jumpstart_users(&$install_state) {

    drush_log('JSE - Starting install users', 'status');

    // Need this for UI install.
    require_once DRUPAL_ROOT . '/includes/password.inc';
    $install_vars = variable_get('stanford_jumpstart_install', array());
    $config_form_data = $install_state['forms']['install_configure_form'];

    // Get some stored variables.
    if ($install_state['interactive']) {
      $full_name = isset($install_vars['full_name']) ? $install_vars['full_name'] : "School of Engineering";
      $sunetid = isset($install_vars['sunetid']) ? $install_vars['sunetid'] : 'jse-admins';
    }
    else {
      if (function_exists('drush_get_option')) {
        $full_name = isset($config_form_data['stanford_sites_requester_name']) ? $config_form_data['stanford_sites_requester_name'] : drush_get_option('full_name', 'Engineering');
        $sunetid = isset($config_form_data['stanford_sites_requester_sunetid']) ? $config_form_data['stanford_sites_requester_sunetid'] : drush_get_option('sunetid', 'jse-admins');
      }
      else {
        $full_name = "Engineering";
        $sunetid = "jse-admins";
      }
    }

    // add WMD user (site owner)
    // drush waau $sunetid --name="$fullname"
    $sunet = strtolower(trim($sunetid));
    $authname = $sunet . '@stanford.edu';

    $sunet_role = user_role_load_by_name('SUNet User');
    $owner_role = user_role_load_by_name('site owner');
    $editor_role = user_role_load_by_name('editor');
    $admin_role = user_role_load_by_name('administrator');
    $member_role = user_role_load_by_name('site member');

    // Change user 1, currently admin, to jse-admins
    $account = user_load(1, TRUE);
    $edit = array();
    $edit['mail'] = "jse-admins@lists.stanford.edu";
    $edit['status'] = TRUE;
    $roles = array(
      DRUPAL_AUTHENTICATED_RID => TRUE,
      $sunet_role->rid => TRUE,
      $admin_role->rid => TRUE
    );
    $edit['roles'] = $roles;
    $edit['timezone'] = variable_get('date_default_timezone', '');
    $account = user_save($account, $edit);

    // Change the sunet requester user to site owner
    $edit = array();
    $user3 = user_load_by_mail($authname);

    if ($user3) {
      $roles = array(
        DRUPAL_AUTHENTICATED_RID => TRUE,
        $sunet_role->rid => TRUE,
        $owner_role->rid => TRUE
      );
      $edit['roles'] = $roles;
      $user3 = user_save($user3, $edit);
      // Check our chosen authentication scheme.
      $auth_method = variable_get('stanford_sites_auth_method', 'webauth');
      if ($auth_method == 'simplesamlphp') {
        user_set_authmaps($user3, array('authname_simplesamlphp_auth' => $authname));
      }
      else {
        user_set_authmaps($user3, array('authname_webauth' => $authname));
      }
    }

    // Map soe:jse-admins to administrator role
    // drush wamr soe:jse-admins administrator
    if (module_exists('webauth_extras')) {
      module_load_include('inc', 'webauth_extras', 'webauth_extras.drush');
      drush_webauth_extras_webauth_map_role('soe:jse-admins', 'administrator');
    }

    drush_log('JSE - Finished installing users', 'ok');

  }

}
