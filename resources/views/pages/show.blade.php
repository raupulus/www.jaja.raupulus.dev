@extends('layouts.master')

@section('content')
    <div class="page-container">
        <div class="page-header">
            <h1 class="page-title">{{ $page->title }}</h1>
        </div>

        <section class="page-content">
            <div class="page-content-wrapper">
                {!! $page->getHtmlContent() !!}
            </div>
        </section>
    </div>
@endsection
