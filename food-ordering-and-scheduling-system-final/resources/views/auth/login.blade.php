@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="max-w-md mx-auto">
        <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200 border border-slate-100 overflow-hidden">
            <div class="p-10 space-y-8">
                <div class="text-center space-y-2">
                    <h2 class="text-3xl font-black text-slate-900">Welcome Back</h2>
                    <p class="text-slate-500 font-bold">Sign in to your beastly account</p>
                </div>

                @if($errors->any())
                    <div class="p-4 bg-red-50 border border-red-100 text-red-600 text-xs font-bold rounded-2xl animate-pulse">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login.post') }}" class="space-y-6">
                    @csrf
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-slate-400 uppercase ml-2">Email Address</label>
                        <input type="email" name="email" required autofocus class="w-full p-4 rounded-2xl bg-slate-50 border border-slate-200 font-bold focus:ring-4 focus:ring-orange-50 focus:border-orange-500 outline-none transition-all" placeholder="your@email.com">
                    </div>

                    <div class="space-y-1">
                        <div class="flex justify-between items-center ml-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase">Password</label>
                            <a href="#" class="text-[10px] font-black text-orange-600 hover:underline">Forgot?</a>
                        </div>
                        <input type="password" name="password" required class="w-full p-4 rounded-2xl bg-slate-50 border border-slate-200 font-bold focus:ring-4 focus:ring-orange-50 focus:border-orange-500 outline-none transition-all" placeholder="••••••••">
                    </div>

                    <button type="submit" class="w-full bg-slate-900 hover:bg-orange-600 text-white font-black py-5 rounded-2xl text-lg shadow-xl shadow-slate-200 transition-all active:scale-95">
                        Sign In
                    </button>
                </form>

                <div class="pt-6 border-t border-slate-50 text-center">
                    <p class="text-sm font-bold text-slate-500">
                        New to the den? 
                        <a href="{{ route('register') }}" class="text-orange-600 font-black hover:underline">Create Account</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
