@if ($values->isNotEmpty())
    <div class="row mt-4">
        <div class="col d-flex justify-content-center">
            <div>{{ $values
                    ->appends([
                        'col' => s(request()->query('col')),
                        'cell' => s(request()->query('cell')),
                        ])
                    ->onEachSide(2)
                    ->links() }}</div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <p class="font-weight-light text-center text-secondary mt-3">{{ __("{$lang}::a.shown") . $values->count() . __("{$lang}::a.of") .  $values->total()}}</p>
        </div>
    </div>
@endif
