@extends('layouts.backend.app')
@section('title','Favorite post')

@push('css')
    <link href="{{asset('public/assets/backend/plugins/jquery-datatable/skin/bootstrap/css/dataTables.bootstrap.css')}}" rel="stylesheet">
@endpush
@section('content')

    <!-- Exportable Table -->
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>
                        ALL COMMENTS
                    </h2>
                </div>
                <div class="body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                            <thead>
                            <tr>
                                <th class="text-center">Comment Info</th>
                                <th class="text-center">Post Info</th>
                                <th class="text-center">Action</th>
                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <th class="text-center">Comment Info</th>
                                <th class="text-center">Post Info</th>
                                <th class="text-center">Action</th>
                            </tr>
                            </tfoot>
                            <tbody>

                           @foreach($posts as $post)
                               @foreach($post->comments as $value)
                               <tr>
                                    <td>
                                        <div class="media">
                                            <div class="media-left">
                                                <a href="#">
                                                    <img class="media-object" src="{{asset('public/storage/profile/'.$value->user->image)}}"  alt="" width="64" height="64">
                                                </a>
                                            </div>
                                            <div class="media-body">
                                                <h4 class="media-heading">
                                                    {{$value->user->name}}
                                                    <small>{{$value->created_at->diffForHumans()}}</small>
                                                </h4>
                                                <p>{{$value->comment}}</p>
                                                <a target="_blank" href="{{route('post.details',$value->post->slug.'#comments')}}">Reply</a>
                                            </div>
                                        </div>
                                    </td>
                                   <td>
                                       <div class="media">
                                           <div class="media-right">
                                               <a target="_blank" href="{{ route('post.details',$value->post->slug) }}">
                                                   <img class="media-object" src="{{ asset('public/storage/post/'.$value->post->image) }}" width="64" height="64">
                                               </a>
                                           </div>
                                           <div class="media-body">
                                               <a target="_blank" href="{{ route('post.details',$value->post->slug) }}">
                                                   <h4 class="media-heading">{{ str_limit($value->post->title,'40') }}</h4>
                                               </a>
                                               <p>by <strong>{{ $value->post->user->name }}</strong></p>
                                           </div>
                                       </div>
                                   </td>
                                   <td>
                                       <button type="button" class="btn btn-danger waves-effect" onclick="deleteComment({{ $value->id }})">
                                           <i class="material-icons">delete</i>
                                       </button>
                                       <form id="delete-form-{{ $value->id }}" method="POST" action="{{ route('author.comment.destroy',$value->id) }}" style="display: none;">
                                           @csrf
                                           @method('DELETE')
                                       </form>
                                   </td>
                                </tr>
                               @endforeach
                           @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- #END# Exportable Table -->



@endsection

@push('js')
    <script src="{{asset('public/assets/backend/plugins/jquery-datatable/jquery.dataTables.js')}}"></script>
    <script src="{{asset('public/assets/backend/plugins/jquery-datatable/skin/bootstrap/js/dataTables.bootstrap.js')}}"></script>
    <script src="{{asset('public/assets/backend/plugins/jquery-datatable/extensions/export/dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('public/assets/backend/plugins/jquery-datatable/extensions/export/buttons.flash.min.js')}}"></script>
    <script src="{{asset('public/assets/backend/plugins/jquery-datatable/extensions/export/jszip.min.js')}}"></script>
    <script src="{{asset('public/assets/backend/plugins/jquery-datatable/extensions/export/pdfmake.min.js')}}"></script>
    <script src="{{asset('public/assets/backend/plugins/jquery-datatable/extensions/export/vfs_fonts.js')}}"></script>
    <script src="{{asset('public/assets/backend/plugins/jquery-datatable/extensions/export/buttons.html5.min.js')}}"></script>
    <script src="{{asset('public/assets/backend/plugins/jquery-datatable/extensions/export/buttons.print.min.js')}}"></script>
    <script src="{{asset('public/assets/backend/js/pages/tables/jquery-datatable.js')}}"></script>

{{--sweet alert--}}

    <script type="text/javascript">
        function deleteComment(id) {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
                title: 'Are you sure?',
                text: "do you want to remove this comment!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, cancel!',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                   event.preventDefault();
                   document.getElementById('delete-form-'+id).submit();
                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons.fire(
                        'Cancelled',
                        'Your Data is safe :)',
                        'error'
                    )
                }
            })
        }
    </script>

@endpush

