@extends("{$viewPath}.layouts.admin")
{{--

Вывод контента

--}}
@section('content')
    {!! $constructor::adminH2(__("{$lang}::a.Cache")) !!}
    <div class="row">
        <div class="col-md-6">
            {!! $constructor::adminBlockLink(__("{$lang}::a.db_caches"), __("{$lang}::s.remove"), route('admin.additionally', 'cache=db'), 'confirm-link', 'data-toggle="modal" data-target="#modal-confirm"') !!}
        </div>
        <div class="col-md-6">
            {!! $constructor::adminBlockLink(__("{$lang}::a.view_caches"), __("{$lang}::s.remove"), route('admin.additionally', 'cache=views'), 'confirm-link', 'data-toggle="modal" data-target="#modal-confirm"') !!}
        </div>
        <div class="col-md-6">
            {!! $constructor::adminBlockLink(__("{$lang}::a.route_caches"), __("{$lang}::s.remove"), route('admin.additionally', 'cache=routes'), 'confirm-link', 'data-toggle="modal" data-target="#modal-confirm"') !!}
        </div>
        <div class="col-md-6">
            {!! $constructor::adminBlockLink(__("{$lang}::a.config_caches"), __("{$lang}::s.remove"), route('admin.additionally', 'cache=config'), 'confirm-link', 'data-toggle="modal" data-target="#modal-confirm"') !!}
        </div>
    </div>

    {!! $constructor::adminH2(__("{$lang}::a.Backup"), 'mt-5') !!}
    <div class="row">
        <div class="col">
            {!! $constructor::adminBlockLink(__("{$lang}::a.backup_files_db"), __("{$lang}::a.run"), route('admin.additionally', 'backup=run'), 'confirm-link' . $backupDisabled, 'data-toggle="modal" data-target="#modal-confirm"') !!}
        </div>
    </div>

    {!! $constructor::adminH2(__("{$lang}::a.Commands"), 'mt-5') !!}
    <div class="row mb-3 command">
        <div class="col-md-4 my-2">
            <div class="form-group mb-0">
                <label for="preset-command" class="sr-only">{{ __("{$lang}::a.select_command") }}</label>
                <select class="form-control" id="preset-command">
                    <option value="" id="preset-command-option">{{ __("{$lang}::a.select_command") }}</option>
                    @if (config('admin.commands'))
                    	@foreach (config('admin.commands') as $v)
                            <option value="{{ $v }}">{{ __("{$lang}::a.{$v}") }}</option>
                    	@endforeach
                    @endif
                </select>
            </div>
        </div>
        <div class="col d-flex justify-content-between align-items-center my-2">
            <div class="form-group w-100 mb-0 mr-md-3">
                <label for="full-command" class="sr-only">{{ __("{$lang}::a.command") }}</label>
                <input type="text" class="form-control" id="full-command" placeholder="{{ __("{$lang}::a.command") }}" value="php artisan">
            </div>

            <form action="{{ route("admin.{$c}") }}" method="post">
                @csrf
                {!! $constructor::hidden('command', '') !!}
                <button type="submit" class="btn btn-primary no-wrap">{{ __("{$lang}::a.run") }}</button>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <button class="btn btn-link pl-0 change-icon" type="button" data-toggle="collapse" data-target="#collapse-slide" aria-expanded="false" aria-controls="collapse-slide">{{ __("{$lang}::a.example_commands") }} <i class="fas fa-angle-down position-relative" data-target="example-command"></i></button>
            <div class="collapse border rounded my-2 p-4" id="collapse-slide">
                <p>php artisan make:module Name --all <span class="font-weight-light text-secondary">({{ __("{$lang}::a.make:module") . __("{$lang}::a.module_add_text") }})</span></p>
                <p>php artisan make:controller NameController <span class="font-weight-light text-secondary">({{ __("{$lang}::a.make:controller") }} Name)</span></p>
                <p>php artisan make:model Name <span class="font-weight-light text-secondary">({{ __("{$lang}::a.make:model") }} Name)</span></p>
                <p>php artisan make:model Helpers/Name <span class="font-weight-light text-secondary">({{ __("{$lang}::a.Create") . __("{$lang}::a.model") }} Name{{ __("{$lang}::a.in") }}app/Helpers)</span></p>
                <p>php artisan make:model Name -m -c <span class="font-weight-light text-secondary">({{ __("{$lang}::a.Create") . __("{$lang}::a.controller") }},{{ __("{$lang}::a.model") }},{{ __("{$lang}::a.migration_") }})</span></p>
                <p>php artisan make:controller Admin/NameController --resource <span class="font-weight-light text-secondary">({{ __("{$lang}::a.Create") . __("{$lang}::a.controller") }} Name{{ __("{$lang}::a.for") }}CRUD)</span></p>
                <p>php artisan make:controller NameController --resource --model=Name <span class="font-weight-light text-secondary">({{ __("{$lang}::a.Create") . __("{$lang}::a.controller") . __("{$lang}::a.for") }}CRUD{{ __("{$lang}::a.with_model") }})</span></p>
                <p>php artisan make:middleware Name <span class="font-weight-light text-secondary">({{ __("{$lang}::a.make:middleware") }} Name)</span></p>
                <br>
                <p>php artisan migrate <span class="font-weight-light text-secondary">({{ __("{$lang}::a.migrate") }})</span></p>
                <p>php artisan migrate:rollback <span class="font-weight-light text-secondary">({{ __("{$lang}::a.migrate:rollback") }})</span></p>
                <p>php artisan make:migration create_names_table --create=names <span class="font-weight-light text-secondary">({{ __("{$lang}::a.create_migration_table") }} names)</span></p>
                <p>php artisan make:migration change_names_table --table=names <span class="font-weight-light text-secondary">({{ __("{$lang}::a.change_migration_keep_data_table") }})</span></p>
                <p>php artisan make:migration add_ip_columns_to_users_table --table=users <span class="font-weight-light text-secondary">({{ __("{$lang}::a.change_add_column_migration_keep_data_table") }})</span></p>
                <p>php artisan make:import ProductsImport --model=Product <span class="font-weight-light text-secondary">({{ __("{$lang}::a.creating_model_to_import") }})</span></p>
                <p class="mb-0">php artisan make:export ProductsExport --model=Product <span class="font-weight-light text-secondary">({{ __("{$lang}::a.creating_model_to_export") }})</span></p>
            </div>
        </div>
    </div>
@endsection
