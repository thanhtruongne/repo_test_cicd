<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class AdminBankAccount extends Model
{

    protected $table = 'admin_bank_accounts';
    protected $fillable = [
        'bank_name',
        'account_number',
        'account_holder',
        'branch',
        'status',
        'notes',
        'main',
    ];
}
