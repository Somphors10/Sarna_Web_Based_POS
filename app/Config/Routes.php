<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->setDefaultController('Saas');

$routes->get('/', 'Saas::index');
$routes->get('login', 'Login::index');
$routes->post('login', 'Login::index');
$routes->get('login/forgot-password', 'Login::forgotPassword');
$routes->post('login/forgot-password', 'Login::forgotPassword');
$routes->get('login/forgot-success', 'Login::forgotPasswordSuccess');
$routes->get('login/reset-password/(:segment)', 'Login::resetPassword/$1');
$routes->post('login/reset-password/(:segment)', 'Login::resetPassword/$1');
$routes->get('saas', 'Saas::index');
$routes->get('saas/register', 'Saas::register');
$routes->post('saas/register', 'Saas::postRegister');
$routes->get('saas/captcha', 'Saas::captchaImage');
$routes->get('saas/verify-email/(:segment)', 'Saas::verifyEmail/$1');
$routes->post('saas/resend-verify', 'Saas::postResendVerify');
$routes->get('saas/pay/(:segment)', 'Saas::pay/$1');
$routes->post('saas/pay/(:segment)', 'Saas::postPay/$1');
$routes->get('saas/checkout', 'Saas::checkout');
$routes->post('saas/checkout', 'Saas::postCheckout');
$routes->get('register-company', 'Company_register::index');
$routes->post('register-company', 'Company_register::index');

$routes->get('super-admin', 'Super_admin::index');
$routes->get('super-admin/overview', 'Super_admin::index/overview');
$routes->get('super-admin/businesses', 'Super_admin::index/businesses');
$routes->get('super-admin/admins', 'Super_admin::index/admins');
$routes->get('super-admin/requests', 'Super_admin::index/requests');
$routes->get('super-admin/history', 'Super_admin::index/history');
$routes->get('super-admin/payments', 'Super_admin::index/payments');
$routes->get('super-admin/plans', 'Super_admin::index/plans');
$routes->post('super-admin/plans/(:num)/feature', 'Super_admin::postTogglePlanFeature/$1');
$routes->post('super-admin/sync-template', 'Super_admin::postSyncTemplate');
$routes->post('super-admin/isolate-tenants', 'Super_admin::postIsolateTenants');
$routes->post('super-admin/isolate-tenant/(:num)', 'Super_admin::postIsolateTenant/$1');
$routes->get('super-admin/features', 'Super_admin::index/features');
$routes->get('super-admin/features/(:segment)', 'Super_admin::feature/$1');
$routes->post('super-admin/features/(:segment)/toggle', 'Super_admin::postToggleFeature/$1');
$routes->get('super-admin/login', 'Super_admin::login');
$routes->post('super-admin/login', 'Super_admin::login');
$routes->get('super-admin/logout', 'Super_admin::logout');
$routes->get('super-admin/changepassword', 'Super_admin::getChangePassword');
$routes->post('super-admin/changepassword', 'Super_admin::postChangePassword');
$routes->get('home', 'Home::getIndex');
$routes->group('home', static function ($routes) {
    $routes->get('/', 'Home::getIndex');
    $routes->get('logout', 'Home::getLogout');
    $routes->get('language/(:segment)', 'Home::getChangeLanguage/$1');
    $routes->get('changelanguage/(:segment)', 'Home::getChangeLanguage/$1');
    $routes->get('changeLanguage/(:segment)', 'Home::getChangeLanguage/$1');
    $routes->get('changepassword/(:num)', 'Home::getChangePassword/$1');
    $routes->get('changePassword/(:num)', 'Home::getChangePassword/$1');
    $routes->get('profile', 'Home::getProfile');
    $routes->get('plan', 'Home::getPlan');
    $routes->post('save/(:num)', 'Home::postSave/$1');
    $routes->post('save', 'Home::postSave');
});
$routes->get('super-admin/notifications/poll', 'Super_admin::getNotificationPoll');
$routes->post('super-admin/toggle-status/(:num)', 'Super_admin::postToggleStatus/$1');
$routes->post('super-admin/extend-subscription/(:num)', 'Super_admin::postExtendSubscription/$1');
$routes->post('super-admin/set-expiry/(:num)', 'Super_admin::postSetExpiry/$1');
$routes->post('super-admin/confirm-renewal/(:num)', 'Super_admin::postConfirmRenewal/$1');
$routes->post('super-admin/create-admin', 'Super_admin::postCreateAdmin');
$routes->post('super-admin/toggle-admin/(:num)', 'Super_admin::postToggleAdminStatus/$1');
$routes->get('super-admin/email', 'Super_admin::index/email');
$routes->post('super-admin/save-gmail', 'Super_admin::postSaveGmailSettings');
$routes->post('super-admin/test-gmail', 'Super_admin::postTestGmail');
$routes->post('super-admin/approve-request/(:num)', 'Super_admin::postApproveRequest/$1');
$routes->post('super-admin/resend-verify/(:num)', 'Super_admin::postResendOwnerVerify/$1');
$routes->get('super-admin/send-payment/(:num)', 'Super_admin::sendPayment/$1');
$routes->get('super-admin/preview-khqr-email/(:num)', 'Super_admin::previewKhqrEmail/$1');
$routes->post('super-admin/resend-payment-email/(:num)', 'Super_admin::postResendPaymentEmail/$1');
$routes->post('super-admin/save-gmail-smtp/(:num)', 'Super_admin::postSaveGmailSmtp/$1');
$routes->post('super-admin/confirm-payment/(:num)', 'Super_admin::postConfirmPayment/$1');
$routes->post('super-admin/reject-request/(:num)', 'Super_admin::postRejectRequest/$1');

$routes->add('no_access/index/(:segment)', 'No_access::index/$1');
$routes->add('no_access/index/(:segment)/(:segment)', 'No_access::index/$1/$2');

$routes->add('reports/summary_(:any)/(:any)/(:any)', 'Reports::Summary_$1/$2/$3/$4');
$routes->add('reports/summary_expenses_categories', 'Reports::date_input_only');
$routes->add('reports/summary_payments', 'Reports::date_input_only');
$routes->add('reports/summary_discounts', 'Reports::summary_discounts_input');
$routes->add('reports/summary_(:any)', 'Reports::date_input');

$routes->add('reports/graphical_(:any)/(:any)/(:any)', 'Reports::Graphical_$1/$2/$3/$4');
$routes->add('reports/graphical_summary_expenses_categories', 'Reports::date_input_only');
$routes->add('reports/graphical_summary_discounts', 'Reports::summary_discounts_input');
$routes->add('reports/graphical_(:any)', 'Reports::date_input');

$routes->add('reports/inventory_(:any)/(:any)', 'Reports::Inventory_$1/$2');
$routes->add('reports/inventory_low', 'Reports::inventory_low');
$routes->add('reports/inventory_summary', 'Reports::inventory_summary_input');
$routes->add('reports/inventory_summary/(:any)/(:any)/(:any)', 'Reports::inventory_summary/$1/$2/$3');

$routes->add('reports/detailed_(:any)/(:any)/(:any)/(:any)', 'Reports::Detailed_$1/$2/$3/$4');
$routes->add('reports/detailed_sales', 'Reports::date_input_sales');
$routes->add('reports/detailed_receivings', 'Reports::date_input_recv');

$routes->add('reports/specific_(:any)/(:any)/(:any)/(:any)', 'Reports::Specific_$1/$2/$3/$4');
$routes->add('reports/specific_customers', 'Reports::specific_customer_input');
$routes->add('reports/specific_employees', 'Reports::specific_employee_input');
$routes->add('reports/specific_discounts', 'Reports::specific_discount_input');
$routes->add('reports/specific_suppliers', 'Reports::specific_supplier_input');
