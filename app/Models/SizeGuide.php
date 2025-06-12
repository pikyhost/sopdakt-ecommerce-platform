<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SizeGuide extends Model
{
    use HasFactory;

    protected $fillable = [
        'image_path',
        'size_id',
        'min_height',
        'max_height',
        'min_weight',
        'max_weight',
        'min_age',
        'max_age',
        'min_shoulder_width',
        'max_shoulder_width',
        'bust_measurement',
        'length_measurement',
    ];

    protected $casts = [
        'min_height' => 'integer',
        'max_height' => 'integer',
        'min_weight' => 'integer',
        'max_weight' => 'integer',
        'min_age' => 'integer',
        'max_age' => 'integer',
        'min_shoulder_width' => 'integer',
        'max_shoulder_width' => 'integer',
        'bust_measurement' => 'integer',
        'length_measurement' => 'integer',
    ];

    public function size(): BelongsTo
    {
        return $this->belongsTo(Size::class);
    }

    /**
     * Check if the given measurements fit within this size guide's ranges
     */
    public function fitsUser(int $height, int $weight, ?int $age, int $shoulderWidth): bool
    {
        // Check height range
        if ($this->min_height && $height < $this->min_height) {
            return false;
        }
        if ($this->max_height && $height > $this->max_height) {
            return false;
        }

        // Check weight range
        if ($this->min_weight && $weight < $this->min_weight) {
            return false;
        }
        if ($this->max_weight && $weight > $this->max_weight) {
            return false;
        }

        // Check age range (if provided)
        if ($age !== null) {
            if ($this->min_age && $age < $this->min_age) {
                return false;
            }
            if ($this->max_age && $age > $this->max_age) {
                return false;
            }
        }

        // Check shoulder width range
        if ($this->min_shoulder_width && $shoulderWidth < $this->min_shoulder_width) {
            return false;
        }
        if ($this->max_shoulder_width && $shoulderWidth > $this->max_shoulder_width) {
            return false;
        }

        return true;
    }

    /**
     * Calculate a fit score for how well the user matches this size
     * Higher score means better fit
     */
    public function calculateFitScore(int $height, int $weight, ?int $age, int $shoulderWidth): float
    {
        $score = 0;
        $factors = 0;

        // Height score
        if ($this->min_height && $this->max_height) {
            $heightMid = ($this->min_height + $this->max_height) / 2;
            $heightRange = $this->max_height - $this->min_height;
            $heightScore = 1 - (abs($height - $heightMid) / max($heightRange, 1));
            $score += max(0, $heightScore);
            $factors++;
        }

        // Weight score
        if ($this->min_weight && $this->max_weight) {
            $weightMid = ($this->min_weight + $this->max_weight) / 2;
            $weightRange = $this->max_weight - $this->min_weight;
            $weightScore = 1 - (abs($weight - $weightMid) / max($weightRange, 1));
            $score += max(0, $weightScore);
            $factors++;
        }

        // Shoulder width score
        if ($this->min_shoulder_width && $this->max_shoulder_width) {
            $shoulderMid = ($this->min_shoulder_width + $this->max_shoulder_width) / 2;
            $shoulderRange = $this->max_shoulder_width - $this->min_shoulder_width;
            $shoulderScore = 1 - (abs($shoulderWidth - $shoulderMid) / max($shoulderRange, 1));
            $score += max(0, $shoulderScore);
            $factors++;
        }

        // Age score (if provided)
        if ($age !== null && $this->min_age && $this->max_age) {
            $ageMid = ($this->min_age + $this->max_age) / 2;
            $ageRange = $this->max_age - $this->min_age;
            $ageScore = 1 - (abs($age - $ageMid) / max($ageRange, 1));
            $score += max(0, $ageScore);
            $factors++;
        }

        return $factors > 0 ? $score / $factors : 0;
    }
}
