<?php

namespace App\Http\Controllers\Api;

use App\Role;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CMRolesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Role::orderBy('id', 'ASC')->get();
        return response()->json([$data], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|min:2|unique:roles'
        ]);
        Role::create($data);
        return response()->json([$data], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required|min:2'
        ]);

        $existingRole = Role::where('name', '=', $request->name)->where('id', '!=', $request->id)->first();

        if ($existingRole) {
            $data = ['error' => 'Role with the same name exists.'];
            return response()->json($data, 409);
        } else {
            $role = Role::where('id', $id)->get();
            $role->update($data);
            $data = ['success', 'Role updated successfully.'];
            return response()->json([$data], 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = Role::where('id', $id)->get();
        if ($role->users->count() > 0) {
            $data = ['aborted' => 'Aborted, this role has users.'];
            return response()->json($data, 409);
        }
        $role->delete();
        $data = ['success', 'Role deleted successfully.'];
        return response()->json([$data], 200);
    }

    public function deleted() {
        $data = Role::onlyTrashed()->get();
        return response()->json([$data], 200);
	}


    /**
     * Function for restoring destroyed Role
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
	public function restore($id) {
	    if($id != ''){
            Role::where('id', $id)->restore();
            $data = ['success' => 'Role restored successfully'];
            return response()->json([$data], 200);
        }else{
            $data = ['aborted' => 'Aborted, ID missing.'];
            return response()->json($data, 409);
        }
	}
}
