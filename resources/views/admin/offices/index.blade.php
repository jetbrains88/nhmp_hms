@extends('layouts.app')

@section('title', 'Office Management')
@section('page-title', 'Office Management')

@section('content')
<div class="p-6">
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="p-12 text-center">
            <div class="w-24 h-24 bg-blue-50 text-blue-500 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-building text-4xl"></i>
            </div>
            <h2 class="text-3xl font-extrabold text-gray-900 mb-4">Office Structure</h2>
            <p class="text-gray-600 max-w-lg mx-auto mb-8 text-lg">
                This module is currently being optimized for the new multi-tenant architecture. 
                Regional and Zonal office hierarchies will be available here shortly.
            </p>
            <div class="flex justify-center gap-4">
                <a href="{{ route('admin.dashboard') }}" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-bold hover:bg-gray-200 transition-all">
                    Return to Dashboard
                </a>
                <button class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-bold shadow-lg shadow-blue-200 hover:scale-105 transition-all">
                    Under Maintenance
                </button>
            </div>
        </div>
        
        <!-- Mock Skeleton to show intended design -->
        <div class="border-t border-gray-100 p-8 bg-gray-50/50">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 opacity-30 grayscale unset-pointer-events">
                @for($i=0; $i<3; $i++)
                <div class="bg-white p-6 rounded-xl border border-gray-200">
                    <div class="h-4 w-1/2 bg-gray-200 rounded mb-4"></div>
                    <div class="h-8 w-full bg-gray-100 rounded"></div>
                </div>
                @endfor
            </div>
        </div>
    </div>
</div>
@endsection
