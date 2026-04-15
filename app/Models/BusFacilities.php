<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name'])]
class BusFacilities extends Model
{
    /** @use HasFactory<\Database\Factories\BusFacilitiesFactory> */
    use HasFactory, HasUuids, SoftDeletes;
}
