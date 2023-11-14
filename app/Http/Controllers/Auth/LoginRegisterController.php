<?php

namespace App\Http\Controllers\Auth;

use Image;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\SendMailJob;
use Illuminate\View\View;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Http\Requests\UpdateUsersRequest;


class LoginRegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except([
            'logout', 'dashboard'
        ]);
    }
    public function register()
    {
        return view('auth.register');
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:250',
            'email' => 'required|email|max:250|unique:users',
            'password' => 'required|min:8|confirmed',
            'photo' => 'image|nullable|max:1999'
        ]);

        $path = null;
        $pathTumbnail = null;
        $pathSquare = null;

        if ($request->hasFile('photo')) {
            $filenameWithExt = $request->file('photo')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);

            $extension = $request->file('photo')->getClientOriginalExtension();
            $filenameSimpan = $filename . '_' . time() . '.' . $extension;

            $path = $request->file('photo')->storeAs('photos', $filenameSimpan);

            $thumbnail = Image::make($request->file('photo')->getRealPath())->resize(150, 150);
            $thumbnailSimpan = time() . '_thumbnail_' . $request->file('photo')->getClientOriginalName(); // penamaan
            $thumbnail->save(public_path() . '/storage/photos/' . $thumbnailSimpan);

            $square = Image::make($request->file('photo')->getRealPath())->resize(200, 200);
            $squareSimpan = time() . '_square_' . $request->file('photo')->getClientOriginalName(); // penamaan
            $square->save(public_path() . '/storage/photos/' . $squareSimpan);

            $pathTumbnail = 'photos/' . $thumbnailSimpan;
            $pathSquare = 'photos/' . $squareSimpan;
        } else {
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'photo' => $path,
            'thumbnail' => $pathTumbnail,
            'square' => $pathSquare
        ]);

        $content = [
            'subject'  => $request->name,
            'body' => $request->email
        ];
        Mail::to($request->email)->send(new SendEmail($content));

        $credentials = $request->only('email', 'password');
        Auth::attempt($credentials);
        $request->session()->regenerate();

        return redirect()->route('dashboard')
            ->withSuccess('You have successfully registered & logged in!');
    }
    public function login()
    {
        return view('auth.login');
    }
    public function authenticate()
    {
        $data = request()->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        $credentials = $data->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $data->session()->regenerate();
            return redirect()->route('dashboard')->withSuccess('You have successfully loggedin!');
        }
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.'
        ])->onlyInput('email');
    }

    // public function editImage(Request $request, $id)
    // {
    //     $this->validate($request, [
    //         'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
    //     ]);

    //     $userImage = UserImage::find($id);

    //     if (!$userImage) {
    //         return redirect()->back()->with('error', 'Gambar tidak ditemukan.');
    //     }

    //     $image = $request->file('image');

    //     if ($image) {
    //         $imageName = time() . '.' . $image->getClientOriginalExtension();
    //         $image->storeAs('images', $imageName);

    //         $img = Image::make(storage_path("app/images/$imageName"));
    //         $img->resize(200, 200);
    //         $img->save();

    //         $thumbnail = Image::make(storage_path("app/images/$imageName"))->fit(100, 100);
    //         $thumbnail->save(storage_path("app/images/thumbnails/$imageName"));

    //         $squareImage = Image::make(storage_path("app/images/$imageName"))->fit(200, 200);
    //         $squareImage->save(storage_path("app/images/squares/$imageName"));

    //         $userImage->original_path = $imageName;
    //         $userImage->save();
    //     }

    //     return redirect()->back()->with('success', 'Gambar berhasil diubah.');
    // }


    public function dashboard()
    {
        if (Auth::check()) {
            return view('auth.dashboard');
        }
        return redirect()->route('login')
            ->withErrors([
                'email' => 'You must be logged in to view the dashboard.'
            ])->onlyInput('email');
    }
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')
            ->withSuccess('You have logged out successfully!');;
    }
    public function index()
    {
        $users = User::all();
        return view('users', ['users' => $users]);
    }
    public function edit($id)
    {
        $users = User::findOrFail($id);
        return view('edit', compact('users'));
    }
    public function destroy($user)
    {
        $user = User::findOrFail($user);
        $user->delete();
        return redirect()->route('users')
            ->withSuccess('Data Deleted Successfully');
    }
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'image' => 'image|mimes:jpeg,jpg,png|max:2048',
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->hasFile('photo')) {
            File::delete(public_path() . 'photos/' . $user->photos);

            $filenameWithExt = $request->file('photo')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('photo')->getClientOriginalExtension();
            $filenameSimpan = $filename . '_' . time() . '.' . $extension;
            $path = $request->file('photo')->storeAs('photos', $filenameSimpan);

            $thumbnail = Image::make($request->file('photo')->getRealPath())->resize(150, 150);
            $thumbnailSimpan = time() . '_thumbnail_' . $request->file('photo')->getClientOriginalName();
            $thumbnail->save(public_path() . '/storage/photos/' . $thumbnailSimpan);

            $square = Image::make($request->file('photo')->getRealPath())->resize(200, 200);
            $squareSimpan = time() . '_square_' . $request->file('photo')->getClientOriginalName();
            $square->save(public_path() . '/storage/photos/' . $squareSimpan);

            $userData['photo'] = $path;
            $userData['thumbnail'] = 'photos/' . $thumbnailSimpan;
            $userData['square'] = 'photos/' . $squareSimpan;
        }

        $user->update($userData);
        return redirect()->route('users')
            ->withSuccess('Data Updated Successfully');
    }
}
