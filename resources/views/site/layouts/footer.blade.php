

<!-- Google AdSense Script -->
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-5263422114514132"
     crossorigin="anonymous"></script>

<!-- Fluid Responsive Ad -->
<ins class="adsbygoogle"
     style="display:block"
     data-ad-format="fluid"
     data-ad-layout-key="-i8-o+l-4h+dj"
     data-ad-client="ca-pub-5263422114514132"
     data-ad-slot="1645527264"></ins>

<script>
     (adsbygoogle = window.adsbygoogle || []).push({});
</script>
@php
$footerWidgets = data_get($widgets, \Modules\Widget\Enums\WidgetLocation::FOOTER, []);
@endphp

@include('site.partials.ads', ['ads' => $footerWidgets])

@if(data_get(activeTheme(), 'options.footer_style') == 'footer_1')
    @include('site.layouts.footer.style_1', ['footerWidgets' => $footerWidgets])
@elseif(data_get(activeTheme(), 'options.footer_style') == 'footer_2')
    @include('site.layouts.footer.style_2', ['footerWidgets' => $footerWidgets])
@elseif(data_get(activeTheme(), 'options.footer_style') == 'footer_3')
    @include('site.layouts.footer.style_3', ['footerWidgets' => $footerWidgets])
@endif
