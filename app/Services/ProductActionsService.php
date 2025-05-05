<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Filament\Pages\ProductAnalysis;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductColorSize;
use App\Models\ProductRating;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Mokhosh\FilamentRating\Components\Rating;

class ProductActionsService
{
    public static function rateProduct(Product $record, array $data): void
    {
        $userId = auth()->id();
        $recordId = $record->id;
        $newRating = intval($data['rating']); // Ensure integer rating

        // Check if the user is an admin or super_admin
        $isAdmin = auth()->user()->hasRole(['admin', 'super_admin']);

        // Set status based on role
        $status = $isAdmin ? 'approved' : 'pending';

        // Find the existing rating
        $existingRating = \App\Models\ProductRating::where('user_id', $userId)
            ->where('product_id', $recordId)
            ->first();

        // Avoid unnecessary updates if the rating and status are unchanged
        if ($existingRating && $existingRating->rating == $newRating && $existingRating->status == $status) {
            Notification::make()
                ->title(__('product.rating.already_saved'))
                ->body(__('product.rating.no_changes'))
                ->info()
                ->send();
            return;
        }

        // Update or create the rating
        \App\Models\ProductRating::updateOrCreate(
            ['user_id' => $userId, 'product_id' => $recordId],
            ['rating' => $newRating, 'status' => $status]
        );

        // Update the fake average rating only if there are approved ratings
        $averageRating = \App\Models\ProductRating::where('product_id', $recordId)
            ->where('status', 'approved')
            ->average('rating');

        // Update only if there's at least one approved rating
        if ($averageRating !== null) {
            $record->update(['fake_average_rating' => $averageRating]);
        }

        Notification::make()
            ->title(__('product.rating.thank_you'))
            ->body(__('product.rating.saved', ['rating' => $newRating]))
            ->success()
            ->send();
    }

    public static function getActions(): array
    {
        return [
            Action::make('view_product_details')
                ->label(__('product.actions.view_details'))
                ->color('gray')
                ->icon('heroicon-m-eye')
                ->openUrlInNewTab(true)
                ->action(fn (Product $record) => redirect(route('product.show', ['slug' => $record->slug]))),


            Action::make('add_to_cart')
                ->icon('heroicon-o-shopping-cart')
                ->color('primary')
                ->label(__('Add to Cart'))
                ->form([
                    Select::make('colorId')
                        ->live()
                        ->label(__('Color'))
                        ->options(fn (Product $record) => $record->productColors()
                            ->with('color') // Ensure we get the related color
                            ->get()
                            ->pluck('color.name', 'color_id'))
                        ->visible(fn (Product $record) => $record->productColors()->exists()),

                    Select::make('sizeId')
                        ->live()
                        ->label(__('Size'))
                        ->options(fn ($get) =>
                        $get('colorId')
                            ? ProductColorSize::whereHas('productColor', fn ($query) =>
                        $query->where('color_id', $get('colorId'))
                        )->with('size')->get()->pluck('size.name', 'size_id')
                            : []
                        )
                        ->visible(fn (Product $record) => $record->productColors()->exists()),

                    TextInput::make('quantity')
                        ->label(__('landing_page_order.quantity'))
                        ->numeric()
                        ->default(1)
                        ->required()
                        ->minValue(1)
                        ->rule(function (Product $record) {
                            return function (string $attribute, $value, $fail) use ($record) {
                                if ($record->must_be_collection && $value < 2) {
                                    $fail(__('This product requires a minimum quantity of 2.'));
                                } elseif ($value < 1) {
                                    $fail(__('Quantity must be at least 1.'));
                                }
                            };
                        }),
                ])
                ->action(function (Product $record, array $data) {
                    $user = Auth::user();
                    if (!$user) {
                        Notification::make()
                            ->danger()
                            ->title(__('You must be logged in to add items to the cart.'))
                            ->send();
                        return;
                    }

                    $quantity = (int) $data['quantity'];
                    $colorId = $data['colorId'] ?? null;
                    $sizeId = $data['sizeId'] ?? null;

                    if ($quantity < 1) {
                        Notification::make()
                            ->danger()
                            ->title(__('Invalid quantity.'))
                            ->send();
                        return;
                    }

                    // ✅ Enforce minimum quantity of 2 if product is marked as collection
                    if ($record->must_be_collection && $quantity < 2) {
                        Notification::make()
                            ->danger()
                            ->title(__('This product must be added to the cart and ordered in a quantity of 2 or more.'))
                            ->send();
                        return;
                    }

                    $availableStock = $record->quantity;
                    if ($availableStock <= 0 || $quantity > $availableStock) {
                        Notification::make()
                            ->danger()
                            ->title(__('Not enough stock available!'))
                            ->send();
                        return;
                    }

                    $pricePerUnit = (float) $record->discount_price_for_current_country;
                    $cart = Cart::firstOrCreate(['user_id' => $user->id]);

                    $cartItem = CartItem::where('cart_id', $cart->id)
                        ->where('product_id', $record->id)
                        ->where('size_id', $sizeId)
                        ->where('color_id', $colorId)
                        ->first();

                    if ($cartItem) {
                        $newQuantity = $cartItem->quantity + $quantity;

                        // Also check combined quantity for collections
                        if ($record->must_be_collection && $newQuantity < 2) {
                            Notification::make()
                                ->danger()
                                ->title(__('This product must be ordered in quantity of 2 or more.'))
                                ->send();
                            return;
                        }

                        if ($newQuantity > $availableStock) {
                            Notification::make()
                                ->danger()
                                ->title(__('Not enough stock available!'))
                                ->send();
                            return;
                        }

                        $cartItem->update([
                            'quantity' => $newQuantity,
                            'subtotal' => $newQuantity * $pricePerUnit,
                        ]);
                    } else {
                        CartItem::create([
                            'cart_id' => $cart->id,
                            'product_id' => $record->id,
                            'size_id' => $sizeId,
                            'color_id' => $colorId,
                            'quantity' => $quantity,
                            'price_per_unit' => $pricePerUnit,
                            'subtotal' => $quantity * $pricePerUnit,
                        ]);
                    }

                    $record->decrement('quantity', $quantity);
                    Notification::make()
                        ->success()
                        ->title(__('Product added to cart successfully!'))
                        ->send();
                }),

            Action::make('analyze')
                ->hidden(function () {
                        if (auth()->check()) {
                            return auth()->user()->hasRole(UserRole::Client->value);
                        }

                        return false;
                })
                ->color('primary')
                ->icon('heroicon-o-chart-bar')
                ->label(__('Detailed Analysis'))
                ->url(fn (Product $record): string => ProductAnalysis::getUrl([
                    'product' => $record->slug,
                    'from' => now()->subMonth()->toDateString(),  // temporary test
                    'to' => now()->toDateString(),                // temporary test
                ]))
                ->openUrlInNewTab(),

            Action::make('rate_and_comment')
                ->color('primary')
                ->visible(fn () => auth()->check())
                ->label(__('product.actions.rate_and_review'))
                ->icon('heroicon-o-star')
                ->modalHeading(fn ($record) => __('product.rating.modal_heading', ['product' => $record->name]))
                ->modalSubmitActionLabel(__('product.rating.confirm'))
                ->form([
                    Rating::make('rating')
                        ->color('warning')
                        ->required()
                        ->label(__('product.rating.your_rating'))
                        ->default(fn ($record) =>
                        ProductRating::where('product_id', $record->id)
                            ->where('user_id', Auth::id())
                            ->value('rating')
                        ),

                    Textarea::make('comment')
                        ->rows(3)
                        ->hiddenLabel()
                        ->helperText(__('Add any notes (optional)'))
                        ->placeholder(__('comments.placeholder'))
                        ->default(fn ($record) =>
                        ProductRating::where('product_id', $record->id)
                            ->where('user_id', Auth::id())
                            ->value('comment')
                        ),
                ])
                ->action(fn ($record, array $data) => self::handleRateAndComment($record, $data))
        ];
    }

    public static function handleRateAndComment(Product $record, array $data): void
    {
        $userId = auth()->id();
        $recordId = $record->id;
        $newRating = intval($data['rating']);
        $comment = $data['comment'] ?? null;

        // Check if the user is an admin or super_admin
        $isAdmin = auth()->user()->hasRole(['admin', 'super_admin']);

        // Set status based on role
        $status = $isAdmin ? 'approved' : 'pending';

        // Save or update the rating & comment
        \App\Models\ProductRating::updateOrCreate(
            ['user_id' => $userId, 'product_id' => $recordId],
            ['rating' => $newRating, 'comment' => $comment, 'status' => $status]
        );

        // Update fake average rating only if there are approved ratings
        $averageRating = \App\Models\ProductRating::where('product_id', $recordId)
            ->where('status', 'approved')
            ->average('rating');

        if ($averageRating !== null) {
            $record->update(['fake_average_rating' => $averageRating]);
        }

        Notification::make()
            ->title(__('product.rating.thank_you'))
            ->body(__('product.rating.saved', ['rating' => $newRating]))
            ->success()
            ->send();
    }

}
