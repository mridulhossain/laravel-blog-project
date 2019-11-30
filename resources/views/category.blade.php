@extends('layouts.frontend.app')
@section('title','Category')

@push('css')
    <link href="{{asset('public/assets/frontend/css/category/responsive.css')}}" rel="stylesheet">
    <link href="{{asset('public/assets/frontend/css/category/styles.css')}}" rel="stylesheet">
    <style>
        .slider {
            height: 400px;
            width: 100%;
            background-image: url({{asset('public/storage/category/'.$category->image)}});
            background-size: cover;
        }
    </style>
@endpush
@section('content')
    <div class="slider display-table center-text">
        <h1 class="title display-table-cell"><b>{{$category->name}}</b></h1>
    </div><!-- slider -->

    <section class="blog-area section">
        <div class="container">

            <div class="row">
                @if($category->posts->count()>0)
                    @foreach($category->posts as $value)
                        <div class="col-lg-4 col-md-6">
                            <div class="card h-100">
                                <div class="single-post post-style-1">

                                    <div class="blog-image"><img src="{{asset('public/storage/post/'.$value->image)}}" alt="{{$value->title}}"></div>

                                    <a class="avatar" href="#"><img src="{{asset('public/storage/profile/'.$value->user->image)}}" alt="Profile Image"></a>

                                    <div class="blog-info">

                                        <h4 class="title"><a href="{{route('post.details',$value->slug)}}"><b>{{$value->title}}</b></a></h4>

                                        <ul class="post-footer">
                                            <li>
                                                @guest
                                                    <a href="javascript:void(0);" onclick="toastr.info('To Add Favorite List, You Have To Login First!','Info',{
                                                    closeButton : true,
                                                    progressBar : true,
                                                })"><i class="ion-heart"></i>{{$value->favorite_to_users->count()}}</a>
                                                @else
                                                    <a href="javascript:void(0);" onclick="document.getElementById('favorite-form-{{$value->id}}').submit();"

                                                       class="{{!Auth::user()->favorite_posts->where('pivot.post_id',$value->id)->count()==0?'favorite_post':''}}"><i class="ion-heart"></i>{{$value->favorite_to_users->count()}}</a>
                                                    <form id="favorite-form-{{$value->id}}" method="POST" action="{{route('post.favorite',$value->id)}}" style="display: none">
                                                        @csrf
                                                    </form>
                                                @endguest

                                            </li>
                                            <li><a href="{{route('post.details',$value->slug)}}"><i class="ion-chatbubble"></i>{{$value->comments->count() }}</a></li>
                                            <li><a href="#"><i class="ion-eye"></i>{{$value->view_count}}</a></li>
                                        </ul>

                                    </div><!-- blog-info -->
                                </div><!-- single-post -->
                            </div><!-- card -->
                        </div><!-- col-lg-4 col-md-6 -->
                    @endforeach
                 @else
                    <h1>No Post Found For This Category!!</h1>
                @endif

            </div><!-- row -->
        </div><!-- container -->
    </section><!-- section -->


    <footer>

        <div class="container">
            <div class="row">

                <div class="col-lg-4 col-md-6">
                    <div class="footer-section">

                        <a class="logo" href="#"><img src="images/logo.png" alt="Logo Image"></a>
                        <p class="copyright">Bona @ 2017. All rights reserved.</p>
                        <p class="copyright">Designed by <a href="https://colorlib.com" target="_blank">Colorlib</a></p>
                        <ul class="icons">
                            <li><a href="#"><i class="ion-social-facebook-outline"></i></a></li>
                            <li><a href="#"><i class="ion-social-twitter-outline"></i></a></li>
                            <li><a href="#"><i class="ion-social-instagram-outline"></i></a></li>
                            <li><a href="#"><i class="ion-social-vimeo-outline"></i></a></li>
                            <li><a href="#"><i class="ion-social-pinterest-outline"></i></a></li>
                        </ul>

                    </div><!-- footer-section -->
                </div><!-- col-lg-4 col-md-6 -->

                <div class="col-lg-4 col-md-6">
                    <div class="footer-section">
                        <h4 class="title"><b>CATAGORIES</b></h4>
                        <ul>
                            <li><a href="#">BEAUTY</a></li>
                            <li><a href="#">HEALTH</a></li>
                            <li><a href="#">MUSIC</a></li>
                        </ul>
                        <ul>
                            <li><a href="#">SPORT</a></li>
                            <li><a href="#">DESIGN</a></li>
                            <li><a href="#">TRAVEL</a></li>
                        </ul>
                    </div><!-- footer-section -->
                </div><!-- col-lg-4 col-md-6 -->

                <div class="col-lg-4 col-md-6">
                    <div class="footer-section">

                        <h4 class="title"><b>SUBSCRIBE</b></h4>
                        <div class="input-area">
                            <form>
                                <input class="email-input" type="text" placeholder="Enter your email">
                                <button class="submit-btn" type="submit"><i class="icon ion-ios-email-outline"></i></button>
                            </form>
                        </div>

                    </div><!-- footer-section -->
                </div><!-- col-lg-4 col-md-6 -->

            </div><!-- row -->
        </div><!-- container -->
    </footer>
@endsection
@push('js')
    {{--js--}}
@endpush
