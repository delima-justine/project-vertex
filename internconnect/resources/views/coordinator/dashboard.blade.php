@extends('layouts.coordinator')

@section('header', 'Dashboard')

@section('content')
	<div class="row g-4 mb-4">
		<div class="col-md-4">
			<div class="card h-100 border-start border-4 border-primary">
				<div class="card-body d-flex justify-content-between align-items-start">
					<div>
						<p class="text-uppercase text-muted fw-bold small mb-1">Assigned Interns</p>
						<h3 class="fw-bold mb-0">12</h3>
					</div>
					<div class="bg-light rounded p-2 text-primary">
						<i class="bi bi-person-badge fs-4"></i>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<div class="card h-100 border-start border-4 border-success">
				<div class="card-body d-flex justify-content-between align-items-start">
					<div>
						<p class="text-uppercase text-muted fw-bold small mb-1">Avg Progress</p>
						<h3 class="fw-bold mb-0">87%</h3>
					</div>
					<div class="bg-light rounded p-2 text-success">
						<i class="bi bi-graph-up-arrow fs-4"></i>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<div class="card h-100 border-start border-4 border-warning">
				<div class="card-body d-flex justify-content-between align-items-start">
					<div>
						<p class="text-uppercase text-muted fw-bold small mb-1">Support Docs</p>
						<h3 class="fw-bold mb-0">24</h3>
					</div>
					<div class="bg-light rounded p-2 text-warning">
						<i class="bi bi-file-earmark-text fs-4"></i>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row g-4">
		<div class="col-lg-7">
			<div class="card shadow-sm h-100">
				<div class="card-body p-4">
					<h5 class="card-title fw-bold mb-3">Needs Attention</h5>
					<div class="p-3 rounded-3 bg-warning bg-opacity-10 border">
						<div class="d-flex align-items-center justify-content-between">
							<div>
								<div class="fw-semibold">Grace Lee</div>
								<small class="text-muted">Design Intern</small>
							</div>
							<div class="text-muted">30%</div>
						</div>
						<div class="mt-3">
							<a href="#" class="btn btn-primary w-100">Provide Support</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-5">
			<div class="card shadow-sm h-100">
				<div class="card-body p-4">
					<h5 class="card-title fw-bold mb-3">Recent Notes</h5>
					<ul class="list-group list-group-flush">
						<li class="list-group-item">
							<div class="d-flex align-items-start gap-2">
								<span class="text-primary">•</span>
								<div>
									<div class="fw-semibold">Emma Davis</div>
									<small class="text-muted">Great progress on marketing campaign. Ready for next milestone. <span class="ms-1">1 day ago</span></small>
								</div>
							</div>
						</li>
						<li class="list-group-item">
							<div class="d-flex align-items-start gap-2">
								<span class="text-primary">•</span>
								<div>
									<div class="fw-semibold">Grace Lee</div>
									<small class="text-muted">Needs additional support with design tools. Scheduled training session. <span class="ms-1">2 days ago</span></small>
								</div>
							</div>
						</li>
						<li class="list-group-item">
							<div class="d-flex align-items-start gap-2">
								<span class="text-primary">•</span>
								<div>
									<div class="fw-semibold">Frank Miller</div>
									<small class="text-muted">Completed code review successfully. Moving to backend tasks. <span class="ms-1">3 days ago</span></small>
								</div>
							</div>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
@endsection
