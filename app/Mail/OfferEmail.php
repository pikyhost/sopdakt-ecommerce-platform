<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OfferEmail extends Mailable
{
    use SerializesModels;

    public $user;
    public $data;

    public function __construct($user, array $data)
    {
        $this->user = $user;
        $this->data = $data;
    }

    public function build()
    {
        return $this->subject('Special Offer for You!')
            ->view('emails.offer')
            ->with([
                'user' => $this->user,
                'data' => $this->data,
                'discount' => isset($this->data['discount_id']) ? \App\Models\Discount::find($this->data['discount_id']) : null,
                'product' => isset($this->data['product_id']) ? \App\Models\Product::find($this->data['product_id']) : null,
                'blog' => isset($this->data['blog_id']) ? \App\Models\Blog::find($this->data['blog_id']) : null,
            ]);
    }
}
