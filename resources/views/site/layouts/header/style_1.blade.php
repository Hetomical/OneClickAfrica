<header class="sg-header">
    <div class="sg-topbar">
        <div class="container">
            <div class="d-md-flex justify-content-md-between">
                <div class="left-contennt">
                    <ul class="global-list">
                        <li><i class="fa fa-calendar mr-2" aria-hidden="true"></i>{{ Carbon\Carbon::parse(date('l, d F Y'))->translatedFormat('l, d F Y')}}</li>
                    </ul>
                </div>
                <div class="right-content d-flex">
                        <div class="d-flex">
                            {{-- @if(settingHelper('submit_news_status')==1)
                                <div class="submit-news d-none d-md-block">
                                    <a href="{{ route('submit.news.form') }}">{{__('submit_now')}}</a>
                                </div>
                            @endif --}}
                            <input type="hidden" id="url" value="{{ url('') }}">
                            <div class="sg-language">
                                <select name="code" id="languges-changer">
                                    @foreach ($activeLang as $lang)
                                        <option value="{{$lang->code}}" {{ \App::getLocale() == ""? ( settingHelper('default_language') == $lang->code? 'selected':'' ) : (\App::getLocale() == $lang->code ? 'selected':'') }}>{{ $lang->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    <div class="d-flex ">
                        <div class="sg-social d-none d-xl-block mr-md-5">
                            <ul class="global-list">
                                @foreach($socialMedias as $socialMedia)
                                <li><a href="{{$socialMedia->url}}" target="_blank" name="{{$socialMedia->name}}"><i class="{{$socialMedia->icon}}" aria-hidden="true"></i></a></li>
                                @endforeach
                                <li><a href="{{ url('feed') }}" name="{{__('feed')}}"><i class="fa fa-rss" aria-hidden="true"></i></a></li>
                            </ul>
                        </div>
                        {{-- <div class="sg-user">
                            @if(Cartalyst\Sentinel\Laravel\Facades\Sentinel::check())
                            <div class="dropdown">
                                <a class="nav-user-img" href="#" id="navbarDropdownMenuLink2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    @if(Sentinel::getUser()->profile_image != null)
                                        <img src="{{static_asset('default-image/user.jpg') }}" data-original="{{static_asset(Sentinel::getUser()->profile_image)}}"  class="profile">
                                    @else
                                        <i class="fa fa-user-circle mr-2"></i>
                                    @endif
                                    {{ Sentinel::getUser()->first_name}}<i class="fa fa-angle-down  ml-2" aria-hidden="true"></i></a>

                                <div class="dropdown-menu dropdown-menu-right nav-user-dropdown site-setting-area" aria-labelledby="navbarDropdownMenuLink2">

                                    @if(Sentinel::getUser()->roles[0]->id != 4 && Sentinel::getUser()->roles[0]->id != 5)
                                    <a class="" href="{{ route('dashboard') }} " target="_blank"><i class="fa fa-tachometer mr-2" aria-hidden="true"></i>{{__('dashboard')}}</a>
                                    @endif
                                    <a class=""  href="{{ route('site.profile') }}"><i class="fa fa-user mr-2"></i>{{__('profile')}}</a>

                                    <a class=""  href="{{ route('site.profile.form') }}"><i class="fa fa-cog mr-2"></i>{{__('edit_profile')}}</a>

                                    <a class="" href="{{ route('site.logout') }}"><i class="fa fa-power-off mr-2"></i>{{__('logout')}}</a>

                                </div>
                            </div>
                            @else
                                <span>
                                    <i class="fa fa-user-circle mr-2" aria-hidden="true"></i>
                                    <a href="{{ route('site.login.form') }}">{{ __('login') }}</a> <span class="d-none-small">/ <a href="{{ route('site.register.form') }}"> {{ __('sign_up') }}</a></span>
                                </span>
                            @endif
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="sg-menu">
        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <div class="menu-content">
                    <a class="navbar-brand" href="{{ route('home') }}">
                        <img src="{{ static_asset(settingHelper('logo')) }}" alt="Logo" class="img-fluid">
                    </a>

                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"><i class="fa fa-align-justify"></i></span>
                    </button>

                    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                        <ul class="navbar-nav">

                            @foreach($primaryMenu as $mainMenu)


@php
                $label = strtolower(trim($mainMenu->label));
            @endphp

            {{-- 🔥 Skip only "gallery" and "page" --}}
            @if($label === 'gallery'  || $label === 'pages')
                @continue
            @endif

                                    <li class="nav-item sg-dropdown">
                                    
 <a 
    href="{{ strtolower(trim($mainMenu->label)) == 'home' ? route('home') : menuUrl($mainMenu) }}" 
    target="{{ $mainMenu->new_teb == 1 ? '_blank' : '' }}"
>
    {{ $mainMenu->label }}
</a>
                                        {{-- <ul class="sg-dropdown-menu">
                                            @foreach($mainMenu->children as $child)
                                                <li class=""><a href="{{menuUrl($child)}}" target="{{$child->new_teb == 1? '_blank':''}}">{{$child->label == 'gallery'? __('gallery'):$child->label}} @if(!blank($child->children)) <span class="pull-right"><i class="fa fa-angle-right" aria-hidden="true"></i></span>@endif</a>
                                                    <ul class="sg-dropdown-menu-menu">
                                                        @foreach($child->children as $subChild)
                                                            <li class=""><a href="{{menuUrl($subChild)}}" target="{{$subChild->new_teb == 1? '_blank':''}}">{{$subChild->label == 'gallery'? __('gallery'):$subChild->label}}</a></li>
                                                        @endforeach
                                                    </ul>
                                                </li>
                                            @endforeach
                                        </ul> --}}
                                    </li>

                                
                              
                            @endforeach
                        </ul>
                    </div>

                    <div class="sg-search">
                        <div class="search-form">
                            <form action="{{ route('article.search') }}" id="search" method="GET">
                                <label for="label" class="d-none">{{ __('search') }}</label>
                                <input class="form-control" id="label" name="search" type="text" placeholder="{{ __('search') }}">
                                <button type="submit"><i class="fa fa-search"></i><span class="d-none">{{__('search')}}</span></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </div>

</header>

<div class="container">
    <div class="row">
        <div class="col-12">
            @if(session('error'))
                <div id="error_m" class="alert alert-danger">
                    {{session('error')}}
                </div>
            @endif
            @if(session('success'))
                <div id="success_m" class="alert alert-success">
                    {{session('success')}}
                </div>
            @endif
            @isset($errors)
            @if ($errors->any())
                <div class="alert alert-danger" id="error_m">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @endif
        </div>
    </div>
</div>

@include('site.partials.ads', ['ads' => $headerWidgets])
