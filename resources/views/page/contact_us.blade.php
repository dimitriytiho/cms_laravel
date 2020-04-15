@extends('layouts.default')
{{--

Подключается блок header

--}}
@section('header')
    @include('components.header')
@endsection
{{--

Вывод контента novalidate

--}}
@section('content')
    <main class="main">
        <div class="container">
            <div class="row">
                <div class="col">
                    <h1 class="font-weight-light text-secondary mt-5">{{ $title }}</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 my-4">
                    <form method="post" action="{{ route('post_contact_us') }}" class="needs-validation loader-submit" novalidate>
                        @csrf
                        <div class="form-group">
                            <label for="name" class="sr-only">{{ __('f.name') }}</label>
                            <input type="text" class="form-control" name="name" id="name" placeholder="{{ __('f.name') . '...' }}">
                        </div>
                        <div class="form-group">
                            <label for="tel" class="sr-only">{{ __('f.tel') }}</label>
                            <input type="text" class="form-control" name="tel" id="tel" placeholder="{{ __('f.tel') . '...' }}">
                        </div>
                        <div class="form-group">
                            <label for="email" class="sr-only">{{ __('f.email') }}</label>
                            <input type="email" class="form-control" name="email" id="email" placeholder="{{ __('f.email') . '...' }}">
                        </div>
                        <div class="form-group">
                            <label for="message" class="sr-only">{{ __('f.message') }}</label>
                            <textarea class="form-control" name="message" id="message" placeholder="{{ __('f.message') . '...' }}" rows="3"></textarea>
                        </div>
                        <div class="custom-control custom-checkbox mr-sm-2">
                            <input type="checkbox" class="custom-control-input" name="accept" id="accept">
                            <label class="custom-control-label" for="accept">{{ __('f.accept') }}</label>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">{{ __('f.submit') }}</button>
                    </form>
                    {{-- {!! Form::open()->route('post_contact_us')->locale('forms')->attrs(['class' => 'needs-validation loader-submit']) !!}
                        {!! Form::text('name', 'Name')->label(null)->placeholder(__('f.name') . '...')->required() !!}
                        {!! Form::text('tel', 'Phone')->label(null)->placeholder(__('f.tel') . '...')
                        ->required() !!}
                        {!! Form::text('email', 'Email')->label(null)->placeholder(__('f.email') . '...')->required() !!}
                        {!! Form::textarea('message', 'Message')->label(null)->placeholder(__('f.message') . '...')->required() !!}
                        {!! Form::checkbox('accept', 'Accept', '1')->required() !!}
                        {!! Form::submit('Send')->attrs(['class'=> 'mt-3'])->required(false) !!}
                    {!! Form::close() !!} --}}
                </div>
            </div>

            {{-- <div class="row mt-5">
                <div class="col">
                    <div>
                        <button class="btn btn-outline-secondary" type="button" aria-controls="collapseExample" @click="collapse = !collapse">Выдвижной элемент</button>
                        <transition name="fade">
                            <div class="mt-3" v-if="collapse">
                                <div class="card card-body">Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident.</div>
                            </div>
                        </transition>
                    </div>


                    <div class="mt-3">
                        <button class="btn btn-outline-secondary" @click="moving = !moving">
                            Переключить отрисовку
                        </button>
                        <transition name="slide-fade">
                            <p v-if="moving">Anim pariatur cliche.</p>
                        </transition>
                    </div>

                    <div class="mt-3">
                        <button class="btn btn-outline-secondary" @click="bounce = !bounce">Переключить отображение</button>
                        <transition name="bounce">
                            <p v-if="bounce">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris facilisis enim libero, at lacinia diam fermentum id. Pellentesque habitant morbi tristique senectus et netus.</p>
                        </transition>
                    </div>
                </div>
            </div> --}}
        </div>
    </main>
@endsection
{{--

Этот код будет выведен после всех скриптов --}}
@push('novalidate')
    {{--<script src="{{ asset('js/jquery.maskedinput.min.js') }}"></script>
    <script>
        $(function() {
            $('.needs-validation').attr('novalidate', '');
            $('#tel').mask('+7(999)999-99-99');
        })
    </script>--}}
@endpush
{{--

Подключается блок footer

--}}
@section('footer')
    @include('components.footer')
@endsection
