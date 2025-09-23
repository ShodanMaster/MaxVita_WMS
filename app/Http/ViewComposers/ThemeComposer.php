<?php

namespace App\Http\ViewComposers;

use App\Models\Permission;
//use App\Models\Setting;
//use App\Models\SubscriptionMenu;
use Illuminate\Contracts\View\View;
use App\Models\Sidebar\Menu;
use App\Models\Sidebar\Submenu;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Sidebar\Configuration;

use Auth;


class ThemeComposer
{


    public function __construct() {}

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $user_id = auth()->id();
        $segment = Request::segment(1);
        if (!$segment) $segment = "/";
        $user = \auth()->user();
        $mainmenu = (new Menu)->where('link', $segment)->first();
        $submenu = (new Submenu)->where('link', $segment)->first();

        //         $favouritetable=(new Favourite)->getTable();
        //        $favouritestable=(new Favourite)->getTable();
        $permissiontable = (new Permission)->getTable();

        //akhilesh on 2020-10-06
        //$setting=(new Setting)->getTable();
        //$subscription=(new SubscriptionMenu)->getTable();
        //akhilesh on 2020-10-06

        //$favourite=DB::table($favouritetable)
        //            ->join($permissiontable,"$permissiontable.submenu_id","$favouritetable.submenu_id")
        //            ->where("$favouritetable.user_id",$user->id)->pluck("$favouritetable.submenu_id");
        //
        //        $favourites=DB::table($favouritestable)
        //            ->join($permissiontable,"$permissiontable.submenu_id","$favouritetable.submenu_id")
        //            ->where("$favouritetable.user_id",$user->id)->get();

        $segment1 = (isset($submenu->menu_id)) ? $submenu->menu_id : (isset($mainmenu->id) ? $mainmenu->id : '');
        $segment2 = (isset($submenu->id)) ? $submenu->id : '';

        //By Sreenesh
        $menu = Menu::all()->sortBy('listing_order');
        $submenu = (new Submenu)->getTable();

        $branch_id = Auth::user()->branch_id ?? 0;

        $config = Configuration::where('configurations.branch_id', '=', "{$branch_id}")->first();

        $menulist = [];
        foreach ($menu as $m) {
            $data = DB::table($permissiontable)
                ->join($submenu, "$submenu.id", "=", "$permissiontable.submenu_id")
                //akhilesh on 2020-10-06
                //->join($subscription,"$subscription.submenu_id","=","$submenu.id")
                //->join($setting,"$setting.sub_type","=","$subscription.subscription_id")
                //akhilesh on 2020-10-06
                //->where("$permissiontable.user_id",$user->admin)
                ->where("$submenu.menu_id", $m->id);
            $data = $data->select("$submenu.id", "$submenu.title", "$submenu.link")->where("$permissiontable.user_id", $user_id)->orderBy("$submenu.listing_order", 'asc')->get();

            if (count($data) >= 1 || $m->id == 1) {
                $menulist[] = array(
                    'id' => $m->id,
                    'title' => $m->title,
                    'link' => $m->link,
                    'icon' => $m->icon,
                    'submenus' => $data
                );
            }
        }
        //BySreenesh
        //		die("$segment1,$segment2");
        $view->withMenus($menulist)->withActivemenu([$segment1, $segment2])->withAnotherArray($config);
    }

    public function userIcon(View $view)
    {

        $user = \auth()->user();

        $text = " ";
        if ($user) {
            $name = $user->name;
            if (strlen($name) == 1) {
                $text = $name . $name;
            } else if (strlen($name) == 2) {
                $text = $name;
            } else {
                $keywords = preg_split("/[\s,\.\_\-]+/", $name);
                $keywords = array_filter($keywords);
                if (sizeof($keywords) > 1) {
                    $text = $keywords[0][0] . $keywords[1][0];
                } else {
                    $text = $keywords[0][0] . $keywords[0][1];
                }
            }
        } else {
            $text = '  ';
        }
        $view->withUsericon('data:image/png;base64,' . $this->base64ImageFromLetters(strtoupper($text)));
    }

    private function base64ImageFromLetters($text = '  ')
    {
        $im = imagecreatetruecolor(75, 75);

        $background = imagecolorallocate($im, 200, 200, 200);
        $text_colour = imagecolorallocate($im, 34, 45, 50);

        imagefilledrectangle($im, 0, 0, 75, 75, $background);

        $font = public_path('font/Roboto-Black.ttf');

        imagettftext($im, 36, 0, 8, 55, $text_colour, $font, $text);

        ob_start();
        imagepng($im);
        $final_image = ob_get_contents();
        ob_end_clean();
        imagedestroy($im);

        $base64Img = base64_encode($final_image);

        return $base64Img;
    }

    public function currentMenuInformation(View $view)
    {

        $segment1 = \Request::segment(1);
        $segment2 = \Request::segment(2);
        $segment3 = \Request::segment(3);

        $pageTitle = '';
        $pageSubtitle = '';

        $submenu = Submenu::where('link', $segment1)->first();
        $mainmenu = $submenu ? $submenu->menu : (Menu::where('link', $segment1)->first());

        $pageTitle = (isset($submenu->menu_id)) ? $submenu->title : (isset($mainmenu->id) ? $mainmenu->title : '');

        if (isset($segment3)) {
            if ($segment2 == 'edit') $pageSubtitle = 'Edit';
        } else if (isset($segment2)) {
            if ($segment2 == 'create') $pageSubtitle = 'Add new';
            else if ($segment2 == 'upload') $pageSubtitle = 'Upload';
        } else {
            $pageSubtitle = '';
        }
        $darkmode = Auth()->user()->darkmode ?? 0;

        $view->with(['activeMenu' => $mainmenu, 'activeSubmenu' => $submenu, 'pageTitle' => $pageTitle, 'pageSubtitle' => $pageSubtitle, 'darkmode' => $darkmode]);
    }
}
