<div class="sg-section">
    <div class="section-content mt-3">
        <div class="section-title">
            <h1>{{ __('latest_post') }}</h1>
        </div>
        <div class="latest-post-area">
            @foreach($posts as $post)
                <div class="sg-post medium-post-style-1">
                    <div class="entry-header">
                        <div class="entry-thumbnail">
                            <a href="{{ route('article.detail', ['id' => $post->slug]) }}">
                                @if(isFileExist($post->image, $result =  @$post->image->medium_image))
                                    <img src="{{safari_check() ? basePath(@$post->image).'/'.$result : static_asset('default-image/default-358x215.png') }} " data-original="{{basePath($post->image)}}/{{ $result }}" class="img-fluid"   alt="{!! $post->title !!}"  >
                                @else
                                    <img src="{{static_asset('default-image/default-358x215.png') }}"  class="img-fluid"   alt="{!! $post->title !!}" >
                                @endif
                            </a>
                        </div>
                        @if($post->post_type=="video")
                            <div class="video-icon large-block">
                                <img src="{{static_asset('default-image/video-icon.svg') }} " alt="video-icon">
                            </div>
                        @elseif($post->post_type=="audio")
                            <div class="video-icon large-block">
                                <img src="{{static_asset('default-image/audio-icon.svg') }} " alt="audio-icon">
                            </div>
                        @endif
                    </div>
                    <div class="category">
                        <ul class="global-list">
                            @isset($post->category->category_name)
                                <li><a href="{{ url('category',$post->category->slug) }}">{{ $post->category->category_name }}</a></li>
                            @endisset
                        </ul>
                    </div>

                    <div class="entry-content align-self-center">
                        <h3 class="entry-title">
                            <a href="{{ route('article.detail', ['id' => $post->slug]) }}">{!! \Illuminate\Support\Str::limit($post->title, 65) !!}</a>
                        </h3>
                        <div class="entry-meta mb-2">
                            <ul class="global-list">
                                <li>
    {{ __('post_by') }}
    @if(!is_null($post->source_content))
        {{ $post->source_content }}
    @else
        {{ $post->user->first_name }}
    @endif
</li>
                                                        <li><a href="#"> {{ Carbon\Carbon::parse($post->updated_at)->translatedFormat('F j, Y') }}</a></li>
                                                                                        {{-- <li><i class="fa fa-eye">{{ $post->total_hit }}</i></li> --}}
                            </ul>
                        </div>
                        <p style="margin-bottom:10px;">{!! strip_tags(\Illuminate\Support\Str::limit($post->content, 120)) !!}</p>
                        <a style="color:green;" href="{{ route('article.detail', ['id' => $post->slug]) }}" class="read-more-link">Read More →</a>
                    </div>
                </div>
            @endforeach
        </div>
        @if($posts->count() != 0)
        <input type="hidden" id="last_id" value="1">
        <div class="col-sm-12 col-xs-12 d-none" id="latest-preloader-area">
            <div class="row latest-preloader">
                <div class="col-md-7 offset-md-5">
                    <img src="{{static_asset('site/images/')}}/preloader-2.gif" alt="Image" class="tr-preloader img-fluid">
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-xs-12">
            <div class="row">
                <button class="btn-load-more {{ $totalPostCount > 10? '':'d-none'}}" id="btn-load-more"> {{ __('load_more') }} </button>
                <button class="btn-load-more {{ $totalPostCount > 10? 'd-none':''}}" id="no-more-data">
                    {{ __('no_more_records') }}
                </button>
                <input type="hidden" id="url" value="{{ url('') }}">
            </div>
        </div>
        @endif
    </div>
</div>

<style>
/* Mobile layout with text wrapping around image */
@media (max-width: 575px) {
    .sg-section .latest-post-area .sg-post.medium-post-style-1 {
        display: block !important;
        margin-bottom: 24px !important;
        padding-bottom: 16px !important;
        border-bottom: 1px solid #eee;
        clear: both;
    }

    /* Clearfix */
    .sg-section .latest-post-area .sg-post.medium-post-style-1::after {
        content: "";
        display: table;
        clear: both;
    }

    /* Float image to the left */
    .sg-section .latest-post-area .sg-post.medium-post-style-1 .entry-header {
        float: left !important;
        width: 110px !important;
        margin: 0 14px 10px 0 !important;
        position: relative;
    }

    .sg-section .latest-post-area .sg-post.medium-post-style-1 .entry-thumbnail {
        width: 100% !important;
        height: 85px !important;
        border-radius: 8px;
        overflow: hidden;
    }

    .sg-section .latest-post-area .sg-post.medium-post-style-1 .entry-thumbnail a {
        display: block;
        width: 100%;
        height: 100%;
    }

    .sg-section .latest-post-area .sg-post.medium-post-style-1 .entry-thumbnail img {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover;
        border-radius: 8px;
    }

    .sg-section .latest-post-area .sg-post.medium-post-style-1 .video-icon {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 10;
    }

    /* Content wraps around image */
    .sg-section .latest-post-area .sg-post.medium-post-style-1 .category {
        margin-bottom: 6px;
    }

    .sg-section .latest-post-area .sg-post.medium-post-style-1 .entry-content {
        display: block !important;
    }

    .sg-section .latest-post-area .sg-post.medium-post-style-1 .entry-title {
        margin: 0 0 8px 0;
        line-height: 1.35;
    }

    .sg-section .latest-post-area .sg-post.medium-post-style-1 .entry-title a {
        font-size: 15px;
        font-weight: 600;
        line-height: 1.35;
    }

    .sg-section .latest-post-area .sg-post.medium-post-style-1 .entry-meta {
        margin-bottom: 8px !important;
    }

    .sg-section .latest-post-area .sg-post.medium-post-style-1 .entry-meta ul {
        font-size: 11px;
    }

    .sg-section .latest-post-area .sg-post.medium-post-style-1 .entry-content p {
        margin: 0 0 8px 0;
        font-size: 13px;
        line-height: 1.5;
    }

    .sg-section .latest-post-area .sg-post.medium-post-style-1 .read-more-link {
        color: red;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
        margin-top: 4px;
        font-size: 13px;
    }

    .sg-section .latest-post-area .sg-post.medium-post-style-1 .read-more-link:hover {
        text-decoration: underline;
    }
}
</style>