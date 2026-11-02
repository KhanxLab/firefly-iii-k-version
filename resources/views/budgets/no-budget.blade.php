@extends('layout.v3.session')
@section('content')

    {{-- upper show-all instruction --}}
    @if(null !== ($periods ?? null) && count($periods) > 0)
        <div class="row">
            <div class="col-12 text-end">
                <p class="small text-center"><a href="{{ route('budgets.no-budget',['all']) }}">{{ __('firefly.showEverything') }}</a></p>
            </div>
        </div>
    @endif

    <div class="row">
        <div class="@if(null !== ($periods ?? null) && count($periods) > 0)col-12 @else col-lg-12 col-md-12 col-sm-12 @endif ">
            <div class="card mb-2">
                <div class="card-header">
                    <h3 class="card-title">{{ $subTitle }}</h3>
                </div>
                <div class="card-body">

                    @if(null !== ($periods ?? null) && count($periods) > 0)
                        <x-lists.groups-large :groups="$groups"  :show-category="true" :show-budget="false" />
                        <p>
                            <span class="bi bi-calendar"></span>
                            <a href="{{ route('budgets.no-budget-all') }}">{{ __('firefly.show_all_no_filter') }}</a>
                        </p>
                    @else
                        <x-lists.groups-large :groups="$groups"  :show-category="true" :show-budget="false" />
                        <p>
                            <span class="bi bi-calendar"></span>
                            <a href="{{ route('budgets.no-budget') }}">{{ __('firefly.show_the_current_period_and_overview') }}</a>
                        </p>
                    @endif
                </div>
            </div>
        </div>

        @if(null !== ($periods ?? null) && count($periods) > 0)
            <div class="col-12 order-first">
                <div class="ff-periods"><x-lists.periods :periods="$periods" /></div>
            </div>
        @endif

    </div>

    {{-- lower show-all instruction --}}
    @if(null !== ($periods ?? null) && count($periods) > 0)
        <div class="row">
            <div class="col-12 text-end">
                <p class="small text-center"><a href="{{ route('budgets.no-budget-all') }}">{{ __('firefly.showEverything') }}</a></p>
            </div>
        </div>
    @endif

@endsection
@section('scripts')
    @vite(['js/pages/generic-nodates.js'])
    {{-- required for groups.twig --}}
     <script src="v1/js/ff/list/groups.js?v={{ $FF_BUILD_TIME }}" nonce="{{ $JS_NONCE }}"></script>
@endsection
