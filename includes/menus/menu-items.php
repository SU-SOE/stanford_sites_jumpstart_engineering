<?php

// /////////////////////////////////////////////////////////////////////////////
// MAIN MENU
// /////////////////////////////////////////////////////////////////////////////

// About
$main_menu['about'] = array(
  'link_path' => drupal_get_normal_path('about'),
  'link_title' => 'About',
  'menu_name' => 'main-menu',
  'weight' => -20,
);
// About / Mission
$main_menu['about/mission'] = array(
  'link_path' => drupal_get_normal_path('about/mission'),
  'link_title' => 'Mission',
  'menu_name' => 'main-menu',
  'weight' => -12,
  'parent' => 'about', // must be saved prior to overview item.
);
// About / affiliated-programs
$main_menu['about/affiliated-programs'] = array(
  'link_path' => drupal_get_normal_path('about/affiliated-programs'),
  'link_title' => 'Programs',
  'menu_name' => 'main-menu',
  'weight' => -10,
  'parent' => 'about', // must be saved prior to contact item.
);
// About / affiliate-organization
$main_menu['about/affiliate-organization'] = array(
  'link_path' => drupal_get_normal_path('about/affiliate-organizations'),
  'link_title' => 'Affiliates',
  'menu_name' => 'main-menu',
  'weight' => -8,
  'parent' => 'about', // must be saved prior to contact item.
);
// Courses
$main_menu['courses'] = array(
  'link_path' => drupal_get_normal_path('courses'),
  'link_title' => 'Courses',
  'menu_name' => 'main-menu',
  'weight' => -6,
  'parent' => 'about', // must be saved prior to web-access item.
);
// About / Contact
$main_menu['about/contact'] = array(
  'link_path' => drupal_get_normal_path('about/contact'),
  'link_title' => 'Contact',
  'menu_name' => 'main-menu',
  'weight' => -4,
  'parent' => 'about', // must be saved prior to web-access item.
);

// People
$main_menu['people'] = array(
  'link_path' => drupal_get_normal_path('people'),
  'link_title' => 'People',
  'menu_name' => 'main-menu',
  'weight' => -16,
);
// People / People
$main_menu['people/all/grid/grouped'] = array(
  'link_path' => drupal_get_normal_path('people/all/grid/grouped'),
  'link_title' => 'People',
  'menu_name' => 'main-menu',
  'weight' => -12,
  'customized' => 1,
  'parent' => 'people',
);
// People / Faculty
$main_menu['people/faculty'] = array(
  'link_path' => drupal_get_normal_path('people/faculty'),
  'link_title' => 'Faculty',
  'menu_name' => 'main-menu',
  'weight' => -10,
  'customized' => 1,
  'parent' => 'people',
);
// People / Students
$main_menu['people/students/cap-list'] = array(
  'link_path' => drupal_get_normal_path('people/students/cap-list'),
  'link_title' => 'Students',
  'menu_name' => 'main-menu',
  'weight' => -8,
  'customized' => 1,
  'parent' => 'people',
);
// People / Staff
$main_menu['people/staff'] = array(
  'link_path' => drupal_get_normal_path('people/staff'),
  'link_title' => 'Staff',
  'menu_name' => 'main-menu',
  'weight' => -6,
  'customized' => 1,
  'parent' => 'people',
);

// Research
$main_menu['research'] = array(
  'link_path' => drupal_get_normal_path('research'),
  'link_title' => 'Research',
  'menu_name' => 'main-menu',
  'weight' => -12,
  'customized' => 1,
);
$main_menu['research/overview'] = array(
  'link_path' => drupal_get_normal_path('research/overview'),
  'link_title' => 'Research Overview',
  'menu_name' => 'main-menu',
  'weight' => -10,
  'customized' => 1,
  'parent' => 'research',
);
$main_menu['research/research-example'] = array(
  'link_path' => drupal_get_normal_path('research/research-example'),
  'link_title' => 'Research Example',
  'menu_name' => 'main-menu',
  'weight' => -8,
  'customized' => 1,
  'parent' => 'research',
);
$main_menu['research/program-example'] = array(
  'link_path' => drupal_get_normal_path('research/program-example'),
  'link_title' => 'Program Overview',
  'menu_name' => 'main-menu',
  'weight' => -6,
  'customized' => 1,
  'parent' => 'research',
);

// Publications
$main_menu['publications'] = array(
  'link_path' => drupal_get_normal_path('publications'),
  'link_title' => 'Publications',
  'menu_name' => 'main-menu',
  'weight' => -10,
  'customized' => 1,
);

// News Landing
$main_menu['news'] = array(
  'link_path' => drupal_get_normal_path('news'),
  'link_title' => 'News',
  'menu_name' => 'main-menu',
  'weight' => -8,
);
  // News / Recent News
  $main_menu['news/recent-news'] = array(
    'link_path' => 'news/recent-news',
    'link_title' => 'Recent News',
    'menu_name' => 'main-menu',
    'weight' => -10,
    'parent' => 'news',
    'customized' => 1,
  );
  // News / Department Newsletter
  $main_menu['news/department-newsletter'] = array(
    'link_path' => drupal_get_normal_path('news/department-newsletter'),
    'link_title' => 'Newsletter',
    'menu_name' => 'main-menu',
    'weight' => -8,
    'parent' => 'news',
  );
  // News / subscribe
  $main_menu['news/subscribe'] = array(
    'link_path' => drupal_get_normal_path('news/subscribe'),
    'link_title' => 'Subscribe',
    'menu_name' => 'main-menu',
    'weight' => -6,
    'parent' => 'news',
  );
  // News / Gallery
  $main_menu['news/gallery'] = array(
    'link_path' => drupal_get_normal_path('news/gallery'),
    'link_title' => 'Gallery',
    'menu_name' => 'main-menu',
    'weight' => -4,
    'parent' => 'news',
    'customized' => 1,
  );

// Events
$main_menu['events'] = array(
  'link_path' => drupal_get_normal_path('events'),
  'link_title' => 'Events',
  'menu_name' => 'main-menu',
  'weight' => -6,
);
  // Events / Upcoming
  $main_menu['events/upcoming-events'] = array(
    'link_path' => drupal_get_normal_path('events/upcoming-events'),
    'link_title' => 'Upcoming Events',
    'menu_name' => 'main-menu',
    'weight' => -10,
    'parent' => 'events',
    'router_path' => 'events/upcoming-events',
    'customized' => 1,
  );
  // Events / Past
  $main_menu['events/past-events'] = array(
    'link_path' => 'events/past-events',
    'link_title' => 'Past Events',
    'menu_name' => 'main-menu',
    'weight' => -8,
    'parent' => 'events',
    'router_path' => 'events/past-events',
    'customized' => 1,
  );
  // Event Series
  $main_menu['events/series'] = array(
    'link_path' => 'events/series',
    'link_title' => 'Event Series',
    'menu_name' => 'main-menu',
    'weight' => -6,
    'parent' => 'events',
    'router_path' => 'events/series',
    'customized' => 1,
  );

// Resources
$main_menu['resources'] = array(
  'link_path' => drupal_get_normal_path('resources'),
  'link_title' => 'Resources',
  'menu_name' => 'main-menu',
  'weight' => -4,
);
// Resources / Overview
$main_menu['resources/overview'] = array(
  'link_path' => drupal_get_normal_path('resources/overview'),
  'link_title' => 'Resources Overview',
  'menu_name' => 'main-menu',
  'weight' => -10,
  'parent' => 'resources',
);
// Resources / Software
$main_menu['resources/software'] = array(
  'link_path' => drupal_get_normal_path('resources/software'),
  'link_title' => 'Software Resources',
  'menu_name' => 'main-menu',
  'weight' => -8,
  'parent' => 'resources',
);
// Resources / References
$main_menu['resources/references'] = array(
  'link_path' => drupal_get_normal_path('resources/references'),
  'link_title' => 'References',
  'menu_name' => 'main-menu',
  'weight' => -6,
  'parent' => 'resources',
);



// /////////////////////////////////////////////////////////////////////////////
// Footer Menus
// /////////////////////////////////////////////////////////////////////////////

// About
$footer_about['about'] = array(
  'link_path' => drupal_get_normal_path('about'),
  'link_title' => 'About Us',
  'menu_name' => 'menu-footer-about-menu',
  'weight' => -50,
);
// Affiliated Programs
$footer_about['about/affiliated-programs'] = array(
  'link_path' => drupal_get_normal_path('about/affiliated-programs'),
  'link_title' => 'Affiliated Programs',
  'menu_name' => 'menu-footer-about-menu',
  'weight' => -48,
);
// Location
$footer_about['about/location'] = array(
  'link_path' => drupal_get_normal_path('about/location'),
  'link_title' => 'Location',
  'menu_name' => 'menu-footer-about-menu',
  'weight' => -46,
);
// Contact
$footer_about['about/contact'] = array(
  'link_path' => drupal_get_normal_path('about/contact'),
  'link_title' => 'Contact',
  'menu_name' => 'menu-footer-about-menu',
  'weight' => -44,
);
// Make a Gift
$footer_about['about/giving'] = array(
  'link_path' => drupal_get_normal_path('about/giving'),
  'link_title' => 'Make a Gift',
  'menu_name' => 'menu-footer-about-menu',
  'weight' => -42,
);

// -----------------------------------------------------------------------------

// Overview
$footer_academics['academics/academics-overview'] = array(
  'link_path' => drupal_get_normal_path('academics/academics-overview'),
  'link_title' => 'About Us',
  'menu_name' => 'menu-footer-academics-menu',
  'weight' => -50,
);
// Undergraduate Program
$footer_academics['academics/undergraduate-program'] = array(
  'link_path' => drupal_get_normal_path('academics/undergraduate-program'),
  'link_title' => 'Undergraduate Program',
  'menu_name' => 'menu-footer-academics-menu',
  'weight' => -48,
);
// Graduate Program
$footer_academics['academics/graduate-programs'] = array(
  'link_path' => drupal_get_normal_path('academics/graduate-programs'),
  'link_title' => 'Graduate Programs',
  'menu_name' => 'menu-footer-academics-menu',
  'weight' => -46,
);
// Courses
$footer_academics['courses'] = array(
  'link_path' => drupal_get_normal_path('courses'),
  'link_title' => 'Courses',
  'menu_name' => 'menu-footer-academics-menu',
  'weight' => -44,
);

// -----------------------------------------------------------------------------

// Department Newsletter
$footer_news_events['news/department-newsletter'] = array(
  'link_path' => drupal_get_normal_path('news/department-newsletter'),
  'link_title' => 'Department Newsletter',
  'menu_name' => 'menu-footer-news-events-menu',
  'weight' => -50,
);
// Recent News
$footer_news_events['news/recent-news'] = array(
  'link_path' => drupal_get_normal_path('news/recent-news'),
  'link_title' => 'Recent News',
  'menu_name' => 'menu-footer-news-events-menu',
  'weight' => -48,
);
// Subscribe
$footer_news_events['news/subscribe'] = array(
  'link_path' => drupal_get_normal_path('news/subscribe'),
  'link_title' => 'Subscribe',
  'menu_name' => 'menu-footer-news-events-menu',
  'weight' => -46,
);
// Upcoming events
$footer_news_events['events/upcoming-events'] = array(
  'link_path' => drupal_get_normal_path('events/upcoming-events'),
  'link_title' => 'Upcoming Events',
  'menu_name' => 'menu-footer-news-events-menu',
  'weight' => -44,
  'router_path' => 'events/upcoming-events',
  'customized' => 1,
);

// -----------------------------------------------------------------------------

// Faculty
$footer_people['people/faculty'] = array(
  'link_path' => drupal_get_normal_path('people/faculty'),
  'link_title' => 'Faculty',
  'menu_name' => 'menu-footer-people-menu',
  'weight' => -50,
);
// Students
$footer_people['people/students'] = array(
  'link_path' => drupal_get_normal_path('people/students'),
  'link_title' => 'Students',
  'menu_name' => 'menu-footer-people-menu',
  'weight' => -48,
);
// Staff
$footer_people['people/staff'] = array(
  'link_path' => drupal_get_normal_path('people/staff'),
  'link_title' => 'Staff',
  'menu_name' => 'menu-footer-people-menu',
  'weight' => -46,
);


