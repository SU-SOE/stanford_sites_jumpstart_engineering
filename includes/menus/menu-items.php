<?php

// /////////////////////////////////////////////////////////////////////////////
// MAIN MENU
// /////////////////////////////////////////////////////////////////////////////

// Research
$main_menu['research'] = array(
  'link_path' => drupal_get_normal_path('research'),
  'link_title' => 'Research',
  'menu_name' => 'main-menu',
  'weight' => -48,
  'customized' => 1,
);
// Courses
$main_menu['courses'] = array(
  'link_path' => drupal_get_normal_path('courses'),
  'link_title' => 'Courses',
  'menu_name' => 'main-menu',
  'weight' => -8,
  'customized' => 1,
);
// People
$main_menu['people'] = array(
  'link_path' => drupal_get_normal_path('people'),
  'link_title' => 'People',
  'menu_name' => 'main-menu',
  'weight' => -7,
);
  // Faculty
  $main_menu['people/faculty'] = array(
    'link_path' => drupal_get_normal_path('people/faculty'),
    'link_title' => 'Faculty',
    'menu_name' => 'main-menu',
    'weight' => -10,
    'customized' => 1,
    'parent' => 'people',
  );
  // Faculty
  $main_menu['people/students'] = array(
    'link_path' => drupal_get_normal_path('people/students'),
    'link_title' => 'Students',
    'menu_name' => 'main-menu',
    'weight' => -8,
    'customized' => 1,
    'parent' => 'people',
  );
  // Staff
  $main_menu['people/staff'] = array(
    'link_path' => drupal_get_normal_path('people/staff'),
    'link_title' => 'Staff',
    'menu_name' => 'main-menu',
    'weight' => -6,
    'customized' => 1,
    'parent' => 'people',
  );
// Publications
$main_menu['publications'] = array(
  'link_path' => drupal_get_normal_path('publications'),
  'link_title' => 'Publications',
  'menu_name' => 'main-menu',
  'weight' => -6,
  'customized' => 1,
);
// News Landing
$main_menu['news'] = array(
  'link_path' => drupal_get_normal_path('news'),
  'link_title' => 'News',
  'menu_name' => 'main-menu',
  'weight' => -5,
);
  // News / Recent News
  $main_menu['news/recent-news'] = array(
    'link_path' => 'news/recent-news',
    'link_title' => 'Recent News',
    'menu_name' => 'main-menu',
    'weight' => -9,
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
    'weight' => -7,
    'parent' => 'news',
  );
// Events Landing
$main_menu['events'] = array(
  'link_path' => drupal_get_normal_path('events'),
  'link_title' => 'Events',
  'menu_name' => 'main-menu',
  'weight' => -4,
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
    'weight' => -9,
    'parent' => 'events',
    'router_path' => 'events/past-events',
    'customized' => 1,
  );
  // Event Series
  $main_menu['events/series'] = array(
    'link_path' => 'events/series',
    'link_title' => 'Event Series',
    'menu_name' => 'main-menu',
    'weight' => -8,
    'parent' => 'events',
    'router_path' => 'events/series',
    'customized' => 1,
  );
  // Gallery
  $main_menu['news/gallery'] = array(
    'link_path' => drupal_get_normal_path('news/gallery'),
    'link_title' => 'Gallery',
    'menu_name' => 'main-menu',
    'weight' => -6,
    'parent' => 'news',
    'customized' => 1,
  );
// About
$main_menu['about'] = array(
  'link_path' => drupal_get_normal_path('about'),
  'link_title' => 'About',
  'menu_name' => 'main-menu',
  'weight' => -2,
);
// About / Overview
  $main_menu['about/overview'] = array(
    'link_path' => drupal_get_normal_path('about/about-us'),
    'link_title' => 'Overview',
    'menu_name' => 'main-menu',
    'weight' => -12,
    'parent' => 'about', // must be saved prior to overview item.
  );
  // About / location
  $main_menu['about/location'] = array(
    'link_path' => drupal_get_normal_path('about/location'),
    'link_title' => 'Location',
    'menu_name' => 'main-menu',
    'weight' => -10,
    'parent' => 'about', // must be saved prior to contact item.
  );
  // About / Contact
  $main_menu['about/contact'] = array(
    'link_path' => drupal_get_normal_path('about/contact'),
    'link_title' => 'Contact',
    'menu_name' => 'main-menu',
    'weight' => -8,
    'parent' => 'about', // must be saved prior to web-access item.
  );
  // About / affiliated-programs
  $main_menu['about/affiliated-programs'] = array(
    'link_path' => drupal_get_normal_path('about/affiliated-programs'),
    'link_title' => 'Affiliated Programs',
    'menu_name' => 'main-menu',
    'weight' => -6,
    'parent' => 'about', // must be saved prior to contact item.
  );
  // About / affiliate-organization
  $main_menu['about/affiliate-organization'] = array(
    'link_path' => drupal_get_normal_path('about/affiliate-organizations'),
    'link_title' => 'Affiliate Organizations',
    'menu_name' => 'main-menu',
    'weight' => -4,
    'parent' => 'about', // must be saved prior to contact item.
  );
  // About / Make a Gift
  $main_menu['about/giving'] = array(
    'link_path' => drupal_get_normal_path('about/giving'),
    'link_title' => 'Make A Gift',
    'menu_name' => 'main-menu',
    'weight' => -2,
    'parent' => 'about', // must be saved prior to web-access item.
  );
  // About / Accessibility
  // $main_menu['about/web-accessibility'] = array(
  //   'link_path' => drupal_get_normal_path('about/web-accessibility'),
  //   'link_title' => 'Web Accessibility',
  //   'menu_name' => 'main-menu',
  //   'weight' => 0,
  //   'parent' => 'about', // must be saved prior to web-access item.
  // );

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


