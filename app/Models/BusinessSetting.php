<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToUser;

class BusinessSetting extends Model
{
    use BelongsToUser;

    protected $fillable = [
        'user_id',
        'business_name',
        'business_type',
        'address',
        'phone',
        'tax_id',
        'logo_path',
        'currency',
        'receipt_footer',
        'default_table_count',
    ];

    /**
     * Get the business setting for the current user, creating defaults if needed.
     */
    public static function current(): self
    {
        $userId = auth()->id();

        return static::withoutGlobalScopes()
            ->firstOrCreate(
                ['user_id' => $userId],
                [
                    'business_name' => 'My Business',
                    'business_type' => 'sari_sari',
                    'currency' => 'PHP',
                ]
            );
    }

    public function isSariSari(): bool
    {
        return $this->business_type === 'sari_sari';
    }

    public function isRestaurant(): bool
    {
        return $this->business_type === 'restaurant';
    }
}
