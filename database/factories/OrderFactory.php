<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Contact;
use App\Models\PaymentMethod;
use App\Models\Coupon;
use App\Models\ShippingType;
use App\Models\Country;
use App\Models\Governorate;
use App\Models\City;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::inRandomOrder()->first();
        $contact = Contact::inRandomOrder()->first();
        $paymentMethod = PaymentMethod::inRandomOrder()->first();
        $coupon = Coupon::inRandomOrder()->first();
        $shippingType = ShippingType::inRandomOrder()->first();
        $country = Country::inRandomOrder()->first();
        $governorate = Governorate::inRandomOrder()->first();
        $city = City::inRandomOrder()->first();

        $subtotal = $this->faker->numberBetween(1000, 5000);
        $shippingCost = $this->faker->optional()->numberBetween(0, 500);
        $taxPercentage = $this->faker->randomElement([0, 5, 10]);
        $taxAmount = intval($subtotal * ($taxPercentage / 100));
        $total = $subtotal + $taxAmount + ($shippingCost ?? 0);

        return [
            'user_id' => $user?->id,
            'contact_id' => $contact?->id,
            'payment_method_id' => $paymentMethod?->id,
            'coupon_id' => $coupon?->id,
            'shipping_cost' => $shippingCost,
            'tax_percentage' => $taxPercentage,
            'tax_amount' => $taxAmount,
            'subtotal' => $subtotal,
            'total' => $total,
            'shipping_type_id' => $shippingType?->id,
            'country_id' => $country?->id,
            'governorate_id' => $governorate?->id,
            'city_id' => $city?->id,
            'status' => $this->faker->randomElement(['pending', 'preparing', 'shipping', 'delayed', 'refund', 'cancelled', 'completed']),
            'notes' => $this->faker->optional()->sentence(),
            'tracking_number' => $this->faker->optional()->uuid(),
            'shipping_status' => $this->faker->optional()->word(),
            'shipping_response' => $this->faker->optional()->json(),
        ];
    }
}
