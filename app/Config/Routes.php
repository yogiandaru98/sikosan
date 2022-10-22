<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (is_file(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.

// For user guest, pencari kos, and penyewa kos
$routes->get('/', 'Home::index');
$routes->get('/about', 'Home::index');
$routes->get('/pusatBantuan', 'Home::pusatBantuan');
$routes->get('/terms', 'Home::terms');
$routes->get('/detail', 'Home::detail');
// -------------------------------------------

// For admin
$routes->get('/admin/data_owner', 'OwnerController::index', ['filter' => 'role:admin']);
$routes->get('/admin/data_customer', 'CustomerController::index', ['filter' => 'role:admin']);
$routes->get('/admin/data_kosan', 'KosanController::index', ['filter' => 'role:admin']);
$routes->get('/admin/data_laporan', 'LaporanController::index', ['filter' => 'role:admin']);
// -----------------------------------------------------------------------------------------------

// For Penyewa Kos
$routes->get('/owner/halaman_pemilik', 'OwnerController::halaman_pemilik', ['filter' => 'role:owner']);
$routes->get('/owner/kosan_anda', 'OwnerController::kosan_anda', ['filter' => 'role:owner']);
$routes->get('/owner/profil', 'OwnerController::profil', ['filter' => 'role:owner']);
// ----------------------------------------------------------------------------------------------------

// For Customer / Pencari Kos
$routes->get('/customer/profil', 'CustomerController::profil', ['filter' => 'role:customer']);
// --------------------------------------------------------------------------------------------------------

// $routes->get('/dummy_test/(:any)', 'KosanController::edit/$1');

// save testing method
$routes->post('/dummy_test', 'KosanController::save');
$routes->get('/creat_dummy',function()
{
    return view('onlyOwners/create');
});


/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
