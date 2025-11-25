@if(!blank($ad))
    <div class="sg-ad">
        <div class="container">
            <div class="ad-content">
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

{{-- 
                @if(data_get($ad, 'ad_type') == 'image')
                    <a href="{{ data_get($ad, 'ad_url', '#') ?  data_get($ad, 'ad_url', '#') : '#'}}">
                        @if(isFileExist(@$ad->adImage, $result = @$ad->adImage->original_image))
                            <img src="{{ safari_check() ? basePath(@$ad->adImage).'/'.$result : static_asset('default-image/default-add-728x90.png') }} " data-original=" {{basePath($ad->adImage)}}/{{ $result }} " class="img-fluid"   alt="{!! $ad->ad_name !!}"  >

                        @else
                            <img src="{{static_asset('default-image/default-add-728x90.png') }} "  class="img-fluid"   alt="{!! $ad->ad_name !!}" >
                        @endif
                    </a>
                @elseif(data_get($ad, 'ad_type') == 'code')
{!! base64_decode($ad->ad_code ?? '') !!}
                @elseif(data_get($ad, 'ad_type') == 'text')
                    {!! $ad->ad_text ?? '' !!}
                @endif --}}
            </div>
        </div>
    </div>
@endif
