@extends('layouts.app')

@section('title', 'Enable Two-Factor Authentication')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-slate-50 px-4">
  <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8">

    <div class="text-center mb-6">
      <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-amber-100 mb-4">
        <svg class="w-7 h-7 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
        </svg>
      </div>
      <h1 class="text-xl font-bold text-slate-800">Two-Factor Authentication Required</h1>
      <p class="text-sm text-slate-500 mt-1">
        Your account type requires 2FA. Enable it to access MedRecords.
      </p>
    </div>

    @if(session('warning'))
      <div class="mb-4 p-3 bg-amber-50 border border-amber-200 text-amber-800 rounded-lg text-sm">
        {{ session('warning') }}
      </div>
    @endif

    @if(session('success'))
      <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">
        {{ session('success') }}
      </div>
    @endif

    @if($errors->any())
      <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm">
        @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
      </div>
    @endif

    <div class="bg-slate-50 rounded-lg p-4 mb-6 text-sm text-slate-700">
      <p class="font-medium mb-1">How it works:</p>
      <ul class="list-disc list-inside space-y-1 text-slate-600">
        <li>When you log in, an SMS code will be sent to your phone.</li>
        <li>You will enter that code to complete sign-in.</li>
        <li>Your phone number: <strong>{{ $user->phone }}</strong></li>
      </ul>
    </div>

    @if(!$user->phone)
      <div class="p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm mb-4">
        You must add a phone number to your profile before enabling 2FA. Please contact your administrator.
      </div>
    @else
      <form method="POST" action="{{ route('account.2fa.toggle') }}">
        @csrf
        <button type="submit"
          class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition">
          Enable Two-Factor Authentication
        </button>
      </form>
    @endif

    <div class="mt-4 text-center">
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="text-sm text-slate-500 hover:text-slate-700 underline">
          Sign out instead
        </button>
      </form>
    </div>

  </div>
</div>
@endsection
