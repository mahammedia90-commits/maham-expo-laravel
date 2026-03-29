<?php
namespace App\Events;
use App\Models\BusinessProfile;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class KycSubmitted
{
    use Dispatchable, SerializesModels;
    public function __construct(public BusinessProfile $profile) {}
}
