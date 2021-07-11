<?php

namespace Database\Factories;

use App\Entities\GameType;
use App\Entities\Servers\Models\Server;
use App\Entities\Servers\Models\ServerCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Server::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->sentence(),
            'ip' => $this->faker->ipv4(),
            'port' => $this->faker->numberBetween(20, 8000),
            'display_order' => $this->faker->numberBetween(1, 15),
            'game_type' => $this->faker->randomElement(GameType::values()),
            'is_port_visible' => true,
            'is_visible' => true,
            'is_querying' => true,
        ];
    }

    public function hasCategory()
    {
        return $this->state(function (array $attributes) {
            return [
                'server_category_id' => ServerCategory::factory()->create()->getKey(),
            ];
        });
    }
}
