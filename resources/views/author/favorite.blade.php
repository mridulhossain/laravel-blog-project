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
                        ALL FAVORITE POSTS
                        <span class="badge bg-blue">{{$posts->count()}}</span>
                    </h2>
                </div>
                <div class="body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Author</th>
                                <th><i class="material-icons">favorite</i></th>
{{--                                <th><i class="material-icons">comment</i></th>--}}
                                <th><i class="material-icons">visibility</i></th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Author</th>
                                <th><i class="material-icons">favorite</i></th>
                                {{--                                <th><i class="material-icons">comment</i></th>--}}
                                <th><i class="material-icons">visibility</i></th>
                                <th>Action</th>
                            </tr>
                            </tfoot>
                            <tbody>

                           @foreach($posts as $key=>$value)
                               <tr>
                                    <td>{{$key+1}}</td>
                                    <td>{{str_limit($value->title,'10')}}</td>
                                    <td>{{$value->user->name}}</td>
                                    <td>{{$value->favorite_to_users()->count()}}</td>
                                    <td>{{$value->view_count}}</td>
                                    <td>
                                        <a href="{{route('admin.post.show',$value->id)}}" class="btn btn-info waves-effect">
                                            <i class="material-icons">visibility</i>
                                        </a>

                                       <button type="button" class="btn btn-danger" onclick="deleteTag({{$value->id}})"><i class="material-icons">delete</i></button>
                                        <form id="delete-form-{{$value->id}}" action="{{route('post.favorite',$value->id)}}" method="POST" style="display: none">
                                            @csrf
                                        </form>
                                    </td>
                                </tr>
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
        function deleteTag(id) {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
                title: 'Are you sure?',
                text: "Are you want to remove this post from your favorite list!",
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

