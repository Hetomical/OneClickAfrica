<div class="sg-widget">
    <h3 class="widget-title">{{ data_get($detail, 'title') }}</h3>

    <ul class="global-list">
        @foreach($content as $post)
            <li>
                <div class="sg-post small-post post-style-1">
                
                    {{-- @include('site.partials.home.category.post_block') --}}
                    <div class="entry-header">
    <div class="entry-thumbnail">
        <a href="{{ route('article.detail', ['id' => $post->slug]) }}">
            @if(isFileExist($post->image, $result = @$post->image->small_image))
                <img src="{{ safari_check() ? basePath(@$post->image).'/'.$result : static_asset('default-image/default-123x83.png') }} "
                     data-original=" {{basePath($post->image)}}/{{ $result }} "
                     class="img-fluid lazy" width="100%" height="100%" alt="{!! $post->title !!}">
            @else
                <img src="{{static_asset('default-image/default-123x83.png') }} " class="img-fluid"
                     alt="{!! $post->title !!}">
            @endif
        </a>
    </div>
    @if($post->post_type=="video")
        <div class="video-icon x-small-block">
            <img src="{{static_asset('default-image/video-icon.svg') }} " alt="video-icon">
        </div>
    @elseif($post->post_type=="audio")
        <div class="video-icon x-small-block">
            <img src="{{static_asset('default-image/audio-icon.svg') }} " alt="audio-icon">
        </div>
    @endif
</div>

                    <div class="entry-content">
                       <a href="{{ route('article.detail', ['id' => $post->slug]) }}"> <p>{{ \Illuminate\Support\Str::limit(data_get($post, 'title'), 25) }}</p></a>
                        <div class="entry-meta">
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
                                                                                        <li><i class="fa fa-eye">{{ $post->total_hit }}</i></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </li>
        @endforeach
    </ul>
</div>

<style>

@media (max-width: 575px) {

    /* Make each small post horizontal */
    .sg-post.small-post.post-style-1 {
        display: flex !important;
        flex-direction: row !important;
        align-items: flex-start !important;
        gap: 10px !important;
    }

    /* Thumbnail (left) */
    .sg-post.small-post.post-style-1 .entry-header {
        width: 35% !important;
        flex-shrink: 0 !important;
        position: relative;
    }

    .sg-post.small-post.post-style-1 .entry-thumbnail {
        width: 100% !important;
        height: 80px !important;
        overflow: hidden;
        border-radius: 6px;
        position: relative;
    }

    .sg-post.small-post.post-style-1 .entry-thumbnail img {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
    }

    /* Content (right) */
    .sg-post.small-post.post-style-1 .entry-content {
        width: 65% !important;
    }

    .sg-post.small-post.post-style-1 .entry-content p {
        margin: 0 0 5px 0;
        font-size: 13px;
        line-height: 1.3;
    }

    .sg-post.small-post.post-style-1 .entry-meta ul {
        font-size: 11px !important;
    }

    /* Video icon — keep centered on thumbnail */
    .sg-post.small-post.post-style-1 .video-icon.x-small-block {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 5;
    }
}

</style>