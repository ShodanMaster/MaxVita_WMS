<?php



namespace App\Providers;



use Illuminate\Support\ServiceProvider;

use Illuminate\View\View;



class ThemeServiceProvider extends ServiceProvider

{

    /**

     * Bootstrap the application services.

     *

     * @return void

     */

    public function boot()

    {

        view()->composer('layout.master',  'App\Http\ViewComposers\ThemeComposer@currentMenuInformation');

        view()->composer('layout.sidebar',  'App\Http\ViewComposers\ThemeComposer@compose');

        view()->composer('layout.header',  'App\Http\ViewComposers\ThemeComposer@userIcon');

    }



    /**

     * Register the application services.

     *

     * @return void

     */

    public function register()

    {

        //

    }

}

