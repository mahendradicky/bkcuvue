<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/admins/{vue?}', function () {
    return view('admins');
})->where('vue', '^(?!.*api).*$[\/\w\.-]*');

Route::post('/login', 'Auth\LoginController@login');
Route::post('/logout', 'Auth\LoginController@logout');

Route::get('/testroute', function () {
    // $user = App\User::find(1);
    // $user->assignRole('cu-full');

    // $role = $user->getAllPermissions();

    // $role = Spatie\Permission\Models\Role::findOrFail(2);

    // $user = App\User::find(8);
    // $roles = $user->getRoleNames();
    // $userdata = $user->combine($roles);

    // create permission
    // Spatie\Permission\Models\Permission::create(['name' => 'index artikelPenulis']);
    // Spatie\Permission\Models\Permission::create(['name' => 'create artikelPenulis']); 
    // Spatie\Permission\Models\Permission::create(['name' => 'update artikelPenulis']);
    // Spatie\Permission\Models\Permission::create(['name' => 'destroy artikelPenulis']);

    // give permission to role
    // $role =  Spatie\Permission\Models\Role::findByName('BKCU Akses Penuh');
    // $role->givePermissionTo([
    //     'index artikelPenulis',
    //     'create artikelPenulis',
    //     'destroy artikelPenulis',
    //     'update artikelPenulis']);

    // activity log
    // $activity = Spatie\Activitylog\Models\Activity::all()->last();
    // echo $activity->changes();
});


