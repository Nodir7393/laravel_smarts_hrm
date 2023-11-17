<a
    href="{{ route('hhparseAll') }}"
    class="btn btn-success btn-add-new quick-update"
    data-loading-text="<i class='voyager-wand'></i> <span class='hidden-xs hidden-sm'>Download All...</span>"
>
    <i class="voyager-download"></i> <span> Download All</span>
</a>

@include('joy-voyager-datatable::partials.quick-add-script', ['dataType' => $dataType, 'data' => null, 'dataId' => $dataId ?? null , 'route' => route('voyager.'.$dataType->slug.'.quick-create')])
