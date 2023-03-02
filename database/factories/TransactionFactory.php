<?php

namespace Database\Factories;

use App\Enums\TransactionStatus;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $card = $this->faker->numberBetween(1000, 9999);
        $merchant = $this->faker->company();

        return [
            'amount' => $this->faker->randomFloat(2, 0, 1000),
            'status' => TransactionStatus::PENDING(),
            'user_id' => 1,
            'source_id' => 1,
            'currency' => $this->faker->currencyCode(),
            'to' => $merchant,
            'from' => $card,
            'merchant' => $merchant,
            'memo' => $this->faker->sentence(),
            'card' => $card,
            'method' => $this->faker->creditCardType(),
            'payment_date' => now(),
            'uuid' => Str::uuid(),
        ];
    }
}
