<?php

namespace App\Http\Controllers\Master;

use App\Enums\PermissionLevel;
use App\Enums\UserType;
use App\Exports\Master\UserExport;
use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index(){
        return view('master.user.index');
    }

    public function getUsers(Request $request){
        if ($request->ajax()) {
            $users = User::with('location', 'branch')
                ->select(['id', 'name', 'branch_id', 'location_id', 'name', 'username', 'email', 'user_type', 'permission_level']);

            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('branch_name', function ($row) {
                    return $row->branch->name ?? '-';
                })
                ->addColumn('location_name', function ($row) {
                    return $row->location->name ?? '-';
                })->addColumn('user_type', function($row){
                    return UserType::from($row->user_type)->label();
                })->addColumn('permission_level', function($row){
                    return PermissionLevel::from($row->permission_level)->label();
                })
                ->addColumn('action', function ($row) {
                    $editUrl = route('user.edit', $row->id);
                    $deleteUrl = route('user.destroy', $row->id);

                    $btn = '
                        <td width="150px">
                            <a href="' . $editUrl . '" data-toggle="tooltip" data-placement="bottom" title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit text-primary">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                </svg>
                            </a>
                            <form method="POST" action="' . $deleteUrl . '" onsubmit="return confirm(\'Are you sure, You want to delete this User?\')" style="display:inline;">
                                ' . csrf_field() . '
                                ' . method_field('DELETE') . '
                                <button type="submit" class="btn" data-toggle="tooltip" title="Delete">
                                    <span class="fa fa-trash text-danger"></span>
                                </button>
                            </form>
                        </td>';

                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function create(){
        $locations = Location::all();
        return view('master.user.create', compact('locations'));
    }

    public function store(Request $request){
        // dd($request->all());
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'user_type' => 'required|integer',
            'permission_level' => 'required|in:1,2,3',
            'location_id' => 'nullable|exists:locations,id',
        ]);

        try{

            $branchId = Location::find($request->location_id)->branch_id ?? null;

            User::create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'user_type' => $request->user_type,
                'permission_level' => $request->permission_level,
                'location_id' => $request->location_id,
                'branch_id' => $branchId,
            ]);

            Alert::toast('User added successfully!', 'success')->autoClose(3000);
            return redirect()->route('user.index');

        }catch(\Exception $e){
            dd($e);
            Log::error('User Store Error: ' . $e->getMessage());

            Alert::toast('An error occurred while adding the user.', 'error')->autoClose(3000);
            return redirect()->route('user.index');
        }

    }

    public function edit(User $user){
        try{
            $locations = Location::all();
            return view('master.user.create', compact('user', 'locations'));
        }catch(\Exception $e){
            dd($e);
            Log::error('User Edit Error: ' . $e->getMessage());

            Alert::toast('An error occurred while fetching the user.', 'error')->autoClose(3000);
            return redirect()->route('user.index');
        }
    }

    public function update(Request $request, User $user){
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,'.$user->id,
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'user_type' => 'required|integer',
            'permission_level' => 'required|in:1,2,3',
            'location_id' => 'nullable|exists:locations,id',
        ]);

        try{

            $branchId = Location::find($request->location_id)->branch_id ?? null;

            $user->name = $request->name;
            $user->username = $request->username;
            $user->email = $request->email;
            if($request->password){
                $user->password = bcrypt($request->password);
            }
            $user->user_type = $request->user_type;
            $user->permission_level = $request->permission_level;
            $user->location_id = $request->location_id;
            $user->branch_id = $branchId;
            $user->save();

            Alert::toast('User updated successfully!', 'success')->autoClose(3000);
            return redirect()->route('user.index');

        }catch(\Exception $e){
            Log::error('User Update Error: ' . $e->getMessage());

            Alert::toast('An error occurred while updating the user.', 'error')->autoClose(3000);
            return redirect()->route('user.index');
        }
    }

    public function destroy(User $user){
        try{
            $user->delete();

            Alert::toast('User deleted successfully!', 'success')->autoClose(3000);
            return redirect()->route('user.index');

        }catch(\Exception $e){
            Log::error('User Delete Error: ' . $e->getMessage());

            Alert::toast('An error occurred while deleting the user.', 'error')->autoClose(3000);
            return redirect()->route('user.index');
        }
    }

    public function userExcelExport()
    {
        try {
            return Excel::download(new UserExport, 'users.xlsx');
        } catch (\Exception $e) {
            Log::error('User Excel Export Error: ' . $e->getMessage());
            Alert::toast('An error occurred while exporting users to Excel.', 'error')->autoClose(3000);
            return redirect()->route('user.index');
        }
    }
}
