@extends('frontend.app')
@section('title', 'Delete Profile - Beauty App')
@push('style')
    <style>
        .profile-info {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
        }

        .btn {
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .alert {
            border-left: 4px solid #dc3545;
        }
    </style>
@endpush
@section('content')
    <section class="min-h-screen d-flex align-items-center justify-content-center h-100"
        style="background: #FF4A26 !important; min-height: 100vh;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8 col-12">
                    <div class="card shadow-lg border-0" style="border-radius: 20px;">
                        <div class="card-body p-5">
                            <!-- Header -->
                            <div class="text-center mb-4">
                                <div class="mb-3">
                                    <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 3rem;"></i>
                                </div>
                                <h2 class="fw-bold text-dark mb-2">Delete Your Profile</h2>
                            </div>

                            <!-- User Profile Information -->
                            <div class="profile-info bg-light rounded-3 p-4 mb-4">
                                <div class="d-flex align-items-center mb-3">
                                    <!-- Profile Image -->
                                    <div class="me-3">
                                        @if ($user->avatar)
                                            <img src="{{ asset($user->avatar) }}" alt="Profile Picture"
                                                class="rounded-circle border border-3 border-white shadow-sm"
                                                style="width: 80px; height: 80px; object-fit: cover;"
                                                onerror="this.src='{{ asset('backend/assets/images/profile.jpeg') }}'">
                                        @else
                                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold border border-3 border-white shadow-sm"
                                                style="width: 80px; height: 80px; font-size: 1.5rem;">
                                                {{ substr($user->name ?? 'U', 0, 1) }}
                                            </div>
                                        @endif
                                    </div>

                                    <!-- User Details -->
                                    <div class="flex-grow-1">
                                        <h5 class="mb-1 fw-bold text-dark">{{ $user->name ?? 'No Name Provided' }}</h5>
                                        <p class="mb-0 text-muted">{{ $user->email ?? 'No Email Provided' }}</p>
                                        <small class="text-muted">
                                            <i class="bi bi-calendar-check me-1"></i>
                                            Member since
                                            {{ $user->created_at ? $user->created_at->format('M Y') : 'Unknown' }}
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <!-- Warning Message -->
                            <div class="alert alert-danger border-0 mb-4" style="border-radius: 10px;">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-exclamation-circle-fill me-2 mt-1"></i>
                                    <div>
                                        <h6 class="alert-heading mb-1">Warning: Permanent Deletion</h6>
                                        <small>
                                            Deleting your profile will permanently remove all your data, including your
                                            account, preferences, and any associated content. This action cannot be undone.

                                        </small>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="row g-3">
                                <div class="col-6">
                                    <button type="button" class="btn btn-secondary w-100 py-2 fw-bold"
                                        style="border-radius: 10px;" onclick="closeWindow()">
                                        <i class="bi bi-arrow-left me-2"></i>
                                        Cancel
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button type="button" id="deleteProfileBtn" class="btn btn-danger w-100 py-2 fw-bold"
                                        style="border-radius: 10px;">
                                        <i class="bi bi-trash me-2"></i>
                                        Delete Profile
                                    </button>
                                </div>
                            </div>

                            <!-- Footer -->
                            <div class="text-center mt-4">
                                <small class="text-muted">
                                    Need help? Contact our support team
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
        data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 15px;">
                <div class="modal-body text-center p-4">
                    <div class="mb-3">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="mb-2">Profile Deleted Successfully</h5>
                    <p class="text-muted mb-3">Your account and all associated data have been permanently removed.</p>
                    <button type="button" class="btn btn-success px-4" style="border-radius: 10px;"
                        onclick="closeWindow()">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- @section('script') --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteBtn = document.getElementById('deleteProfileBtn');
            const successModal = new bootstrap.Modal(document.getElementById('successModal'));

            deleteBtn.addEventListener('click', function() {

                // Show confirmation dialog by SweetAlert2
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Are you absolutely sure you want to delete your profile? This action cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const token = deleteBtn.getAttribute('data-token');

                        // Make delete request
                        fetch(`/profile-deletion`, {
                                method: 'DELETE',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {

                                if (data.success) {
                                    setTimeout(() => {
                                        successModal.show();
                                    }, 300);
                                } else {
                                    Swal.fire('Error', data.message ||
                                        'Failed to delete profile. Please try again.',
                                        'error');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire('Error',
                                    'An error occurred while deleting your profile. Please try again.',
                                    'error');
                            });
                    }
                })
            });
        });

        function closeWindow() {
            window.open('', '_self');
            window.close();

            setTimeout(() => {
                if (!window.closed) {
                    history.back();
                }
            }, 200);
        }
    </script>

@endsection
