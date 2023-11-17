{{$route = ''}}
@if(Request::route()->getName() === 'voyager.hh-users.datatable')
    <a
        href="{{ route('hh-register') }}"
        class="btn btn-success btn-add-new quick-create"
        data-loading-text="<i class='voyager-wand'></i> <span class='hidden-xs hidden-sm'>Download All...</span>"
    >
        <i class="voyager-plus"></i> <span> Add user</span>
    </a>
    @php
        $route = route('hh-register');
    @endphp
@endif

@if(Request::route()->getName() === 'voyager.insta-bots.datatable')
    <a
        href="{{ route('insta-bots') }}"
        class="btn btn-success btn-add-new quick-create"
        data-loading-text="<i class='voyager-wand'></i> <span class='hidden-xs hidden-sm'>Download All...</span>"
    >
        <i class="voyager-plus"></i> <span> Add bot</span>
    </a>
    @php
        $route = route('insta-bots');
    @endphp
@endif

@if(Request::route()->getName() === 'voyager.insta-users.datatable')
    <a
        href="{{ route('insta-users') }}"
        class="btn btn-success btn-add-new quick-create"
        data-loading-text="<i class='voyager-wand'></i> <span class='hidden-xs hidden-sm'>Download All...</span>"
    >
        <i class="voyager-plus"></i> <span> Add user</span>
    </a>
    @php
        $route = route('insta-users');
    @endphp
@endif

@if(Request::route()->getName() === 'voyager.insta-posts.datatable')
    <a
        href="{{ route('show-like-random') }}"
        class="btn btn-success btn-add-new quick-update"
    >
        <i class="voyager-heart"></i> <span> Like</span>
    </a>
@endif

@if(Request::route()->getName() === 'voyager.insta-posts.datatable')
    <a
        href="{{ route('show-comment-random') }}"
        class="btn btn-success btn-add-new quick-update"
    >
        <i class="voyager-bubble"></i> <span> Comment</span>
    </a>
@endif

@if(Request::route()->getName() === 'voyager.insta-posts.datatable')
    <a
        href="{{ route('user-post-show') }}"
        class="btn btn-success btn-add-new quick-update"
    >
        <i class="voyager-file-text"></i> <span> Parse post</span>
    </a>
@endif

@if(Request::route()->getName() === 'voyager.insta-posts.datatable')
    <a
        href="{{ route('show-tag-search') }}"
        class="btn btn-success btn-add-new quick-update"
    >
        <i class="voyager-tag"></i> <span> Tag search</span>
    </a>
@endif

@if(Request::route()->getName() === 'voyager.insta-followers.datatable')
    <a
        href="{{ route('follow') }}"
        class="btn btn-success btn-add-new quick-update"
    >
        <i class="voyager-people"></i> <span> Follow</span>
    </a>
@endif
@if(Request::route()->getName() === 'voyager.insta-followers.datatable')
    <a
        href="{{ route('unfollow') }}"
        class="btn btn-success btn-add-new quick-update"
    >
        <i class="voyager-people"></i> <span> Unfollow</span>
    </a>
@endif
@if(Request::route()->getName() === 'voyager.insta-followers.datatable')
    <a
        href="{{ route('storyview') }}"
        class="btn btn-success btn-add-new quick-update"
    >
        <i class="voyager-eye"></i> <span> Story view</span>
    </a>
@endif
@if(Request::route()->getName() === 'voyager.insta-followers.datatable')
    <a
        href="{{ route('autoaccept') }}"
        class="btn btn-success btn-add-new quick-update"
    >
        <i class="voyager-people"></i> <span> Auto Accept</span>
    </a>
@endif

@include('joy-voyager-datatable::partials.quick-add-script', ['dataType' => $dataType, 'data' => null, 'dataId' => $dataId ?? null, 'route' => $route])



