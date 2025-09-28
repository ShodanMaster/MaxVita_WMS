<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Sidebar\Menu;
use App\Models\Sidebar\Submenu;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use RealRashid\SweetAlert\Facades\Alert;

class PermissionController extends Controller
{
    public function index(){
        $authUserId = auth()->id();

        $users = $authUserId == 1
            ? User::orderBy('name')->get()
            : User::where('id', '!=', 1)->orderBy('name')->select('id', 'name')->get();

        return view('utility.permission', compact('users'));
    }

    public function getPermissionMenu(Request $request){
        try{
            $data = array();
            $menu = new Menu;
            $menu = $menu->select('id', 'title', 'link')->get()->sortBy('id');
            $submenu = new Submenu;
            $submenu = $submenu->select('id', 'title', 'menu_id')->get()->sortBy('id');
            $permission = new Permission;
            $permission = $permission->select('submenu_id')->where('user_id', $request->get('uid'))->get()->sortBy('submenu_id');

            $data[] = [
                'menu' => $menu,
                'submenu' => $submenu,
                'permission' => $permission
            ];

            return response()->json($data);
        } catch(Exception $e){

        }
    }

    public function store(Request $request){
        try{
            $i = Submenu::count();
            $per = Submenu::select('id')->where('title', 'User Permission')->get();
            $permission = Permission::where('user_id', $request->user_id)
                ->whereNotIn('submenu_id', $per);
            $permission->forceDelete();
            $pr = Permission::select('id')->orderBy('id', 'desc')->first();
            $pr = $pr->id;

            $data = $request->all();
            $k = 0;

            foreach ($data as $key => $value) {
                if ($k >= 2 && $key != "cbox" && $key != "2") {

                    if ($value == "1") {
                        $pr = $pr + 1;
                        Permission::insert([
                            'id' => $pr,
                            'user_id' => $request->user_id,
                            'submenu_id' => $key,
                        ]);
                    }
                }
                $k++;
            }

            Alert::toast('Permission(s) assigned to user.', 'success')->autoClose(3000);
            return redirect()->back();
        } catch (Exception $e) {
            dd($e);
            Log::error('Permission Assign Error: ' . $e->getMessage());
            Alert::toast('An error occurred while asigning permision to user.', 'error')->autoClose(3000);
            return redirect()->route('vendr.index');
        }
    }
}

