@extends('layouts.base')

@section('body')
    <div class="flex items-center justify-center my-8 md:my-20">
        <div class="w-full md:w-1/2 lg:w-1/3 xl:w-1/4">
            <h1 class="text-4xl text-gray-700 text-center mb-2 font-bold">{{ $title }}</h1>
            <div class="p-8 md:border-2 md:rounded md:bg-gray-100">
                @include('layouts._alerts')

                @yield('small-content')

                @yield('small-content-after')
            </div>
        </div>
    </div>
@endsection
