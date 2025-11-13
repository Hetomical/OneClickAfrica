@php
    //$posts = data_get($categorySection, 'category.post', collect([]));
    $blockPosts = $posts->skip(1)->take(4);
    $firstPost = $posts->first();
@endphp

<div class="sg-section">
    <div class="section-content">
        <div class="section-title">
            <h1>
                @if(data_get($categorySection, 'label') == 'videos')
                    {{__('videos')}}
                @else
                    {{ \Illuminate\Support\Str::upper(data_get($categorySection, 'label')) }}
                @endif

            </h1>
        </div>
        <div class="row">
            @if(!blank($firstPost))
                <div class="col-lg-6">
    <div style="padding:10px;" class="sg-post sg-post-horizontal">
        <div class="post-layout-flex">
            {{-- Left Side: Image/Video --}}
            <div class="entry-thumbnail-wrapper">
                <div class="entry-header">
                    <div class="entry-thumbnail">
                        <a href="{{ route('article.detail', ['id' => @$firstPost->slug]) }}">
                            @if(isFileExist(@$firstPost->image, $result =@$firstPost->image->medium_image))
                                <img src="{{ safari_check() ? basePath(@$firstPost->image).'/'.$result : static_asset('default-image/default-358x215.png') }}"
                                     data-original="{{basePath($firstPost->image)}}/{{ $result }}"
                                     class="img-fluid" alt="{!! $firstPost->title !!}">
                            @else
                                <img src="{{static_asset('default-image/default-358x215.png') }}" class="img-fluid"
                                     alt="{!! $firstPost->title !!}">
                            @endif
                        </a>
                    </div>
                    @if($firstPost->post_type=="video")
                        <div class="video-icon small-block">
                            <img src="{{static_asset('default-image/video-icon.svg') }}" alt="video-icon">
                        </div>
                    @elseif($firstPost->post_type=="audio")
                        <div class="video-icon small-block">
                            <img src="{{static_asset('default-image/audio-icon.svg') }}" alt="audio-icon">
                        </div>
                    @endif
                </div>
            </div>

            {{-- Right Side: Content --}}
            <div class="entry-content-wrapper">
                <div class="entry-content">
                    <h3 class="entry-title">
                        <a href="{{ route('article.detail', ['id' => $firstPost->slug]) }}">
                            {!! $firstPost->title !!}
                        </a>
                    </h3>
                    <div class="entry-meta mb-2">
                        <ul class="global-list">
                            <li>{{__('post_by')}} <a href="{{ route('site.author',['id' => $firstPost['user']->id]) }}">{{ data_get($firstPost, 'user.first_name') }}</a></li>
                            <li><a href="{{route('article.date', date('Y-m-d', strtotime($firstPost->updated_at)))}}">{{ Carbon\Carbon::parse($firstPost->updated_at)->translatedFormat('F j, Y') }}</a></li>
                            <li><i class="fa fa-eye"></i> {{ $firstPost->total_hit }}</li>
                        </ul>
                    </div>
                    <p style="margin-bottom:10px;" class="entry-excerpt">{!! strip_tags(\Illuminate\Support\Str::limit($firstPost->content, 100)) !!}</p>
                    <a  style="color:red;" href="{{ route('article.detail', ['id' => $firstPost->slug]) }}" class="read-more-link">
                        Read More →
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Horizontal Layout for Mobile */
.sg-post-horizontal .post-layout-flex {
    display: flex;
    gap: 15px;
    align-items: flex-start;
}

/* Left Side: Thumbnail */
.entry-thumbnail-wrapper {
    flex-shrink: 0;
    width: 120px;
    position: relative;
}

.entry-thumbnail-wrapper .entry-thumbnail {
    width: 100%;
    border-radius: 8px;
    overflow: hidden;
}

.entry-thumbnail-wrapper .entry-thumbnail img {
    width: 100%;
    height: 120px;
    object-fit: cover;
    display: block;
}

/* Video/Audio Icon for Mobile */
.video-icon.small-block {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 30px;
    height: 30px;
    z-index: 2;
}

.video-icon.small-block img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

/* Right Side: Content */
.entry-content-wrapper {
    flex: 1;
    min-width: 0;
}

.sg-post-horizontal .entry-content {
    padding: 0;
}

.sg-post-horizontal .entry-title {
    font-size: 16px;
    line-height: 1.4;
    margin-bottom: 8px;
    font-weight: 600;
}

.sg-post-horizontal .entry-title a {
    color: #333;
    text-decoration: none;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.sg-post-horizontal .entry-title a:hover {
    color: #007bff;
}

/* Meta Info */
.sg-post-horizontal .entry-meta {
    margin-bottom: 8px;
}

.sg-post-horizontal .entry-meta ul {
    padding: 0;
    margin: 0;
    list-style: none;
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    font-size: 11px;
    color: #666;
}

.sg-post-horizontal .entry-meta li {
    display: inline-flex;
    align-items: center;
}

.sg-post-horizontal .entry-meta a {
    color: #666;
    text-decoration: none;
}

.sg-post-horizontal .entry-meta a:hover {
    color: #007bff;
}

.sg-post-horizontal .entry-meta i {
    margin-right: 3px;
}

/* Excerpt */
.entry-excerpt {
    font-size: 13px;
    line-height: 1.5;
    color: #555;
    margin-bottom: 10px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Read More Link */
.read-more-link {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 13px;
    font-weight: 600;
    color: #007bff;
    text-decoration: none;
    transition: gap 0.3s ease;
}

.read-more-link:hover {
    gap: 8px;
    color: #0056b3;
}

.read-more-link i {
    font-size: 12px;
}

/* Tablet and Desktop - Keep Original Layout */
@media (min-width: 768px) {
    .sg-post-horizontal .post-layout-flex {
        display: block;
    }

    .entry-thumbnail-wrapper {
        width: 100%;
        margin-bottom: 15px;
    }

    .entry-thumbnail-wrapper .entry-thumbnail img {
        height: auto;
        aspect-ratio: 16/9;
    }

    .video-icon.small-block {
        width: 50px;
        height: 50px;
    }

    .sg-post-horizontal .entry-title {
        font-size: 20px;
        margin-bottom: 12px;
    }

    .sg-post-horizontal .entry-title a {
        -webkit-line-clamp: 3;
    }

    .sg-post-horizontal .entry-meta ul {
        font-size: 13px;
        gap: 12px;
    }

    .entry-excerpt {
        font-size: 15px;
        -webkit-line-clamp: 3;
        margin-bottom: 15px;
    }

    .read-more-link {
        font-size: 14px;
    }
}

/* Large Desktop */
@media (min-width: 1200px) {
    .sg-post-horizontal .entry-title {
        font-size: 22px;
    }

    .entry-excerpt {
        font-size: 16px;
    }
}
</style>
            @endif

            <div class="col-lg-6">
                <div class="row">
                    @foreach($blockPosts as $post)
                        <div class="col-md-6">
                            <div class="sg-post small-post">
                                @include('site.partials.home.category.block')
                                <div class="entry-content">
                                    <a href="{{ route('article.detail', ['id' => $post->slug]) }}"><p>{!! \Illuminate\Support\Str::limit($post->title, 25) !!}</p></a>
                                    <div class="entry-meta">
                                        <ul class="global-list">
                                            <li>{{ __('post_by') }} <a href="{{ route('site.author',['id' => $post->user->id]) }}">{{ data_get($post, 'user.first_name') }}</a></li>
                                            <li><a href="{{route('article.date', date('Y-m-d', strtotime($post->updated_at)))}}"> {{ Carbon\Carbon::parse($post->updated_at)->translatedFormat('F j, Y') }}</a></li>
                                             <li><i class="fa fa-eye">{{ $post->total_hit }}</i></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
