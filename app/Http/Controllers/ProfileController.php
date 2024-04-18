<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Models\Image;
use App\Models\Wishlist;
use App\Models\Location;
use App\Models\User;
use App\Models\Admin;
use App\Models\Item;
use App\Models\Cart;
use App\Models\Purchase;




class ProfileController extends Controller{

    /**
   * @method Displays the edit profile form
   * @param id Id of the User whose profile will be edited
   */
    public function show(){
      
      $user = User::find(Auth::id());
      $this->authorize('show', $user);

      $image = Image::where('id_user', $user->id)->first();

      if ($image && $image->filepath) {
          $profile_picture = $image->filepath;
        } else {
          $profile_picture = 'images/default-person.png';
      }  


      Log::info($profile_picture);
      $wishlist = Wishlist::where('id_user', $user->id)->get();

      $items_wishlist = [];

      foreach($wishlist as $item){
        $items_wishlist[] = Item::find($item->id_item);
      }

      $carts_orders = [];
      $locations_orders = [];
      $orders = Purchase::where('id_user', $user->id)->where('purchase_status', '!=', 'Delivered')->get();
      foreach($orders as $order){
        $carts_orders[] = Cart::find($order->id_cart);
        $locations_orders[] = Location::find($order->id_location);
      }

      $carts_purchases = [];
      $purchases_locations = [];
      $purchases = Purchase::where('id_user', $user->id)->where('purchase_status', '=', 'Delivered')->get();
      foreach($purchases as $purchase){
        $carts_purchases[] = Cart::find($purchase->id_cart);
        $purchases_locations[] = Location::find($purchase->id_location);
      }


      return view('pages.profile.profile', [
        'user' => $user,
        'items_wishlist' => $items_wishlist,
        'profile_picture' => $profile_picture,
        'orders' => $orders,
        'carts_orders' => $carts_orders,
        'locations_orders' => $locations_orders,
        'purchases' => $purchases,
        'carts_purchases' => $carts_purchases,
        'purchases_locations' => $purchases_locations,
        'breadcrumbs' => ['Home' => route('home')],
        'current' => 'Profile'
      ]);
    }

    public function showEditProfile() {
      $user = User::find(Auth::id());
      $this->authorize('show', $user);

      return view('pages.profile.edit_profile', [
        'breadcrumbs' => ['Profile' => route('profile')],
        'current' => 'Edit Profile', 
        'user' => $user
      ]);
    }
  
    public function changeUsername(Request $request) {
      if (Auth::check()) {
          $user = User::find(Auth::id());
          $new_username = $request->input('new_username');
  
          if($new_username === null){
              return view('pages.profile.edit_profile', ['user' => $user, 'errorUsername' => 'Username cannot be empty',         'breadcrumbs' => ['Home' => route('home')],
              'current' => 'Profile']);
          }
          else{
              $existingUser = User::where('username', $new_username)->first();
              if($existingUser){
                  return view('pages.profile.edit_profile', ['user' => $user, 'errorUsername' => 'Username already exists',         'breadcrumbs' => ['Home' => route('home')],
                  'current' => 'Profile']);
              }
              else{
                  $user->username = $new_username;
                  $user->save();
                  return view('pages.profile.edit_profile', ['user' => $user, 'successUsername' => 'Username changed successfully',         'breadcrumbs' => ['Home' => route('home')],
                  'current' => 'Profile']);
              }
          }
      } else {
          return response()->json(['message' => 'User not authenticated']);
      }
    }
  
    public function changeName(Request $request){
      if(Auth::check()) {
        $user = User::find(Auth::id());
        $new_name = $request->input('new_name');
        $user->name = $new_name;
        $user->save();
  
  
        return view('pages.profile.edit_profile', ['user' => $user, 'successName' => 'Name changed successfully',         'breadcrumbs' => ['Home' => route('home')],
        'current' => 'Profile']);
      }else{
          return response()->json(['message' => 'User not authenticated']);
      }
    }
  
    public function changePassword(){
      if(Auth::check()){
        $user = User::find(Auth::id());
        $new_password = $request->input('new_password');
        $new_password_confirmation = $request->input('new_password_confirmation');
        if(strlen($new_password) < 10){
          return view('pages.profile.edit_profile', ['user' => $user, 'errorPassword' => 'Password must be longer than 10 characters',        'breadcrumbs' => ['Home' => route('home')],
          'current' => 'Profile']);
        }
        else if($new_password !== $new_password_confirmation){
          return view('pages.profile.edit_profile', ['user' => $user, 'errorPassword' => 'Passwords do not match',         'breadcrumbs' => ['Home' => route('home')],
          'current' => 'Profile']);
        }
        else{
          $user->password = Hash::make($new_password);
          $user->save();
          return view('pages.profile.edit_profile', ['user' => $user, 'successPassword' => 'Password changed successfully',         'breadcrumbs' => ['Home' => route('home')],
          'current' => 'Profile']);
        }
      }
    }
  
    public function removeUser(Request $request){
      if(Auth::check()){
        $user = User::find(Auth::id());
        $password = request()->input('password');
        if(!Hash::check($password, $user->password)){
          return view('pages.profile.edit_profile', ['user' => $user, 'errorRemove' => 'Incorrect password',         'breadcrumbs' => ['Home' => route('home')],
          'current' => 'Profile']);
        }
        else{
          $user->delete();
          return redirect()->route('home');
        }
      }
    }
  
    public function changePicture(Request $request){
      if(Auth::check()){
        $user_id = Auth::user()->id;
        $request->validate([
          'imageInput' => 'required|image|mimes:jpeg,png,jpg',
        ]);
  
        if ($request->hasFile('imageInput')) {
          $file = $request->file('imageInput');
          $extension = $file->getClientOriginalExtension();
                
          $filename = uniqid() . '.' . $extension;
  
          if (Storage::disk('public')->exists('images/' . $filename)) {
            Storage::disk('public')->delete($filename);
          }
  
          $destinationPath = public_path('images');
          $file->move($destinationPath, $filename);

          $existingImage = Image::where('id_user', $user_id)->first();
  
          if ($existingImage) {
            $existingImage->filepath = 'images/' . $filename;
            $existingImage->save();
          } else {
            $newImage = new Image;
            $newImage->id_user = $user_id;
            $newImage->filepath = 'images/' . $filename;
            $newImage->save();
          }
        
        }
        return redirect()->route('profile')->with('success', 'Profile picture updated successfully.');
      }
    }

    public function newPassword(){
      $email = request()->input('email');
      $new_password = request()->input('password');
      $new_password_confirmation = request()->input('password-confirmation');
  
      $user = User::where('email', $email)->first();
      
      if($new_password === $new_password_confirmation) {
          $user->password = Hash::make($new_password);
          $user->save();
  
          $credentials = ['email' => $email, 'password' => $new_password];
  
          if (Auth::attempt($credentials)) {
              return redirect()->intended('/home');
          } else {
              return back()->withErrors([
                  'email' => 'The provided credentials do not match our records.',
              ])->onlyInput('email');
          }
      } else {
          return redirect()->back()->withErrors(['password' => 'The passwords do not match']);
      }
    }
}
