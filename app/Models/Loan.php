<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'amount',  'loan_term', 'repayment_frequency', 'repayment_amount', 'interest_rate', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function repayment()
    {
        return $this->hasMany(Repayment::class);
    }

    public function finalLoanAmount()
    {
        return $this->amount + ($this->amount * $this->duration * $this->interest_rate) / 100;
    }
}
