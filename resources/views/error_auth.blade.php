@extends('layouts.master')

@section('title', 'Успешная авторизация 🎉')

@section('content')
    <div class="flex-center position-ref full-height">
        <div class="content">
            <span class="title m-b-md">
                Произошла ошибка 😢
            </span>

            <div class="links">
                <a href="tg://resolve?domain=nutnet_redmine_statistic_bot" id="resolve">
                    Вернуться в бота
                </a>
            </div>
        </div>
    </div>
@endsection
