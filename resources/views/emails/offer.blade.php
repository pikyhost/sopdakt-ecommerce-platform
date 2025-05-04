<h2>Hello {{ $user->name }},</h2>

@if($data['type'] === 'discount' && $discount)
    <p>🎁 Enjoy this exclusive discount: <strong>{{ $discount->name }}</strong></p>
    <p>{{ $discount->description }}</p>
    <p>Valid until: {{ optional($discount->ends_at)->format('Y-m-d') }}</p>
@endif

@if($data['type'] === 'product' && $product)
    <p>🛍️ Check out our new product: <strong>{{ $product->name }}</strong></p>
    <p>{{ $product->description }}</p>
    <a href="{{ route('products.show', $product) }}">View Product</a>
@endif

@if($data['type'] === 'article' && $article)
    <p>📰 New article: <strong>{{ $article->title }}</strong></p>
    <p>{{ Str::limit(strip_tags($article->content), 150) }}</p>
    <a href="{{ route('articles.show', $article) }}">Read More</a>
@endif

@if($data['type'] === 'custom')
    <p>{{ $data['custom_message'] }}</p>
@endif

<p>Thanks,<br>Your Company</p>
