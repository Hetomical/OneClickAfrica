<div class="sg-widget">
    <h3 class="widget-title">{{ data_get($detail, 'title') }}</h3>
    <div class="row">
        @foreach($content as $post)
            <div class="col-md-6">
                <div class="sg-post small-post">
                    @include('site.partials.home.category.block')
                    <div class="entry-content">
                        <a href="{{ route('article.detail', ['id' => $post->slug]) }}"><p>{!! \Illuminate\Support\Str::limit($post->title, 25) !!}</p></a>
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
                                                                                        {{-- <li><i class="fa fa-eye">{{ $post->total_hit }}</i></li> --}}
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
