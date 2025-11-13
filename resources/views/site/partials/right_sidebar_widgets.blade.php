@php
    use Modules\Widget\Enums\WidgetLocation;

    $rightWidgets = data_get($widgets, WidgetLocation::RIGHT_SIDEBAR, []);

    // ✅ Reorder: popular_post first, categories second, then others
    usort($rightWidgets, function ($a, $b) {
        $order = ['popular_post', 'categories'];

        $aIndex = array_search($a['view'], $order);
        $bIndex = array_search($b['view'], $order);

        // If one of them is in the priority list, it comes first
        if ($aIndex !== false && $bIndex === false) return -1;
        if ($aIndex === false && $bIndex !== false) return 1;
        if ($aIndex !== false && $bIndex !== false) return $aIndex <=> $bIndex;

        // Otherwise, keep original order
        return 0;
    });
@endphp

@foreach($rightWidgets as $widget)
    @php
        $viewFile = 'site.widgets.' . $widget['view'];
    @endphp
    @if(view()->exists($viewFile))
        @include($viewFile, $widget)
    @endif
@endforeach
