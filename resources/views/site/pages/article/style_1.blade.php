<div class="entry-header">
    <div class="entry-thumbnail" height="100%">
        @include('site.pages.article.partials.detail_image')
    </div>
</div>

<div class="entry-content p-4">
    <h3 class="entry-title">{!! $post->title ?? '' !!}</h3>
    <div class="entry-meta mb-2">
        <ul class="global-list">
            <li><i class="fa fa-calendar-minus-o" aria-hidden="true"></i>
                <a href="{{route('article.date', date('Y-m-d', strtotime($post->updated_at)))}}">{{ Carbon\Carbon::parse($post->updated_at)->locale('bn')->translatedFormat('F j, Y') }}</a>
            </li>
 <li><i class="fa fa-eye"></i> {{ $post->total_hit }}</li>

    <!-- Share Button -->
    <li class="share-li">
        <button class="share-btn">
            <i class="fa fa-share-alt"></i> Share
        </button>
        <div class="social-icons">
            <a href="#" class="facebook" target="_blank"><i class="fa fa-facebook"></i></a>
            <a href="#" class="twitter" target="_blank"><i class="fa fa-twitter"></i></a>
            <a href="#" class="whatsapp" target="_blank"><i class="fa fa-whatsapp"></i></a>
            <a href="#" class="linkedin" target="_blank"><i class="fa fa-linkedin"></i></a>
        </div>
    </li>
    <style>
    
    .post-meta {
    list-style: none;
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 0;
    margin: 0;
}

.share-li {
    position: relative;
}

.share-btn {
    background: red; /* dark orange */
    color: #fff;
    border: none;
    padding: 6px 12px;
    border-radius: 20px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 14px;
}

.share-btn:hover {
    background: black;
    transform: scale(1.05);
}

.social-icons {
    position: absolute;
    top: 40px;
    left: 0;
    display: none;
    flex-direction: row;
    gap: 10px;
    background: #111;
    padding: 8px 10px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.3);
    z-index: 10;
}

.social-icons a {
    color: white;
    font-size: 16px;
    transition: transform 0.2s;
}

.social-icons a:hover {
    transform: scale(1.2);
}

/* Colors for each icon */
.social-icons .facebook { color: #1877F2; }
.social-icons .twitter { color: #1DA1F2; }
.social-icons .whatsapp { color: #25D366; }
.social-icons .linkedin { color: #0A66C2; }
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const shareBtn = document.querySelector('.share-btn');
    const socialIcons = document.querySelector('.social-icons');
    
    shareBtn.addEventListener('click', function() {
        socialIcons.style.display = socialIcons.style.display === 'flex' ? 'none' : 'flex';
    });

    const pageUrl = encodeURIComponent(window.location.href);
    const pageTitle = encodeURIComponent(document.title);

    document.querySelector('.facebook').href = `https://www.facebook.com/sharer/sharer.php?u=${pageUrl}`;
    document.querySelector('.twitter').href = `https://twitter.com/intent/tweet?url=${pageUrl}&text=${pageTitle}`;
    document.querySelector('.whatsapp').href = `https://api.whatsapp.com/send?text=${pageTitle}%20${pageUrl}`;
    document.querySelector('.linkedin').href = `https://www.linkedin.com/sharing/share-offsite/?url=${pageUrl}`;
});
</script>

        </ul>
    </div>
    @if(@$post->post_type == 'audio')
        @include('site.pages.article.partials.audio')
    @endif
    <div class="paragraph">
        {!! $post->content !!}
    </div>
    @if(isset($post->read_more_link))
        <div class="rss-content-actual-link">
            <a href="{{ $post->read_more_link }}" class="btn btn-primary" target="_blank">{{ __('read_actual_content') }} <i class="fa fa-long-arrow-right"></i>
            </a>
        </div>
    @endif
    @include('site.pages.article.partials.content')
    @if(settingHelper('adthis_option')==1 and settingHelper('addthis_public_id')!=null and settingHelper('addthis_toolbox')!=null)
        {!! base64_decode(settingHelper('addthis_toolbox')) !!}
    @endif

    @if(@$post->post_type == 'trivia-quiz')
        @include('site.pages.article.partials.trivia-quiz')
    @endif
    @if(@$post->post_type == 'personality-quiz')
        @include('site.pages.article.partials.personality-quiz')
    @endif

    @if(@$post->user->permissions['author_show'] == 1)
        @include('site.pages.article.partials.author')
    @endif

</div>
