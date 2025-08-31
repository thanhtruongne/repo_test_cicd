@extends('layouts.admin')
@section('banks-active', 'active')
@section('page-title', 'Bank Account Management')
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
            <h4 class="mb-0">All Bank Accounts</h4>
            <div class="d-flex align-items-center">
                <form method="GET" action="{{ route('admin.bank.accounts.index') }}" class="me-2">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Tìm kiếm"
                            value="{{ request('search') }}">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoomModal">
                    <i class="fas fa-plus"></i> Add
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Bank name</th>
                        <th>Account number</th>
                        <th>Account holder</th>
                        <th>Branch</th>
                        <th>Main</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($datas as $data)
                        <tr>
                            <td>#{{ $data->id }}</td>
                            <td>
                                <span class="fw-bold">{{ $data->bank_name }}</span>
                            </td>
                            <td>
                                 <span class="">{{ $data->account_number }}</span>
                            </td>
                            <td>
                               <span class="">{{ $data->account_holder }}</span>
                            </td>
                            <td>
                                 <div style="max-width: 200px;">
                                  <span class="">{{ $data->branch }}</span>
                                </div>
                            </td>
                            <td>
                               <div class="form-check form-switch">
                                    <input class="form-check-input onChangMain" value="1" data-id="{{ $data->id }}"  type="checkbox" id="toggleSwitch" @checked($data->main == 1)>
                                </div>
                            </td>
                            <td>
                                <select class="form-select form-select-sm status-dropdown" data-id="{{ $data->id }}">
                                    <option value="active" {{ $data->status == 'active' ? 'selected' : '' }}>
                                        Active</option>
                                    <option value="inactive" {{ $data->status == 'inactive' ? 'selected' : '' }}>
                                        Inactive</option>
                                </select>
                            </td>

                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-sm btn-outline-danger"
                                        onclick="deleteRoom({{ $data->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <button class="btn btn-sm btn-warning editRoomBtn"
                                        data-id="{{ $data->id }}"
                                        data-bank_name="{{ $data->bank_name }}"
                                        data-account_holder="{{ $data->account_holder }}"
                                        data-account_number="{{ $data->account_number }}"
                                        data-branch="{{ $data->branch }}"
                                        data-notes="{{ $data->notes }}"
                                        data-status="{{ $data->status }}"
                                        data-main="{{ $data->main }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No bank accounts found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $datas->links() }}
        </div>

        <!-- Add Room Modal -->
        <div class="modal fade" id="addRoomModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.bank.accounts.store') }}" method="POST" id="addRoomForm"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Add New Bank Accounts</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label required">Bank Name </label>
                                <input type="text" class="form-control" name="bank_name" required
                                    placeholder="Nhập tên ngân hàng">
                            </div>
                            <div class="mb-3">
                                <label class="form-label required">Account Number </label>
                                <input type="text" class="form-control" name="account_number" required
                                    placeholder="Nhập số tài khoản ngân hàng">
                            </div>

                              <div class="mb-3">
                                <label class="form-label required">Account Holder </label>
                                <input type="text" class="form-control" name="account_holder" required
                                    placeholder="Nhập tên chủ tài khoản.">
                            </div>

                              <div class="mb-3">
                                <label class="form-label">Branch </label>
                                <input type="text" class="form-control" name="branch"
                                    placeholder="Nhập chi nhánh ngân hàng (tùy chọn).">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Notes</label>
                                <textarea class="form-control" name="notes" rows="3" placeholder="Ghi chú (tùy chọn)"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label required">Status </label>
                                <select class="form-select" name="status" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>

                             <div class="form-check mb-3">
                                <input class="form-check-input" name="main" type="checkbox" value="1" id="flexCheckChecked">
                                <label class="form-check-label" for="flexCheckChecked">
                                    Main
                                </label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create
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
                    <input type="hidden" name="id" id="edit-bank">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Bank</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                             <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label required">Bank Name </label>
                                <input type="text" class="form-control" name="bank_name" id="edit-bank-name" required
                                    placeholder="Nhập tên ngân hàng">
                            </div>
                            <div class="mb-3">
                                <label class="form-label required">Account Number </label>
                                <input type="text" class="form-control" name="account_number" required   id="edit-account-number"
                                    placeholder="Nhập số tài khoản ngân hàng">
                            </div>

                              <div class="mb-3">
                                <label class="form-label required">Account Holder </label>
                                <input type="text" class="form-control" name="account_holder" required   id="edit-account-holder"
                                    placeholder="Nhập tên chủ tài khoản.">
                            </div>

                              <div class="mb-3">
                                <label class="form-label">Branch </label>
                                <input type="text" class="form-control" name="branch"  id="edit-branch"
                                    placeholder="Nhập chi nhánh ngân hàng (tùy chọn).">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Notes</label>
                                <textarea class="form-control" name="notes" rows="3" id="edit-notes" placeholder="Ghi chú (tùy chọn)"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label required">Status </label>
                                <select class="form-select" name="status"  id="edit-status" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>

                             <div class="form-check mb-3">
                                <input class="form-check-input" name="main" value="1" type="checkbox" id="edit-main">
                                <label class="form-check-label" for="edit-main">
                                    Main
                                </label>
                            </div>
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
                const id = this.dataset.id;
                const status = this.value;
                fetch(`/admin/bank/status/${id}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            status
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        alert(data.success ? 'Trạng thái đã được cập nhật' : 'Cập nhật thất bại')
                        location.reload()
                    } )
                    .catch(err => console.error('Error:', err));
            });
        });
        document.querySelectorAll('.onChangMain').forEach(select => {
            select.addEventListener('change', function() {
                const id = this.dataset.id;
                const main = this.checked ? 1 : 0;
                fetch(`/admin/bank/main/${id}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            main
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        alert(data.success ? 'Trạng thái đã được cập nhật' : 'Cập nhật thất bại')
                        location.reload()
                    } )
                    .catch(err => console.error('Error:', err));
            });
        });



        // Delete Room Function
        function deleteRoom(id) {
            if (confirm('Bạn có chắc muốn xóa bank account này?')) {
                fetch(`/admin/bank/delete/${id}`, {
                        method: 'DELETE',
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
                const name = this.dataset.bank_name;
                const account_holder = this.dataset.account_holder;
                const account_number = this.dataset.account_number;
                const status = this.dataset.status;
                const notes = this.dataset.notes;
                const main = this.dataset.main;
                const branch = this.dataset.branch;
                console.log(this.dataset,main == '1');

                document.getElementById('edit-bank').value = id;
                document.getElementById('editRoomForm').action = `/admin/bank/update/${id}`;
                document.getElementById('edit-bank-name').value = name;
                document.getElementById('edit-account-number').value = account_holder;
                document.getElementById('edit-account-holder').value = account_number;
                document.getElementById('edit-notes').value = notes;
                document.getElementById('edit-branch').value = branch;
                document.getElementById('edit-status').value = status;
                document.getElementById('edit-main').checked = main == '1';

                const modal = new bootstrap.Modal(document.getElementById('editRoomModal'));
                modal.show();
            });
        });

        // Edit Room Form Handler
        document.getElementById('editRoomForm').addEventListener('submit', function(e) {
            e.preventDefault();

            let form = e.target;
            let formData = new FormData(form);
            let id = form.querySelector('input[name="id"]').value;

            fetch(`/admin/bank/update/${id}`, {
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
