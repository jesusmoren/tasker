<?php

namespace App\Models;

use App\Models\TaskGroup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;

    protected $table = 'tasks';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public $status = [
        'pending' => 'Pendiente',
        'done' => 'Completado',
    ];

    public function getStatus($key)
    {
        return $this->status[$key];
    }

    public function generateCode()
    {
        $this->code = 'TSK' . str_pad($this->id, 7, 0, STR_PAD_LEFT);
        $this->save();
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(TaskGroup::class);
    }
}
