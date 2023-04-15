@extends('layouts.blog')

@section('title', 'Blog')

@section('content')
<x-markdown>
    # My title

    This is a [link to our website](https://spatie.be)

    ```php
    echo 'Hello world';
    ```
</x-markdown>
@endsection