<?php

namespace Database\Factories;

use App\Enums\SourceType;
use App\Models\ConnectionLink;
use App\Models\Source;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConnectionLinkFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ConnectionLink::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => 1,
            'source_id' => 1,
            'destination_id' => 1,
            'active' => true,
        ];
    }
}
