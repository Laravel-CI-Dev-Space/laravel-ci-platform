@props(['items' => []])

@if(count($items))
  <nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
      @foreach($items as $item)
        @if($loop->last)
          <li class="breadcrumb-item active" aria-current="page">{{ $item['label'] }}</li>
        @else
          <li class="breadcrumb-item">
            @if(isset($item['href']))
              <a href="{{ $item['href'] }}">{{ $item['label'] }}</a>
            @else
              {{ $item['label'] }}
            @endif
          </li>
        @endif
      @endforeach
    </ol>
  </nav>
@endif
