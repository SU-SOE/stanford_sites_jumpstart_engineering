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

    $tasks['jse_configure_capx'] = array(
      'display_name' => st('Create CAPx default configuration.'),
      'display' => FALSE,
      'type' => 'normal',
      'function' => 'capx_default_configuration',
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


  /**
   * Installs default settings and creates an importer and a mapper.
   *
   * @param [type] &$install_state
   *   Description.
   */
  public function capx_default_configuration(&$install_state) {
    drush_log('JSE - Configuring CAPx Defaults' , 'status');

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

    drush_log('JSE - Finished Configuring CAPx' , 'status');
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
      'sunet_id' => 'tsui,sheamck,regula,olgary,whitmore,chu,rbaltman,katekate,tomq1,herschla,harbury,rhiju,kobilka,ingmar,danb,hblau,pblumen,lynnw',
      'cron_option' => 'daily',
      'cron_option_day_number' => '1',
      'cron_option_day_week' => 'monday',
      'cron_option_month' => '0',
      'cron_option_hour' => '2:00',
      'orphan_action' => 'unpublish',
    );
    $importers['default']['meta'] = array();

    return $importers;
  }



 }
