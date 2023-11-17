@if(Request::route()->getName() === 'voyager.insta-users.datatable')
    <a
        href="{{ route('show-parse-users') }}"
        class="btn btn-success btn-add-new"
        data-loading-text="<i class='voyager-wand'></i> <span class='hidden-xs hidden-sm'>Download All...</span>"
    >
    <i class="voyager-pirate-hat"></i> <span> Parse</span>
    </a>
@endif





