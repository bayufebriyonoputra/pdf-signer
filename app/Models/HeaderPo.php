<?php

namespace App\Models;

use App\Enum\StatusEnum;
use App\Enum\JenisTransaksiEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HeaderPo extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $casts = [
        'status' => StatusEnum::class,
        'jenis_transaksi' => JenisTransaksiEnum::class
    ];

    public function approverPertama(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_1', 'id');
    }

    public function approverKedua(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_2', 'id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }

    public function setDueDateAttribute($value)
    {
        $this->attributes['due_date'] = Carbon::parse($value)->format('Y-m-d');
    }


}
