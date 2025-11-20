<?php

namespace Database\Factories;

use App\Enums\RoomStatus;
use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Room>
 */
class RoomFactory extends Factory
{
    protected $model = Room::class;

    public function definition(): array
    {
        return [
            'name' => 'Operating Room ' . fake()->unique()->numerify('#'),
            'code' => fake()->unique()->bothify('OR-##'),
            'status' => RoomStatus::Available->value,
            'capacity' => fake()->numberBetween(1, 3),
            'notes' => fake()->sentence(),
        ];
    }
}
