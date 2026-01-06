@extends('layouts.coordinator')

@section('header', 'Dashboard')

@section('content')
	<div class="row g-4 mb-4">
		<!-- Assigned Interns Card -->
		<div class="col-md-4">
			<div class="card h-100 border-start border-4 border-primary shadow-sm">
				<div class="card-body d-flex justify-content-between align-items-start">
					<div>
						<p class="text-uppercase text-muted fw-bold small mb-1">Assigned Interns</p>
						<h3 class="fw-bold mb-0">{{ $totalInterns }}</h3>
						<small class="text-muted">
							<span class="text-success">{{ $activeInterns }} active</span>
						</small>
					</div>
					<div class="bg-primary bg-opacity-10 rounded p-2 text-primary">
						<i class="bi bi-person-badge fs-4"></i>
					</div>
				</div>
			</div>
		</div>

		<!-- Average Progress Card -->
		<div class="col-md-4">
			<div class="card h-100 border-start border-4 border-success shadow-sm">
				<div class="card-body d-flex justify-content-between align-items-start">
					<div>
						<p class="text-uppercase text-muted fw-bold small mb-1">Avg Progress</p>
						<h3 class="fw-bold mb-0">{{ $avgProgress }}%</h3>
						<small class="text-muted">Overall completion rate</small>
					</div>
					<div class="bg-success bg-opacity-10 rounded p-2 text-success">
						<i class="bi bi-graph-up-arrow fs-4"></i>
					</div>
				</div>
			</div>
		</div>

		<!-- Pending Documents Card -->
		<div class="col-md-4">
			<div class="card h-100 border-start border-4 border-warning shadow-sm">
				<div class="card-body d-flex justify-content-between align-items-start">
					<div>
						<p class="text-uppercase text-muted fw-bold small mb-1">Pending Documents</p>
						<h3 class="fw-bold mb-0">{{ $pendingDocuments }}</h3>
						<small class="text-muted">Awaiting verification</small>
					</div>
					<div class="bg-warning bg-opacity-10 rounded p-2 text-warning">
						<i class="bi bi-file-earmark-text fs-4"></i>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Recent Activity Section -->
	<div class="row g-4">
		<div class="col-12">
			<div class="card shadow-sm">
				<div class="card-body p-4">
					<div class="d-flex justify-content-between align-items-center mb-3">
						<h5 class="card-title fw-bold mb-0">Recent Activity</h5>
						<a href="{{ route('coordinator.monitor-interns') }}" class="btn btn-sm btn-outline-primary">
							View All Interns <i class="bi bi-arrow-right ms-1"></i>
						</a>
					</div>

					@if($recentActivities->count() > 0)
						<div class="list-group list-group-flush">
							@foreach($recentActivities as $activity)
								<div class="list-group-item px-0">
									<div class="d-flex align-items-start gap-3">
										<div class="bg-{{ $activity['color'] }} bg-opacity-10 rounded p-2 text-{{ $activity['color'] }}">
											<i class="bi bi-{{ $activity['icon'] }} fs-5"></i>
										</div>
										<div class="flex-grow-1">
											<div class="d-flex justify-content-between align-items-start">
												<div>
													<div class="fw-semibold">{{ $activity['intern_name'] }}</div>
													<small class="text-muted">{{ $activity['description'] }}</small>
												</div>
												<small class="text-muted">{{ $activity['time']->diffForHumans() }}</small>
											</div>
										</div>
									</div>
								</div>
							@endforeach
						</div>
					@else
						<div class="text-center py-5">
							<div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
								<i class="bi bi-clock-history fs-1 text-muted"></i>
							</div>
							<p class="text-muted mb-0">No recent activity to display</p>
							<small class="text-muted">Activities from your interns will appear here</small>
						</div>
					@endif
				</div>
			</div>
		</div>
	</div>

	<!-- Quick Links Section -->
	<div class="row g-4 mt-2">
		<div class="col-md-6">
			<div class="card shadow-sm h-100">
				<div class="card-body p-4">
					<h6 class="fw-bold mb-3">
						<i class="bi bi-speedometer2 me-2 text-primary"></i>Quick Actions
					</h6>
					<div class="d-grid gap-2">
						<a href="{{ route('coordinator.monitor-interns') }}" class="btn btn-outline-primary text-start">
							<i class="bi bi-people me-2"></i>Monitor Interns
						</a>
						<a href="{{ route('coordinator.support-docs') }}" class="btn btn-outline-primary text-start">
							<i class="bi bi-folder me-2"></i>Support Documents
						</a>
						<a href="{{ route('coordinator.settings') }}" class="btn btn-outline-primary text-start">
							<i class="bi bi-gear me-2"></i>Settings
						</a>
					</div>
				</div>
			</div>
		</div>

		<div class="col-md-6">
			<div class="card shadow-sm h-100 border-start border-4 border-info">
				<div class="card-body p-4">
					<h6 class="fw-bold mb-3">
						<i class="bi bi-info-circle me-2 text-info"></i>System Information
					</h6>
					<div class="small">
						<div class="d-flex justify-content-between mb-2">
							<span class="text-muted">Your School:</span>
							<span class="fw-semibold">{{ Auth::user()->school ? Auth::user()->school->school_name : 'N/A' }}</span>
						</div>
						<div class="d-flex justify-content-between mb-2">
							<span class="text-muted">Role:</span>
							<span class="fw-semibold">{{ Auth::user()->user_role }}</span>
						</div>
						<div class="d-flex justify-content-between mb-2">
							<span class="text-muted">Total Interns:</span>
							<span class="fw-semibold">{{ $totalInterns }}</span>
						</div>
						<div class="d-flex justify-content-between">
							<span class="text-muted">Last Updated:</span>
							<span class="fw-semibold">{{ now()->format('M d, Y') }}</span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
