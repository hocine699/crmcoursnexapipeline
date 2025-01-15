<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $permissions = Permission::get();

        return view('permission.index', compact('permissions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        // $roles = Role::where('created_by', \Auth::user()->creatorId())->get();
        $roles = Role::get();

        return view('permission.create')->with('roles', $roles);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate(
            $request, [
                        'name' => 'required|max:40',
                    ]
        );

        $name             = $request['name'];
        $permission       = new Permission();
        $permission->name = $name;

        $roles = $request['roles'];

        $permission->save();

        if(!empty($request['roles']))
        {
            foreach($roles as $role)
            {
                $r          = Role::where('id', '=', $role)->firstOrFail();
                $permission = Permission::where('name', '=', $name)->first();
                $r->givePermissionTo($permission);
            }
        }

        return redirect()->route('permission.index')->with('success', 'Permission ' . $permission->name . ' Added!');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $permission = Permission::find($id);

        return view('permission.edit', compact('permission'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,Permission $permission)
    {
        $permission = Permission::findOrFail($permission['id']);
        $this->validate(
            $request, [
                        'name' => 'required|max:40',
                    ]
        );
        $input = $request->all();
        $permission->fill($input)->save();

        return redirect()->route('permission.index')->with(
            'success', 'Permission ' . $permission->name . ' Updated!'
        );

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();

        return redirect()->route('permission.index')->with( 'success', 'Permission Successfully Deleted.' );
    }
}
