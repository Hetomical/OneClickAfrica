<div class="footer footer-style-1">
    <div class="footer-top">
        <div class="container">
            <div class="footer-content">
                <div class="row">
                    @foreach($footerWidgets as $widget)
                        @if($widget['view'] == 'popular_post')
                            @include('site.widgets.footer.popular_post', $widget)
                        @elseif($widget['view'] == 'editor_picks')
                            @include('site.widgets.footer.editor_picks', $widget)
                        @elseif($widget['view'] == 'categories')
                            @include('site.widgets.footer.categories', $widget)
                        @elseif($widget['view'] == 'newsletter')
                            @include('site.widgets.footer.newsletter', $widget)
                        @endif
                    @endforeach
                </div><!-- /.row -->
            </div>
        </div><!-- /.container -->
    </div>
    <div class="footer-bottom">
    
        <div class="container text-center" style="display: flex; justify-content: center; align-items: center; flex-wrap: wrap; gap: 10px;">
        <a href="{{ route('home') }}">Home</a>
    <a href="{{ url('/page/about-us') }}">About</a>
    <a href="{{ url('/page/contact-us') }}">Contact</a>
    <a href="{{ url('/page/privacy-policy') }}">Private Policy</a>
    <a href="{{ url('/login') }}">Login</a>

    </div>

    <br/>
<div style="display:block; text-align:center;"> 
   <span>{{ settingHelper('copyright_text') }}</span>
</div>    
<!-- /.container -->
    </div>
</div><!-- /.footer -->
