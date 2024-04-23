# laravel-website-template
This template includes the authentication using jetstream for the auth and functionality and tailwind for the front end. Use this to quickly start a website/application. The only thing you will need to change is the directory level (potentially) and the env file and permissions. These are listed below.

This file also includes some instructinos i used to make the project


************** permission issues with npm and serve **************************
sudo chmod -R 775 storage
sudo chown -R williamlattus:staff storage

********************** commands to give permission to computer to run on localhost (mac) ************
sudo chown -R daemon:daemon storage
sudo chown -R daemon:daemon bootstrap/cache
php artisan config:clear

********************** database ***************************************************************
create a database and name it

NOTE: make sure this is here:
# DB_CONNECTION=sqlite
DB_CONNECTION=mysql

and make sure: SESSION_DRIVER=file

NOTE: name this properly: DB_DATABASE= SE-Find-Work

***************************************************************************************************************
//for google authentication change these in env:
//here is where you get this: https://console.cloud.google.com/apis/credentials?project=hoast-hive&supportedpurview=project
GOOGLE_CLIENT_ID= ""
GOOGLE_CLIENT_SECRET= ""

//to make sure the google stuff is correct each OAuth 2.0 should look like this:
---------------------------------------------------------------------------------------------------------------
0Auth section:
App information
This shows in the consent screen, and helps end users know who you are and contact you

App name
Host Hive
The name of the app asking for consent
User support email
wslattus@gmail.com
For users to contact you with questions about their consent. Learn more 
App logo
This is your logo. It helps people recognize your app and is displayed on the OAuth consent screen.
After you upload a logo, you will need to submit your app for verification unless the app is configured for internal use only or has a publishing status of "Testing". Learn more 

Logo file to upload
Upload an image, not larger than 1MB on the consent screen that will help users recognize your app. Allowed image formats are JPG, PNG, and BMP. Logos should be square and 120px by 120px for the best results.
App domain
To protect you and your users, Google only allows apps using OAuth to use Authorized Domains. The following information will be shown to your users on the consent screen.

Application home page
http://localhost/HostHive/public/dashboard
Provide users a link to your home page
Application privacy policy link
http://localhost/HostHive/public/
Provide users a link to your public privacy policy
Application terms of service link
http://localhost/HostHive/public/
Provide users a link to your public terms of service

Authorized domains
When a domain is used on the consent screen or in an OAuth client’s configuration, it must be pre-registered here. If your app needs to go through verification, please go to the Google Search Console to check if your domains are authorized. Learn more  about the authorized domain limit.
Authorized domain 1 
localhost.com


Credentials section:

Name
Host Hive
The name of your OAuth 2.0 client. This name is only used to identify the client in the console and will not be shown to end users.
The domains of the URIs you add below will be automatically added to your OAuth consent screen as authorized domains .

Authorized JavaScript origins
For use with requests from a browser
URIs 1 
http://localhost

Authorized redirect URIs
For use with requests from a web server
URIs 1 
http://127.0.0.1:8000/auth/google/callback


------------------------------------------------------------------------------------------------------

************** enable user authentication breeze (use jetstream for more features)**************
https://www.youtube.com/watch?v=f1hCx-NXbek
sudo chmod -R 777 storage
sudo chmod -R 777 bootstrap/cache
composer require laravel/breeze --dev
php artisan breeze:install

- breeze
- yes darkmode
- artesion support
//dont need below
php artisan breeze
php artisan migrate
php artisan optimize
composer dump-autoload

*******8 add google using socialite ***********8
https://youtu.be/ZyUNGtAJ4ck

https://laravel.com/docs/9.x/socialite

//site
https://console.cloud.google.com/apis/credentials?project=hoast-hive&supportedpurview=project

run:
sudo chmod -R 777 storage
sudo chmod -R 777 bootstrap/cache
composer require laravel/socialite

//github example for configuration in config/services.php
'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => '/auth/google/callback',
    ]

//go to .env file now
//scroll to bottom and add
GOOGLE_CLIENT_ID= 
GOOGLE_CLIENT_SECRET= 

//go to google cloud
https://youtu.be/j-lVevL_72E

https://console.cloud.google.com/apis/credentials?highlightClient=785105675608-445er74j6r2p12djpi9mdp02qoi1s79i.apps.googleusercontent.com&project=hoast-hive&supportedpurview=project

NOTE TO TEST GOOGLE YOU HAVE TO USE NPM WITH THIS IN THE services.php PATH: 'http://localhost/auth/google/callback'

and sign up using http://localhost for the first one and http://localhost/auth/google/callback for the next

copy client id and client secret and past into GOOGLE_CLIENT_ID= GOOGLE_CLIENT_SECRET= 

//next routing
//pase in this into web.php
//this is the example this will be changed below
use Laravel\Socialite\Facades\Socialite;
 
Route::get('/auth/redirect', function () {
    return Socialite::driver('github')->redirect();
});
 
Route::get('/auth/callback', function () {
    $user = Socialite::driver('github')->user();
 
    // $user->token
});


//now open terminal and type this to put it in auth too
php artisan make:controller Auth/ProviderController

//now change that web.php up above to this and add this:
use App\Http\Controllers\ProviderController;
//add this:
Route::get('/auth/{provider}/redirect', [ProviderController::class, 'redirect']);
Route::get('/auth/{provider}/callback', [ProviderController::class, 'callback']);


//now open ProviderController under app/http/auth
//now add this to ProviderController and import this


use Laravel\Socialite\Facades\Socialite;

class ProviderController extends Controller
{
    public function redirect($provider) {
        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider) {
        $user = Socialite::driver($provider)->user();
        // dd($user);//read it back

        try {
            $google_user = Socialite::driver($provider)->user();
            //check if it exsits in db
            $user = User::where('google_id', $google_user->getID())->first();
            
            if(!$user) {//if user doesnt exist in db
                //call usermodel
                $new_user = User::create([
                'name' => $google_user->getName(),
                'email' => $google_user->getEmail(),
                'google_id' => $google_user->getId()
                ]);

                //login the user
                Auth::login($new_user);
                //redirect user
                return redirect()->intendent('dashboard');
            } else {//if user already exists
                Auth::login('$user');
                //now return
                return redirect()->intendent('dashboard');
            }

        } catch (\Throwable $th) {
            dd('Something went wrong! ', $th->getMessage());
        }
    }
}


//next go here:
https://flowbite.com/docs/components/buttons/#social-buttons
//look at social buttons html
//now copy which one you want i.e google:

</button>
<button type="button" class="text-white bg-[#050708] hover:bg-[#050708]/90 focus:ring-4 focus:outline-none focus:ring-[#050708]/50 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:focus:ring-[#050708]/50 dark:hover:bg-[#050708]/30 me-2 mb-2">
<svg class="w-5 h-5 me-2 -ms-1" aria-hidden="true" focusable="false" data-prefix="fab" data-icon="apple" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path fill="currentColor" d="M318.7 268.7c-.2-36.7 16.4-64.4 50-84.8-18.8-26.9-47.2-41.7-84.7-44.6-35.5-2.8-74.3 20.7-88.5 20.7-15 0-49.4-19.7-76.4-19.7C63.3 141.2 4 184.8 4 273.5q0 39.3 14.4 81.2c12.8 36.7 59 126.7 107.2 125.2 25.2-.6 43-17.9 75.8-17.9 31.8 0 48.3 17.9 76.4 17.9 48.6-.7 90.4-82.5 102.6-119.3-65.2-30.7-61.7-90-61.7-91.9zm-56.6-164.2c27.3-32.4 24.8-61.9 24-72.5-24.1 1.4-52 16.4-67.9 34.9-17.5 19.8-27.8 44.3-25.6 71.9 26.1 2 49.9-11.4 69.5-34.3z"></path></svg>
Sign in with Apple
</button>


//navigate to resources/view/auth/login.blade.php
//under first <x-auth-session-status class="mb-4" :status="session('status')" /> add this:


<div class="w-full flex justify-center mx-2">
        
    </div>

//now add the google button in this

<div class="w-full flex justify-center mx-2">
        <button type="button"
            class="text-white bg-[#4285F4] hover:bg-[#4285F4]/90 focus:ring-4 focus:outline-none focus:ring-[#4285F4]/50 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:focus:ring-[#4285F4]/55 me-2 mb-2">
            <svg class="w-4 h-4 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                viewBox="0 0 18 19">
                <path fill-rule="evenodd"
                    d="M8.842 18.083a8.8 8.8 0 0 1-8.65-8.948 8.841 8.841 0 0 1 8.8-8.652h.153a8.464 8.464 0 0 1 5.7 2.257l-2.193 2.038A5.27 5.27 0 0 0 9.09 3.4a5.882 5.882 0 0 0-.2 11.76h.124a5.091 5.091 0 0 0 5.248-4.057L14.3 11H9V8h8.34c.066.543.095 1.09.088 1.636-.086 5.053-3.463 8.449-8.4 8.449l-.186-.002Z"
                    clip-rule="evenodd" />
            </svg>
            Sign in with Google
        </button>
    </div>


//now change "button" to "a" and remove type button to:
//<a href="/auth/google/redirect" ending like this:

<div class="w-full flex justify-center mx-2">
        <a href="/auth/google/redirect"
            class="text-white bg-[#4285F4] hover:bg-[#4285F4]/90 focus:ring-4 focus:outline-none focus:ring-[#4285F4]/50 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:focus:ring-[#4285F4]/55 me-2 mb-2">
            <svg class="w-4 h-4 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                viewBox="0 0 18 19">
                <path fill-rule="evenodd"
                    d="M8.842 18.083a8.8 8.8 0 0 1-8.65-8.948 8.841 8.841 0 0 1 8.8-8.652h.153a8.464 8.464 0 0 1 5.7 2.257l-2.193 2.038A5.27 5.27 0 0 0 9.09 3.4a5.882 5.882 0 0 0-.2 11.76h.124a5.091 5.091 0 0 0 5.248-4.057L14.3 11H9V8h8.34c.066.543.095 1.09.088 1.636-.086 5.053-3.463 8.449-8.4 8.449l-.186-.002Z"
                    clip-rule="evenodd" />
            </svg>
            Sign in with Google
        </a>
    </div>


//make sure that it goes into it first... at the end of the google sign in process it should throw an error
go to database/migrations/0001_01_01_000000_create_users_table.php
//change the password line and add this one below

	$table->string('password')->nullable();
        $table->string('google_id')->nullable();

//stop your npm server and put this in there (the extra terminal where migrations is
php artisan migrate:fresh



******************************** to run on local terminal *******************************
npm install
npm run dev
//open new terminal
php artisan serve

****************************** to run on xampp *****************************************8
in the env file comment out this

# REDIS_CLIENT=phpredis


****************************** how to make the reset email link for laravel *************
https://youtu.be/ZyUNGtAJ4ck

**************** install jetstream (must be installed early on)*************************
//dont use 775 it doesn't work
sudo chmod -R 777 storage
sudo chmod -R 777 bootstrap/cache
composer require laravel/jetstream

php artisan jetstream:install livewire
php artisan migrate

php artisan vendor:publish --tag=jetstream-views
php artisan jetstream:install livewire --dark

npm install
npm run build
php artisan migrate

********************** profile photos ************************************************
https://youtu.be/K2xJrZlPBsk
Enabling Profile Photos ​
If you wish to allow users to upload custom profile photos, you must enable the feature in your application's config/jetstream.php configuration file. To enable the feature, simply uncomment the corresponding feature entry from the features configuration item within this file:

PHP
use Laravel\Jetstream\Features;

'features' => [
    Features::profilePhotos(),
    Features::api(),
    Features::teams(),
],
After enabling the profile photo feature, you should execute the storage:link Artisan command. This command will create a symbolic link in your application's public directory that will allow your user's images to be served by your application. For information regarding this command, please consult the Laravel filesystem documentation:

BASH
php artisan storage:link

(if photo not working then remove the '/' from the url in env

******************************** social for jetstream (install early on) **************
https://youtu.be/d_JKbOAlxN8
https://youtu.be/XFmJfTzGqzY


(need social lite done a similar way) (can use social stream
composer require laravel/socialite
