@extends('layouts.admin')
{{--

Вывод контента

--}}
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="btn-group mb-4" role="group" aria-label="Buttons group">
                    @if (!empty($queryArr))
                        @foreach ($queryArr as $v)
                            <a href="{{ route('admin.import_export') . '?' . $v }}" class="btn btn-primary @if ($query === $v) active @endif">@lang("{$lang}::a.{$v}_many")</a>
                        @endforeach
                    @endif
                </div>
                <form action="{{ $routeImport }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    {{--


                    Табы --}}
                    <ul class="nav nav-tabs" role="tablist" id="import-export">
                        <li class="nav-item">
                            <a class="nav-link active" id="export-tab" data-toggle="tab" href="#export" role="tab" aria-controls="export" aria-selected="true">@lang("{$lang}::a.export")</a>
                        </li>
                        @if ($routeImport)
                            <li class="nav-item">
                                <a class="nav-link" id="import-tab" data-toggle="tab" href="#import" role="tab" aria-controls="import" aria-selected="false">@lang("{$lang}::a.import")</a>
                            </li>
                        @endif
                    </ul>
                    {{--

                    Контент табов --}}
                    <div class="tab-content">
                        <div class="tab-pane fade show active pt-4" id="export" role="tabpanel" aria-labelledby="export-tab">
                            <a class="btn btn-primary btn-pulse mt-3" href="{{ $routeExport }}">@lang("{$lang}::a.export")</a>
                        </div>
                        @if ($routeImport)
                            <div class="tab-pane fade pt-4" id="import" role="tabpanel" aria-labelledby="import-tab">
                                <div class="custom-file mt-3">
                                    <input type="file" name="import_file" class="custom-file-input">
                                    <label class="custom-file-label" for="import_file">@lang("{$lang}::a.choose_file")</label>
                                </div>
                                <button class="btn btn-primary btn-pulse mt-3">@lang("{$lang}::a.import")</button>
                            </div>
                        @endif
                    </div>
                    {{-- Конец табов


                    --}}
                </form>
            </div>
        </div>
    </div>
    <script>

        // При выборе файла, подставим его имя в input file
        var fileInput = document.querySelector('input[type=file][name=import_file]')
        if (fileInput) {
            fileInput.addEventListener('change', function(e) {

                if (e.target.files[0]) {
                    var fileName = e.target.files[0].name,
                        label = e.target.parentElement.querySelector('label')

                    if (fileName && label) {
                        label.innerText = fileName
                    }
                }
            })
        }
    </script>
@endsection
