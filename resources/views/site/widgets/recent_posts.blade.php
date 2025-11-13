<div class="sg-widget">
    <h3 class="widget-title">{{ data_get($detail, 'title') }}</h3>
    <div class="row">
        @foreach($content as $post)
            <div class="col-md-6">
                <div class="sg-post small-post">
                    <div class="entry-header">
                        <div class="entry-thumbnail">
                            <a href="{{ route('article.detail', ['id' => $post->slug]) }}">
                                @if(isFileExist($post->image, $result = @$post->image->medium_image_three))
                                    <img src="{{ safari_check() ? basePath(@$post->image).'/'.$result : static_asset('default-image/default-123x83.png') }} "
                                         data-original=" {{basePath($post->image)}}/{{ $result }} "
                                         class="img-fluid lazy" width="100%" height="100%" alt="{!! $post->title !!}">
                                @else
                                    <img src="{{static_asset('default-image/default-255x175.png') }} " class="img-fluid"
                                         alt="{!! $post->title !!}">
                                @endif
                            </a>
                        </div>
                        @if($post->post_type=="video")
                            <div class="video-icon small-block">
                                <img src="{{static_asset('default-image/video-icon.svg') }} " alt="video-icon">
                            </div>
                        @elseif($post->post_type=="audio")
                            <div class="video-icon small-block">
                                <img src="{{static_asset('default-image/audio-icon.svg') }} " alt="audio-icon">
                            </div>
                        @endif
                    </div>

                    <div class="entry-content">
                        <a href="{{ route('article.detail', ['id' => $post->slug]) }}">
                            <p>{!! \Illuminate\Support\Str::limit($post->title, 25) !!}</p>
                        </a>
                        <div class="entry-meta">
                            <ul class="global-list">
                                <li class="d-sm-none d-md-none d-lg-block">{{ __('post_by') }}<a href="{{ route('site.author',['id' => $post->user->id]) }}"> {{ data_get($post, 'user.first_name') }}</a></li>
                                <li> <a href="{{route('article.date', date('Y-m-d', strtotime($post->updated_at)))}}">{{ Carbon\Carbon::parse($post->updated_at)->translatedFormat('F j, Y') }}</a></li>
                            </ul>
                        </div>
                        <a href="{{ route('article.detail', ['id' => $post->slug]) }}" class="read-more-link-small">Read more →</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<style>
/* Desktop & Tablet: Left-Right Layout */
.small-post {
    display: flex;
    flex-direction: row;
    gap: 15px;
    align-items: flex-start;
    margin-bottom: 20px;
}

.small-post .entry-header {
    width: 40%;
    flex-shrink: 0;
    position: relative;
}

.small-post .entry-thumbnail {
    width: 100%;
    height: 0;
    padding-bottom: 67%; /* Aspect ratio for small images */
    position: relative;
    overflow: hidden;
    border-radius: 8px;
}

.small-post .entry-thumbnail a {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}

.small-post .entry-thumbnail img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 8px;
}

.small-post .video-icon.small-block {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 10;
}

.small-post .entry-content {
    width: 60%;
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
}

.small-post .entry-content p {
    font-size: 14px;
    line-height: 1.4;
    font-weight: 600;
    margin-bottom: 8px;
}

.small-post .entry-meta ul {
    font-size: 12px;
    margin-bottom: 8px;
}

/* Read More Link */
.read-more-link-small {
    color: #dc3545;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    display: inline-block;
    margin-top: 5px;
}

.read-more-link-small:hover {
    color: #c82333;
    text-decoration: underline;
}

/* Mobile: Keep Left-Right Layout */
@media (max-width: 767px) {
    .small-post {
        gap: 10px;
    }

    .small-post .entry-header {
        width: 40%;
    }

    .small-post .entry-content {
        width: 60%;
    }

    .small-post .entry-content p {
        font-size: 13px;
        line-height: 1.3;
    }

    .small-post .entry-meta ul {
        font-size: 11px;
    }

    .read-more-link-small {
        font-size: 12px;
    }
}

/* Very Small Mobile */
@media (max-width: 575px) {
    .small-post {
        gap: 8px;
    }

    .small-post .entry-content p {
        font-size: 12px;
    }

    .small-post .entry-meta ul {
        font-size: 10px;
    }
}
</style>