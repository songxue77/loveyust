@php($subTitle = isset($activeTag) ? $activeTag->name() : null)
@title('Forum' . (isset($subTitle) ? ' > ' . $subTitle : ''))

@extends('layouts.default')

@section('subnav')
    <div class="bg-white border-b">
        <div class="container mx-auto flex justify-between items-center px-4">
            <h1 class="text-xl py-4 text-gray-900">{{ $title }}</h1>
        </div>
    </div>
@endsection

@section('content')
    <div class="container mx-auto px-4 pt-4 flex flex-wrap flex-col-reverse md:flex-row">
        <div class="w-full md:w-3/4 md:pr-3">
            @if (count($threads))
                <div>
                    @foreach ($threads as $thread)
                        <div class="thread-card">
                            <a href="{{ route('thread', $thread->slug()) }}">
                                <h4 class="flex justify-between text-xl font-bold text-gray-900">
                                    {{ $thread->subject() }}
                                    <span class="text-base font-normal">
                                        <i class="fa fa-comment text-gray-500 mr-2"></i>
                                        {{ count($thread->replies()) }}
                                    </span>
                                </h4>
                                <p class="text-gray-600">{!! $thread->excerpt() !!}</p>
                            </a>

                            <div class="flex flex-col justify-between md:flex-row md:items-center text-sm pt-5">
                                <div class="flex flex-col md:flex-row md:items-center">
                                    <div class="flex mb-4 md:mb-0">
                                        @if (count($thread->replies()))
                                            @include('forum.threads.info.avatar', ['user' => $thread->replies()->last()->author()])
                                        @else
                                            @include('forum.threads.info.avatar', ['user' => $thread->author()])
                                        @endif

                                        <div class="mr-6 text-gray-700">
                                            @if (count($thread->replies()))
                                                @php($lastReply = $thread->replies()->last())
                                                <a href="{{ route('profile', $lastReply->author()->username()) }}" class="text-green-darker mr-2">{{ $lastReply->author()->name() }}</a> replied
                                                {{ $lastReply->createdAt()->diffForHumans() }}
                                            @else
                                                <a href="{{ route('profile', $thread->author()->username()) }}" class="text-green-darker mr-2">{{ $thread->author()->name() }}</a> posted
                                                {{ $thread->createdAt()->diffForHumans() }}
                                            @endif
                                        </div>
                                    </div>

                                    @include('forum.threads.info.tags')
                                </div>

                                @include('forum.threads._view_solution')
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="flex justify-center">
                    {!! $threads->render() !!}
                </div>
            @else
                <div class="flex flex-col items-center justify-center pt-4 text-gray-700">
                    <h2 class="text-2xl pb-4">No threads were found!</h2>
                    <a href="{{ route('threads.create') }}" 
                    class="button button-primary">
                        Create a new one
                    </a>
                </div>
            @endif
        </div>
        <div class="w-full md:w-1/4 md:pl-3 md:pt-4">
            <a 
                href="{{ route('threads.create') }}"
                class="button button-primary button-full mb-4">
                    Create Thread
            </a>
            <a 
                href="{{ route("feeds.forum") }}"
                class="button button-muted button-full mb-4"
                target="_blank">
                    <i class="fa fa-feed"></i>
                    RSS Feed
            </a>

            @include('layouts._ads._forum_sidebar')

            <h3 class="text-xs font-bold tracking-wider uppercase text-gray-500">Tags</h3>

            <ul class="tags">
                <li class="{{ active('forum*', ! isset($activeTag) || $activeTag === null) }}">
                    <a href="{{ route('forum') }}">
                        All
                    </a>
                </li>   

                @foreach (App\Models\Tag::orderBy('name')->get() as $tag)
                    <li class="{{ isset($activeTag) && $tag->matches($activeTag) ? ' active' : '' }}">
                        <a href="{{ route('forum.tag', $tag->slug()) }}">
                            {{ $tag->name() }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@endsection
