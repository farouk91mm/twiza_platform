<?php

// ── الصفحة الرئيسية ──
$router->get('/',               'ProjectController', 'index');
$router->get('/projects',       'ProjectController', 'projectsList');
$router->get('/projects/show',  'ProjectController', 'show');

// ── المصادقة ──
$router->get ('/auth/login',    'AuthController', 'loginForm');
$router->post('/auth/login',    'AuthController', 'login');
$router->get ('/auth/register', 'AuthController', 'registerForm');
$router->post('/auth/register', 'AuthController', 'register');
$router->get ('/auth/logout',   'AuthController', 'logout');

// ── الجمعية ──
$router->get ('/association/dashboard',
              'AssociationController', 'dashboard');
$router->get ('/association/setup',
              'AssociationController', 'setupForm');
$router->post('/association/setup',
              'AssociationController', 'setup');
$router->get ('/association/projects/add',
              'AssociationController', 'addProjectForm');
$router->post('/association/projects/add',
              'AssociationController', 'addProject');
$router->get ('/association/donations',
              'AssociationController', 'donations');
$router->post('/association/donations/confirm',
              'AssociationController', 'confirmDonation');

// ── الفرد ──
$router->get('/individual/dashboard',
             'IndividualController', 'dashboard');
$router->get('/individual/donations',
             'IndividualController', 'myDonations');

// ── التبرعات ──
$router->post('/donations/add', 'DonationController', 'store');

// ── التاجر ──
$router->get('/merchant/dashboard', 'MerchantController', 'dashboard');
$router->get('/merchant/products',  'MerchantController', 'products');

// ── Admin ──
$router->get('/admin/dashboard', 'AdminController', 'dashboard');

// ── الإشعارات ──
$router->get ('/notifications',        'NotificationController', 'index');
$router->post('/notifications/read',   'NotificationController', 'markRead');
$router->get ('/notifications/count',  'NotificationController', 'count');
$router->post('/notifications/delete', 'NotificationController', 'delete');

// ── تعديل المشروع ──
$router->get ('/association/projects/edit',
              'AssociationController', 'editProjectForm');
$router->post('/association/projects/edit',
              'AssociationController', 'editProject');

              // ── حذف المشروع ──
$router->post('/association/projects/delete',
              'AssociationController', 'deleteProject');

              // ── تعديل وحذف التبرع ──
$router->get ('/donations/edit',   'DonationController', 'editForm');
$router->post('/donations/edit',   'DonationController', 'edit');
$router->post('/donations/delete', 'DonationController', 'delete');

// ── المجموعات ──
$router->get ('/groups/create',  'GroupController', 'createForm');
$router->post('/groups/create',  'GroupController', 'create');
$router->get ('/groups/show',    'GroupController', 'show');
$router->get ('/groups/join',    'GroupController', 'joinForm');
$router->post('/groups/join',    'GroupController', 'join');
$router->post('/groups/donate',  'GroupController', 'donate');

// ── مجموعات الجمعية ──
$router->get ('/association/groups',
              'AssociationController', 'groups');
$router->post('/association/groups/approve',
              'AssociationController', 'approveGroup');
$router->post('/association/groups/confirm-donation',
              'AssociationController', 'confirmGroupDonation');

              // ── مجموعاتي ──
$router->get('/groups', 'GroupController', 'myGroups');

