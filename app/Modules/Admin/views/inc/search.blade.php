@if (!empty($route) && !empty($queryArr))
    <form action="{{ route("admin.{$route}.index") }}" class="mb-3">
        <div class="form-row">
            <div class="col-sm-2 mb-2">
                <label for="col" class="sr-only"></label>
                <select class="form-control" name="col">
                    @if ($queryArr)
                        @foreach ($queryArr as $option)
                            <option value="{{ $option }}" @if ($col === $option) selected @endif>@lang("{$lang}::f.{$option}")</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col col-sm-3">
                <label for="cell" class="sr-only"></label>
                <input type="text" name="cell" class="form-control" placeholder="@lang("{$lang}::a.search")..." value="@if (!empty($cell)){{ $cell }}@endif">
            </div>
            <div class="col-1 d-flex">
                <div>
                    <button type="submit" class="btn btn-primary btn-icons">
                        <i aria-hidden="true" class="material-icons" title="@lang("{$lang}::a.search")">search</i>
                    </button>
                </div>
                @if ($cell)
                    <div>
                        <a href="{{ route("admin.{$route}.index") }}" class="btn btn-outline-primary ml-2 btn-icons">
                            <i aria-hidden="true" class="material-icons" title="@lang("{$lang}::s.reset")">clear</i>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </form>
@endif
