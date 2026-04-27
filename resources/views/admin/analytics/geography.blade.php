@extends('admin.layouts.app')

@section('title', 'Geographic Analytics')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0">
                <i class="fas fa-globe text-primary me-2"></i>Geographic Analytics
            </h1>
            <p class="text-muted mt-2">Track visitor distribution by country</p>
        </div>
        <div class="col-md-4 text-end">
            <form method="GET" class="d-inline-block">
                <select name="date_range" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="7" {{ $dateRange == 7 ? 'selected' : '' }}>Last 7 Days</option>
                    <option value="30" {{ $dateRange == 30 ? 'selected' : '' }}>Last 30 Days</option>
                    <option value="90" {{ $dateRange == 90 ? 'selected' : '' }}>Last 90 Days</option>
                    <option value="365" {{ $dateRange == 365 ? 'selected' : '' }}>Last Year</option>
                </select>
            </form>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header bg-warning text-dark">
            <h6 class="mb-0">Country Distribution Report</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 30%;">Country</th>
                            <th class="text-end" style="width: 25%;">Total Visits</th>
                            <th class="text-end" style="width: 25%;">Unique IPs</th>
                            <th class="text-end" style="width: 20%;">Unique Users</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($countries as $country)
                            <tr>
                                <td>
                                    @if($country->country === 'Local')
                                        <span class="badge bg-secondary">{{ $country->country }}</span>
                                    @elseif($country->country === 'Private Network')
                                        <span class="badge bg-warning text-dark">{{ $country->country }}</span>
                                    @else
                                        {{ $country->country ?? 'Unknown' }}
                                    @endif
                                </td>
                                <td class="text-end">
                                    <span class="badge bg-primary">{{ number_format($country->visit_count) }}</span>
                                </td>
                                <td class="text-end">
                                    <span class="badge bg-success">{{ number_format($country->unique_ips) }}</span>
                                </td>
                                <td class="text-end">
                                    <span class="badge bg-info">{{ number_format($country->unique_users) }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox mb-3" style="font-size: 2rem;"></i>
                                    <p>No geographic data recorded in this period</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($countries->hasPages())
                <div class="mt-4">
                    <nav>
                        <ul class="pagination justify-content-center">
                            {{ $countries->links() }}
                        </ul>
                    </nav>
                </div>
            @endif
        </div>
    </div>

    <!-- Back Button -->
    <div class="mt-4">
        <a href="{{ route('admin.analytics') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
        </a>
    </div>
</div>
@endsection
