<?php

namespace App\Http\Middleware;

use App\Models\Utility;
use Closure;
use App\Models\Payment;
use App\Models\LandingPageSections;
use Illuminate\Http\Request;

class XSS
{
    use \RachidLaasri\LaravelInstaller\Helpers\MigrationsHelper;

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(\Auth::check())
        {
            \App::setLocale(\Auth::user()->lang);
            if(\Auth::user()->type == 'super admin')
            {
                if(\Schema::hasTable('messages'))
                {
                    if(\Schema::hasColumn('messages', 'type') == false)
                    {
                        \Schema::drop('messages');
                        \DB::table('migrations')->where('migration', 'like', '%messages%')->delete();
                    }
                }

                $migrations             = $this->getMigrations();
                $dbMigrations           = $this->getExecutedMigrations();
                $Modulemigrations = glob(base_path().'/Modules/LandingPage/Database'.DIRECTORY_SEPARATOR.'Migrations'.DIRECTORY_SEPARATOR.'*.php');
                $numberOfUpdatesPending = (count($migrations) + count($Modulemigrations)) - count($dbMigrations);
                if($numberOfUpdatesPending > 0)
                {
                    // run code like seeder only when new migration
                    Utility::addNewData();
                    Utility::updateUserDefaultEmailTempData();
                    Utility::defaultEmail();
                    return redirect()->route('LaravelUpdater::welcome');
                }

            }
            else
            {
                if(\Auth::user()->type == 'owner')
                {
                    $payment_permission = Payment::all()->count();

                    if($payment_permission == 0)
                    {
                        Utility::addNewData();
                    }
                }
            }

        }

        if(!file_exists(storage_path() . "/installed"))
        {
            header('location:install');
            die;

        }
        $landingdata = LandingPageSections::all()->count();

        if($landingdata == 0)
        {
            //Utility::add_landing_page_data();
        }

        $input = $request->all();
        // array_walk_recursive($input, function (&$input){
        //     $input = strip_tags($input);
        // });
        $request->merge($input);

        return $next($request);
    }
}
