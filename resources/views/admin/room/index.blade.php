@extends('layouts.admin')
@section('room-active', 'active')
@section('page-title', 'Room Management')
@section('styles')
    <style>
        .room-avatar {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 18px;
        }

        .room-image {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            object-fit: cover;
        }

        .action-buttons {
            display: flex;
            flex-direction: row;
            gap: 8px;
            align-items: center;
            justify-content: flex-start;
            flex-wrap: nowrap;
        }

        .action-buttons .btn {
            flex-shrink: 0;
            min-width: auto;
            white-space: nowrap;
        }

        .table td:last-child {
            white-space: nowrap;
            width: 120px;
        }

        .status-badge {
            font-size: 11px;
            padding: 4px 8px;
        }

        .image-preview {
            max-width: 200px;
            max-height: 150px;
            border-radius: 8px;
            margin-top: 10px;
        }

        .current-image {
            max-width: 150px;
            max-height: 100px;
            border-radius: 8px;
        }

        .form-label.required::after {
            content: " *";
            color: red;
        }
    </style>
@endsection

@section('content')
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">All Rooms</h4>
            <div class="d-flex align-items-center">
                <form method="GET" action="{{ route('admin.room.index') }}" class="me-2">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Tìm kiếm phòng..."
                            value="{{ request('search') }}">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoomModal">
                    <i class="fas fa-plus"></i> Add Room
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Room</th>
                        <th>Description</th>
                        <th>Hourly Rate</th>
                        <th>Status</th>
                        <th>Bookings</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rooms as $room)
                        <tr>
                            <td>#{{ $room->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if ($room->image_url)
                                        <img src="{{ asset($room->image_url) }}" alt="{{ $room->name }}"
                                            class="room-image me-2">
                                    @else
                                        <div class="room-avatar me-2">
                                            {{ strtoupper(substr($room->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="fw-bold">{{ $room->name }}</div>
                                        <small class="text-muted">ID: {{ $room->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="max-width: 200px;">
                                    {{ Str::limit($room->description ?? 'N/A', 50) }}
                                </div>
                            </td>
                            <td>
                                <span class="fw-bold text-success">{{ number_format($room->hourly_rate, 2) }}Đ</span>
                                <small class="text-muted d-block">/hour</small>
                            </td>
                            <td>
                                <select class="form-select form-select-sm status-dropdown" data-id="{{ $room->id }}">
                                    <option value="active" {{ $room->status == 'active' ? 'selected' : '' }}>
                                        Active</option>
                                    <option value="maintenance" {{ $room->status == 'maintenance' ? 'selected' : '' }}>
                                        Maintenance</option>
                                    <option value="inactive" {{ $room->status == 'inactive' ? 'selected' : '' }}>
                                        Inactive</option>
                                </select>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $room->bookings_count }} bookings</span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-sm btn-outline-danger"
                                        onclick="deleteRoom({{ $room->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <button class="btn btn-sm btn-warning editRoomBtn" data-id="{{ $room->id }}"
                                        data-name="{{ $room->name }}" data-description="{{ $room->description }}"
                                        data-hourly_rate="{{ $room->hourly_rate }}" data-status="{{ $room->status }}"
                                        data-image_url="{{ $room->image_url }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No rooms found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $rooms->links() }}
        </div>

        <!-- Add Room Modal -->
        <div class="modal fade" id="addRoomModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.room.store') }}" method="POST" id="addRoomForm"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Add New Room</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label required">Room Name </label>
                                <input type="text" class="form-control" name="name" required
                                    placeholder="Nhập tên phòng">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" name="description" rows="3" placeholder="Mô tả phòng (tùy chọn)"></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label required">Hourly Rate </label>
                                <div class="input-group">
                                    <span class="input-group-text">Đ</span>
                                    <input type="number" class="form-control" name="hourly_rate" step="0.01"
                                        min="0" required placeholder="0.00">
                                    <span class="input-group-text">/hour</span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label required">Status </label>
                                <select class="form-select" name="status" required>
                                    <option value="active">Active</option>
                                    <option value="maintenance">Maintenance</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Room Image</label>
                                <input type="file" class="form-control" name="image" accept="image/*"
                                    id="addImageInput">
                                <small class="text-muted">Chọn file ảnh (jpg, png, gif). Tối đa 2MB</small>
                                <div id="addImagePreview"></div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Room
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Room Modal -->
        <div class="modal fade" id="editRoomModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form id="editRoomForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="room_id" id="edit-room-id">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Room</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Room Name *</label>
                                <input type="text" class="form-control" name="name" id="edit-room-name" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" name="description" id="edit-room-description" rows="3"></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Hourly Rate *</label>
                                <div class="input-group">
                                    <span class="input-group-text">Đ</span>
                                    <input type="number" class="form-control" name="hourly_rate" id="edit-room-rate"
                                        step="0.01" min="0" required>
                                    <span class="input-group-text">/hour</span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Status *</label>
                                <select class="form-select" name="status" id="edit-room-status" required>
                                    <option value="active">Active</option>
                                    <option value="maintenance">Maintenance</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Current Image</label>
                                <div id="currentImageContainer"></div>

                                <label class="form-label mt-3">Change Image (Optional)</label>
                                <input type="file" class="form-control" name="image" accept="image/*"
                                    id="editImageInput">
                                <small class="text-muted">Chọn file ảnh mới nếu muốn thay đổi. Tối đa 2MB</small>
                                <div id="editImagePreview"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Cập nhật</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Status Update Handler
        document.querySelectorAll('.status-dropdown').forEach(select => {
            select.addEventListener('change', function() {
                const roomId = this.dataset.id;
                const status = this.value;
                fetch(`/admin/room/${roomId}/status`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content'),
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            status
                        })
                    })
                    .then(res => res.json())
                    .then(data => alert(data.success ? 'Trạng thái đã được cập nhật' : 'Cập nhật thất bại'))
                    .catch(err => console.error('Error:', err));
            });
        });

        // Delete Room Function
        function deleteRoom(id) {
            if (confirm('Bạn có chắc muốn xóa phòng này?')) {
                fetch(`/admin/room/${id}/delete`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            alert('Đã xóa phòng thành công');
                            location.reload();
                        } else {
                            alert(data.message || 'Xóa thất bại');
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
        }

        // Image Preview for Add Modal
        document.getElementById('addImageInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('addImagePreview');

            if (file) {
                // Kiểm tra kích thước file (2MB = 2 * 1024 * 1024 bytes)
                if (file.size > 2 * 1024 * 1024) {
                    alert('File quá lớn! Vui lòng chọn file nhỏ hơn 2MB.');
                    e.target.value = '';
                    preview.innerHTML = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" class="image-preview img-thumbnail">`;
                };
                reader.readAsDataURL(file);
            } else {
                preview.innerHTML = '';
            }
        });

        // Image Preview for Edit Modal
        document.getElementById('editImageInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('editImagePreview');

            if (file) {
                // Kiểm tra kích thước file
                if (file.size > 2 * 1024 * 1024) {
                    alert('File quá lớn! Vui lòng chọn file nhỏ hơn 2MB.');
                    e.target.value = '';
                    preview.innerHTML = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" class="image-preview img-thumbnail">`;
                };
                reader.readAsDataURL(file);
            } else {
                preview.innerHTML = '';
            }
        });

        // Add Room Form Handler
        document.getElementById('addRoomForm').addEventListener('submit', function(e) {
            e.preventDefault();

            let form = e.target;
            let formData = new FormData(form);

            fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(async (res) => {
                    const data = await res.json();
                    if (!res.ok) {
                        throw data;
                    }
                    alert(data.message);
                    location.reload();
                })
                .catch(err => {
                    if (err.errors) {
                        let msg = Object.values(err.errors).flat().join("\n");
                        alert("Lỗi nhập liệu:\n" + msg);
                    } else {
                        alert(err.message || "Đã có lỗi xảy ra");
                    }
                });
        });

        // Edit Room Button Handler
        document.querySelectorAll('.editRoomBtn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const name = this.dataset.name;
                const description = this.dataset.description;
                const hourlyRate = this.dataset.hourly_rate;
                const status = this.dataset.status;
                const imageUrl = this.dataset.image_url;

                document.getElementById('edit-room-id').value = id;
                document.getElementById('editRoomForm').action = `/admin/room/${id}/update`;
                document.getElementById('edit-room-name').value = name;
                document.getElementById('edit-room-description').value = description || '';
                document.getElementById('edit-room-rate').value = hourlyRate;
                document.getElementById('edit-room-status').value = status;

                // Reset previews
                document.getElementById('editImagePreview').innerHTML = '';
                document.getElementById('editImageInput').value = '';

                // Show current image
                const currentImageContainer = document.getElementById('currentImageContainer');
                if (imageUrl) {
                    currentImageContainer.innerHTML = `
                        <img src="{{ asset('') }}${imageUrl}" class="current-image img-thumbnail" alt="Current room image">
                        <p class="text-muted mt-1">Ảnh hiện tại</p>
                    `;
                } else {
                    currentImageContainer.innerHTML = '<p class="text-muted">Chưa có ảnh</p>';
                }

                const modal = new bootstrap.Modal(document.getElementById('editRoomModal'));
                modal.show();
            });
        });

        // Edit Room Form Handler
        document.getElementById('editRoomForm').addEventListener('submit', function(e) {
            e.preventDefault();

            let form = e.target;
            let formData = new FormData(form);
            let roomId = form.querySelector('input[name="room_id"]').value;

            fetch(`/admin/room/${roomId}/update`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(async res => {
                    const data = await res.json();
                    if (!res.ok) throw data;

                    alert(data.message);
                    location.reload();
                })
                .catch(err => {
                    if (err.errors) {
                        alert(Object.values(err.errors).flat().join('\n'));
                    } else {
                        alert(err.message || 'Đã có lỗi xảy ra');
                    }
                });
        });
    </script>
@endsection
