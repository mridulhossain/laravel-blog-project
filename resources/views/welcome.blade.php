@extends('layouts.frontend.app')
@section('title','Login')
@push('css')
    <link href="{{asset('public/assets/frontend/css/home/styles.css')}}" rel="stylesheet">
    <link href="{{asset('public/assets/frontend/css/home/responsive.css')}}" rel="stylesheet">
    <style>
        .favorite_post{
            color: red;
        }
    </style>
@endpush
@section('content')
    <div class="main-slider">
        <div class="swiper-container position-static" data-slide-effect="slide" data-autoheight="false"
             data-swiper-speed="500" data-swiper-autoplay="10000" data-swiper-margin="0" data-swiper-slides-per-view="4"
             data-swiper-breakpoints="true" data-swiper-loop="true" >
            <div class="swiper-wrapper">


                @foreach($catagory as $value)
                    <div class="swiper-slide">
                        <a class="slider-category" href="{{route('category.post',$value->slug)}}">
                            <div class="blog-image"><img src="{{asset('public/storage/category/slider/'.$value->image)}}" alt="{{$value->name}}"></div>

                            <div class="category">
                                <div class="display-table center-text">
                                    <div class="display-table-cell">
                                        <h3><b>{{$value->name}}</b></h3>
                                    </div>
                                </div>
                            </div>

                        </a>
                    </div><!-- swiper-slide -->
                @endforeach
            </div><!-- swiper-wrapper -->

        </div><!-- swiper-container -->

    </div><!-- slider -->

    <section class="blog-area section">
        <div class="container">

            <div class="row">



                @foreach($post as $value)
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
                                        <li><a href="{{route('post.details',$value->slug)}}"><i class="ion-chatbubble"></i>{{$value->comments->count()}}</a></li>
                                        <li><a href="#"><i class="ion-eye"></i>{{$value->view_count}}</a></li>
                                    </ul>

                                </div><!-- blog-info -->
                            </div><!-- single-post -->
                        </div><!-- card -->
                    </div><!-- col-lg-4 col-md-6 -->
                @endforeach

            </div><!-- row -->

            <a class="load-more-btn" href="#"><b>LOAD MORE</b></a>

        </div><!-- container -->
    </section><!-- section -->
@endsection

@push('js')
{{--js--}}
@endpush
