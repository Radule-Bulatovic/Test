<?php

namespace App\Http\Controllers;

use App\Role;
use Illuminate\Http\Request;

class RolesController extends Controller
{
    /**
     * Function for displaying View Roles Admin Page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
	public function index() {
		return view('admin.roles.index')->with('roles', Role::orderBy('id', 'ASC')->get());
	}


    /**
     * Function for displaying View for Role Crete
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
	public function create() {
		return view('admin.roles.create');
	}


    /**
     * Function for creating new Role
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
	public function store(Request $request) {
		$data = $request->validate([
			'name' => 'required|min:2|unique:roles'
		]);
		Role::create($data);
		return redirect(route('roles.index'))->with('success', 'Role created successfully.');
	}


    /**
     * Function for displaying View for Role Edit
     * @param Role $role
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
	public function edit(Role $role) {
		return view('admin.roles.edit', compact('role'));
	}


    /**
     * Function for Updating a Role
     * @param Request $request
     * @param Role $role
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
	public function update(Request $request, Role $role) {
		$data = $request->validate([
			'name' => 'required|min:2'
		]);

		$existingRole = Role::where('name', '=', $request->name)->where('id', '!=', $request->id)->first();

		if($existingRole){
            return back()->withErrors(['name' => 'Role with the same name exists.']);
        }else{
            $role->update($data);
            return redirect(route('roles.index'))->with('success', 'Role updated successfully.');
        }
	}


    /**
     * Empty function
     */
	public function show() {
		
	}


    /**
     * Function for deleting a ROle
     * @param Role $role
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Exception
     */
	public function destroy(Role $role) {
		if($role->users->count() > 0) {
			return back()->withErrors(['aborted' => 'Aborted, this role has users.']);
		}
		$role->delete();
		return redirect(route('roles.index'))->with('success', 'Role deleted successfully.');
	}


    /**
     * Function for displaying View with Soft Deleted Roles
     * @return mixed
     */
	public function deleted() {
		return view('admin.roles.deleted')->withRoles(Role::onlyTrashed()->get());
	}


    /**
     * Function for restoring destroyed Role
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
	public function restore($id) {
	    if($id != ''){
            Role::where('id', $id)->restore();
            return back()->with('success', 'Role restored successfully');
        }else{
            return back()->withErrors(['aborted' => 'Aborted, ID missing.']);
        }
	}
}
