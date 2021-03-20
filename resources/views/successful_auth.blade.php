@extends('layouts.master')

@section('title', 'Успешная авторизация 🎉')

@section('content')
    <div class="flex-center position-ref full-height">
        <div class="content">
            <div class="title m-b-md">
                <img
                    style="border-radius: 50%"
                    width="150"
                    src="https://sun9-18.userapi.com/impg/mFF__i1g8N3QVoxcPwuQXUjf-yJJS7uTKRBDyg/i3ZfuGTT4Ec.jpg?size=320x320&quality=96&sign=c2d0d4941b362ab7fb60882fbb5cead3&type=album"
                    alt="nutnet">
            </div>
            <span class="title m-b-md">
                Успешно вошли 🎉
            </span>

            <div class="links">
                <a href="tg://resolve?domain=nutnet_redmine_statistic_bot" id="resolve">
                    Переадресация через <span class="counter__number" id="counter">3</span>
                </a>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        let counter = document.getElementById('counter')
        let link = document.getElementById('resolve')

        setTimer(counter)

        function setTimer(counter) {
            let start = 3

            let timer = setInterval(() => {
                start--
                counter.innerText = start

                if (!start) {
                    clearInterval(timer)
                    link.click()
                }
            }, 1000)
        }
    </script>
@endsection
