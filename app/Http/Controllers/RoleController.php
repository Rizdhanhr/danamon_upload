<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Module;
use App\Models\Menu;
use Illuminate\Support\Facades\Gate;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use DataTables;
use DB;
use Log;
use Auth;

class RoleController extends Controller implements HasMiddleware
{
    /**
     * Display a listing of the resource.
     */

     public static function middleware(): array
     {
         return [
             new Middleware('can:VIEW-ROLE', only: ['index','getData']),
             new Middleware('can:CREATE-ROLE', only: ['create','store']),
             new Middleware('can:UPDATE-ROLE', only: ['edit','update']),
             new Middleware('can:DELETE-ROLE', only: ['destroy']),
         ];
     }

    public function index()
    {
        return view('role.index');
    }

    public function getData(Request $request){
        $role = Role::query();
        return Datatables::of($role)
        ->addColumn('action', function ($row){
            $action = '';
                if($row->id != Auth::user()->role_id && $row->is_super_admin != 1 && Gate::any(['UPDATE-ROLE','DELETE-ROLE'])){
                    $action .=
                    '<div class="dropdown">
                        <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Option
                        </button>
                        <ul class="dropdown-menu">';
                          if(Gate::allows('UPDATE-ROLE')){
                            $action .=  '<li><a class="dropdown-item" href="'.route('role.edit',$row->id).'">Edit</a></li>';
                          }
                          if(Gate::allows('DELETE-ROLE')){
                            $action .=   '<li><button type="button" onclick="deleteConfirm('.$row->id.')" class="dropdown-item" >Delete</button></li>';
                          }
                    $action .=
                        '</ul>
                    </div>';
                }
            return $action;
        })
        ->editColumn('updated_at', function ($row){
            return date('Y-m-d H:i:s',strtotime($row->updated_at));
        })

        ->rawColumns(['action','updated_at'])
        ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $module = Module::orderBy('ordering', 'ASC')->get();
        $menu = Menu::orderBy('name','ASC')->get();
        $permission = Permission::orderBy('name','ASC')->get();

        $array = [];
        foreach($menu as $m){
            foreach($module as $md){
                foreach($permission as $p){
                    if($p->menu_id == $m->id && $p->module_id == $md->id){
                        $array[$m->id][$md->id] = $p->id;
                    }
                }
            }
        }
        return view('role.create',compact('module','permission','menu','array'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255'
        ]);
      
        try{
            DB::beginTransaction();
                $role = Role::create([
                    'name' => $request->name,
                    'description' => $request->description
                ])->id;

                $access = Role::findOrFail($role);
                $access->permission()->sync($request->permission);

            DB::commit();

            return redirect()->route('role.index')->with('success', 'Data Saved');

        }catch(\Exception $e){
            DB::rollback();
            return redirect()->back()->with('error', 'Error, please contact administrator.')->withInput();
        }
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
        
        $role = Role::with('permission')->findOrFail($id);
        if($role->is_super_admin === 1 || $role->id === Auth::user()->role_id){
            abort(403);
        }

        $module = Module::orderBy('ordering', 'ASC')->get();
        $menu = Menu::orderBy('name','ASC')->get();
        $permission = Permission::orderBy('name','ASC')->get();
        $permission_selected = $role->permission->pluck('id')->toArray();

        $array = [];
        foreach($menu as $m){
            foreach($module as $md){
                foreach($permission as $p){
                    if($p->menu_id == $m->id && $p->module_id == $md->id){
                        $array[$m->id][$md->id] = $p->id;
                    }
                }
            }
        }

        return view('role.edit',compact('module','permission','menu','array','role','permission_selected'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
       
        $request->validate([
            'name' => 'required|max:255',
            'description' => 'required|max:255'
        ]);

        $role = Role::findOrFail($id);
        if($role->is_super_admin === 1 || $role->id === Auth::user()->role_id){
            abort(403);
        }
        
        try{
            DB::beginTransaction();
                $role->name = $request->name;
                $role->description = $request->description;
                $role->save();
                $role->permission()->sync($request->permission);

            DB::commit();

            return redirect()->route('role.index')->with('success', 'Data Updated');

        }catch(\Exception $e){
            DB::rollback();
            Log::info($e->getMessage());
            return redirect()->back()->with('error', 'Error, please contact administrator.')->withInput();
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = Role::findOrFail($id);
        if($role->is_super_admin === 1 || $role->id === Auth::user()->role_id){
            abort(403);
        }

        if ($role->user()->exists()) {
            return response()->json([
                'error_message' => 'Role still assigned to active users'
            ], 422);
        }

        $role->delete();

        return response()->json(['message' => 'Data Deleted'],200);
    }
}
