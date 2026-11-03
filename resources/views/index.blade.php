@extends('layout.v3.session')
@section('content')
    <!-- TODO internals modal voor pagina settings -->
    <!-- TODO wizard modal voor weet ik veel -->
    <x-dashboard.boxes :start="$start" :end="$end"/>

    <div class="row" x-data="index">
        {{-- Balance chart + accounts with their latest transactions --}}
        <div class="col-xl-8 col-lg-12">
            <div class="card card-outline">
                <div class="card-header">
                    <h2 class="card-title">{{ __('firefly.yourAccounts') }}</h2>
                    <a class="ff-card-link d-print-none" href="{{ route('accounts.index',['asset']) }}">{{ __('firefly.go_to_asset_accounts') }}</a>
                </div>
                <div class="card-body">
                    <canvas id="accounts-chart" class="wide-chart" height="400" width="100%"></canvas>
                </div>
            </div>

            <div class="card card-outline">
                <div class="card-header">
                    <h2 class="card-title">{{ __('firefly.budgetsAndSpending') }}</h2>
                    <a class="ff-card-link d-print-none" href="{{ route('budgets.index') }}">{{ __('firefly.go_to_budgets') }}</a>
                </div>
                <div class="card-body">
                    <canvas id="budgets-chart" class="wide-chart" height="400" width="100%"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-12" id="all_transactions">
            @foreach($transactions as $data)
                <div class="card card-outline ff-mini-account">
                    <div class="card-header">
                        <h2 class="card-title"><a href="{{ route('accounts.show', [$data['account']->id]) }}">{{ $data['account']->name }}</a></h2>
                        <div class="ms-auto d-print-none">
                            <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ account_balance($data['account']) }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('accounts.show', [$data['account']['id']]) }}">{{ __('firefly.show') }}</a></li>
                                <li><a class="dropdown-item" href="{{ route('accounts.reconcile', [$data['account']['id']]) }}">{{ __('firefly.reconcile') }}</a></li>
                                <li><a class="dropdown-item" href="{{ route('accounts.edit', [$data['account']['id']]) }}?_from={{ urlencode($FF3_FROM) }}">{{ __('firefly.edit') }}</a></li>
                                <li><a class="dropdown-item" href="{{ route('accounts.delete', [$data['account']['id']]) }}">{{ __('firefly.delete') }}</a></li>
                            </ul>
                        </div>
                    </div>
                    @if(count($data['transactions']) > 0)
                        <div class="card-body p-0">
                            <x-lists.groups-tiny :account="$data['account']" :transactions="$data['transactions']"/>
                        </div>
                    @endif
                    @if(0 === count($data['transactions']))
                        <div class="card-body">
                            <p class="text-muted mb-0">{{ __('firefly.no_transactions_account', ['name' => $data['account']['name']]) }}</p>
                        </div>
                    @endif
                    <div class="card-footer d-print-none">
                        <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <em class="bi bi-plus-lg" aria-hidden="true"></em>{{ __('firefly.sidebar_frontpage_create') }}
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('transactions.create', ['withdrawal']) }}?source={{ $data['account']['id'] }}">{{ __('firefly.create_new_withdrawal') }}</a></li>
                            <li><a class="dropdown-item" href="{{ route('transactions.create', ['deposit']) }}?destination={{ $data['account']['id'] }}">{{ __('firefly.create_new_deposit') }}</a></li>
                            <li><a class="dropdown-item" href="{{ route('transactions.create', ['transfer']) }}?source={{ $data['account']['id'] }}">{{ __('firefly.create_new_transfer') }}</a></li>
                        </ul>
                    </div>
                </div>
            @endforeach

            @if($billCount > 0)
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">{{ __('firefly.bills') }}</h2>
                        <a class="ff-card-link d-print-none" href="{{ route('subscriptions.index') }}">{{ __('firefly.go_to_bills') }}</a>
                    </div>
                    <div class="card-body">
                        <div class="center-chart">
                            <canvas id="bills-chart" class="low-chart" height="200"></canvas>
                        </div>
                    </div>
                </div>
            @endif

            <div id="piggy_bank_overview">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">{{ __('firefly.piggyBanks') }}</h2>
                        <a class="ff-card-link d-print-none" href="{{ route('piggy-banks.index') }}">{{ __('firefly.go_to_piggies') }}</a>
                    </div>
                    <div class="card-body">
                        <template x-if="loadingPiggyBanks">
                            <div class="text-center">
                                <div class="spinner-border spinner-border-sm" role="status">
                                    <span class="visually-hidden">{{ __('firefly.thinking') }}</span>
                                </div>
                            </div>
                        </template>
                        <template x-if="!loadingPiggyBanks && piggyBanks.length === 0">
                            <p class="text-muted mb-0">{{ __('firefly.no_piggies_intro_default') }}</p>
                        </template>
                        <template x-for="piggyBank in piggyBanks" :key="piggyBank.id">
                            <div class="ff-piggy">
                                <div class="ff-piggy-row">
                                    <a :href="'./piggy-banks/show/' + piggyBank.id" x-text="piggyBank.name"></a>
                                    <span x-text="piggyBank.amount"></span>
                                </div>
                                <div class="progress" role="progressbar" :aria-valuenow="piggyBank.percentage" aria-valuemin="0" aria-valuemax="100">
                                    <div class="progress-bar" :class="'w-'+piggyBank.percentage"></div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Where the money went and came from --}}
    <div class="row">
        <div class="col-xl-4 col-lg-6 col-md-12">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">{{ __('firefly.categories') }}</h2>
                    <a class="ff-card-link d-print-none" href="{{ route('categories.index') }}">{{ __('firefly.go_to_categories') }}</a>
                </div>
                <div class="card-body">
                    <canvas id="categories-chart" class="medium-chart" height="250" width="100%"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-6 col-md-12">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">{{ __('firefly.expense_accounts') }}</h2>
                    <a class="ff-card-link d-print-none" href="{{ route('accounts.index', ['expense']) }}">{{ __('firefly.go_to_expense_accounts') }}</a>
                </div>
                <div class="card-body">
                    <canvas id="expense-accounts-chart" class="medium-chart" height="250" width="100%"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-12 col-md-12">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">{{ __('firefly.revenue_accounts') }}</h2>
                    <a class="ff-card-link d-print-none" href="{{ route('accounts.index', ['revenue']) }}">{{ __('firefly.go_to_revenue_accounts') }}</a>
                </div>
                <div class="card-body">
                    <canvas id="revenue-accounts-chart" class="medium-chart" height="250" width="100%"></canvas>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('scripts')
    @vite(['js/pages/dashboard/dashboard.js'])

     <script nonce="{{ $JS_NONCE }}">
        var lineColor = '#b54708';
        var lineTextColor = '#0f172a';
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            lineColor = '#e8a33d';
            lineTextColor = '#e6eaf0';
        }
        var billCount = {{ $billCount }};
        var accountRevenueUrl = '{{ route('chart.account.revenue') }}';
        var accountExpenseUrl = '{{ route('chart.account.expense') }}';
    </script>

     <script src="v1/js/lib/Chart.bundle.min.js?v={{ $FF_BUILD_TIME }}"
            nonce="{{ $JS_NONCE }}"></script>
     <script src="v1/js/lib/chartjs-plugin-annotation.min.js?v={{ $FF_BUILD_TIME }}"
            nonce="{{ $JS_NONCE }}"></script>
     <script src="v1/js/ff/charts.defaults.js?v={{ $FF_BUILD_TIME }}"
            nonce="{{ $JS_NONCE }}"></script>
     <script src="v1/js/ff/charts.js?v={{ $FF_BUILD_TIME }}" nonce="{{ $JS_NONCE }}"></script>
     <script src="v1/js/ff/index.js?v={{ $FF_BUILD_TIME }}" nonce="{{ $JS_NONCE }}"></script>

@endsection
