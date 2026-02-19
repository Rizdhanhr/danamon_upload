<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use App\Models\User;
use App\Models\Role;
use Auth;
use Hash;
use Illuminate\Support\Facades\Gate;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class UserController extends Controller implements HasMiddleware
{
    /**
     * Display a listing of the resource.
     */

    public static function middleware(): array
    {
         return [
             new Middleware('can:VIEW-USER', only: ['index','getData']),
             new Middleware('can:CREATE-USER', only: ['create','store']),
             new Middleware('can:UPDATE-USER', only: ['edit','update','updatePassword']),
             new Middleware('can:DELETE-USER', only: ['destroy']),
         ];
    }


    public function index(){
        return view('user.index');
    }

    public function getData(Request $request){
        $user = User::with('role')
        ->where('id','!=',Auth::user()->id);

        return Datatables::of($user)
        ->addColumn('action', function ($row){
            $action = '';
                if($row->role->is_super_admin != 1 && Gate::any(['UPDATE-USER','DELETE-USER'])){
                    $action .=
                    '<div class="dropdown">
                        <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Option
                        </button>
                        <ul class="dropdown-menu">';
                            if(Gate::allows('UPDATE-USER')){
                                $action .= '<li><a class="dropdown-item" href="'.route('user.edit',$row->id).'">Edit</a></li>';
                                $action .= '<li><button type="button" onclick="changePassword(\''.$row->email.'\',\''.$row->id.'\')" class="dropdown-item" >Change Password</button></li>';
                            }
                            if(Gate::allows('DELETE-USER')){
                                $action .= '<li><button type="button" onclick="deleteConfirm('.$row->id.')" class="dropdown-item" >Delete</button></li>';
                            }

                    $action .=
                        '</ul>
                    </div>';
                }
            return $action;
        })
        ->editColumn('updated_at', function ($row){
            return date('Y-m-d',strtotime($row->updated_at));
        })
        ->addColumn('role', function ($row){
            return $row->role->name;
        })
        ->filterColumn('role', function($query, $keyword) {
            $query->whereHas('role', function($query) use ($keyword) {
                $query->where('role.name', 'like', "%$keyword%");
            });
        })
        ->rawColumns(['action','updated_at','role'])
        ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $role = Role::select('id','name')->orderBy('name','ASC')->get();
        return view('user.create',compact('role'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:40',
            'email' => 'required|email|unique:users,email',
            'role' => 'required',
            'password' => 'min:6|max:12|required_with:password_confirmation|same:password_confirmation',
            'password_confirmation' => 'min:6'
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role_id = $request->role;
        $user->save();

        return redirect()->route('user.index')->with('success', 'Data Tersimpan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::with('role')->findOrFail($id);
        if($user->role->is_super_admin === 1 || $user->id === Auth::user()->id){
            abort(403);
        } 
        
        $role = Role::select('id','name')->orderBy('name','ASC')->get();
        return view('user.edit',compact('user','role'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|max:40',
            'email' => 'required|email|unique:users,email,'. $id,
            'role' => 'required',
        ]);

        $user = User::with('role')->findOrFail($id);
        if($user->role->is_super_admin === 1 || $user->id === Auth::user()->id){
            abort(403);
        } 

        $user->name = $request->name;
        $user->role_id = $request->role;
        $user->email = $request->email;
        $user->save();

        return redirect()->route('user.index')->with('success', 'Data Saved');
    }

    public function updatePassword(Request $request){
        $request->validate([
            'password' => 'min:6|max:12|required_with:password_confirmation|same:password_confirmation',
            'password_confirmation' => 'min:6'
        ]);

        $user = User::findOrFail($request->id);
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json(['success' => 'Data Tersimpan']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::with('role')->findOrFail($id);
        if($user->role->is_super_admin === 1 || $user->id === Auth::user()->id){
            abort(403);
        } 
        $user->delete();
        return response()->json(['success' => 'Data Terhapus']);
    }
}
