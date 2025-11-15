@php
    // Ensure latest category shows first
    $categorySections = $categorySections->reverse();
@endphp

@foreach($categorySections as $categorySection)
    @php
        if($categorySection->type != 3):
            $viewFile = 'site.partials.home.category.'. data_get($categorySection, 'section_style', 'style_3');
        else:
            $viewFile = 'site.partials.home.category.latest_posts';
        endif;

        if($categorySection->type == 1):
            $posts = data_get($categorySection, 'post', collect([]));
        elseif($categorySection->type == 2):
            $posts = $video_posts;
        elseif($categorySection->type == 3):
            $posts = $latest_posts;
        endif;
    @endphp

    @if(!blank($posts))
        @if(view()->exists($viewFile))
            @include($viewFile, [
                '$categorySection' => $categorySection,
                '$posts' => $posts
            ])
        @endif

        @if(data_get($categorySection, 'ad') != "")
            @include('site.partials.home.category.ad_section', ["ad" => data_get($categorySection, 'ad')])
        @endif
    @endif
@endforeach
