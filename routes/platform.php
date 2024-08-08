<?php

declare(strict_types=1);

use App\Orchid\Screens\Examples\ExampleActionsScreen;
use App\Orchid\Screens\Examples\ExampleCardsScreen;
use App\Orchid\Screens\Examples\ExampleChartsScreen;
use App\Orchid\Screens\Examples\ExampleFieldsAdvancedScreen;
use App\Orchid\Screens\Examples\ExampleFieldsScreen;
use App\Orchid\Screens\Examples\ExampleGridScreen;
use App\Orchid\Screens\Examples\ExampleLayoutsScreen;
use App\Orchid\Screens\Examples\ExampleScreen;
use App\Orchid\Screens\Examples\ExampleTextEditorsScreen;
use App\Orchid\Screens\PlatformScreen;
use App\Orchid\Screens\Role\RoleEditScreen;
use App\Orchid\Screens\Role\RoleListScreen;
use App\Orchid\Screens\User\UserEditScreen;
use App\Orchid\Screens\User\UserListScreen;
use App\Orchid\Screens\User\UserProfileScreen;
use Illuminate\Support\Facades\Route;
use Tabuna\Breadcrumbs\Trail;



use App\Orchid\Screens\Dashboard\DashboardScreen;
use App\Orchid\Screens\TopUp\TopUpScreen;

use App\Orchid\Screens\InternetQuotaList\InternetQuotaListScreen;
use App\Orchid\Screens\InternetQuotaList\InternetQuotaDetailScreen;

use App\Orchid\Screens\UserBalance\UserBalanceScreen;
use App\Orchid\Screens\UserBalance\UserBalanceEditScreen;

use App\Orchid\Screens\ProductList\ProductListScreen;
use App\Orchid\Screens\ProductList\ProductListEditScreen;

use App\Orchid\Screens\AddInternetQuotaList\AddInternetQuotaListScreen;
use App\Orchid\Screens\AddInternetQuotaList\AddInternetQuotaListEditScreen;

use App\Orchid\Screens\HistoryPayment\HistoryPaymentListScreen;
use App\Orchid\Screens\ListHistoryPayment\ListHistoryPaymentListScreen;

use App\Orchid\Screens\ListFileUsers\ListFileUserScreen;

use App\Orchid\Screens\DeleteQuota\DeleteQuotaScreen;

use App\Orchid\Screens\PageController\PageListScreen;
use App\Orchid\Screens\PageController\PageEditScreen;


use App\Orchid\Screens\Maintenance\MaintenanceScreen;



/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the need "dashboard" middleware group. Now create something great!
|
*/

// Main
// Route::screen('/main', PlatformScreen::class)
//     ->name('platform.main');

// Main
Route::screen('/main', DashboardScreen::class)
    ->middleware('check.maintenance.status')
    ->middleware('clear.session')
    ->name('platform.main');

// Maintenance
Route::screen('/maintenance', MaintenanceScreen::class)
    ->name('platform.maintenance');

// Platform > Top Up
Route::screen('top-up', TopUpScreen::class)
    ->middleware('clear.session')
    ->middleware('check.maintenance.status')
    ->name('platform.top-up')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Top Up'), route('platform.top-up')));

// Platform > Internet Quota List
Route::screen('internet-quota-list', InternetQuotaListScreen::class)
    ->middleware('clear.session')
    ->middleware('check.maintenance.status')
    ->name('platform.internet-quota-list')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Internet Quota List'), route('platform.internet-quota-list')));


// Platform > Internet Quota List > Buy
Route::screen('internet-quota-list/{encryptedId}/buy', InternetQuotaDetailScreen::class)
    ->name('platform.internet-quota-list.buy')
    ->middleware('check.maintenance.status')
    ->breadcrumbs(fn(Trail $trail, $encryptedId) => $trail
        ->parent('platform.internet-quota-list')
        ->push(__('Buy Product'), route('platform.internet-quota-list.buy', $encryptedId)));


// Platform > History Payments
Route::screen('history-payment', HistoryPaymentListScreen::class)
    ->middleware('clear.session')
    ->name('platform.history-payment')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('History Payment'), route('platform.history-payment')));


// Platform > Delete Quota
Route::screen('delete-quota', DeleteQuotaScreen::class)
    ->middleware('clear.session')
    ->name('platform.delete-quota')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Delete Quota'), route('platform.delete-quota')));


// Platform > Product List
Route::screen('product-list', ProductListScreen::class)
    ->middleware('clear.session')
    ->name('platform.product-list')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Product List'), route('platform.product-list')));


// Platform > Product List > Edit
Route::screen('product-list/{id}/edit', ProductListEditScreen::class)
    ->name('platform.product-list.edit')
    ->breadcrumbs(fn(Trail $trail, $id) => $trail
        ->parent('platform.product-list')
        ->push(__('Edit Product List'), route('platform.product-list.edit', $id)));


// Platform > Add Internet Quota List
Route::screen('add-internet-quota-list', AddInternetQuotaListScreen::class)
    ->name('platform.add-internet-quota-list')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Add Quota List'), route('platform.add-internet-quota-list')));


// Platform > Add Internet Quota List > Edit
Route::screen('add-internet-quota-list/{id}/edit', AddInternetQuotaListEditScreen::class)
    ->name('platform.add-internet-quota-list.edit')
    ->breadcrumbs(fn(Trail $trail, $id) => $trail
        ->parent('platform.add-internet-quota-list')
        ->push(__('Edit'), route('platform.add-internet-quota-list.edit', $id)));


// Platform > User Balance
Route::screen('user-balance', UserBalanceScreen::class)
    ->middleware('clear.session')
    ->name('platform.user-balance')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('User Balance'), route('platform.user-balance')));


// Platform > User Balance > Create New Balance
Route::screen('user-balance/top-up', UserBalanceEditScreen::class)
    ->name('platform.user-balance.create')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.user-balance')
        ->push(__('Top up user balance'), route('platform.user-balance.create')));

// Platform > User Data
Route::screen('user-data', ListFileUserScreen::class)
    ->middleware('clear.session')
    ->name('platform.user-data')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('User Data'), route('platform.user-data')));


// Platform > Pages Controllers
Route::screen('page-controller', PageListScreen::class)
    ->name('platform.page-controller')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Pages Controllers'), route('platform.page-controller')));


// Platform > Pages Controllers > Add New Page
Route::screen('page-controller/create-page', PageEditScreen::class)
    ->name('platform.page-controller.create-page')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.page-controller')
        ->push(__('Add New Pages'), route('platform.page-controller.create-page')));


// Platform > Pages Controllers > Edit New Page
Route::screen('page-controller/{id}/edit', PageEditScreen::class)
    ->name('platform.page-controller.edit-page')
    ->breadcrumbs(fn(Trail $trail, $id) => $trail
        ->parent('platform.page-controller')
        ->push(__('Edit Pages'), route('platform.page-controller.edit-page', $id)));


// Platform > History Payments
Route::screen('admin-history-payment', ListHistoryPaymentListScreen::class)
    ->middleware('clear.session')
    ->name('platform.admin-history-payment')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('History Payment'), route('platform.admin-history-payment')));


// Platform > Profile
Route::screen('profile', UserProfileScreen::class)
    ->middleware('clear.session')
    ->name('platform.profile')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Profile'), route('platform.profile')));

// Platform > System > Users > User
Route::screen('users/{user}/edit', UserEditScreen::class)
    ->name('platform.systems.users.edit')
    ->breadcrumbs(fn(Trail $trail, $user) => $trail
        ->parent('platform.systems.users')
        ->push($user->name, route('platform.systems.users.edit', $user)));

// Platform > System > Users > Create
Route::screen('users/create', UserEditScreen::class)
    ->name('platform.systems.users.create')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.systems.users')
        ->push(__('Create'), route('platform.systems.users.create')));

// Platform > System > Users
Route::screen('users', UserListScreen::class)
    ->middleware('clear.session')
    ->name('platform.systems.users')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Users'), route('platform.systems.users')));

// Platform > System > Roles > Role
Route::screen('roles/{role}/edit', RoleEditScreen::class)
    ->name('platform.systems.roles.edit')
    ->breadcrumbs(fn(Trail $trail, $role) => $trail
        ->parent('platform.systems.roles')
        ->push($role->name, route('platform.systems.roles.edit', $role)));

// Platform > System > Roles > Create
Route::screen('roles/create', RoleEditScreen::class)
    ->name('platform.systems.roles.create')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.systems.roles')
        ->push(__('Create'), route('platform.systems.roles.create')));

// Platform > System > Roles
Route::screen('roles', RoleListScreen::class)
    ->middleware('clear.session')
    ->name('platform.systems.roles')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Roles'), route('platform.systems.roles')));













// Example...
Route::screen('example', ExampleScreen::class)
    ->name('platform.example')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push('Example Screen'));

Route::screen('/examples/form/fields', ExampleFieldsScreen::class)->name('platform.example.fields');
Route::screen('/examples/form/advanced', ExampleFieldsAdvancedScreen::class)->name('platform.example.advanced');
Route::screen('/examples/form/editors', ExampleTextEditorsScreen::class)->name('platform.example.editors');
Route::screen('/examples/form/actions', ExampleActionsScreen::class)->name('platform.example.actions');

Route::screen('/examples/layouts', ExampleLayoutsScreen::class)->name('platform.example.layouts');
Route::screen('/examples/grid', ExampleGridScreen::class)->name('platform.example.grid');
Route::screen('/examples/charts', ExampleChartsScreen::class)->name('platform.example.charts');
Route::screen('/examples/cards', ExampleCardsScreen::class)->name('platform.example.cards');

//Route::screen('idea', Idea::class, 'platform.screens.idea');
