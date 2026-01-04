<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Coordinator Dashboard</title>
	@vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
<div class="d-flex">
	<!-- Sidebar -->
	<aside class="sidebar d-flex flex-column p-3 text-white" style="width: 260px;">
		<div class="d-flex align-items-center mb-4">
			<div class="bg-warning text-dark fw-bold rounded-circle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">R</div>
			<div class="ms-3">
				<div class="fw-bold">ROC.PH</div>
				<small class="opacity-75">Coordinator Portal</small>
			</div>
		</div>

		<div class="d-flex align-items-center gap-3 bg-dark rounded-3 p-3 mb-3">
			<div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">MC</div>
			<div>
				<div class="fw-semibold">Mike Chen</div>
				<small class="opacity-75">Coordinator</small>
			</div>
		</div>

		<ul class="nav nav-pills flex-column mb-auto">
			<li class="nav-item">
				<a 
					href="#" 
					class="nav-link active d-flex align-items-center gap-2">
						<i class="bi bi-speedometer2"></i> Dashboard
				</a>
			</li>

			<li>
				<a 
					href="#" 
					class="nav-link d-flex align-items-center gap-2">
						<i class="bi bi-people"></i> Monitor Interns
				</a>
			</li>

			<li>
				<a 
					href="#" 
					class="nav-link d-flex align-items-center gap-2">
						<i class="bi bi-folder2"></i> Support Documents
				</a>
			</li>

			<li>
				<a 
					href="#" 
					class="nav-link d-flex align-items-center gap-2">
						<i class="bi bi-gear"></i> Settings
				</a>
			</li>
		</ul>

		<div class="mt-auto">
			<form method="POST" action="{{ route('logout') }}">
				@csrf
				<button 
					class="btn btn-outline-light w-100">
						<i class="bi bi-box-arrow-right me-2"></i>Logout
				</button>
			</form>
		</div>
	</aside>

	<!-- Main -->
	<main class="flex-grow-1 bg-light" style="min-height: 100vh;">
		<div class="container-fluid py-4">
			<div class="d-flex align-items-center justify-content-between mb-3">
				<h5 class="mb-0">Dashboard</h5>
				<div class="position-relative">
					<i class="bi bi-bell"></i>
					<span class="badge rounded-pill bg-danger position-absolute top-0 start-100 translate-middle">3</span>
				</div>
			</div>

			<!-- KPI cards -->
			<div class="row g-3 mb-3">
				<div class="col-sm-6 col-xl-3">
					<div class="card p-3">
						<div class="d-flex align-items-center gap-3">
							<div class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
								<i class="bi bi-person-badge fs-5"></i>
							</div>
							<div>
								<div class="fs-5 fw-bold">12</div>
								<small class="text-muted">Assigned Interns</small>
							</div>
						</div>
					</div>
				</div>
				<div class="col-sm-6 col-xl-3">
					<div class="card p-3">
						<div class="d-flex align-items-center gap-3">
							<div class="bg-success bg-opacity-10 text-success rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
								<i class="bi bi-graph-up-arrow fs-5"></i>
							</div>
							<div>
								<div class="fs-5 fw-bold">87%</div>
								<small class="text-muted">Avg Progress</small>
							</div>
						</div>
					</div>
				</div>
				<div class="col-sm-6 col-xl-3">
					<div class="card p-3">
						<div class="d-flex align-items-center gap-3">
							<div class="bg-warning bg-opacity-10 text-warning rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
								<i class="bi bi-file-earmark-text fs-5"></i>
							</div>
							<div>
								<div class="fs-5 fw-bold">24</div>
								<small class="text-muted">Support Docs</small>
							</div>
						</div>
					</div>
				</div>
				<div class="col-sm-6 col-xl-3">
					<div class="card p-3">
						<div class="d-flex align-items-center gap-3">
							<div class="bg-danger bg-opacity-10 text-danger rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
								<i class="bi bi-exclamation-circle fs-5"></i>
							</div>
							<div>
								<div class="fs-5 fw-bold">3</div>
								<small class="text-muted">Needs Support</small>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Tabs -->
			<ul class="nav nav-tabs mb-3">
				<li class="nav-item"><a class="nav-link active" href="#">Overview</a></li>
				<li class="nav-item"><a class="nav-link" href="#">Monitor Interns</a></li>
				<li class="nav-item"><a class="nav-link" href="#">Support Docs</a></li>
			</ul>

			<div class="row g-3">
				<!-- Needs Attention -->
				<div class="col-xl-7">
					<div class="card">
						<div class="card-body">
							<h6 class="fw-semibold mb-3">Needs Attention</h6>
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

				<!-- Recent Notes -->
				<div class="col-xl-5">
					<div class="card">
						<div class="card-body">
							<h6 class="fw-semibold mb-3">Recent Notes</h6>
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
		</div>
	</main>
</div>
</body>
</html>
