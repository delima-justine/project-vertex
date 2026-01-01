@extends('layouts.hr')

@section('header', 'Job Postings')

@section('content')
    <div class="mb-6 flex justify-end">
        <a href="{{ route('hr.job-postings.create') }}" class="btn btn-primary">+ Post New Job</a>
    </div>

    <div style="background:#fff; border-radius:10px; padding:18px;">
        <div style="display:flex; flex-direction:column; gap:14px">
            @forelse($jobPostings as $job)
                <div style="display:flex; justify-content:space-between; align-items:center; padding:16px; border:1px solid #eef2f5; border-radius:8px; background:#fff">
                    <div>
                        <div style="font-weight:600; margin-bottom:6px">{{ $job->title }}</div>
                        <div style="color:#6b7280; font-size:13px">{{ $job->department }}</div>
                        <div style="margin-top:10px; color:#6b7280; font-size:13px">{{ $job->applications_count ?? 0 }} applications · Posted {{ $job->post_date ? $job->post_date->diffForHumans() : '—' }}</div>
                    </div>
                    <div style="display:flex; align-items:center; gap:12px">
                        <div class="px-2.5 py-1.5 rounded-full text-xs font-semibold {{ $job->is_active ? 'text-[#05332f] bg-[#bff3df]' : 'text-gray-500 bg-slate-100' }}">
                            {{ $job->is_active ? 'Active' : 'Closed' }}
                        </div>
                    </div>
                </div>
            @empty
                <div style="padding:18px; color:#6b7280">No job postings yet.</div>
            @endforelse
        </div>
    </div>

@endsection
