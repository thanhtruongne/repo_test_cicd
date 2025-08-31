<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminBankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class BankAccountController extends Controller
{
    public function index(Request $request)
    {
        $query = AdminBankAccount::whereNotNull('bank_name');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('bank_name', 'like', "%{$search}%")
                    ->orWhere('account_number', 'like', "%{$search}%")
                    ->orWhere('account_holder', 'like', "%{$search}%");
            });
        }

        $datas = $query->latest()->paginate(10);
        return view('admin.bankAccounts.index', compact('datas'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bank_name' => 'required',
            'account_number' => 'required|string',
            'account_holder' => 'required|string',
            'notes' => 'nullable|string',
            'branch' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'main' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }


        $bank = AdminBankAccount::create($request->all());


        if (!is_null($request->main) && $request->main == 1) {
            AdminBankAccount::where('id', '!=', $bank->id)->update([
                'main' => 0
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Tạo bank accounts thành công',
        ]);
    }

    public function updateStatus(AdminBankAccount $bank, Request $request)
    {
        $request->validate([
            'status' => 'required|in:active,inactive',
        ]);

        if ($bank->main == 1 && $request->status == 'inactive') {
            return response()->json(['success' => false, 'message' => 'Cannot change status in default main.']);
        }
        $bank->update(['status' => $request->status]);

        return response()->json(['success' => true, 'message' => 'Trạng thái bank account đã được cập nhật']);
    }

    public function updateMain(AdminBankAccount $bank, Request $request)
    {
        $request->validate([
            'main' => 'required|in:1,0',
        ]);

        if ($bank->main == 1 && $request->main == 0) {
            return response()->json(['success' => false, 'message' => 'Cannot change main in default main.']);
        }
        $bank->update(['main' => $request->main]);

        if ($request->main == 1) {
            AdminBankAccount::where('id', '!=', $bank->id)->update([
                'main' => 0
            ]);
        }


        return response()->json(['success' => true, 'message' => 'Đặt default bank account đã được cập nhật']);
    }



    public function update(AdminBankAccount $bank, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bank_name' => 'required',
            'account_number' => 'required|string',
            'account_holder' => 'required|string',
            'notes' => 'nullable|string',
            'branch' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'main' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        $main = $request->main;

        if ($bank->main == 1 && is_null($main)) {
            return response()->json(['message' => "At least have a default bank accounts", 'status' => true]);
        }

        $bank->update($request->all());

        if (!is_null($main) && $main == 1) {
            AdminBankAccount::where('id', '!=', $bank->id)->update([
                'main' => 0
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Update accounts thành công',
        ]);
    }

    public function destroy(AdminBankAccount $bank)
    {
        if ($bank->main == 1) {
            return response()->json([
                'status' => false,
                'message' => 'Cannot delete a default bank accounts.',
            ]);
        }
        $bank->delete();
        return response()->json([
            'success' => true,
            'message' => 'Delete accounts thành công',
        ]);
    }
}
