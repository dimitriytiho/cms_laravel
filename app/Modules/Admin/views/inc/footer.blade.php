<footer class="footer-block">
    <div class="row">
        <div class="col font-weight-light text-secondary text-right mt-4 py-2 px-4 footer">
            <small class="pr-4">&copy; {{ date('Y') }} {{ config('add.dev') }} | Laravel {{ App::version() }}</small>
        </div>
    </div>
</footer>

<div class="position-fixed" id="btn-up">
    <div class="d-flex justify-content-center align-items-center bg-primary text-white rounded-circle cur btn-up-click">
        <i class="fas fa-chevron-up btn-up-click"></i>
    </div>
</div>

<div class="modal fade" id="modal-confirm" tabindex="-1" role="dialog" aria-labelledby="modal-confirm" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title text-secondary">@lang("{$lang}::s.confirm")</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h5 class="mt-3 mb-4">@lang("{$lang}::a.you_sure")</h5>
                <div class="text-right">
                    <button type="button" class="btn btn-primary btn-pulse mr-1" data-dismiss="modal">@lang("{$lang}::s.cancel")</button>
                    <button type="button" class="btn btn-outline-primary btn-pulse">Ок</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="spinner">
    <div class="spinner-block">
        <div class="spinner-border" role="status">
            <span class="sr-only">Загрузка...</span>
        </div>
    </div>
</div>
