<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HomePageSettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'main_slider' => [
                'image_url' => $this->getSlider1ImageUrl(),
                'thumbnail_url' => $this->getSlider1ThumbnailUrl(),
                'heading' => $this->getTranslations('main_heading'),
                'discount_text' => $this->getTranslations('discount_text'),
                'discount_value' => $this->getTranslations('discount_value'),
                'starting_price' => $this->starting_price,
                'currency_symbol' => $this->currency_symbol,
                'button_text' => $this->getTranslations('button_text'),
                'button_url' => $this->button_url,
            ],
            'second_slider' => [
                'image_url' => $this->getSlider2ImageUrl(),
                'thumbnail_url' => $this->getSlider2ThumbnailUrl(),
            ],
            'center_section' => [
                'image_url' => $this->getCenterImageUrl(),
                'heading' => $this->getTranslations('center_main_heading'),
                'button_text' => $this->getTranslations('center_button_text'),
                'button_url' => $this->center_button_url,
            ],
            'last_sections' => [
                [
                    'image_url' => $this->getLast1ImageUrl(),
                    'heading' => $this->getTranslations('last1_heading'),
                    'subheading' => $this->getTranslations('last1_subheading'),
                    'button_text' => $this->getTranslations('last1_button_text'),
                    'button_url' => $this->last1_button_url,
                ],
                [
                    'image_url' => $this->getLast2ImageUrl(),
                    'heading' => $this->getTranslations('last2_heading'),
                    'subheading' => $this->getTranslations('last2_subheading'),
                    'button_text' => $this->getTranslations('last2_button_text'),
                    'button_url' => $this->last2_button_url,
                ],
            ],
            'latest_section' => [
                'image_url' => $this->getLatestImageUrl(),
                'heading' => $this->getTranslations('latest_heading'),
                'button_text' => $this->getTranslations('latest_button_text'),
                'button_url' => $this->latest_button_url,
            ],
        ];
    }
}
