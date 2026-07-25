<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use PhpParser\Node\Expr\Array_;

class AdminController extends Controller
{
    public function loginAdmin(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (Auth::guard('admin')->attempt($request->only('email', 'password'))) {
            return redirect()->intended('/admin/home');
        }
        return redirect()->back()->with('error', 'Email or password is incorrect');
    }

    public function adminEditInfo(Request $request)
    {
        $adminInfo = Admin::find($request->id);
        $role = Role::where('status', 1)->get();
        return view('adminPanel.role.edit_admin')->with(compact('adminInfo', 'role'))->render();
    }

    public function adminUpdate(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'admin_id' => 'required|exists:admins,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'role_id' => 'required|integer',
            'password' => 'nullable|min:6', // Make password optional
        ]);

        $admin = Admin::find($request->admin_id);

        if ($admin) {
            // Update the admin record
            $admin->name = $request->name;
            $admin->email = $request->email;
            $admin->admin_type = $request->role_id;

            // Only update the password if it is provided
            if ($request->filled('password')) {
                $admin->password = Hash::make($request->password);
            }

            // Save changes
            $admin->save();

            return redirect()->back()->with('success', 'Successfully Updated User');
        } else {
            return redirect()->back()->with('error', 'User not found');
        }
    }

    public function loginView()
    {
        return view('adminPanel.login');
    }

    public function logOutAdmin()
    {
        Auth::guard('admin')->logout();
        return redirect()->intended('/admin');
    }
    public function adminDelete(Request $request)
    {
        Admin::where('id', $request->id)->delete();
        return redirect()->back()->with('success', 'Successfully admin deleted');
    }

    public function adminRole()
    {
        $common_data = new Array_();
        $common_data->title = 'Role Create';
        return view('adminPanel.role.create_role')->with(compact('common_data'));
    }

    public function adminStore(Request $request)
    {
        $info = Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'admin_type' => $request->role_id,
        ]);
        if ($info) {
            return redirect()->back()->with('success', 'Successfully Created User');
        } else {
            return redirect()->back()->with('error', 'Internal Error');
        }
    }

    public function adminCreate()
    {
        $common_data = new Array_();
        $common_data->title = 'User Create';
        $role = Role::where('status', 1)->get();
        $admin = Admin::get();
        return view('adminPanel.role.create_admin')->with(compact('common_data', 'role', 'admin'));
    }

    public function adminRoleStore(Request $request)
    {
        $role = new Role();
        $role->name = $request->role_name;
        $role->access_role_list = implode(',', $request->role_id);
        $role->save();
        return redirect()->back()->with('success', 'Successfully Created Role');
    }
}
