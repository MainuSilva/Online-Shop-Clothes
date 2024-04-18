@extends('layouts.app')

@section('css')
<link href="{{ url('css/edit_profile.css') }}" rel="stylesheet">
@endsection

@section('scripts')
  <script src="{{ asset('js/edit-profile.js') }}" defer></script>
@endsection

@section('content')
  @include('partials.common.breadcrumbs', ['breadcrumbs' => $breadcrumbs , 'current' => $current ])

  <section id="edit-profile">
    <script src="{{ asset('js/edit_profile.js') }}" defer></script>
    <article class="update-form">
      <h2>Update Profile Picture</h2>
      <form id="update-photo-form" class="change-information" method="POST" action="{{route('update_profile_pic')}}" enctype="multipart/form-data">
        @csrf
        <input type="file" id="imageInput" name="imageInput" accept="image/*" >
        <img id="imagePreview" style="max-width: 200px; max-height: 200px; display: none;">
        <button id="update_photo_button" type="submit" value="Update Password">Update</button>
      </form>
    </article>

    <article class="update-form">
        <h2>Update Username</h2>
        <form id="update-username-form" class="change-username" method="POST" action="{{ route('change_username') }}">
            @csrf
            <input type="text" name="new_username" placeholder="{{ $user->username }}">
            <button id="update-username-button" type="submit" value="Update Username">Update Username</button>
        </form>
        @if(isset($successUsername))
            <div class="alert alert-success">
                {{ $successUsername }}
            </div>
        @endif
        @if(isset($errorUsername))
            <div class="alert alert-danger">
                {{ $errorUsername }}
            </div>
        @endif

    </article>

    <article class="update-form">
        <h2>Update Name</h2>
        <form id="update-name-form" class="change-name" method="POST" action="{{ route('change_name') }}">
            @csrf
            <input type="text" name="new_name" placeholder="{{ $user->name }}">
            <button id="update-name-button" type="submit" value="Update Name">Update Name</button>
        </form>
        @if(isset($successName))
            <div class="alert alert-success">
                {{ $successName }}
            </div>
        @endif
    </article> 



    <article class="update-form">
      <h2>Update Password</h2>
      <form id="update-password-form" class="change-password" method="POST" action="{{ route('change_password') }}">
        @csrf
        <input type="password" name="new_password" placeholder="New Password">
        <input type="password" name="new_password_confirmation" placeholder="Confirm New Password">

        <button id="update-password-button" type="submit" value="Update Password">Update Password</button>
      </form>

      @if(isset($successPassword))
            <div class="alert alert-success">
                {{ $successPassword }}
            </div>
      @endif
      @if(isset($errorPassword))
            <div class="alert alert-danger">
                {{ $errorPassword }}
            </div>
      @endif
    </article>

    <article class="update-form">
      <h2>Delete Profile</h2>
      <form id="delete-profile-form" class="delete-profile" method="POST" action="{{ route('remove_user') }}">
        @csrf
        <input type="password" name="password" placeholder="Current Password">
        <button id="delete-profile-button" type="submit" value="Delete Profile" onclick="return confirmDelete()">Delete Profile</button>
      </form>
      @if(isset($errorRemove))
            <div class="alert alert-danger">
                {{ $errorRemove }}
            </div>
      @endif    
    </article>

@endsection