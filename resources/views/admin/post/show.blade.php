@extends('layouts.backend.app')
@section('title','Post')

@push('css')
{{--css--}}
@endpush
@section('content')
    <div class="container-fluid">
        <a href="{{route('admin.post.index')}}" class="btn btn-danger waves-effect ">Back</a>
        @if ($post->is_approved == false)
            <button type="button" class="btn btn-success pull-right waves-effect" onclick="approvedpost({{$post->id}})">
                <i class="material-icons">done</i>
                <span>Approve</span>
            </button>
            <form method="POST" action="{{route('admin.post.approve',$post->id)}}" id="approval-form" style="display: none">
                @csrf
                @method('PUT')
            </form>

        @else
            <button type="button" class="btn btn-success pull-right waves-effect" disabled>
                <i class="material-icons">done</i>
                <span>Approved</span>
            </button>
        @endif
        <br><br>
        <div class="row clearfix">
            <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="header bg-blue">
                        <h2>
                         <span style="color: #2d2d2d">Post Title :</span> {{$post->title}}
                            <small style="margin-left: 30px">posted by <strong class="color-name"> <a href="">{{$post->user->name}}</a></strong> on {{$post->created_at->toFormattedDateString()}}</small>
                        </h2>
                    </div>
                    <div class="body">
                        {!! $post->body !!}
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="header bg-blue">
                        <h2>
                            Categories
                        </h2>
                    </div>
                    <div class="body">
                        @foreach($post->categories as $categories)
                        <span class="label bg-cyan">{{$categories->name}}</span>
                        @endforeach
                    </div>
                </div>
                <div class="card">
                    <div class="header bg-green">
                        <h2>
                            Tags
                        </h2>
                    </div>
                    <div class="body">
                        @foreach ($post->tags as $tags)
                        <span class="label bg-green">{{$tags->name}}</span>
                        @endforeach
                    </div>
                </div>
                <div class="card">
                    <div class="header bg-yellow">
                        <h2>
                            Image
                        </h2>
                    </div>
                    <div class="body">
                        <img class="img-responsive thumbnail" src="{{asset('public/storage/post/'.$post->image)}}" alt="">
                    </div>
                </div>
            </div>
        </div>
        </div>
    </form>
@endsection

@push('js')
    {{--sweet alert--}}

    <script type="text/javascript">
        function approvedpost(id) {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
                title: 'Are you sure?',
                text: "You want to approve this post!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Approve it!',
                cancelButtonText: 'No, cancel!',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    event.preventDefault();
                    document.getElementById('approval-form').submit();
                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons.fire(
                        'Cancelled',
                        'The post remain pending :)',
                        'info'
                    )
                }
            })
        }
    </script>
@endpush

