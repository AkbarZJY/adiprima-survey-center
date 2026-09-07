<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyDimension extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'color',
        'order',
    ];

    public function questions()
    {
        return $this->hasMany(SurveyQuestion::class, 'dimension_id');
    }

    public function questionTemplates()
    {
        return $this->hasMany(QuestionTemplate::class, 'dimension_id');
    }
}
