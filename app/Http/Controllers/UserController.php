<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function index()
    {
        $users = User::orderBy('lastname')->get();
        return view('admin.users.usersManagement', ['users' => $users]);
    }

    public function create()
    {
        return view('admin.users.addUser');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'firstname' => ['required', 'string', 'max:255'],
            'lastname'  => ['required', 'string', 'max:255'],
            'suffix'    => ['nullable', 'string', 'max:20'],
            'username'  => ['required', 'string', 'max:255', Rule::unique('users', 'username')],
            'email'     => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password'  => ['required', 'confirmed', 'min:8'],
            'role'      => ['required', 'in:admin,manager,staff'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        User::create([
            'firstname' => $data['firstname'],
            'lastname'  => $data['lastname'],
            'suffix'    => $data['suffix'] ?? null,
            'username'  => $data['username'],
            'email'     => $data['email'],
            'password'  => Hash::make($data['password']),
            'role'      => $data['role'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        return view('admin.users.editUser', ['user' => $user]);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'firstname' => ['required', 'string', 'max:255'],
            'lastname'  => ['required', 'string', 'max:255'],
            'suffix'    => ['nullable', 'string', 'max:20'],
            'username'  => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($user->id)],
            'email'     => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password'  => ['nullable', 'confirmed', 'min:8'],
            'role'      => ['required', 'in:admin,manager,staff'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $user->firstname = $data['firstname'];
        $user->lastname  = $data['lastname'];
        $user->suffix    = $data['suffix'] ?? null;
        $user->username  = $data['username'];
        $user->email     = $data['email'];
        $user->role      = $data['role'];
        $user->is_active = $request->boolean('is_active');

        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    public function logout() {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function login(Request $request){
        $data = $request->validate([
            'loginname' => ['required'],
            'loginpassword' => ['required'],
        ]);

        $field = filter_var($data['loginname'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $user = User::where($field, $data['loginname'])->first();

        if (! $user) {
            return back()->withErrors(['loginname' => 'Invalid username/email or password'])
                        ->withInput($request->only('loginname'));
        }

        if (! $user->is_active) {
            return back()->withErrors(['loginname' => 'Your account has been deactivated. Contact an administrator.'])
                        ->withInput($request->only('loginname'));
        }

        if (Auth::attempt([$field => $data['loginname'], 'password' => $data['loginpassword']])) {
            $request->session()->regenerate();
            return redirect()->route('home');
        }

        return back()->withErrors(['loginname' => 'Invalid username/email or password'])
                    ->withInput($request->only('loginname'));
    }

    public function showLogin() {
        return view('auth.login');
    }
}