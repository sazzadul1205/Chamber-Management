{{-- backend.roles.index --}}
@extends('backend.layout.structure')

@section('title')
    Roles
@endsection

@section('styles')
    <style>
        .role-card {
            transition: transform 0.3s, box-shadow 0.3s;
            border-radius: 12px;
            overflow: hidden;
            cursor: pointer;
            border: 1px solid #e0e0e0;
        }

        /* Hover effect */
        .role-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 25px rgba(79, 70, 229, 0.3);
            /* Indigo shadow */
            border-color: #4f46e5;
        }

        .role-card-body {
            padding: 1.5rem;
            text-align: center;
        }

        .role-icon {
            font-size: 2.8rem;
            color: #4f46e5;
            margin-bottom: 0.5rem;
        }

        .role-name {
            font-size: 1.25rem;
            font-weight: 600;
            color: #111827;
        }

        .role-subtitle {
            font-size: 0.9rem;
            color: #6b7280;
            /* Gray-500 */
            margin-top: 0.25rem;
        }
    </style>
@endsection

@section('content')
    <div class="px-4 py-4">
        <h1 class="mb-1">All Roles</h1>
        <p class="text-muted mb-4">
            List of all roles available in the system. Hover over a role to see details.
        </p>

        @if ($roles->isEmpty())
            <div class="alert alert-info">
                No roles found.
            </div>
        @else
            <div class="row g-4">
                @foreach ($roles as $role)
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <div class="card role-card shadow-sm">
                            <div class="card-body role-card-body">
                                <i class="bi bi-person-badge role-icon"></i>
                                <div class="role-name">{{ $role->name }}</div>
                                <div class="role-subtitle">
                                    {{ ucfirst($role->name) }} role in system
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

@endsection
