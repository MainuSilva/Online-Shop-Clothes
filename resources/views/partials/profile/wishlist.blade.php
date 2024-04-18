@if(count($items_wishlist) === 0)
    <p class="text-center text-muted">Wishlist is empty... Add some products!</p>
@else
    @foreach($items_wishlist as $item)
        <div class="wishlist-item mb-3 d-flex align-items-center">                   
            @if($item->images()->first() && $item->images()->first()->filepath)
                <img src="{{ asset($item->images()->first()->filepath) }}" class="w-30 h-30" style="max-width:150px; max-height: 150px; border-radius: 10px; margin-left: 10px">
            @else
                <img src="{{ asset('images/default-product-image.png') }}" class="w-30 h-30" style="max-width:150px; max-height: 150px; border-radius: 10px; margin-left: 10px">
            @endif
            <div class="ms-2" style="max-width: 200px;">
                <h6>{{ $item->name }}</h6>
                <form method="POST" action="{{ url('users/wishlist/product/'.$item->id) }}">
                    @csrf
                    @method('delete')
                    <button class="btn btn-outline-danger btn-sm" type="submit">
                        <i class="fa fa-times"></i>
                        <span>Remove</span>
                    </button>
                </form>
            </div>
        </div>
        @if(!$loop->last)
            <hr class="my-2">
        @endif
    @endforeach
@endif
